<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'models/HistorialOcupacion.php';

class HistorialOcupacionController {
    private $historial;

    public function __construct() {
        $this->historial = new HistorialOcupacion();
    }

    // Crear nuevo registro de historial
    public function crear() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Datos JSON inválidos"
                ]);
                return;
            }

            // Asignar datos al modelo
            $this->historial->fecha = $data['fecha'] ?? null;
            $this->historial->hora_inicio = $data['hora_inicio'] ?? null;
            $this->historial->hora_fin = $data['hora_fin'] ?? null;
            $this->historial->estado = $data['estado'] ?? 'manual';
            $this->historial->observaciones = $data['observaciones'] ?? null;
            $this->historial->espacios_ocupados = $data['espacios_ocupados'] ?? null;
            $this->historial->espacios_libres = $data['espacios_libres'] ?? null;
            $this->historial->porcentaje_ocupacion = $data['porcentaje_ocupacion'] ?? null;
            $this->historial->idZona = $data['idZona'] ?? null;

            // Validar datos
            $errores = $this->historial->validar();
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Datos de validación incorrectos",
                    "errores" => $errores
                ]);
                return;
            }

            // Crear registro
            if ($this->historial->crear()) {
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Registro de historial creado exitosamente",
                    "id" => $this->historial->idHistorial
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Error al crear el registro de historial"
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error interno del servidor",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Listar registros con filtros y paginación
    public function listar() {
        try {
            $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $offset = ($pagina - 1) * $limite;

            // Filtros opcionales
            $filtros = [];
            if (isset($_GET['zona'])) $filtros['zona'] = $_GET['zona'];
            if (isset($_GET['fecha_inicio'])) $filtros['fecha_inicio'] = $_GET['fecha_inicio'];
            if (isset($_GET['fecha_fin'])) $filtros['fecha_fin'] = $_GET['fecha_fin'];
            if (isset($_GET['estado'])) $filtros['estado'] = $_GET['estado'];

            $stmt = $this->historial->leer($limite, $offset, $filtros);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $registros,
                "pagination" => [
                    "pagina" => $pagina,
                    "limite" => $limite,
                    "total_registros" => count($registros)
                ],
                "filtros" => $filtros
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener registros",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Obtener registro por ID
    public function obtenerPorId($id) {
        try {
            $this->historial->idHistorial = $id;

            if ($this->historial->leerPorId()) {
                echo json_encode([
                    "success" => true,
                    "data" => [
                        "idHistorial" => $this->historial->idHistorial,
                        "fecha" => $this->historial->fecha,
                        "hora_inicio" => $this->historial->hora_inicio,
                        "hora_fin" => $this->historial->hora_fin,
                        "estado" => $this->historial->estado,
                        "observaciones" => $this->historial->observaciones,
                        "espacios_ocupados" => $this->historial->espacios_ocupados,
                        "espacios_libres" => $this->historial->espacios_libres,
                        "porcentaje_ocupacion" => $this->historial->porcentaje_ocupacion,
                        "idZona" => $this->historial->idZona,
                        "fechaCreacion" => $this->historial->fechaCreacion,
                        "fechaActualizacion" => $this->historial->fechaActualizacion
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Registro de historial no encontrado"
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener registro",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Actualizar registro
    public function actualizar($id) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Datos JSON inválidos"
                ]);
                return;
            }

            $this->historial->idHistorial = $id;

            // Verificar que existe
            if (!$this->historial->leerPorId()) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Registro de historial no encontrado"
                ]);
                return;
            }

            // Actualizar campos
            if (isset($data['fecha'])) $this->historial->fecha = $data['fecha'];
            if (isset($data['hora_inicio'])) $this->historial->hora_inicio = $data['hora_inicio'];
            if (isset($data['hora_fin'])) $this->historial->hora_fin = $data['hora_fin'];
            if (isset($data['estado'])) $this->historial->estado = $data['estado'];
            if (isset($data['observaciones'])) $this->historial->observaciones = $data['observaciones'];
            if (isset($data['espacios_ocupados'])) $this->historial->espacios_ocupados = $data['espacios_ocupados'];
            if (isset($data['espacios_libres'])) $this->historial->espacios_libres = $data['espacios_libres'];
            if (isset($data['porcentaje_ocupacion'])) $this->historial->porcentaje_ocupacion = $data['porcentaje_ocupacion'];
            if (isset($data['idZona'])) $this->historial->idZona = $data['idZona'];

            // Validar datos
            $errores = $this->historial->validar();
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Datos de validación incorrectos",
                    "errores" => $errores
                ]);
                return;
            }

            if ($this->historial->actualizar()) {
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Registro de historial actualizado exitosamente"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Error al actualizar el registro"
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error interno del servidor",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Eliminar registro
    public function eliminar($id) {
        try {
            $this->historial->idHistorial = $id;

            if (!$this->historial->leerPorId()) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Registro de historial no encontrado"
                ]);
                return;
            }

            if ($this->historial->eliminar()) {
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Registro de historial eliminado exitosamente"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Error al eliminar el registro"
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error interno del servidor",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Generar snapshot de ocupación actual
    public function generarSnapshot() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $idZona = $data['idZona'] ?? null;

            if (!$idZona) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "ID de zona requerido"
                ]);
                return;
            }

            if ($this->historial->generarSnapshot($idZona)) {
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Snapshot de ocupación generado exitosamente",
                    "id" => $this->historial->idHistorial
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Error al generar snapshot"
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error interno del servidor",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Obtener estadísticas
    public function obtenerEstadisticas() {
        try {
            $idZona = $_GET['zona'] ?? null;
            $fechaInicio = $_GET['fecha_inicio'] ?? null;
            $fechaFin = $_GET['fecha_fin'] ?? null;

            $estadisticas = $this->historial->obtenerEstadisticas($idZona, $fechaInicio, $fechaFin);

            echo json_encode([
                "success" => true,
                "data" => $estadisticas,
                "filtros" => [
                    "zona" => $idZona,
                    "fecha_inicio" => $fechaInicio,
                    "fecha_fin" => $fechaFin
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener estadísticas",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Obtener tendencias semanales
    public function obtenerTendenciasSemanal() {
        try {
            $idZona = $_GET['zona'] ?? null;
            $tendencias = $this->historial->obtenerTendenciasSemanal($idZona);

            echo json_encode([
                "success" => true,
                "data" => $tendencias,
                "zona" => $idZona
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener tendencias",
                "error" => $e->getMessage()
            ]);
        }
    }
}
