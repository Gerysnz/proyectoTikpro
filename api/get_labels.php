<?php
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // $dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
        // $db_user = 'adminsimbio';
        // $db_pass = 'AdminSimbi@26';
        $dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
        $db_user = 'gery';
        $db_pass = 'superlocal';

        try {
            $pdo = new PDO($dsn, $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit();
        }

        $sql = "SELECT p.project_id, p.title, p.description, p.video_path
        FROM project p
        WHERE p.video_path IS NOT NULL
        ORDER BY p.project_id DESC";

        $result = $pdo->query($sql);
        $videos = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            // Obtener categorías asociadas a este proyecto
            $cat_stmt = $pdo->prepare("SELECT c.name FROM project_category pc JOIN categories c ON pc.category_id = c.category_id WHERE pc.project_id = ?");
            $cat_stmt->execute([$row['project_id']]);
            $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
            $row['categories'] = $categories;
            $videos[] = $row;
        }

        echo json_encode($videos);
    }
?>