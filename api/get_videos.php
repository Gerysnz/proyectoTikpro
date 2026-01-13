<?php
    header('Content-Type: application/json');

    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
        $db_user = 'adminsimbio';
        $db_pass = 'AdminSimbi@26';

        try {
            $pdo = new PDO($dsn, $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit();
        }

        $sql = "SELECT project_id, title, description, video_path 
        FROM project 
        WHERE video_path IS NOT NULL 
        ORDER BY project_id DESC";

        $result = $pdo->query($sql);

        $videos = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $videos[] = $row;
        }

        echo json_encode($videos);
    }
?>