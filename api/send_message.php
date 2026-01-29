

<?php
require_once 'db.php';
header('Content-Type: application/json');
session_start();

$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$partner_id = isset($_POST['partner_id']) ? intval($_POST['partner_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validar que el usuario autenticado es uno de los participantes
$session_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if (!$session_user_id || ($session_user_id !== $user_id && $session_user_id !== $partner_id)) {
    echo json_encode(['error' => 'No tens permís per enviar missatges en aquesta conversa']);
    exit;
}

// Validar relación legítima con el proyecto (solo creador o usuario que ha dado like)
$stmtRel = $pdo->prepare("SELECT user_id FROM project WHERE project_id = ?");
$stmtRel->execute([$project_id]);
$project_owner_id = $stmtRel->fetchColumn();

$stmtLike = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND project_id = ?");
$stmtLike->execute([$session_user_id, $project_id]);
$has_liked = $stmtLike->fetchColumn() > 0;

if ($session_user_id !== intval($project_owner_id) && !$has_liked) {
    echo json_encode(['error' => 'No tens permís per enviar missatges en aquest xat']);
    exit;
}

require_once 'db.php';
header('Content-Type: application/json');
session_start();

// Recoger datos por POST
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$partner_id = isset($_POST['partner_id']) ? intval($_POST['partner_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

// Validar que el usuario autenticado es uno de los participantes
$session_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if (!$session_user_id || ($session_user_id !== $user_id && $session_user_id !== $partner_id)) {
    echo json_encode(['error' => 'No tens permís per enviar missatges en aquesta conversa']);
    exit;
}

if (!$project_id || !$partner_id || !$user_id || $content === '') {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

// El destinatario es el partner
$remitent_id = $user_id;
$destination_id = $partner_id;

try {

    // Verificar que el proyecto no está eliminado
    $stmtCheck = $pdo->prepare("SELECT is_deleted FROM project WHERE project_id = ?");
    $stmtCheck->execute([$project_id]);
    $proj = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    if (!$proj || !isset($proj['is_deleted']) || $proj['is_deleted']) {
        echo json_encode(['error' => 'El projecte està eliminat o no existeix']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO message (remitent_id, destination_id, project_id, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$remitent_id, $destination_id, $project_id, $content]);

    // Log detallado
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);
    // Obtener emails/nombres de usuarios
    $remitent_email = '';
    $destination_email = '';
    $remitent_name = '';
    $destination_name = '';
    $project_title = '';
    try {
        $stmtU = $pdo->prepare("SELECT email, user_name FROM users WHERE user_id = ?");
        $stmtU->execute([$remitent_id]);
        $u1 = $stmtU->fetch(PDO::FETCH_ASSOC);
        if ($u1) { $remitent_email = $u1['email']; $remitent_name = $u1['user_name']; }
        $stmtU->execute([$destination_id]);
        $u2 = $stmtU->fetch(PDO::FETCH_ASSOC);
        if ($u2) { $destination_email = $u2['email']; $destination_name = $u2['user_name']; }
        $stmtP = $pdo->prepare("SELECT title FROM project WHERE project_id = ?");
        $stmtP->execute([$project_id]);
        $p = $stmtP->fetch(PDO::FETCH_ASSOC);
        if ($p) { $project_title = $p['title']; }
    } catch (Exception $e) {}
    $linea = "[$fecha] [chat.php] Mensaje: '$content' | Proyecto: $project_title ($project_id) | De: $remitent_name <$remitent_email> ($remitent_id) | Para: $destination_name <$destination_email> ($destination_id)" . PHP_EOL;
    $logfile = __DIR__ . "/../admin/logs/" . date('Y-m-d') . ".txt";
    file_put_contents($logfile, $linea, FILE_APPEND);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
