<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

require_once 'db.php';
$user_id = $_SESSION['user_id'];

// Versión anterior: puede devolver duplicados si el partner tiene varios proyectos


$sql = "
SELECT
    CASE WHEN m.remitent_id = :user_id THEN m.destination_id ELSE m.remitent_id END AS partner_id,
    u.entity_name AS partner_entity,
    u.user_name AS partner_name,
    u.user_surname AS partner_surname,
    u.user_type AS partner_type,
    p.project_id,
    p.title AS project_title,
    p.image_path AS project_logo,
    m2.content AS last_message,
    m2.created_at AS last_message_time
FROM message m
JOIN users u ON u.user_id = CASE WHEN m.remitent_id = :user_id THEN m.destination_id ELSE m.remitent_id END
JOIN project p ON p.project_id = m.project_id
JOIN (
    SELECT
        project_id,
        LEAST(remitent_id, destination_id) AS user1,
        GREATEST(remitent_id, destination_id) AS user2,
        MAX(created_at) AS max_time
    FROM message
    WHERE remitent_id = :user_id OR destination_id = :user_id
    GROUP BY project_id, user1, user2
) last_msg ON (
    m.project_id = last_msg.project_id
    AND LEAST(m.remitent_id, m.destination_id) = last_msg.user1
    AND GREATEST(m.remitent_id, m.destination_id) = last_msg.user2
    AND m.created_at = last_msg.max_time
)
JOIN message m2 ON m2.message_id = m.message_id
WHERE (m.remitent_id = :user_id OR m.destination_id = :user_id)
ORDER BY last_message_time DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($conversations);