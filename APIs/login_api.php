<?php

//incluimos la clase que establecen la conexión con la base de datos y la clase Usuario que contiene los metodos necesarios para manejar las operaciones de usuarios

include_once '../DBConnection.php';
include_once '../clases/usuario.php';

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

// obtenemos el metodo HTTP de la solicitud que se realiza desde el frontend
$method = $_SERVER['REQUEST_METHOD'];

//bloque para procesar la solicitud según el método HTTP

switch ($method) {
    case 'POST':
        //MANEJAR LOGIN
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->correo) && isset($data->contrasenia)) {
            $usuario->correo = $data->correo;
            $contrasenia = $data->contrasenia;
            //Verificamos con la base de datos que las credenciales sean correctas, mediante el metodo de la clase
            if ($usuario->verificarLogin()) {
                if ($usuario->contrasenia === $contrasenia) {
                    //si son correctas emepzamos la sesión y alamacenamos los datos del usuario en la sesión
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