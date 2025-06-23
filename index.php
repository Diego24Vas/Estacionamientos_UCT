<?php
// Incluir configuraciones y gestor de sesiones
require_once __DIR__ . '/estructura/config/config.php';
require_once __DIR__ . '/estructura/services/session_manager.php';

// Crear carpeta de logs si no existe
$log_dir = __DIR__ . '/estructura/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Registrar acceso para depuración
file_put_contents(
    $log_dir . '/index_access.log',
    date('Y-m-d H:i:s') . " - Acceso al index principal - Autenticado: " . (is_authenticated() ? 'Sí' : 'No') . PHP_EOL,
    FILE_APPEND
);

// Construir URL base
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$baseURL = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

// Redirigir según estado de autenticación
if (is_authenticated()) {
    // Si está autenticado, redirigir al panel principal
    $redirect_url = $baseURL . "/estructura/views/pag_inicio.php";
    file_put_contents(
        $log_dir . '/index_redirect.log',
        date('Y-m-d H:i:s') . " - Usuario autenticado, redirigiendo a: " . $redirect_url . PHP_EOL,
        FILE_APPEND
    );
} else {
    // Si no está autenticado, redirigir a la página de inicio de sesión
    $redirect_url = $baseURL . "/estructura/views/inicio.php";
    file_put_contents(
        $log_dir . '/index_redirect.log',
        date('Y-m-d H:i:s') . " - Usuario NO autenticado, redirigiendo a: " . $redirect_url . PHP_EOL,
        FILE_APPEND
    );
}

// Ejecutar la redirección
if (!headers_sent()) {
    header("Location: " . $redirect_url);
} else {
    echo '<script>window.location.href="' . htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8') . '";</script>';
}
exit();
?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 32px 36px 24px 36px;
        }
        h2 {
            margin-top: 0;
            color: #2d3a4b;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .path {
            color: #888;
            font-size: 1rem;
            margin-bottom: 18px;
        }
        .up-link {
            display: inline-block;
            margin-bottom: 18px;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .up-link:hover {
            color: #0056b3;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            padding: 10px 0 10px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }
        li:last-child {
            border-bottom: none;
        }
        .fa-folder, .fa-file {
            margin-right: 12px;
            font-size: 1.2em;
        }
        .folder {
            color: #f7b731;
            font-weight: 600;
        }
        .file {
            color: #2d3a4b;
        }
        .actions {
            margin-left: auto;
        }
        .actions a {
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
            font-size: 1em;
            transition: color 0.2s;
        }
        .actions a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
<?php
function listarDirectorio($ruta) {
    $archivos = scandir($ruta);
    echo '<ul>';
    foreach ($archivos as $archivo) {
        if ($archivo === '.' || $archivo === '..') continue;
        $rutaCompleta = $ruta . DIRECTORY_SEPARATOR . $archivo;
        // Convertir ruta absoluta a ruta relativa para el servidor web
        $rutaRelativa = str_replace(str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT'])), '', str_replace("\\", "/", realpath($rutaCompleta)));
        $rutaRelativa = '/' . ltrim($rutaRelativa, '/');
        if (is_dir($rutaCompleta)) {
            echo '<li><i class="fa-solid fa-folder folder"></i> <span class="folder">' . htmlspecialchars($archivo) . '/</span>';
            echo '<span class="actions"><a href="?dir=' . urlencode($rutaCompleta) . '"><i class="fa-solid fa-arrow-right"></i> Abrir</a></span></li>';
        } else {
            echo '<li><i class="fa-solid fa-file file"></i> <span class="file">' . htmlspecialchars($archivo) . '</span>';
            echo '<span class="actions"><a href="' . htmlspecialchars($rutaRelativa) . '" target="_blank"><i class="fa-solid fa-eye"></i> Ver</a></span></li>';
        }
    }
    echo '</ul>';
}

$directorio = isset($_GET['dir']) ? $_GET['dir'] : __DIR__;
if (strpos(realpath($directorio), realpath(__DIR__)) !== 0) {
    die('Acceso denegado.');
}

echo '<h2><i class="fa-solid fa-folder-tree"></i> Navegador de archivos</h2>';
echo '<div class="path">Directorio actual: ' . htmlspecialchars(str_replace(__DIR__, '.', $directorio)) . '</div>';
if ($directorio !== __DIR__) {
    $padre = dirname($directorio);
    echo '<a class="up-link" href="?dir=' . urlencode($padre) . '"><i class="fa-solid fa-arrow-up"></i> Subir</a>';
}
listarDirectorio($directorio);
?>
</div>
</body>
</html>
