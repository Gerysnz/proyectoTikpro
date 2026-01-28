<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/api/db.php';
require_once __DIR__ . "/feedback.php";

setNotification('info', 'Benvingut/da a Simbio, ' . htmlspecialchars($_SESSION['user_email']) . '!');
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Simbio - Descobrir Projectes</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
<link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>

<body class="page-discover">
<header class="discover-header">
  <div class="header-logo">Simbio</div>
  <div class="header-user">
    <span><?= htmlspecialchars($_SESSION['user_email']) ?></span>
    <a href="logout.php" class="logout">Log out</a>
  </div>
</header>

<?php showNotification(); ?>
<main>
  <div class="container">
    <div class="card">
      <div class="actions">
        <button class="nope">No m'interesa</button>
        <button class="like">M'agrada</button>
      </div>

      <div class="footer">
        <span>Perfil 👤</span>
        <span>Chat 💬</span>
        <span>Detalls ℹ️</span>
      </div>
    </div>
    <div class="container">
      <div id="proyecto-card"></div>
    </div>
    <div class="info"></div>
  </div>
</main>

<script src="./js/app.js?t=<?php echo time(); ?>"></script>
<script>
setTimeout(() => {
    const notification = document.querySelector('.notification');
    if (notification) {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }
}, 2000);
</script>
</body>
</html>