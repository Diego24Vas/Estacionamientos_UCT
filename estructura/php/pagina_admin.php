<?php
include('cabecera.php');
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>

<h1>Panel de Administración</h1>
<p>Bienvenido al panel de administración, donde puedes gestionar usuarios y ver informes detallados.</p>
