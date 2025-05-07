<?php


include_once 'DBConnection.php';
include_once 'alumno.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//obtenemos la conexión con la base de datos

$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase profesor
$alumno = new Alumno($db);

//almacenamos el metodo HTTP de la peticion en una variable

$method = $_SERVER['REQUEST_METHOD'];

//ruta de la API

switch ($method) {
    case 'GET':
        //primero verificmos si se solicita la lista de asignaturas del alumno
        if (isset($_GET['asignaturas']) && $_GET['asignaturas'] == 1) {
            //verificamos autenticacion y rol
            if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' && $_SESSION['rol'] != 'administrador')) {
                echo json_encode(array("message" => "Acceso denegado."));
                exit;
            }
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;

            if (!$id_usuario || $id_usuario != $_SESSION['id_usuario']) {
                echo json_encode(array("message" => "ID de usuario no valido"));
                exit;
            }

            $asignaturas->alumno->obtenerAsignaturas($id_usuario);
            echo json_encode($asignaturas);
        } else {
            // leer un alumno o todos, dependera de si se pasa id o no
            if (isset($_GET['id'])) {
                $alumno->id_usuario = $_GET['id'];
                $alumno->leer();
                $alumno_data = array(
                    "id_usuario" => $alumno->id_usuario,
                    "correo" => $alumno->correo,
                    "contrasenia" => $alumno->contrasenia,
                    "nombre" => $alumno->nombre,
                    "apellidos" => $alumno->apellidos,
                    "DNI" => $alumno->DNI,
                    "telefono" => $alumno->telefono,
                    "fecha_nacimiento" => $alumno->fecha_nacimiento,
                    "rol" => $alumno->rol,
                    "tutores" => $alumno->tutores
                );
                echo json_encode($alumno_data);
            } else {
                $result = $alumno->leer_todos();
                echo json_encode($result);
            }
        }
        break;


    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) && isset($data->apellidos)) {
            $alumno->correo = $data->correo;
            $alumno->contrasenia = $data->contrasenia;
            $alumno->nombre = $data->nombre;
            $alumno->apellidos = $data->apellidos;
            $alumno->DNI = $data->DNI;
            $alumno->telefono = $data->telefono;
            $alumno->fecha_nacimiento = $data->fecha_nacimiento;
            $alumno->rol = $data->rol;
            //almacenamos el array de tutores con los id de los tutores selccionados en el front
            $tutores = $data->tutores;
            // este array se lo pasaremos al metodo crear para que este maneje la asociación de alumnos con tutores

            $id_alumno = $alumno->crear($tutores);

            if ($id_alumno) {
                echo json_encode(array("message" => "Alumno creado exitosamente", "id_usuario" => $id_alumno));
            } else {
                echo json_encode(array("message" => "No se pudo crear el alumno"));
            }
        } else {
            echo json_encode(array("message" => "faltan datos requeridos"));
        }
        break;

    case 'PUT':
        //actualizar profesor

        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_usuario) && isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) && isset($data->apellidos)) {

            $alumno->id_usuario = $data->id_usuario;
            $alumno->correo = $data->correo;
            $alumno->contrasenia = $data->contrasenia;
            $alumno->nombre = $data->nombre;
            $alumno->apellidos = $data->apellidos;
            $alumno->DNI = $data->DNI;
            $alumno->telefono = $data->telefono;
            $alumno->fecha_nacimiento = $data->fecha_nacimiento;
            $alumno->rol = $data->rol;
            $tutores = $data->tutores;

            if ($alumno->actualizar($tutores)) {
                echo json_encode(array("message" => "Alumno actualizado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo actualizar al Alumno"));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'DELETE':
        //eliminar un profesor
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_usuario)) {
            $alumno->id_usuario = $data->id_usuario;

            if ($alumno->eliminar()) {
                echo json_encode(array("message" => "Alumno eliminado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo eliminar el Alumno"));
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