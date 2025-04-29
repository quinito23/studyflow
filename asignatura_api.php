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
        try {
            $asignaturas = $asignatura->leer_todos();
            echo json_encode($asignaturas);
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al listar asignaturas: " . $e->getMessage()));
        }
        break;
    default:
        echo json_encode(array("message" => "Metodo no permitido"));
        break;
}



?>