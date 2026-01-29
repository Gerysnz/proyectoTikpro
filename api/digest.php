<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '../src/Exception.php';
require_once __DIR__ . '../src/PHPMailer.php';
require_once __DIR__ . '../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$since = date('Y-m-d H:i:s', strtotime('-30 minutes'));
$now   = date('Y-m-d H:i:s');

$stmt = $pdo->prepare("
    SELECT 
        m.remitent_id,
        m.destination_id,
        m.content,
        m.created_at,
        u1.user_name AS remitent_name,
        u1.email AS remitent_email,
        u2.user_name AS dest_name,
        u2.email AS dest_email
    FROM message m
    JOIN users u1 ON u1.user_id = m.remitent_id
    JOIN users u2 ON u2.user_id = m.destination_id
    WHERE m.created_at BETWEEN ? AND ?
");
$stmt->execute([$since, $now]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$digests = [];

foreach ($messages as $msg) {
    $digests[$msg['remitent_id']][] = [
        'other' => $msg['dest_name'],
        'content' => $msg['content'],
        'time' => $msg['created_at'],
        'email' => $msg['remitent_email']
    ];
}

foreach ($digests as $items) {
    sendDigestEmail($items);
}

function sendDigestEmail($items) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'loopsis753@gmail.com';
    $mail->Password = 'xehg clnw axvr obac';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('loopsis753@gmail.com', 'Simbio');
    $mail->addAddress($items[0]['email']);
    $mail->isHTML(true);
    $mail->Subject = '📬 Resum recent del teu xat';

    $body = "
    <div style='font-family: Arial, Helvetica, sans-serif; background-color:#f6f7f9; padding:20px;'>
        <div style='max-width:600px; margin:0 auto; background-color:#ffffff; padding:30px; border-radius:6px;'>

            <h2 style='color:#2c3e50; margin-top:0;'>📬 Resum recent del teu xat</h2>

            <p style='color:#555; font-size:14px; line-height:1.5;'>
                Aquest és un resum dels missatges intercanviats recentment a <strong>Simbio</strong>.
            </p>

            <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>

            <div style='font-size:14px; color:#333;'>
    ";

    foreach ($items as $item) {
        $body .= "
            <div style='margin-bottom:20px; padding-bottom:15px; border-bottom:1px solid #eee;'>
                <p style='margin:0; font-weight:bold; color:#4CAF50;'>
                    {$item['other']}
                </p>
                <p style='margin:5px 0 8px 0; color:#555;'>
                    {$item['content']}
                </p>
                <p style='margin:0; font-size:12px; color:#999;'>
                    {$item['time']}
                </p>
            </div>
        ";
    }

    $body .= "
            </div>

            <p style='text-align:center; margin:30px 0;'>
                <a href='http://localhost:8080'
                style='background-color:#4CAF50; color:#ffffff; padding:12px 20px;
                        text-decoration:none; border-radius:4px; font-weight:bold; display:inline-block;'>
                    Obrir Simbio
                </a>
            </p>

            <hr style='border:none; border-top:1px solid #eee; margin:30px 0;'>

            <p style='font-size:12px; color:#999; margin-bottom:0;'>
                Has rebut aquest correu perquè has interactuat recentment en un xat.<br>
                — L'equip de <strong>Simbio</strong>
            </p>
        </div>
    </div>
    ";

    $mail->Body = $body;

    $mail->send();
}
