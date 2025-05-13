<?php

include_once '../DBConnection.php';
include_once '../clases/tutor.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//obtenemos la conexión con la base de datos

$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase tutor
$tutor = new Tutor($db);

//almacenamos el metodo HTTP de la peticion en una variable

$method = $_SERVER['REQUEST_METHOD'];

//ruta de la API

switch ($method) {
    case 'GET':
        // leer un tutor o todos, dependera de si se pasa id o no
        if (isset($_GET['id'])) {
            $tutor->id_tutor = $_GET['id'];
            $tutor->leer();
            $tutor_data = array(
                "id_tutor" => $tutor->id_tutor,
                "nombre" => $tutor->nombre,
                "apellidos" => $tutor->apellidos,
                "telefono" => $tutor->telefono,
            );
            echo json_encode($tutor_data);
        } else {
            $result = $tutor->leer_todos();
            echo json_encode($result);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->nombre) && isset($data->apellidos)) {
            $tutor->nombre = $data->nombre;
            $tutor->apellidos = $data->apellidos;
            $tutor->telefono = $data->telefono;

            $id_tutor = $tutor->crear();

            if ($id_tutor) {
                echo json_encode(array("message" => "Tutor creado exitosamente", "id_tutor" => $id_tutor));
            } else {
                echo json_encode(array("message" => "No se pudo crear el tutor"));
            }
        } else {
            echo json_encode(array("message" => "faltan datos requeridos"));
        }
        break;

    case 'PUT':
        //actualizar tutor

        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->nombre) && isset($data->apellidos)) {

            $tutor->id_tutor = $data->id_tutor;
            $tutor->nombre = $data->nombre;
            $tutor->apellidos = $data->apellidos;
            $tutor->telefono = $data->telefono;

            if ($tutor->actualizar()) {
                echo json_encode(array("message" => "Tutor actualizado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo actualizar al Tuttor"));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'DELETE':
        //eliminar un tutor
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_tutor)) {
            $tutor->id_tutor = $data->id_tutor;

            if ($tutor->eliminar()) {
                echo json_encode(array("message" => "Tutor eliminado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo eliminar el Tutor"));
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