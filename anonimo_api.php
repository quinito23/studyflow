<?php

include_once 'DBConnection.php';
include_once 'anonimo.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// obtenemos la conexion con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase anonimo pasandole la conexion a la bd como parametro
$anonimo = new Anonimo($db);

//almacenamos el metodo HTTP de la petición
$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) && isset($data->apellidos) && isset($data->telefono)){
            $anonimo->correo = $data->correo;
            $anonimo->contrasenia = $data->contrasenia;
            $anonimo->nombre = $data->nombre;
            $anonimo->apellidos = $data->apellidos;
            $anonimo->telefono = $data->telefono;

            $id_anonimo = $anonimo->crear();

            if($id_anonimo){
                echo json_encode(array("message" => "Anonimo creado exitosamente", "id_anonimo" => $id_anonimo));
            }else{
                echo json_encode(array("message" => "No se pudo crear el anonimo"));
            }
        }else{
            echo json_encode(array("message" => "Faltan datos necesarios"));
        }
        break;
    default:
        echo json_encode(array("message" => "Metodo no permitido"));
        break;

}

?>