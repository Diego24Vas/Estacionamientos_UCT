<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'models/Evento.php';

class EventoController {
    private $evento;

    public function __construct() {
        $this->evento = new Evento();
    }

    // Crear nuevo evento
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
            $this->evento->nombre = $data['nombre'] ?? null;
            $this->evento->descripcion = $data['descripcion'] ?? null;
            $this->evento->fecha_inicio = $data['fecha_inicio'] ?? null;
            $this->evento->fecha_fin = $data['fecha_fin'] ?? null;
            $this->evento->hora_inicio = $data['hora_inicio'] ?? null;
            $this->evento->hora_fin = $data['hora_fin'] ?? null;
            $this->evento->tipo_evento = $data['tipo_evento'] ?? null;
            $this->evento->capacidad_reservada = $data['capacidad_reservada'] ?? null;
            $this->evento->estado = $data['estado'] ?? 'planificado';
            $this->evento->prioridad = $data['prioridad'] ?? 'media';
            $this->evento->ubicacion = $data['ubicacion'] ?? null;
            $this->evento->organizador = $data['organizador'] ?? null;
            $this->evento->contacto = $data['contacto'] ?? null;
            $this->evento->restricciones = $data['restricciones'] ?? null;
            $this->evento->costo_adicional = $data['costo_adicional'] ?? null;
            $this->evento->idZona = $data['idZona'] ?? null;

            // Validar datos
            $errores = $this->evento->validar();
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Datos de validación incorrectos",
                    "errores" => $errores
                ]);
                return;
            }

            // Verificar conflictos
            $conflictos = $this->evento->verificarConflictos();
            if (!empty($conflictos)) {
                http_response_code(409);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Existen conflictos con otros eventos",
                    "conflictos" => $conflictos
                ]);
                return;
            }

            // Crear evento
            if ($this->evento->crear()) {
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Evento creado exitosamente",
                    "id" => $this->evento->idEvento
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Error al crear el evento"
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

    // Listar eventos con filtros y paginación
    public function listar() {
        try {
            $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $offset = ($pagina - 1) * $limite;

            // Filtros opcionales
            $filtros = [];
            if (isset($_GET['zona'])) $filtros['zona'] = $_GET['zona'];
            if (isset($_GET['estado'])) $filtros['estado'] = $_GET['estado'];
            if (isset($_GET['tipo'])) $filtros['tipo'] = $_GET['tipo'];
            if (isset($_GET['fecha_inicio'])) $filtros['fecha_inicio'] = $_GET['fecha_inicio'];
            if (isset($_GET['fecha_fin'])) $filtros['fecha_fin'] = $_GET['fecha_fin'];
            if (isset($_GET['prioridad'])) $filtros['prioridad'] = $_GET['prioridad'];
            if (isset($_GET['organizador'])) $filtros['organizador'] = $_GET['organizador'];

            $stmt = $this->evento->leer($limite, $offset, $filtros);
            $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $eventos,
                "pagination" => [
                    "pagina" => $pagina,
                    "limite" => $limite,
                    "total_registros" => count($eventos)
                ],
                "filtros" => $filtros
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener eventos",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Obtener evento por ID
    public function obtenerPorId($id) {
        try {
            $this->evento->idEvento = $id;

            if ($this->evento->leerPorId()) {
                echo json_encode([
                    "success" => true,
                    "data" => [
                        "idEvento" => $this->evento->idEvento,
                        "nombre" => $this->evento->nombre,
                        "descripcion" => $this->evento->descripcion,
                        "fecha_inicio" => $this->evento->fecha_inicio,
                        "fecha_fin" => $this->evento->fecha_fin,
                        "hora_inicio" => $this->evento->hora_inicio,
                        "hora_fin" => $this->evento->hora_fin,
                        "tipo_evento" => $this->evento->tipo_evento,
                        "capacidad_reservada" => $this->evento->capacidad_reservada,
                        "estado" => $this->evento->estado,
                        "prioridad" => $this->evento->prioridad,
                        "ubicacion" => $this->evento->ubicacion,
                        "organizador" => $this->evento->organizador,
                        "contacto" => $this->evento->contacto,
                        "restricciones" => $this->evento->restricciones,
                        "costo_adicional" => $this->evento->costo_adicional,
                        "idZona" => $this->evento->idZona,
                        "fechaCreacion" => $this->evento->fechaCreacion,
                        "fechaActualizacion" => $this->evento->fechaActualizacion
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Evento no encontrado"
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener evento",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Actualizar evento
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

            $this->evento->idEvento = $id;

            // Verificar que existe
            if (!$this->evento->leerPorId()) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Evento no encontrado"
                ]);
                return;
            }

            // Actualizar campos
            if (isset($data['nombre'])) $this->evento->nombre = $data['nombre'];
            if (isset($data['descripcion'])) $this->evento->descripcion = $data['descripcion'];
            if (isset($data['fecha_inicio'])) $this->evento->fecha_inicio = $data['fecha_inicio'];
            if (isset($data['fecha_fin'])) $this->evento->fecha_fin = $data['fecha_fin'];
            if (isset($data['hora_inicio'])) $this->evento->hora_inicio = $data['hora_inicio'];
            if (isset($data['hora_fin'])) $this->evento->hora_fin = $data['hora_fin'];
            if (isset($data['tipo_evento'])) $this->evento->tipo_evento = $data['tipo_evento'];
            if (isset($data['capacidad_reservada'])) $this->evento->capacidad_reservada = $data['capacidad_reservada'];
            if (isset($data['estado'])) $this->evento->estado = $data['estado'];
            if (isset($data['prioridad'])) $this->evento->prioridad = $data['prioridad'];
            if (isset($data['ubicacion'])) $this->evento->ubicacion = $data['ubicacion'];
            if (isset($data['organizador'])) $this->evento->organizador = $data['organizador'];
            if (isset($data['contacto'])) $this->evento->contacto = $data['contacto'];
            if (isset($data['restricciones'])) $this->evento->restricciones = $data['restricciones'];
            if (isset($data['costo_adicional'])) $this->evento->costo_adicional = $data['costo_adicional'];
            if (isset($data['idZona'])) $this->evento->idZona = $data['idZona'];

            // Validar datos
            $errores = $this->evento->validar();
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Datos de validación incorrectos",
                    "errores" => $errores
                ]);
                return;
            }

            // Verificar conflictos (excluyendo el evento actual)
            $conflictos = $this->evento->verificarConflictos($id);
            if (!empty($conflictos)) {
                http_response_code(409);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Existen conflictos con otros eventos",
                    "conflictos" => $conflictos
                ]);
                return;
            }

            if ($this->evento->actualizar()) {
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Evento actualizado exitosamente"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Error al actualizar el evento"
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

    // Eliminar evento
    public function eliminar($id) {
        try {
            $this->evento->idEvento = $id;

            if (!$this->evento->leerPorId()) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Evento no encontrado"
                ]);
                return;
            }

            if ($this->evento->eliminar()) {
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Evento eliminado exitosamente"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "No se puede eliminar el evento. Puede tener reservas asociadas."
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

    // Cambiar estado del evento
    public function cambiarEstado($id) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $nuevoEstado = $data['estado'] ?? null;

            if (!$nuevoEstado) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Estado requerido"
                ]);
                return;
            }

            $this->evento->idEvento = $id;

            if (!$this->evento->leerPorId()) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Evento no encontrado"
                ]);
                return;
            }

            if ($this->evento->cambiarEstado($nuevoEstado)) {
                echo json_encode([
                    "success" => true,
                    "mensaje" => "Estado del evento actualizado exitosamente",
                    "nuevo_estado" => $nuevoEstado
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Estado inválido o error al actualizar"
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

    // Verificar conflictos de eventos
    public function verificarConflictos() {
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

            $this->evento->idZona = $data['idZona'] ?? null;
            $this->evento->fecha_inicio = $data['fecha_inicio'] ?? null;
            $this->evento->fecha_fin = $data['fecha_fin'] ?? null;
            $excluirId = $data['excluir_id'] ?? null;

            if (!$this->evento->idZona || !$this->evento->fecha_inicio) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "mensaje" => "Zona y fecha de inicio son requeridas"
                ]);
                return;
            }

            $conflictos = $this->evento->verificarConflictos($excluirId);

            echo json_encode([
                "success" => true,
                "tiene_conflictos" => !empty($conflictos),
                "conflictos" => $conflictos
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error interno del servidor",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Obtener eventos activos
    public function obtenerEventosActivos() {
        try {
            $fecha = $_GET['fecha'] ?? date('Y-m-d');
            $eventos = $this->evento->obtenerEventosActivos($fecha);

            echo json_encode([
                "success" => true,
                "data" => $eventos,
                "fecha" => $fecha
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener eventos activos",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Obtener próximos eventos
    public function obtenerProximosEventos() {
        try {
            $dias = isset($_GET['dias']) ? (int)$_GET['dias'] : 7;
            $eventos = $this->evento->obtenerProximosEventos($dias);

            echo json_encode([
                "success" => true,
                "data" => $eventos,
                "dias" => $dias
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener próximos eventos",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Obtener estadísticas de eventos
    public function obtenerEstadisticas() {
        try {
            $fechaInicio = $_GET['fecha_inicio'] ?? null;
            $fechaFin = $_GET['fecha_fin'] ?? null;

            $estadisticas = $this->evento->obtenerEstadisticas($fechaInicio, $fechaFin);

            echo json_encode([
                "success" => true,
                "data" => $estadisticas,
                "filtros" => [
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

    // Obtener opciones de configuración
    public function obtenerOpciones() {
        try {
            echo json_encode([
                "success" => true,
                "data" => [
                    "estados" => $this->evento->getEstadosValidos(),
                    "tipos" => $this->evento->getTiposValidos(),
                    "prioridades" => $this->evento->getPrioridadesValidas()
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "mensaje" => "Error al obtener opciones",
                "error" => $e->getMessage()
            ]);
        }
    }
}
