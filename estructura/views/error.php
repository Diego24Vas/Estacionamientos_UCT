<?php
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Sistema de Estacionamientos</title>
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/styles.css">
</head>
<body>
    <div class="error-container">
        <h1>Error</h1>
        <p>Lo sentimos, ha ocurrido un error al procesar su solicitud.</p>
        <a href="<?php echo VIEWS_PATH; ?>/inicio.php" class="btn">Volver al inicio</a>
    </div>
</body>
</html> 