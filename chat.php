<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="styles.css?=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
    <div class="chat-container">
      <!-- Cabecera -->
      <header class="chat-header">
        <button class="chat-back" onclick="window.location.href='messages.php'">←</button>
        <img id="chat-project-logo" class="chat-project-logo" src="uploads/default-avatar.png" alt="Logo proyecto">
        <div class="chat-header-info">
          <div id="chat-project" class="chat-project-title">Nom projecte</div>
          <div id="chat-header" class="chat-partner-name">Nom partner</div>
        </div>
      </header>

      <!-- Mensajes -->
      <main class="chat-messages" id="chat-messages">
        <!-- Aquí se insertarán las burbujas de mensajes por JS -->
      </main>

      <!-- Input -->
      <form class="chat-input-bar" id="chat-form" autocomplete="off">
        <input type="text" id="chat-input" class="chat-input" placeholder="Escriu un missatge..." maxlength="1000" required>
        <button type="submit" class="chat-send-btn">➤</button>
      </form>
    </div>
    <script>
      // Exponer el user_id de la sesión para JS
      window.MY_USER_ID = <?php echo isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'null'; ?>;
    </script>
    <script src="js/chat.js?=<?php echo time(); ?>"></script>
</body>
</html>