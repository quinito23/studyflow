<?php

include_once 'DBConnection.php';
include_once 'asignatura.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$database = new DBConnection();
$db = $database->getConnection();

$asignatura = new Asignatura($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $asignatura->id_asignatura = $_GET['id'];
            if ($asignatura->leer()) {
                echo json_encode(array(
                    "id_asignatura" => $asignatura->id_asignatura,
                    "nombre" => $asignatura->nombre,
                    "descripcion" => $asignatura->descripcion,
                    "nivel" => $asignatura->nivel
                ));
            } else {
                echo json_encode(array("message" => "asignatura no encontrada"));
            }
        } else {
            try {
                $asignaturas = $asignatura->leer_todos();
                echo json_encode($asignaturas);
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error al listar las asignaturas :" . $e->getMessage()));
            }
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->nombre) && !isset($data->descripcion) && !isset($data->nivel)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        $asignatura->nombre = $data->nombre;
        $asignatura->descripcion = $data->descripcion;
        $asignatura->nivel = $data->nivel;

        try {
            if ($asignatura->crear()) {
                echo json_encode(array("message" => "Asignatura creada exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo crear la asignatura"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al crear la asignatura: " . $e->getMessage()));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id_asignatura) && !isset($data->nombre) && !isset($data->descripcion) && !isset($data->nivel)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }

        $asignatura->id_asignatura = $data->id_asignatura;
        $asignatura->nombre = $data->nombre;
        $asignatura->descripcion = $data->descripcion;
        $asignatura->nivel = $data->nivel;

        try {
            if ($asignatura->actualizar()) {
                echo json_encode(array("message" => "Asignatura actualizada exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo actualizar la asignatura"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al actualizar la asignatura" . $e->getMessage()));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data->id_asignatura)) {
            $asignatura->id_asignatura = $data->id_asignatura;

            if ($asignatura->eliminar()) {
                echo json_encode(array("message" => "Asignatura eliminada exitosamente"));
            } else {
                echo json_encode(array("message" => "No se ha podido eliminar la asignatura"));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    default:
        echo json_encode(array("message" => "Metodo no permitido"));
        break;
}

?>