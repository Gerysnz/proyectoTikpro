<?php
session_start();
require_once __DIR__ . "/admin/logs.php";
writeLog("User " . ($_SESSION['user_email'] ?? 'unknown') . " se ha desconectado.");
session_destroy();
header("Location: login.php");
exit();
