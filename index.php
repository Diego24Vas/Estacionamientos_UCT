<?php
// Navegador de archivos simple para la carpeta actual
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Navegador de archivos</title>
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
