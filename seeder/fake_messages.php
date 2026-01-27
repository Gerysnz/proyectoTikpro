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

for ($i = 0; $i < 20; $i++) {
    $content = $mensajes[array_rand($mensajes)];
    // Alternar remitente y destinatario y project_id
    if ($i % 2 === 0) {
        $from = $remitent_id;
        $to = $destination_id;
        $project_id = $project_id_2; // Conversación sobre el proyecto del partner
    } else {
        $from = $destination_id;
        $to = $remitent_id;
        $project_id = $project_id_1; // Conversación sobre el proyecto del partner
    }
    $fecha = date('Y-m-d H:i:s', strtotime("-" . (20 - $i) . " minutes"));
    $stmt = $pdo->prepare("INSERT INTO message (remitent_id, destination_id, project_id, content, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$from, $to, $project_id, $content, $fecha]);
}
// Mensaje especial: el proyecto 17 (usuario 48) le envía 1 mensaje al proyecto 1 (usuario 1)

// Forzar project_id = 1 (del user 1)
$stmt = $pdo->prepare("INSERT INTO message (remitent_id, destination_id, project_id, content, created_at) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([
    $destination_id, // user 48
    $remitent_id,    // user 1
    1,               // project_id = 1 (del user 1)
    "¡Hola desde el proyecto 48 al proyecto 1!",
    date('Y-m-d H:i:s')
]);

echo "Mensajes de prueba insertados para los proyectos $project_id_1 y $project_id_2, y uno especial de 48 a 1.";