<?php
require_once __DIR__ . '/../config/DatabaseConnection.php';

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }
    
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Iniciar sesión",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string", example="admin"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login exitoso"),
     *     @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['username']) || !isset($data['password'])) {
                http_response_code(400);
                return ['error' => 'Nombre de usuario y contraseña son requeridos'];
            }
            
            // Buscar usuario por nombre en la tabla correcta
            $user = $this->db->fetch(
                "SELECT id, nombre, email, contraseña FROM INFO1170_RegistroUsuarios WHERE nombre = ?",
                [$data['username']]
            );
            
            if ($user && password_verify($data['password'], $user['contraseña'])) {
                // Generar token JWT simple
                $token = base64_encode(json_encode([
                    'user_id' => $user['id'],
                    'username' => $user['nombre'],
                    'email' => $user['email'],
                    'exp' => time() + 3600 // 1 hora
                ]));
                
                return [
                    'success' => true,
                    'message' => 'Login exitoso',
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'nombre' => $user['nombre'],
                        'email' => $user['email']
                    ]
                ];
            } else {
                // Si no funciona con contraseñas hasheadas, intentar texto plano (para compatibilidad)
                if ($user && $user['contraseña'] === $data['password']) {
                    $token = base64_encode(json_encode([
                        'user_id' => $user['id'],
                        'username' => $user['nombre'],
                        'email' => $user['email'],
                        'exp' => time() + 3600
                    ]));
                    
                    return [
                        'success' => true,
                        'message' => 'Login exitoso',
                        'token' => $token,
                        'user' => [
                            'id' => $user['id'],
                            'nombre' => $user['nombre'],
                            'email' => $user['email']
                        ]
                    ];
                }
                
                http_response_code(401);
                return ['error' => 'Credenciales inválidas'];
            }
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error interno del servidor', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Registrar nuevo usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", example="juan@uct.cl"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario creado exitosamente"),
     *     @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function register() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['nombre', 'email', 'password'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    return ['error' => "El campo $field es requerido"];
                }
            }
            
            // Verificar si el email ya existe
            $existingUser = $this->db->fetch(
                "SELECT id FROM INFO1170_RegistroUsuarios WHERE email = ?",
                [$data['email']]
            );
            
            if ($existingUser) {
                http_response_code(400);
                return ['error' => 'El email ya está registrado'];
            }
            
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $userId = $this->db->insert(
                "INSERT INTO INFO1170_RegistroUsuarios (nombre, email, contraseña) VALUES (?, ?, ?)",
                [$data['nombre'], $data['email'], $hashedPassword]
            );
            
            http_response_code(201);
            return [
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'user_id' => $userId
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error interno del servidor', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Get(
     *     path="/auth/verify",
     *     summary="Verificar token de autenticación",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Token válido"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function verify() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            http_response_code(401);
            return ['error' => 'Token de autorización requerido'];
        }
        
        $token = $matches[1];
        
        try {
            $payload = json_decode(base64_decode($token), true);
            
            if (!$payload || $payload['exp'] < time()) {
                http_response_code(401);
                return ['error' => 'Token expirado o inválido'];
            }
            
            return [
                'valid' => true,
                'user_id' => $payload['user_id'],
                'email' => $payload['email'],
                'nombre' => $payload['nombre']
            ];
        } catch (Exception $e) {
            http_response_code(401);
            return ['error' => 'Token inválido'];
        }
    }
}
?>