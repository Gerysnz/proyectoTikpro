<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
$db_user = 'adminsimbio';
$db_pass = 'AdminSimbi@26';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión");
}

// Datos Usuario
$stmt = $pdo->prepare("
    SELECT name, surname, entity_name, city, email, phone
    FROM users
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Proyectos del Usuario
$stmt = $pdo->prepare("
    SELECT project_id, title, featured_image
    FROM projects
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Perfil</h1>

<section class="profile-data">
    <h2>Dades de l'entitat</h2>

    <label>Nom i cognoms</label>
    <input type="text" value="<?= htmlspecialchars($user['name'] . ' ' . $user['surname']) ?>">

    <label>Nom entitat</label>
    <input type="text" value="<?= htmlspecialchars($user['entity_name']) ?>">

    <label>Població</label>
    <input type="text" value="<?= htmlspecialchars($user['city']) ?>">

    <label>Email</label>
    <input type="email" value="<?= htmlspecialchars($user['email']) ?>">

    <label>Telèfon</label>
    <input type="text" value="<?= htmlspecialchars($user['phone']) ?>">
</section>

<section class="tags">
    <h2>Etiquetes</h2>

    <div class="tag">Informàtica <span class="remove">✕</span></div>
    <div class="tag">SMX <span class="remove">✕</span></div>

    <button disabled>+ Afegir</button>
</section>

<section class="projects">
    <h2>Projectes propis</h2>

    <?php foreach ($projects as $project): ?>
        <div class="project-card">
            <a href="project.php?id=<?= $project['project_id'] ?>">
                <img src="<?= htmlspecialchars($project['featured_image']) ?>" alt="">
                <h3><?= htmlspecialchars($project['title']) ?></h3>
            </a>
        </div>
    <?php endforeach; ?>

    <a class="btn" href="new_project.php">+ Nou projecte</a>
</section>

<nav class="profile-links">
    <a href="conversations.php">💬 Converses</a>
    <a href="discover.php">🔍 Descobrir</a>
</nav>

</body>
</html>

