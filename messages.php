<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Logging acceso a mensajes
$mensaje = $_SESSION['name'] . " ha entrado en la seccion de mensajes";
$fecha = date("Y-m-d H:i:s");
$archivo = basename(__FILE__);
$linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
$ruta_logs = "logs/" . $fecha . ".txt";
file_put_contents($ruta_logs, $linea, FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missatges</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
    
</head>
<body class="profile-page">
<header class="discover-header">
  <div class="header-logo">Simbio</div>
  <div class="header-user">
    <span><?= htmlspecialchars($_SESSION['user_email']) ?></span>
    <a href="logout.php" class="logout">Log out</a>
  </div>
</header>
<main>
  <div class="messages-list-container">
    <div class="messages-list-title">Missatges</div>
    <div id="conversations-list" class="conversations-list">
      <!-- Aquí se cargarán las conversaciones por JS -->
    </div>
  </div>
</main>

<nav class="profile-links messages-footer-links">
  <a href="profile.php">👤 Perfil</a>
  <a href="discover.php">🔍 Descobrir</a>
</nav>
<script src="js/messages.js"></script>
</body>
</html>