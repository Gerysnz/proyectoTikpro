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

  <style>
    :root {
      --green-forest: #2A9D8F;
      --blue-gray: #264653;
      --turquoise-light: #48B9B9;
      --beige-light: #F1F3F2;
      --gray-medium: #7C7C7C;
      --orange-bright: #FF6F3C; /* color rompedor para etiquetas */
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Open Sans', sans-serif;
      background-color: var(--beige-light);
      color: var(--blue-gray);
    }

    header {
      background-color: white;
      padding: 20px 24px;
      border-bottom: 2px solid var(--green-forest);
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 24px;
      color: var(--green-forest);
      text-align: center;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .container > .notification {
        padding: 12px 16px;
        border-left: 6px solid;
        border-radius: 6px;
        margin-bottom: 12px;
        font-size: 0.95rem;
    }

    .container > .notification--info {
        border-color: #1e88e5;
        background-color: #e3f2fd;
        color: #0d47a1;
    }

    .container > .notification--warning {
        border-color: #fbc02d;
        background-color: #fff8e1;
        color: #7a5a00;
    }

    .container > .notification--error {
        border-color: #e53935;
        background-color: #fdecea;
        color: #b71c1c;
    }

    .card {
      background-color: white;
      border-radius: 16px;
      padding: 24px 20px;
      margin-bottom: 30px;
      box-shadow: 0 6px 15px rgba(38, 70, 83, 0.1);
      transition: transform 0.3s ease;
      max-width: 720px;
      margin-left: auto;
      margin-right: auto;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(38, 70, 83, 0.15);
    }

    .video-wrapper {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    iframe {
      border-radius: 12px;
      width: 100%;
      max-width: 640px;
      height: 360px;
      border: none;
    }

    h2 {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      margin: 0 0 12px;
      color: var(--blue-gray);
    }

    p {
      font-size: 17px;
      line-height: 1.5;
      margin: 0 0 16px;
    }

    .tags {
      margin-bottom: 20px;
    }

    .tag {
      display: inline-block;
      background-color: var(--orange-bright);
      color: white;
      padding: 6px 14px;
      border-radius: 24px;
      font-size: 15px;
      font-weight: 600;
      margin-right: 10px;
      user-select: none;
      font-family: 'Poppins', sans-serif;
      transition: background-color 0.3s ease;
      cursor: default;
    }

    .tag:hover {
      background-color: #e65a2a;
    }

    .actions {
      display: flex;
      gap: 16px;
    }

    button {
      flex: 1;
      padding: 14px 0;
      border-radius: 12px;
      border: none;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      user-select: none;
    }

    .btn-like {
      background-color: var(--green-forest);
      color: white;
    }

    .btn-like:hover {
      background-color: #238171;
    }

    .btn-dislike {
      background-color: var(--gray-medium);
      color: white;
    }

    .btn-dislike:hover {
      background-color: #5c5c5c;
    }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 14px;
      color: #666;
      font-family: 'Open Sans', sans-serif;
    }

    /* Responsive: que el iframe no quede muy alto en móviles */
    @media (max-width: 600px) {
      .card {
        padding: 12px 2vw;
      }
      iframe {
        height: 200px;
      }
    }
  </style>
</head>

<body>

  <header>
    Chamba <i class="bi bi-wechat"></i>
  </header>

  <main class="container">

    <?php showNotification(); ?>

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
        <span class="tag">Porno gay salvaje</span>
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