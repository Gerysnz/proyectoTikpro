<?php
// soft_delete.php
session_start();
require_once 'api/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$project_id = $_GET['id'] ?? 0;
if (!$project_id) {
    header("Location: profile.php");
    exit;
}

$pdo = getConnection();

// 1. Verificar que el proyecto es del usuario
$stmt = $pdo->prepare("SELECT user_id FROM project WHERE project_id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: profile.php");
    exit;
}

// Solo el dueño puede eliminar
if ($project['user_id'] != $_SESSION['user_id']) {
    header("Location: profile.php");
    exit;
}

// 2. SOFT DELETE - Solo marcar
$stmt = $pdo->prepare("UPDATE project SET is_deleted = TRUE WHERE project_id = ?");
$stmt->execute([$project_id]);

// 3. Redirigir
header("Location: profile.php?msg=deleted");
exit;
?>