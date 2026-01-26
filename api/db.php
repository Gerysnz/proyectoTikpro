<?php
// db.php
$dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';

// $db_user = 'javi';
$db_user = 'gery';
$db_pass = 'superlocal';


// $dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
// $db_user = 'adminsimbio';
// $db_pass = 'AdminSimbi@26';


try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
