<?php
// Script para crear un proyecto manualmente para el usuario 48
require __DIR__ . '/../api/db.php';

$user_id = 48;
$title = 'Cuina Moderna';
$description = 'Projecte de cuina moderna amb receptes innovadores.';
$image_path = '/uploads/cuimoderna.jpg';
$video_path = '/uploads/video9.mp4';

$stmt = $pdo->prepare('INSERT INTO project (user_id, title, description, image_path, video_path) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$user_id, $title, $description, $image_path, $video_path]);
$project_id = $pdo->lastInsertId();

// Añadir categoría "Direcció de serveis en restauració" (id 149)
$stmt2 = $pdo->prepare('INSERT INTO project_category (project_id, category_id) VALUES (?, ?)');
$stmt2->execute([$project_id, 149]);

echo "Proyecto creado con ID $project_id para el usuario 48.";
