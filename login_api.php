<?php

include_once 'DBConnection.php';
include_once 'usuario.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//obtener la conexion con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase usuario

$usuario = new Usuario($db);

$method = $_SERVER['REQUEST_METHOD'];

//RUTA DE LA API

switch ($method) {
    case 'POST':
        //MANEJAR LOGIN
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->correo) && isset($data->contrasenia)) {
            $usuario->correo = $data->correo;
            $contrasenia = $data->contrasenia;

            if ($usuario->verificarLogin()) {
                if ($usuario->contrasenia === $contrasenia) {
                    session_start();
                    $_SESSION['id_usuario'] = $usuario->id_usuario;
                    $_SESSION['correo'] = $usuario->correo;
                    $_SESSION['rol'] = $usuario->rol;
                    echo json_encode(array("message" => "Login exitoso", "rol" => $usuario->rol));
                } else {
                    echo json_encode(array("message" => "Usuario o contraseña incorrectos"));
                }
            } else {
                echo json_encode(array("message" => "Usuario o contraseña incorrectos"));
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