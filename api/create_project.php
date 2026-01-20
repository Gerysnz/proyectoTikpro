<?php
// api/create_project.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

require_once __DIR__ . '/db.php';

try {
    $user_id = $_SESSION['user_id'];
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Validar campos obligatorios
    if (!$title || !$description) {
        echo json_encode(['error' => 'Título y descripción son obligatorios']);
        exit();
    }
    
    $image_path = null;
    $video_path = null;
    
    // Procesar imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_path = uploadFile($_FILES['image'], 'image');
        if (!$image_path) {
            echo json_encode(['error' => 'Error subiendo la imagen']);
            exit();
        }
    }
    
    // Procesar video
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        // Validar tamaño máximo: 200 MB
        if ($_FILES['video']['size'] > 200 * 1024 * 1024) {
            echo json_encode(['error' => 'El vídeo excede el tamaño máximo de 200 MB']);
            exit();
        }
        
        $video_path = uploadFile($_FILES['video'], 'video');
        if (!$video_path) {
            echo json_encode(['error' => 'Error subiendo el vídeo']);
            exit();
        }
    }
    
    // Insertar proyecto en la base de datos
    $stmt = $pdo->prepare('INSERT INTO project (user_id, title, description, image_path, video_path) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$user_id, $title, $description, $image_path, $video_path]);
    $project_id = $pdo->lastInsertId();
    
    // Procesar tags de organizador
    if (isset($_POST['organizer_tags'])) {
        $organizer_tags = json_decode($_POST['organizer_tags'], true);
        if (is_array($organizer_tags)) {
            insertProjectCategories($pdo, $project_id, $organizer_tags);
        }
    }
    
    // Procesar tags de partners (son informativos, también se guardan como categorías)
    if (isset($_POST['partner_tags'])) {
        $partner_tags = json_decode($_POST['partner_tags'], true);
        if (is_array($partner_tags)) {
            insertProjectCategories($pdo, $project_id, $partner_tags);
        }
    }
    
    echo json_encode([
        'success' => true,
        'project_id' => $project_id,
        'message' => 'Proyecto creado correctamente'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de servidor: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}

/**
 * Sube un archivo a la carpeta uploads
 */
function uploadFile($file, $type) {
    $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_video_types = ['video/mp4', 'video/webm', 'video/quicktime'];
    
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if ($type === 'image') {
        if (!in_array($file['type'], $allowed_image_types)) {
            return false;
        }
        $prefix = 'project_img_';
    } else {
        if (!in_array($file['type'], $allowed_video_types)) {
            return false;
        }
        $prefix = 'project_video_';
    }
    
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return '/uploads/' . $file_name;
    }
    
    return false;
}

/**
 * Inserta las categorías asociadas a un proyecto
 */
function insertProjectCategories($pdo, $project_id, $category_names) {
    // Obtener IDs de las categorías por nombre
    $placeholders = implode(',', array_fill(0, count($category_names), '?'));
    $stmt = $pdo->prepare("SELECT category_id FROM categories WHERE name IN ($placeholders)");
    $stmt->execute($category_names);
    $category_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Insertar relaciones proyecto-categoría
    $insert_stmt = $pdo->prepare('INSERT INTO project_category (project_id, category_id) VALUES (?, ?)');
    foreach ($category_ids as $category_id) {
        $insert_stmt->execute([$project_id, $category_id]);
    }
}
?>
