<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/services/session_manager.php';

// Verificar autenticación obligatoria
redirect_if_not_authenticated();

include(VIEWS_PATH . '/components/cabecera.php');
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>

<h1>Panel de Administración</h1>
<p>Bienvenido al panel de administración, donde puedes gestionar usuarios y ver informes detallados.</p>

<?php include(VIEWS_PATH . '/components/pie.php'); ?>
