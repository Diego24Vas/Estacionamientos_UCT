<?php
/**
 * @OA\Info(
 *     title="API de Estacionamientos UCT",
 *     version="1.0.0",
 *     description="API RESTful para el sistema de gestión de estacionamientos de la UCT",
 *     @OA\Contact(
 *         email="admin@uct.cl",
 *         name="Administrador del Sistema"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost/PROYECTO/Estacionamientos_UCT/api",
 *     description="Servidor de desarrollo"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class SwaggerController {
    
    /**
     * Generar documentación Swagger
     */
    public function documentation() {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>API de Estacionamientos UCT - Documentación</title>
            <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3.25.0/swagger-ui.css" />
            <style>
                html {
                    box-sizing: border-box;
                    overflow: -moz-scrollbars-vertical;
                    overflow-y: scroll;
                }
                *, *:before, *:after {
                    box-sizing: inherit;
                }
                body {
                    margin:0;
                    background: #fafafa;
                }
            </style>
        </head>
        <body>
            <div id="swagger-ui"></div>
            <script src="https://unpkg.com/swagger-ui-dist@3.25.0/swagger-ui-bundle.js"></script>
            <script src="https://unpkg.com/swagger-ui-dist@3.25.0/swagger-ui-standalone-preset.js"></script>
            <script>
                window.onload = function() {
                    const ui = SwaggerUIBundle({
                        url: "./swagger.json",
                        dom_id: "#swagger-ui",
                        deepLinking: true,
                        presets: [
                            SwaggerUIBundle.presets.apis,
                            SwaggerUIStandalonePreset
                        ],
                        plugins: [
                            SwaggerUIBundle.plugins.DownloadUrl
                        ],
                        layout: "StandaloneLayout"
                    });
                };
            </script>
        </body>
        </html>';
        
        echo $html;
    }
    
    /**
     * Generar especificación OpenAPI en JSON
     */
    public function getSwaggerJson() {
        $spec = [
            "openapi" => "3.0.0",
            "info" => [
                "title" => "API de Estacionamientos UCT",
                "version" => "1.0.0",
                "description" => "API RESTful para el sistema de gestión de estacionamientos de la UCT",
                "contact" => [
                    "email" => "admin@uct.cl",
                    "name" => "Administrador del Sistema"
                ]
            ],
            "servers" => [
                [
                    "url" => "http://localhost/PROYECTO/Estacionamientos_UCT/api",
                    "description" => "Servidor de desarrollo"
                ]
            ],
            "components" => [
                "securitySchemes" => [
                    "bearerAuth" => [
                        "type" => "http",
                        "scheme" => "bearer",
                        "bearerFormat" => "JWT"
                    ]
                ]
            ],
            "paths" => [
                "/auth/login" => [
                    "post" => [
                        "tags" => ["Autenticación"],
                        "summary" => "Iniciar sesión",
                        "requestBody" => [
                            "required" => true,
                            "content" => [
                                "application/json" => [
                                    "schema" => [
                                        "type" => "object",
                                        "properties" => [
                                            "email" => ["type" => "string", "example" => "usuario@uct.cl"],
                                            "password" => ["type" => "string", "example" => "password123"]
                                        ],
                                        "required" => ["email", "password"]
                                    ]
                                ]
                            ]
                        ],
                        "responses" => [
                            "200" => ["description" => "Login exitoso"],
                            "401" => ["description" => "Credenciales inválidas"]
                        ]
                    ]
                ],
                "/auth/register" => [
                    "post" => [
                        "tags" => ["Autenticación"],
                        "summary" => "Registrar nuevo usuario",
                        "requestBody" => [
                            "required" => true,
                            "content" => [
                                "application/json" => [
                                    "schema" => [
                                        "type" => "object",
                                        "properties" => [
                                            "nombre" => ["type" => "string", "example" => "Juan Pérez"],
                                            "email" => ["type" => "string", "example" => "juan@uct.cl"],
                                            "password" => ["type" => "string", "example" => "password123"],
                                            "rol" => ["type" => "string", "example" => "estudiante"]
                                        ],
                                        "required" => ["nombre", "email", "password"]
                                    ]
                                ]
                            ]
                        ],
                        "responses" => [
                            "201" => ["description" => "Usuario creado exitosamente"],
                            "400" => ["description" => "Datos inválidos"]
                        ]
                    ]
                ],
                "/vehicles" => [
                    "get" => [
                        "tags" => ["Vehículos"],
                        "summary" => "Obtener todos los vehículos",
                        "security" => [["bearerAuth" => []]],
                        "parameters" => [
                            [
                                "name" => "user_id",
                                "in" => "query",
                                "description" => "ID del usuario para filtrar vehículos",
                                "required" => false,
                                "schema" => ["type" => "integer"]
                            ]
                        ],
                        "responses" => [
                            "200" => ["description" => "Lista de vehículos"]
                        ]
                    ],
                    "post" => [
                        "tags" => ["Vehículos"],
                        "summary" => "Crear nuevo vehículo",
                        "security" => [["bearerAuth" => []]],
                        "requestBody" => [
                            "required" => true,
                            "content" => [
                                "application/json" => [
                                    "schema" => [
                                        "type" => "object",
                                        "properties" => [
                                            "patente" => ["type" => "string", "example" => "ABC123"],
                                            "marca" => ["type" => "string", "example" => "Toyota"],
                                            "modelo" => ["type" => "string", "example" => "Corolla"],
                                            "color" => ["type" => "string", "example" => "Blanco"],
                                            "usuario_id" => ["type" => "integer", "example" => 1]
                                        ],
                                        "required" => ["patente", "marca", "modelo", "usuario_id"]
                                    ]
                                ]
                            ]
                        ],
                        "responses" => [
                            "201" => ["description" => "Vehículo creado exitosamente"],
                            "400" => ["description" => "Datos inválidos"]
                        ]
                    ]
                ],
                "/vehicles/validate/{plate}" => [
                    "get" => [
                        "tags" => ["Vehículos"],
                        "summary" => "Validar patente de vehículo",
                        "parameters" => [
                            [
                                "name" => "plate",
                                "in" => "path",
                                "required" => true,
                                "schema" => ["type" => "string"]
                            ]
                        ],
                        "responses" => [
                            "200" => ["description" => "Resultado de validación"]
                        ]
                    ]
                ],
                "/reservations" => [
                    "get" => [
                        "tags" => ["Reservas"],
                        "summary" => "Obtener todas las reservas",
                        "security" => [["bearerAuth" => []]],
                        "parameters" => [
                            [
                                "name" => "user_id",
                                "in" => "query",
                                "description" => "ID del usuario para filtrar reservas",
                                "required" => false,
                                "schema" => ["type" => "integer"]
                            ],
                            [
                                "name" => "fecha",
                                "in" => "query",
                                "description" => "Fecha específica (YYYY-MM-DD)",
                                "required" => false,
                                "schema" => ["type" => "string"]
                            ]
                        ],
                        "responses" => [
                            "200" => ["description" => "Lista de reservas"]
                        ]
                    ],
                    "post" => [
                        "tags" => ["Reservas"],
                        "summary" => "Crear nueva reserva",
                        "security" => [["bearerAuth" => []]],
                        "requestBody" => [
                            "required" => true,
                            "content" => [
                                "application/json" => [
                                    "schema" => [
                                        "type" => "object",
                                        "properties" => [
                                            "usuario_id" => ["type" => "integer", "example" => 1],
                                            "vehiculo_id" => ["type" => "integer", "example" => 1],
                                            "espacio_id" => ["type" => "integer", "example" => 1],
                                            "fecha_inicio" => ["type" => "string", "format" => "datetime", "example" => "2025-06-25 08:00:00"],
                                            "fecha_fin" => ["type" => "string", "format" => "datetime", "example" => "2025-06-25 18:00:00"]
                                        ],
                                        "required" => ["usuario_id", "vehiculo_id", "espacio_id", "fecha_inicio", "fecha_fin"]
                                    ]
                                ]
                            ]
                        ],
                        "responses" => [
                            "201" => ["description" => "Reserva creada exitosamente"],
                            "400" => ["description" => "Datos inválidos"],
                            "409" => ["description" => "Conflicto de horarios"]
                        ]
                    ]
                ],
                "/reservations/availability" => [
                    "get" => [
                        "tags" => ["Reservas"],
                        "summary" => "Verificar disponibilidad de espacios",
                        "parameters" => [
                            [
                                "name" => "fecha_inicio",
                                "in" => "query",
                                "required" => true,
                                "schema" => ["type" => "string", "format" => "datetime"]
                            ],
                            [
                                "name" => "fecha_fin",
                                "in" => "query",
                                "required" => true,
                                "schema" => ["type" => "string", "format" => "datetime"]
                            ]
                        ],
                        "responses" => [
                            "200" => ["description" => "Espacios disponibles"]
                        ]
                    ]
                ]
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($spec, JSON_PRETTY_PRINT);
    }
}
?>