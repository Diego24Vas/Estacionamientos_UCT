<?php
// Redirección simple al controlador de logout
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $basePath;

// Redirigir al controlador de logout
header("Location: " . $baseURL . "/estructura/controllers/logout.php");
exit;
?>
