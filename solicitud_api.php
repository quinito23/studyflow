<?php

include_once 'DBConnection.php';
include_once 'solicitud.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//obtenemos la conexión con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase solicitud
$solicitud = new Solicitud($db);

//almacenamos el metodo http de la peticion
$method = $_SERVER['REQUEST_METHOD'];

switch ($method){
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        //verificamos que todos los datos requeridos esten presentes
        if (isset($data->id_anonimo) && isset($data->estado) && isset($data->fecha_realizacion) && isset($data->rol_propuesto)){
            $solicitud->id_anonimo = $data->id_anonimo;
            $solicitud->estado = $data->estado;
            $solicitud->fecha_realizacion = $data->fecha_realizacion;
            $solicitud->rol_propuesto = $data->rol_propuesto;

            $id_solicitud = $solicitud->crear();

            if($id_solicitud){
                echo json_encode(array("message" => "Solicitud creada exitosamente", "id_solicitud" => $id_solicitud));
            }else{
                echo json_encode(array("message" => "No se pudo crear la solicitud"));
            }
        }else{
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;
    default:
        echo json_encode(array("message" => "Método no permitido"));
        break;
}


?>