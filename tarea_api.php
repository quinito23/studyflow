<?php
session_start();

include_once 'DBConnection.php';
include_once 'tarea.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar autenticación
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'alumno')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

// Conexión con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

$tarea = new Tarea($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['asignatura'])) {
            if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' && $_SESSION['rol'] != 'administrador')) {
                echo json_encode(array('message' => 'Acceso denegado'));
                exit;
            }
            $id_asignatura = $_GET['asignatura'];
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;
            $tareas = $tarea->obtenerPorAsignatura($id_asignatura, $id_usuario);
            echo json_encode($tareas);
        } else {
            if (isset($_GET['id'])) {
                $tarea->id_tarea = $_GET['id'];
                if ($tarea->leer()) {
                    $tarea_data = array(
                        "id_tarea" => $tarea->id_tarea,
                        "id_usuario" => $tarea->id_usuario,
                        "descripcion" => $tarea->descripcion,
                        "fecha_creacion" => $tarea->fecha_creacion,
                        "fecha_entrega" => $tarea->fecha_entrega,
                        "estado" => $tarea->estado, // Now safe to access
                        "id_asignatura" => $tarea->id_asignatura,
                        "id_grupo" => $tarea->id_grupo
                    );
                    echo json_encode($tarea_data);
                } else {
                    echo json_encode(array("message" => "Tarea no encontrada"));
                }
            } else {
                if (isset($_GET['todas']) && $_GET['todas'] == 1) {
                    $result = $tarea->leer_todos(null);
                } else {
                    $id_usuario = $_SESSION['id_usuario'];
                    $result = $tarea->leer_todos($id_usuario);
                }
                echo json_encode($result);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->descripcion) && isset($data->fecha_entrega) && isset($data->id_asignatura) && isset($data->id_grupo)) {
            try {
                $tarea->id_usuario = $_SESSION['id_usuario'];
                $tarea->descripcion = $data->descripcion;
                $tarea->fecha_entrega = $data->fecha_entrega;
                $tarea->id_asignatura = $data->id_asignatura;
                $tarea->id_grupo = $data->id_grupo;

                $id_tarea = $tarea->crear();
                if ($id_tarea) {
                    echo json_encode(array("message" => "Tarea creada exitosamente"));
                } else {
                    echo json_encode(array("message" => "No se pudo crear la tarea"));
                }
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_tarea) && isset($data->descripcion) && isset($data->fecha_entrega) && isset($data->id_asignatura) && isset($data->id_grupo)) {
            try {
                $tarea->id_tarea = $data->id_tarea;
                $tarea->descripcion = $data->descripcion;
                $tarea->fecha_entrega = $data->fecha_entrega;
                $tarea->id_asignatura = $data->id_asignatura;
                $tarea->id_grupo = $data->id_grupo;

                if ($tarea->actualizar()) {
                    echo json_encode(array("message" => "Tarea actualizada exitosamente"));
                } else {
                    echo json_encode(array("message" => "No se pudo actualizar la tarea"));
                }
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_tarea)) {
            $tarea->id_tarea = $data->id_tarea;
            if ($tarea->eliminar()) {
                echo json_encode(array("message" => "Tarea eliminada exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo eliminar la tarea"));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    default:
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>