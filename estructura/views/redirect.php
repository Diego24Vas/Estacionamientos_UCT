<?php
/**
 * Página de redirección de emergencia
 * Esta página se utiliza como último recurso cuando otras redirecciones fallan
 */

// Detectar URL base
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    return $protocol . $host . $uri;
}

$baseUrl = getBaseUrl();
$destino = isset($_GET['destino']) ? $_GET['destino'] : 'inicio.php';

// Lista de destinos permitidos para prevenir redirecciones no seguras
$destinos_permitidos = [
    'inicio.php',
    'pag_inicio.php',
    'registro.php',
    'recuperar_contraseña.php'
];

// Verificar que el destino sea seguro
if (!in_array($destino, $destinos_permitidos)) {
    $destino = 'inicio.php';
}

$url_final = $baseUrl . '/' . $destino;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirigiendo...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            width: 50px;
            height: 50px;
            margin: 20px auto;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .link {
            margin-top: 20px;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Redirigiendo...</h1>
        <div class="loader"></div>
        <p>Serás redirigido automáticamente en unos segundos.</p>
        <div class="link">
            Si no eres redirigido automáticamente, 
            <a href="<?php echo htmlspecialchars($url_final); ?>">haz clic aquí</a>.
        </div>
    </div>

    <script>
        // Redirigir después de un breve retraso
        setTimeout(function() {
            window.location.href = "<?php echo htmlspecialchars($url_final); ?>";
        }, 1500);
    </script>
</body>
</html>
