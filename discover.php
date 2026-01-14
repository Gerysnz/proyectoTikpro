<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
  require_once "feedback.php";
  require_once "admin/logs.php";

  setNotification('info', 'Inici de sessió correcte. Benvingut/da a Chamba!');
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chamba - Descobrir Projectes</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
<!-- styles.css globales -->
<link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>

<body class="page-discover">

<header class="discover-header">
  <div class="header-logo">Chamba</div>
  <div class="header-user">
    <span><?= htmlspecialchars($_SESSION['user_email']) ?></span>
    <a href="logout.php" class="logout">Log out</a>
  </div>
</header>

<?php showNotification(); ?>
<main>
  <div class="container">
    <div class="card">

      <!--<div class="video">Vídeo del projecte</div>

      <div class="details">
        <h2>Projecte Smart Green City</h2>
        <p>
          Projecte interdisciplinari per desenvolupar solucions digitals
          orientades a la sostenibilitat urbana.
        </p>

        <div class="tags">
          <span class="tag">Informàtica</span>
          <span class="tag">GS</span>
          <span class="tag">Sostenibilitat</span>
        </div>
      </div> -->

      <div class="actions">
        <button class="nope">Nope</button>
        <button class="like">Like</button>
      </div>

      <div class="footer">
        <span>Perfil</span>
        <span>Converses</span>
        <span>Detalls</span>
      </div>

  <main>
    <div class="container">
      <div id="proyecto-card"></div>
    </div>
  <!-- <script src="js/discover-mock.js"></script> -->
  </div>
</main>
<script src="./js/app.js?t=<?php echo time(); ?>"></script>
</body>
</html>
