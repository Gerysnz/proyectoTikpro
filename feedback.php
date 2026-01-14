<?php
    // Añadir las notiicaciones en session
    function setNotification($type, $message) {
        if (!isset($_SESSION["notifications"])) {
            $_SESSION["notifications"] = [];
        }
        $_SESSION["notifications"][] = [
            "type" => $type,
            "message" => $message
        ];
    }


    // Mostrar y limpiar
    function showNotification() {
        if (isset($_SESSION["notifications"]) && count($_SESSION["notifications"]) > 0) {
            foreach ($_SESSION["notifications"] as $note) {
                echo '<div class="notification notification--' . htmlspecialchars($note['type']) . '">';
                echo htmlspecialchars($note['message']);
                echo '</div>';
            }

            // Limpiamos las notificaciones
            $_SESSION["notifications"] = [];
        }
    }
?>