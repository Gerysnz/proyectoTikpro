<?php
    header('Content-Type: application/json');
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        require_once __DIR__ . '/db.php';

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not logged in']);
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Obtener las categorías del usuario actual
        $user_cat_stmt = $pdo->prepare("
            SELECT category_id FROM user_category WHERE user_id = ?
        ");
        $user_cat_stmt->execute([$user_id]);
        $user_categories = $user_cat_stmt->fetchAll(PDO::FETCH_COLUMN);

        
        $liked_stmt = $pdo->prepare("
            SELECT project_id FROM likes WHERE user_id = ?
        ");
        $liked_stmt->execute([$user_id]);
        $liked_projects = $liked_stmt->fetchAll(PDO::FETCH_COLUMN);

        
        $sql = "SELECT p.project_id, p.title, p.description, p.video_path
                FROM project p
                WHERE p.video_path IS NOT NULL
                AND p.is_deleted = FALSE  
                ORDER BY p.project_id DESC";

        $result = $pdo->query($sql);
        $videos = [];
        $matched_videos = [];
        $other_videos = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $cat_stmt = $pdo->prepare("SELECT c.name, c.category_id FROM project_category pc JOIN categories c ON pc.category_id = c.category_id WHERE pc.project_id = ?");
            $cat_stmt->execute([$row['project_id']]);
            $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $cat_names = array_column($categories, 'name');
            $cat_ids = array_column($categories, 'category_id');
            
            $row['categories'] = $cat_names;
            // user_id ya viene en $row
            
            // Verificar si ya lo ha likeado
            $row['is_liked'] = in_array($row['project_id'], $liked_projects);
            
            
            $has_match = false;
            if (!empty($user_categories) && !empty($cat_ids)) {
                $has_match = (bool) array_intersect($user_categories, $cat_ids);
            }
            $row['has_match'] = $has_match;
            
            if ($has_match) {
                $matched_videos[] = $row;
            } else {
                $other_videos[] = $row;
            }
        }

        // Combinar: primero los con coincidencia, luego los otros
        $videos = array_merge($matched_videos, $other_videos);

        echo json_encode($videos);
    }
?>