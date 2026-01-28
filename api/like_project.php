<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . "/../admin/logs.php";
require_once __DIR__ . '/../src/Exception.php';
require __DIR__ . '/../src/PHPMailer.php';
require __DIR__ . '/../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? '';
    $project_id = $data['proyectoId'] ?? null;
    $user_email = $_SESSION['user_email'] ?? 'unknown';
    $user_id = $_SESSION['user_id'];

    if (($action === 'like' || $action === 'nope') && $project_id) {
        try {
            if ($action === 'like') {
                $stmt = $pdo->prepare("INSERT IGNORE INTO likes (user_id, project_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $project_id]);
                
                processMatchWithProjectOwner($user_id, $project_id, $pdo);
            } else if ($action === 'nope') {
                // Si quieres guardar los 'nope', crea una tabla o ignora
            }

            // Obtener título del proyecto
            $stmt = $pdo->prepare("SELECT title FROM project WHERE project_id = ?");
            $stmt->execute([$project_id]);
            $title = $stmt->fetchColumn();

            // Log con discover.php como origen
            $accion_txt = $action === 'like' ? 'le ha dado like' : 'le ha dado nope';
            writeLog("[discover.php] User $user_email $accion_txt al video $project_id ($title)");

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DB error', 'msg' => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
}

function processMatchWithProjectOwner($userId, $projectId, $pdo) {
    try {
        writeLog("DEBUG: Buscando creador del proyecto $projectId...");
        
        $stmt = $pdo->prepare("
            SELECT u.user_id, u.user_name, u.user_surname, u.email, p.title as project_title
            FROM project p
            JOIN users u ON p.user_id = u.user_id
            WHERE p.project_id = ?
        ");
        $stmt->execute([$projectId]);
        $projectOwner = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$projectOwner) {
            writeLog("ERROR: No se encontró el creador del proyecto $projectId");
            return;
        }
        
        writeLog("DEBUG: Creador encontrado: {$projectOwner['email']}");
        
        $stmt = $pdo->prepare("SELECT user_id, user_name, user_surname, email FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentUser) {
            writeLog("ERROR: Usuario $userId no encontrado");
            return;
        }
        
        writeLog("DEBUG: Usuario que da like: {$currentUser['email']}");

        if ($projectOwner['user_id'] == $currentUser['user_id']) {
            writeLog("INFO: El usuario es el creador de su propio proyecto, no hay match");
            return;
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM likes 
            WHERE user_id = ? AND project_id = ?
        ");
        $stmt->execute([$projectOwner['user_id'], $projectId]);
        $creatorLikedOwnProject = $stmt->fetch(PDO::FETCH_ASSOC);
        
        writeLog("DEBUG: Enviando email match entre creador y usuario...");
        
        $emailSent = sendMatchEmail($currentUser, $projectOwner, $projectOwner['project_title']);
        
        if ($emailSent) {
            writeLog("✅ Email de match ENVIADO: Creador ({$projectOwner['email']}) <-> Usuario ({$currentUser['email']})");
            
            // Opcional: Guardar el match en la base de datos
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO matches (user1_id, user2_id, project_id, matched_at) 
                    VALUES (?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE matched_at = NOW()
                ");
                $stmt->execute([
                    min($currentUser['user_id'], $projectOwner['user_id']),
                    max($currentUser['user_id'], $projectOwner['user_id']),
                    $projectId
                ]);
                writeLog("✅ Match guardado en base de datos");
            } catch (Exception $e) {
                writeLog("⚠️ No se pudo guardar match en BD: " . $e->getMessage());
            }
        } else {
            writeLog("❌ Email de match FALLÓ");
        }
        
    } catch (Exception $e) {
        writeLog("ERROR en processMatchWithProjectOwner: " . $e->getMessage());
    }
}

function sendMatchEmail($userWhoLiked, $projectOwner, $projectTitle) {
    try {
        $mail = new PHPMailer(true);
        
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'loopsis753@gmail.com';
        $mail->Password = 'xehg clnw axvr obac';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('loopsis753@gmail.com', 'Simbio');
        $mail->isHTML(true);
        $mail->Subject = '🎉 Has fet match a Simbio!';

        // ===== Correo al USUARIO QUE DIO LIKE =====
        $mail->addAddress($userWhoLiked['email']);
        $mail->Body = "
            <h2>🎉 Has fet match!</h2>
            <p>Hola <strong>{$userWhoLiked['user_name']}</strong>,</p>
            <p>Has fet match amb el creador del projecte <strong>{$projectTitle}</strong>.</p>
            <p><strong>Creador del projecte:</strong> {$projectOwner['user_name']} {$projectOwner['user_surname']}</p>
            <p><strong>Email del creador:</strong> {$projectOwner['email']}</p>
            <p>El creador ha vist el teu interès en el seu projecte i podreu començar a xatejar!</p>
            <br>
            <p>Salutacions,<br>L'equip de Simbio</p>
        ";
        
        if (!$mail->send()) {
            throw new Exception("Error enviando a usuario: " . $mail->ErrorInfo);
        }
        
        // Limpiar para segundo correo
        $mail->clearAddresses();
        
        $mail->addAddress($projectOwner['email']);
        $mail->Body = "
            <h2>🎉 Algú s'ha interessat pel teu projecte!</h2>
            <p>Hola <strong>{$projectOwner['user_name']}</strong>,</p>
            <p>Un usuari s'ha interessat pel teu projecte <strong>{$projectTitle}</strong>.</p>
            <p><strong>Usuari interessat:</strong> {$userWhoLiked['user_name']} {$userWhoLiked['user_surname']}</p>
            <p><strong>Email de l'usuari:</strong> {$userWhoLiked['email']}</p>
            <p>Podreu començar a xatejar per parlar sobre el projecte!</p>
            <br>
            <p>Salutacions,<br>L'equip de Simbio</p>
        ";
        
        if (!$mail->send()) {
            throw new Exception("Error enviando a creador: " . $mail->ErrorInfo);
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error en sendMatchEmail: " . $e->getMessage());
        writeLog("EMAIL ERROR: " . $e->getMessage());
        return false;
    }
}
?>