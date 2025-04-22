<?php

include_once 'DBConnection.php';
include_once 'profesor.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//obtenemos la conexión con la base de datos

$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase profesor
$profesor = new Profesor($db);

//almacenamos el metodo HTTP de la peticion en una variable

$method = $_SERVER['REQUEST_METHOD'];

//ruta de la API

switch($method){
    case 'GET':
        // leer un autor o todos, dependera de si se pasa id o no
        if(isset($_GET['id'])){
            $profesor->id_usuario = $_GET['id'];
            $profesor->leer();
            $profesor_data = array(
                "id_usuario" => $profesor->id_usuario,
                "correo" => $profesor->correo,
                "contrasenia" => $profesor->contrasenia,
                "nombre" => $profesor->nombre,
                "apellidos" => $profesor->apellidos,
                "DNI" => $profesor->DNI,
                "telefono" => $profesor->telefono,
                "fecha_nacimiento" => $profesor->fecha_nacimiento,
                "rol" => $profesor->rol,
                "sueldo" => $profesor->sueldo,
                "jornada" => $profesor->jornada,
                "fecha_inicio_contrato" => $profesor->fecha_inicio_contrato,
                "fecha_fin_contrato"=> $profesor->fecha_fin_contrato
            );
            echo json_encode($profesor_data);
        }else{
            $result = $profesor->leer_todos();
            echo json_encode($result);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->correo) && isset($data->contrasenia)  && isset($data->nombre) && isset($data->apellidos)){
            $profesor->correo = $data->correo;
            $profesor->contrasenia = $data->contrasenia;
            $profesor->nombre = $data->nombre;
            $profesor->apellidos = $data->apellidos;
            $profesor->DNI = $data->DNI;
            $profesor->telefono = $data->telefono;
            $profesor->fecha_nacimiento = $data->fecha_nacimiento;
            $profesor->rol = $data->rol;
            $profesor->sueldo = $data->sueldo;
            $profesor->jornada = $data->jornada;
            $profesor->fecha_inicio_contrato = $data->fecha_inicio_contrato;
            $profesor->fecha_fin_contrato = $data->fecha_fin_contrato;

            $id_profesor = $profesor->crear();

            if($id_profesor){
                echo json_encode(array("message" => "Autor creado exitosamente", "id_usuario" => $id_profesor));
            }else{
                echo json_encode(array("message" => "No se pudo crear el autor"));
            }
        }else{
            echo json_encode(array("message" => "faltan datos requeridos"));
        }
        break;

    case 'PUT':
        //actualizar profesor

        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id_usuario) && isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) &&  isset($data->apellidos)){

            $profesor->id_usuario = $data->id_usuario;
            $profesor->correo = $data->correo;
            $profesor->contrasenia = $data->contrasenia;
            $profesor->nombre = $data->nombre;
            $profesor->apellidos = $data->apellidos;
            $profesor->DNI = $data->DNI;
            $profesor->telefono = $data->telefono;
            $profesor->fecha_nacimiento = $data->fecha_nacimiento;
            $profesor->rol = $data->rol;
            $profesor->sueldo = $data->sueldo;
            $profesor->jornada = $data->jornada;
            $profesor->fecha_inicio_contrato = $data->fecha_inicio_contrato;
            $profesor->fecha_fin_contrato = $data->fecha_fin_contrato;

            if($profesor->actualizar()){
                echo json_encode(array("message" => "Autor actualizado exitosamente"));
            }else{
                echo json_encode(array("message" => "No se pudo actualizar al autor"));
            }
        }else{
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'DELETE':
        //eliminar un profesor
        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id_usuario)){
            $profesor->id_usuario = $data->id_usuario;

            if($profesor->eliminar()){
                echo json_encode(array("message" => "Autor eliminado exitosamente"));
            }else{
                echo json_encode(array("message" => "No se pudo eliminar el autor"));
            }
        }else{
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;
    
    default:
        echo json_encode(array("message" => "Metodo no permitido"));
        break;
    
}

?>