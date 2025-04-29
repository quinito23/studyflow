<?php

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
        try {
            $grupos = $grupo->leer_todos();
            echo json_encode($grupos);
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al listar grupos" . $e->getMessage()));
        }
        break;
    default:
        echo json_encode(array("message" => "Metodo no permitido"));
        break;

}

?>