<?php
session_start();
require_once __DIR__ . "/logs.php";

// Headers para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0");
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// Registrar el logout
if (isset($_SESSION['admin_email'])) {
    writeLog($_SESSION['admin_email'] . " ha cerrado sesión del panel de administración");
}

// Destruir la sesión completamente
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirigir al login
header("Location: login.php", true, 302);
exit();
?>
?>
