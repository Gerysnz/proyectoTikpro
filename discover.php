<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/feedback.php";
require_once __DIR__ . "/admin/logs.php";

require_once __DIR__ . "/api/db.php";

/* Algoritmo FEED: ordenar por interés */
$stmt = $pdo->prepare("
    SELECT
        p.project_id,
        p.title,
        p.description,
        p.video_path,
        COUNT(uc.category_id) AS interest_score
    FROM project p
    LEFT JOIN project_category pc ON p.project_id = pc.project_id
    LEFT JOIN user_category uc ON pc.category_id = uc.category_id
        AND uc.user_id = ?
    GROUP BY p.project_id
    ORDER BY interest_score DESC, p.project_id ASC
");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Índice del vídeo actual */
$index = isset($_GET['i']) ? (int)$_GET['i'] : 0;
$currentProject = $projects[$index] ?? null;


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

<?php if ($currentProject === null): ?>

      <!-- FIN DE LOS VÍDEOS -->
      <div class="info">
        <p>Ja has vist tots els vídeos</p>
        <a href="discover.php" class="btn">🔁 Tornar a començar</a>

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
        <button class="nope">No m'interesa</button>
        <button class="like">M'agrada</button>

      </div>

<?php else: ?>

      <!-- VÍDEO -->
      <div class="video">
        <video src="<?= htmlspecialchars($currentProject['video_path']) ?>" controls autoplay></video>
      </div>

      <!-- DETALLES -->
      <div class="details">
        <h2><?= htmlspecialchars($currentProject['title']) ?></h2>
        <p><?= htmlspecialchars($currentProject['description']) ?></p>
      </div>

      <!-- ACCIONES -->
      <div class="actions">
        <a class="nope" href="discover.php?i=<?= $index + 1 ?>">No m'interessa</a>
        <a class="like" href="discover.php?i=<?= $index + 1 ?>">M'agrada</a>
      </div>

<?php endif; ?>

      <div class="footer">
        <span>Perfil</span>
        <span>Converses</span>
        <span>Detalls</span>
      </div>

    </div>
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