<?php
session_start();

include_once 'DBConnection.php';
include_once 'grupo.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$database = new DBConnection();
$db = $database->getConnection();

$grupo = new Grupo($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $grupo->id_grupo = $_GET['id'];
            if ($grupo->leer()) {
                echo json_encode(array(
                    "id_grupo" => $grupo->id_grupo,
                    "nombre" => $grupo->nombre,
                    "capacidad_maxima" => $grupo->capacidad_maxima,
                    "id_asignatura" => $grupo->id_asignatura,
                    "nombre_asignatura" => $grupo->nombre_asignatura,
                    "numero_alumnos" => $grupo->numero_alumnos
                ));
            } else {
                echo json_encode(array("message" => "Grupo no encontrado"));
            }
        } else if (isset($_GET['id_asignatura'])) {
            // Filter groups by subject (id_asignatura)
            $id_asignatura = $_GET['id_asignatura'];
            try {
                $query = "SELECT g.id_grupo, g.nombre, g.capacidad_maxima, g.id_asignatura, 
                          a.nombre AS nombre_asignatura, 
                          (SELECT COUNT(*) FROM alumno_grupo ag WHERE ag.id_grupo = g.id_grupo) AS numero_alumnos 
                          FROM grupo g 
                          JOIN asignatura a ON g.id_asignatura = a.id_asignatura 
                          WHERE g.id_asignatura = :id_asignatura";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
                $stmt->execute();
                $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($grupos);
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error al listar grupos: " . $e->getMessage()));
            }
        } else {
            $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
            $hora_inicio = isset($_GET['hora_inicio']) ? $_GET['hora_inicio'] : null;
            $hora_fin = isset($_GET['hora_fin']) ? $_GET['hora_fin'] : null;

            try {
                if ($fecha && $hora_inicio && $hora_fin) {
                    $grupos = $grupo->leerDisponibles($fecha, $hora_inicio, $hora_fin);
                } else {
                    $grupos = $grupo->leer_todos();
                }
                echo json_encode($grupos);
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error al listar grupos: " . $e->getMessage()));
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nombre) || !isset($data->capacidad_maxima) || !isset($data->id_asignatura)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        $grupo->nombre = $data->nombre;
        $grupo->capacidad_maxima = $data->capacidad_maxima;
        $grupo->id_asignatura = $data->id_asignatura;

        try {
            if ($grupo->crear()) {
                echo json_encode(array("message" => "Grupo creado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo crear el grupo"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al crear grupo: " . $e->getMessage()));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'));

        if (!isset($data->id_grupo) || !isset($data->nombre) || !isset($data->capacidad_maxima) || !isset($data->id_asignatura)) {
            echo json_encode(array('message' => 'Faltan datos requeridos'));
            exit;
        }

        $grupo->id_grupo = $data->id_grupo;
        $grupo->nombre = $data->nombre;
        $grupo->capacidad_maxima = $data->capacidad_maxima;
        $grupo->id_asignatura = $data->id_asignatura;

        try {
            if ($grupo->actualizar()) {
                echo json_encode(array("message" => "Grupo actualizado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo actualizar el grupo"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al actualizar grupo: " . $e->getMessage()));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'));

        if (!isset($data->id_grupo)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        $grupo->id_grupo = $data->id_grupo;
        try {
            if ($grupo->eliminar()) {
                echo json_encode(array("message" => "Grupo eliminado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo eliminar el grupo"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al eliminar el grupo: " . $e->getMessage()));
        }
        break;

    default:
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>