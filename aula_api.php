<?php
session_start();

include_once 'DBConnection.php';
include_once 'aula.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar autenticación y rol
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'administrador')) {
    echo json_encode(array("message" => "Acceso denegado", "error" => "No autorizado"));
    http_response_code(403);
    exit;
}

// Crear la conexión con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

$aula = new Aula($db);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener parámetros de horario y grupo para reservas
        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
        $hora_inicio = isset($_GET['hora_inicio']) ? $_GET['hora_inicio'] : null;
        $hora_fin = isset($_GET['hora_fin']) ? $_GET['hora_fin'] : null;
        $id_grupo = isset($_GET['id_grupo']) ? $_GET['id_grupo'] : null;
        $id_asignatura = isset($_GET['id_asignatura']) ? $_GET['id_asignatura'] : null;
        $id_reserva = isset($_GET['id_reserva']) ? $_GET['id_reserva'] : null;

        if ($fecha && $hora_inicio && $hora_fin) {
            // Validar que la hora de finalización sea posterior a la hora de inicio
            if (strtotime($fecha . ' ' . $hora_fin) <= strtotime($fecha . ' ' . $hora_inicio)) {
                echo json_encode(array('message' => 'La hora de finalización debe ser posterior a la hora de inicio', 'error' => 'Invalid time range'));
                http_response_code(400);
                exit;
            }

            // Consulta para obtener aulas disponibles
            $currentTime = date('Y-m-d H:i:s');
            $query = "SELECT a.id_aula, a.nombre 
                      FROM aula a 
                      LEFT JOIN reserva r ON a.id_aula = r.id_aula 
                      AND r.fecha = :fecha 
                      AND (CONCAT(r.fecha, ' ', r.hora_fin) > :currentTime) 
                      AND NOT (r.hora_fin <= :hora_inicio OR :hora_fin <= r.hora_inicio)";

            if ($id_reserva) {
                $query .= " AND (r.id_reserva != :id_reserva OR r.id_reserva IS NULL)";
            } else {
                $query .= " AND r.id_aula IS NULL";
            }

            $query .= " WHERE a.id_aula NOT IN (
                          SELECT id_aula FROM reserva WHERE fecha = :fecha 
                          AND NOT (hora_fin <= :hora_inicio OR :hora_fin <= hora_inicio)";

            if ($id_reserva) {
                $query .= " AND id_reserva != :id_reserva";
            }

            $query .= ") GROUP BY a.id_aula, a.nombre ORDER BY a.nombre ASC";

            try {
                $stmt = $db->prepare($query);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':hora_inicio', $hora_inicio);
                $stmt->bindParam(':hora_fin', $hora_fin);
                $stmt->bindParam(':currentTime', $currentTime);
                if ($id_reserva) {
                    $stmt->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
                }

                $stmt->execute();
                $listaAulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $aulas = array();
                foreach ($listaAulas as $aula_row) {
                    $aulas[] = array(
                        "id_aula" => $aula_row['id_aula'],
                        "nombre" => $aula_row['nombre']
                    );
                }

                if (empty($aulas)) {
                    echo json_encode(array("message" => "No hay aulas disponibles para el horario seleccionado", "aulas" => []));
                } else {
                    echo json_encode($aulas);
                }
            } catch (PDOException $e) {
                echo json_encode(array("message" => "Error en la consulta de aulas", "error" => $e->getMessage()));
                http_response_code(500);
                exit;
            }
        } elseif (isset($_GET['id'])) {
            // Obtener un aula específica
            $aula->id_aula = $_GET['id'];
            if ($aula->leer()) {
                echo json_encode(array(
                    "id_aula" => $aula->id_aula,
                    "nombre" => $aula->nombre,
                    "capacidad" => $aula->capacidad,
                    "equipamiento" => $aula->equipamiento
                ));
            } else {
                echo json_encode(array("message" => "Aula no encontrada", "error" => "Not found"));
                http_response_code(404);
            }
        } else {
            // Obtener todas las aulas
            try {
                $aulas = $aula->leer_todos();
                echo json_encode($aulas);
            } catch (PDOException $e) {
                echo json_encode(array("message" => "Error al obtener aulas", "error" => $e->getMessage()));
                http_response_code(500);
            }
        }
        break;

    case 'POST':
        // Crear un nuevo aula
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->nombre) && isset($data->capacidad) && isset($data->equipamiento)) {
            $aula->nombre = $data->nombre;
            $aula->capacidad = $data->capacidad;
            $aula->equipamiento = $data->equipamiento;

            if ($aula->crear()) {
                echo json_encode(array("message" => "Aula creada exitosamente", "id_aula" => $aula->id_aula));
                http_response_code(201);
            } else {
                echo json_encode(array("message" => "No se pudo crear el aula", "error" => "Creation failed"));
                http_response_code(500);
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos", "error" => "Missing data"));
            http_response_code(400);
        }
        break;

    case 'PUT':
        // Actualizar un aula existente
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_aula) && isset($data->nombre) && isset($data->capacidad) && isset($data->equipamiento)) {
            $aula->id_aula = $data->id_aula;
            $aula->nombre = $data->nombre;
            $aula->capacidad = $data->capacidad;
            $aula->equipamiento = $data->equipamiento;

            if ($aula->actualizar()) {
                echo json_encode(array("message" => "Aula actualizada exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo actualizar el aula", "error" => "Update failed"));
                http_response_code(500);
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos", "error" => "Missing data"));
            http_response_code(400);
        }
        break;

    case 'DELETE':
        // Eliminar un aula
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_aula)) {
            $aula->id_aula = $data->id_aula;
            if ($aula->eliminar()) {
                echo json_encode(array("message" => "Aula eliminada exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo eliminar el aula", "error" => "Deletion failed"));
                http_response_code(500);
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos", "error" => "Missing data"));
            http_response_code(400);
        }
        break;

    default:
        echo json_encode(array("message" => "Método no permitido", "error" => "Method not allowed"));
        http_response_code(405);
        break;
}
?>