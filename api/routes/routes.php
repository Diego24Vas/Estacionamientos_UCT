<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$request = str_replace('/api', '', $request);

switch ($request) {
    case '/login':
        if ($method === 'GET') {
            echo "Esta es la ruta /login. Usa POST con JSON para autenticar.";
        } elseif ($method === 'POST') {
            require_once './controllers/AuthController.php';
            (new AuthController())->login();
        }
        break;

    case '/usuarios':
        require_once './controllers/UsuarioController.php';
        $controller = new UsuarioController();

        if ($method === 'GET') {
            $controller->listar();
        } elseif ($method === 'POST') {
            $controller->crear();
        } else {
            http_response_code(405);
            echo json_encode(["mensaje" => "Método no permitido"]);
        }
        break;

    case '/reservas':
        require_once './controllers/ReservaController.php';
        $controller = new ReservaController();

        if ($method === 'POST') {
            $controller->crear();
        } elseif ($method === 'GET') {
            $controller->listarPorUsuario();
        } else {
            http_response_code(405);
            echo json_encode(["mensaje" => "Método no permitido"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["mensaje" => "Ruta no encontrada"]);
        break;
}
