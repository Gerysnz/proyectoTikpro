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
    <title>Nou Projecte</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>

<body class="edit-project-page">

<header class="discover-header">
  <div class="header-logo">Simbio</div>
  <div class="header-user">
    <span><?= htmlspecialchars($_SESSION['user_email']) ?></span>
    <a href="logout.php" class="logout">Log out</a>
  </div>
</header>

<section class="project-form">
    <h1>Nou Projecte</h1>
    
    <form id="project-form">
        <!-- Título -->
        <div class="form-group">
            <label for="project-title">Títol del projecte *</label>
            <input type="text" id="project-title" name="title" required placeholder="Introduïx el títol del projecte">
        </div>

        <!-- Descripción -->
        <div class="form-group">
            <label for="project-description">Descripció *</label>
            <textarea id="project-description" name="description" required placeholder="Descripcio del projecte" rows="4"></textarea>
        </div>

        <!-- Imagen destacada -->
        <div class="form-group">
            <label for="project-image">Imatge destacada</label>
            <div class="image-upload-container">
                <div id="image-preview" class="image-preview">
                    <img id="preview-img" src="" alt="Imatge destacada" style="display: none;">
                    <div id="preview-placeholder" class="preview-placeholder">Imatge destacada del projecte</div>
                </div>
                <input type="file" id="project-image" name="image" accept="image/*">
                <small>Deixa en blanc per usar l'imatge del teu perfil</small>
            </div>
        </div>

        <!-- Etiquetes d'organitzador -->
        <div class="form-group">
            <label>Etiquetes d'organitzador del projecte</label>
            <div class="tags-container" id="organizer-tags-container">
                <div class="selected-tags" id="organizer-selected-tags"></div>
                <button type="button" class="btn-add-tags" id="btn-add-organizer-tags">+ Afegir etiquetes</button>
            </div>
        </div>

        <!-- Etiquetes de partners -->
        <div class="form-group">
            <label>Etiquetes de partners cercats</label>
            <div class="tags-container" id="partner-tags-container">
                <div class="selected-tags" id="partner-selected-tags"></div>
                <button type="button" class="btn-add-tags" id="btn-add-partner-tags">+ Afegir etiquetes</button>
            </div>
        </div>

        <!-- Video upload -->
        <div class="form-group">
            <label for="project-video">Pujada de vídeo (màx 200 MB)</label>
            <div class="video-upload-container">
                <input type="file" id="project-video" name="video" accept="video/*">
                <div id="video-info"></div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Crear projecte</button>
            <a href="profile.php" class="btn btn-secondary">Cancelar</a>
        </div>

        <div id="form-message"></div>
    </form>
</section>

<!-- Modal para seleccionar tags -->
<div id="tags-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Selecciona etiquetes</h2>
            <button type="button" class="modal-close" id="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div id="tags-list" class="tags-list"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="modal-cancel">Cancelar</button>
            <button type="button" class="btn btn-primary" id="modal-confirm">Confirmar</button>
        </div>
    </div>
</div>

<nav class="profile-links">
    <a href="profile.php">👤 Perfil</a>
    <a href="discover.php">🔍 Descobrir</a>
</nav>

<script src="js/edit_project.js"></script>
</body>

</html>
