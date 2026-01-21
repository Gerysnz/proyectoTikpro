<?php
require_once '../api/db.php';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado a la base de datos project_platform...\n";
    echo "\nLimpiando datos existentes...\n";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $tables = ['likes', 'user_category', 'project_category', 'project', 'users', 'categories'];
    
    foreach ($tables as $table) {
        try {
            $pdo->exec("TRUNCATE TABLE $table");
            echo "  Tabla $table limpiada\n";
        } catch (Exception $e) {
            echo "  Error al limpiar $table: " . $e->getMessage() . "\n";
        }
    }
    
    echo "Datos existentes eliminados.\n";
    
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
    exit();
}
