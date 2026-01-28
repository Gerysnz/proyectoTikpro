<?php
// Script para generar mensajes de prueba entre user_id 1 y user_id 48

date_default_timezone_set('Europe/Madrid');
require __DIR__ . '/../api/db.php';

global $pdo;
if (!isset($pdo) || !$pdo instanceof PDO) {
    die("No se pudo obtener la conexión PDO");
}

$remitent_id = 1;
$destination_id = 48;
$mensajes = [
    "Hola, ¿qué tal?",
    "¿Has visto el nuevo proyecto?",
    "Me gustaría saber más detalles.",
    "¿Cuándo tienes tiempo para hablar?",
    "¡Genial! Hablamos luego.",
    "¿Puedes enviarme el logo?",
    "Perfecto, muchas gracias.",
    "¿Tienes alguna duda?",
    "¿Te ha llegado el correu?",
    "Ens veiem demà."
];


// Obtener el project_id de ambos usuarios
$stmt1 = $pdo->prepare('SELECT project_id FROM project WHERE user_id = ? ORDER BY project_id DESC LIMIT 1');
$stmt1->execute([$remitent_id]);
$project_id_1 = $stmt1->fetchColumn();
$stmt2 = $pdo->prepare('SELECT project_id FROM project WHERE user_id = ? ORDER BY project_id DESC LIMIT 1');
$stmt2->execute([$destination_id]);
$project_id_2 = $stmt2->fetchColumn();

if (!$project_id_1 || !$project_id_2) {
    die("No se encontró proyecto para uno de los usuarios de prueba");
}
// Generar mensajes en ambos sentidos para cada proyecto
$total = 10;
$stmt = $pdo->prepare("INSERT INTO message (remitent_id, destination_id, project_id, content, created_at) VALUES (?, ?, ?, ?, ?)");
for ($i = 0; $i < $total; $i++) {
    // Conversación sobre el proyecto del user 1
    $content1 = $mensajes[array_rand($mensajes)];
    $fecha1 = date('Y-m-d H:i:s', strtotime("-" . (2 * $total - $i) . " minutes"));
    // user 1 -> user 48
    $stmt->execute([$remitent_id, $destination_id, $project_id_1, $content1, $fecha1]);
    // user 48 -> user 1
    $content2 = $mensajes[array_rand($mensajes)];
    $fecha2 = date('Y-m-d H:i:s', strtotime("-" . (2 * $total - $i - 1) . " minutes"));
    $stmt->execute([$destination_id, $remitent_id, $project_id_1, $content2, $fecha2]);

    // Conversación sobre el proyecto del user 48
    $content3 = $mensajes[array_rand($mensajes)];
    $fecha3 = date('Y-m-d H:i:s', strtotime("-" . (2 * $total - $i) . " minutes"));
    // user 48 -> user 1
    $stmt->execute([$destination_id, $remitent_id, $project_id_2, $content3, $fecha3]);
    // user 1 -> user 48
    $content4 = $mensajes[array_rand($mensajes)];
    $fecha4 = date('Y-m-d H:i:s', strtotime("-" . (2 * $total - $i - 1) . " minutes"));
    $stmt->execute([$remitent_id, $destination_id, $project_id_2, $content4, $fecha4]);
}

echo "Mensajes de prueba bidireccionales insertados para los proyectos $project_id_1 y $project_id_2.";