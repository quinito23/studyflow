<?php

session_start();

include_once 'DBConnection.php';
include_once 'tarea.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//autenticacion
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'alumno')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

$database = new DBConnection();
$db = $database->getConnection();
$tarea = new Tarea($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['asignatura'])) {
            $id_asignatura = htmlspecialchars(strip_tags($_GET['asignatrua']));
            $tareas = $tarea->obtenerPorAsignatura($id_asignatura);
            echo json_encode($tareas);
        } elseif (isset($_GET['grupo'])) {
            $id_grupo = htmlspecialchars(strip_tags($_GET['grupo']));
            $tareas = $tarea->obtenerPorGrupo($id_grupo);
            echo json_encode($tareas);
        } elseif (isset($_GET['id'])) {
            $tarea->id_tarea = htmlspecialchars(strip_tags($_GET['id']));
            if ($tarea->leer()) {
                $tarea_data = array(
                    "id_tarea" => $tarea->id_tarea,
                    "ïd_usuario" => $tarea->id_usuario,
                    "descripcion" => $tarea->descripcion,
                    "fecha_creacion" => $tarea->fecha_creacion,
                    "fecha_entrega" => $tarea->fecha_entrega
                );
                echo json_encode($tarea_data);
            } else {
                echo json_encode(array("message" => "Tarea no encontrada"));
            }
        } else {
            $tareas = $tarea->leer_todos();
            echo json_encode($tareas);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->descripcion) && isset($data->fecha_entrega) && isset($data->asignaturas) && isset($data->grupos)) {
            $tarea->id_usuario = $_SESSION['id_usuario'];
            $tarea->descripcion = $data->descripcion;
            $tarea->fecha_creacion = date('Y-m-d H:i:s');
            $tarea->fecha_entrega = $data->fecha_entrega;

            if ($tarea->crear()) {
                $tarea->asignarAsignaturas($data->asignaturas);
                $tarea->asignarGrupos($data->grupos);
                echo json_encode(array('message' => 'Tarea creada exitosamente'));
            } else {
                echo json_encode(array('message' => 'No se pudo crear la tarea'));
            }
        } else {
            echo json_encode(array('message' => 'Faltan datos requeridos'));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_tarea) && isset($data->descripcion) && isset($data->fecha_entrega)) {
            $tarea->id_tarea = $data->id_tarea;
            $tarea->id_usuario = $_SESSION['id_usuario'];
            $tarea->descripcion = $data->descripcion;
            $tarea->fecha_creacion = date('Y-m-d H:i:s');
            $tarea->fecha_entrega = $data->fecha_entrega;

            if ($tarea->actualizar()) {
                echo json_encode(array('message' => 'Tarea actualizada exitosamente'));
            } else {
                echo json_encode(array('message' => 'No se pudo actualizar la tarea'));
            }
        } else {
            echo json_encode(array('message' => 'Faltan datos requeridos'));
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