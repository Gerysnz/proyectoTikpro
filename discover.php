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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chamba - Descobrir Projectes</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
<!-- styles.css globales -->
<link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
<style>
  .page-discover {
    font-family: 'Open Sans', Arial, sans-serif;
    background: var(--beige);
    color: var(--blue);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* VARIABLES (pueden ser globales si quieres) */
:root {
    --green: #2A9D8F;
    --blue: #264653;
    --beige: #F1F3F2;
    --gray: #7C7C7C;
    --orange: #FF6F3C;
}

/* RESET SOLO PARA ESTA PÁGINA */
.page-discover * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* HEADER */
.page-discover header {
    background: white;
    padding: 18px 24px;
    border-bottom: 2px solid var(--green);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-discover .header-logo {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 26px;
    color: var(--green);
}

.page-discover .header-user {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 15px;
}

.page-discover .header-user span {
    color: var(--blue);
    font-weight: 500;
}

.page-discover .logout {
    color: var(--green);
    font-weight: 600;
    text-decoration: none;
}

.page-discover .logout:hover {
    text-decoration: underline;
}

/* MAIN */
.page-discover main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px 20px;
}

.page-discover .container {
    width: 100%;
    max-width: 700px;
}

/* CARD */
.page-discover .card {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 8px 20px rgba(38, 70, 83, 0.12);
}

/* VIDEO */
.page-discover .video {
    width: 100%;
    height: 320px;
    background: black;
    border-radius: 14px;
    margin-bottom: 20px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.page-discover .video-card {
    width: 100%;
    height: 320px;
    background: black;
    border-radius: 14px;
    margin-bottom: 20px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

/* DETAILS */
.page-discover .details h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    margin-bottom: 10px;
}

.page-discover .details p {
    font-size: 15px;
    line-height: 1.6;
    color: var(--gray);
    margin-bottom: 14px;
}

.page-discover .tags {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
}

.page-discover .tag {
    background: var(--orange);
    color: white;
    padding: 6px 14px;
    border-radius: 24px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
}

/* ACTION BUTTONS */
.page-discover .actions {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}

.page-discover .actions button {
    flex: 1;
    padding: 16px;
    border: none;
    border-radius: 14px;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 17px;
    cursor: pointer;
}

.page-discover .actions button:active {
    transform: scale(0.97);
}

.page-discover .nope {
    background: var(--gray);
    color: white;
}

.page-discover .like {
    background: var(--green);
    color: white;
}

/* FOOTER */
.page-discover .footer {
    display: flex;
    justify-content: space-around;
    padding-top: 12px;
    border-top: 1.5px solid #e0e0e0;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
}
</style>
</head>

<body class="page-discover">

<header>
  <div class="header-logo">Chamba</div>
  <div class="header-user">
    <span>usuari@demo.com</span>
    <a href="#" class="logout">Log out</a>
  </div>
</header>


<main>
  <div class="container">
    <div class="card">

      <div class="video">Vídeo del projecte</div>

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
      </div>

      <div class="actions">
        <button class="nope">Nope</button>
        <button class="like">Like</button>
      </div>

      <div class="footer">
        <span>Perfil</span>
        <span>Converses</span>
        <span>Detalls</span>
      </div>

    </div>
  </div>
</main>
<script src="./js/app.js?t=<?php echo time(); ?>"></script>

</body>
</html>
