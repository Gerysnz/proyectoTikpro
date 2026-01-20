<?php
// Script para normalizar vídeos de preupload/ y moverlos a uploads/
// Requisitos: ffmpeg instalado en el sistema


$preuploadDir = __DIR__ . '/../pre-upload/';
$uploadDir = __DIR__ . '/../uploads/';
$maxSize = 20 * 1024 * 1024; // 20MB en bytes
$logFile = __DIR__ . '/../admin/logs/cron_normalize_videos.log';

function logMsg($msg) {
    global $logFile;
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $msg\n", FILE_APPEND);
}


if (!is_dir($preuploadDir)) {
    logMsg("ERROR: No existe la carpeta preupload");
    die("No existe la carpeta preupload\n");
}
if (!is_dir($uploadDir)) {
    logMsg("ERROR: No existe la carpeta uploads");
    die("No existe la carpeta uploads\n");
}

$files = scandir($preuploadDir);
foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $src = $preuploadDir . $file;
    if (!is_file($src)) continue;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm']);
    if (!$isVideo) continue;

    $size = filesize($src);
    $dest = $uploadDir . $file;

    // Si ya cumple requisitos, solo mover
    if ($size <= $maxSize && $ext === 'mp4') {
        if (rename($src, $dest)) {
            $msg = "Movido sin cambios: $file";
            echo "$msg\n";
            logMsg($msg);
        } else {
            $msg = "ERROR al mover $file";
            echo "$msg\n";
            logMsg($msg);
        }
        continue;
    }

    // Si no cumple, normalizar con ffmpeg
    $tmpDest = $uploadDir . pathinfo($file, PATHINFO_FILENAME) . '_web.mp4';
    $cmd = "ffmpeg -i " . escapeshellarg($src) . " -vcodec libx264 -acodec aac -preset fast -crf 28 -vf 'scale=trunc(min(1280,iw)/2)*2:trunc(min(720,ih)/2)*2' -y " . escapeshellarg($tmpDest) . " 2>&1";
    exec($cmd, $output, $ret);

    if ($ret === 0 && file_exists($tmpDest) && filesize($tmpDest) <= $maxSize) {
        unlink($src);
        $msg = "Convertido y movido: $file -> " . basename($tmpDest);
        echo "$msg\n";
        logMsg($msg);
    } else {
        // Si la conversión falla o el archivo sigue siendo grande, no mover
        if (file_exists($tmpDest)) unlink($tmpDest);
        $msg = "ERROR al convertir $file";
        echo "$msg\n";
        logMsg($msg);
    }
}

