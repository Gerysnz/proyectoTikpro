<?php
// Script para normalizar vídeos de preupload/ y moverlos a uploads/
// Requisitos: ffmpeg instalado en el sistema

$preuploadDir = __DIR__ . '/../preupload/';
$uploadDir = __DIR__ . '/../uploads/';
$maxSize = 20 * 1024 * 1024; // 20MB en bytes

if (!is_dir($preuploadDir)) die("No existe la carpeta preupload\n");
if (!is_dir($uploadDir)) die("No existe la carpeta uploads\n");

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
        rename($src, $dest);
        echo "Movido sin cambios: $file\n";
        continue;
    }

    // Si no cumple, normalizar con ffmpeg
    $tmpDest = $uploadDir . pathinfo($file, PATHINFO_FILENAME) . '_web.mp4';
    $cmd = "ffmpeg -i " . escapeshellarg($src) . " -vcodec libx264 -acodec aac -preset fast -crf 28 -vf 'scale=trunc(min(1280,iw)/2)*2:trunc(min(720,ih)/2)*2' -y " . escapeshellarg($tmpDest) . " 2>&1";
    exec($cmd, $output, $ret);

    if ($ret === 0 && file_exists($tmpDest) && filesize($tmpDest) <= $maxSize) {
        unlink($src);
        echo "Convertido y movido: $file -> " . basename($tmpDest) . "\n";
    } else {
        // Si la conversión falla o el archivo sigue siendo grande, no mover
        if (file_exists($tmpDest)) unlink($tmpDest);
        echo "ERROR al convertir $file\n";
    }
}
