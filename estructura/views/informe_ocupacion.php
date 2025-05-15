    <?php 
    session_start();
    require_once MODELS_PATH . '/conex.php'; // Conexión a la base de datos
    include(VIEWS_PATH . '/components/cabecera.php'); // Cabecera de la página
    // Datos de ejemplo para simular la disponibilidad de los estacionamientos
    $parking_spaces = [
        'A1' => 'Libre',
        'A2' => 'Ocupado',
        'B1' => 'Libre',
        'B2' => 'Ocupado'
    ];
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Informe de Ocupación</title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/styles.css"> <!-- Estilo para la tabla -->
    </head>
    <body>

    <h2>Informe de Ocupación</h2>

    <table>
        <thead>
            <tr>
                <th>Número de Espacio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parking_spaces as $space => $status): ?>
                <tr>
                    <td><?php echo $space; ?></td>
                    <td>
                        <?php if ($status === 'Libre'): ?>
                            <span style="color: green;">Disponible</span>
                        <?php else: ?>
                            <span style="color: red;">Ocupado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php include('pie.php'); // Pie de página ?>

    </body>
    </html>

