<?php
  session_start();
  require_once "feedback.php";

  setNotification('info', 'Mensaje enviado correctamente.');
  setNotification('warning', 'No puedes enviar un mensaje vacío.');
  setNotification('error', 'Ocurrió un error al subir el vídeo.');
?>

<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8" />
  <title>Chamba – Demo UI Paleta Fresca</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
  <!-- cargar boostrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- styles.css globales -->
  <link href="/styles.css" rel="stylesheet">
</head>

<body>

  <header>
    Chamba <i class="bi bi-wechat"></i>
  </header>

  <main class="container">

    <?php // showNotification(); ?>

    <div class="card">
      <div class="video-wrapper">
        <iframe src="https://www.youtube.com/embed/GhBmXxDfnAY" title="YouTube video player" allowfullscreen></iframe>
      </div>

      <h2>Projecte Smart Green City</h2>
      <p>
        Projecte interdisciplinari per desenvolupar solucions digitals orientades a la sostenibilitat urbana.
        Es busquen centres col·laboradors i empreses.
      </p>

      <div class="tags">
        <span class="tag">Informàtica</span>
        <span class="tag">JavaScript</span>
        <span class="tag">GS</span>
      </div>

      <div class="actions">
        <button class="btn-dislike">No m'interessa</button>
        <button class="btn-like">M'interessa</button>
      </div>
    </div>

  </main>

  <footer>
    Projecte acadèmic · Xarxa InnovaFP
  </footer>

</body>
</html>