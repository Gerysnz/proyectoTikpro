
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Solo se usa la sesión para mostrar el email en el header
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
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

<section class="profile-data" id="profile-data">
    <h2>Dades de l'entitat</h2>
    <!-- Los datos del usuario se cargarán aquí vía JS/API -->
</section>

<section class="tags" id="profile-tags">
    <h2>Etiquetes</h2>
    <!-- Etiquetas del usuario -->
    <button disabled>+ Afegir</button>
</section>

<section class="projects" id="profile-projects">
    <h2>Projectes propis</h2>
    <!-- Proyectos del usuario -->
    <a class="btn" href="edit_project.php">+ Nou projecte</a>
</section>

<nav class="profile-links">
    <a href="conversations.php">💬 Converses</a>
    <a href="discover.php">🔍 Descobrir</a>
</nav>

<script src="js/profile.js"></script>
</body>
