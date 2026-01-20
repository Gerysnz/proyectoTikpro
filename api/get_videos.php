<?php
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        require_once __DIR__ . '/db.php';

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