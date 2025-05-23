<?php
session_start(); //inicio de la sesión

//incluimos las clases necesarias para la conexión a la base de datos y manejar las solicitudes
include_once '../../db/DBConnection.php';
include_once '../models/reserva.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//verificar autenticacion
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'alumno')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

//conexion con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//creamos un objeto de la clase reserva
$reserva = new Reserva($db);

//obtenemos el método
$method = $_SERVER['REQUEST_METHOD'];

//bloque para procesar la solicitud según el método HTTP
switch ($method) {
    //caso para obtener las reservas
    case 'GET':
        //primero verificamos si se solicitan reservas por asignatura , para mostrarselas a los alumnos en su página
        if (isset($_GET['asignatura'])) {
            //verificamos autenticacion y roles
            if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' && $_SESSION['rol'] != 'administrador')) {
                echo json_encode(array('message' => 'Acceso denegado'));
                exit;
            }
            $id_asignatura = $_GET['asignatura'];
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null; //obtenemos el valor del id del usuario para pasarselo al metodo
            $reservas = $reserva->obtenerPorAsignatura($id_asignatura, $id_usuario);
            //ejecutamos el método
            echo json_encode($reservas);
        } else {
            //sino se obtiene una reserva específica por su id
            if (isset($_GET['id'])) {
                $reserva->id_reserva = $_GET['id'];
                if ($reserva->leer()) {
                    $reserva_data = array(
                        "id_reserva" => $reserva->id_reserva,
                        "id_usuario" => $reserva->id_usuario,
                        "id_aula" => $reserva->id_aula,
                        "id_asignatura" => $reserva->id_asignatura,
                        "id_grupo" => $reserva->id_grupo,
                        "fecha" => $reserva->fecha,
                        "hora_inicio" => $reserva->hora_inicio,
                        "hora_fin" => $reserva->hora_fin,
                        "estado" => $reserva->estado
                    );
                    http_response_code(200);
                    echo json_encode($reserva_data);
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Reserva no encontrada"));
                }
            } else {
                //sino se obtienen todas las reservas si la solicitud tiene el parametro todas
                if (isset($_GET['todas']) && $_GET['todas'] == 1) {
                    $result = $reserva->leer_todos(null); // le pasamos null para obtener todas las reservas
                    if ($result) {
                        http_response_code(200);
                    } else {
                        http_response_code(500);
                    }
                } else {
                    //sino todas las de un usuario
                    //tenemos que obtener el id_usuario de la sesion
                    $id_usuario = $_SESSION['id_usuario'];
                    $result = $reserva->leer_todos($id_usuario);
                    if ($result) {
                        http_response_code(200);
                    } else {
                        http_response_code(500);
                    }

                }

                echo json_encode($result);
            }

        }
        break;


    //caso para crear una reserva
    case 'POST':
        //crear una nueva reserva
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_aula) && isset($data->id_asignatura) && isset($data->id_grupo) && isset($data->fecha) && isset($data->hora_inicio) && isset($data->hora_fin)) {
            try {
                $reserva->id_usuario = $_SESSION['id_usuario'];
                $reserva->id_aula = $data->id_aula;
                $reserva->id_asignatura = $data->id_asignatura;
                $reserva->id_grupo = $data->id_grupo;
                $reserva->fecha = $data->fecha;
                $reserva->hora_inicio = $data->hora_inicio;
                $reserva->hora_fin = $data->hora_fin;

                //verificamos que no haya solapamiento con otra reserva
                if (!$reserva->verificarSolapamiento()) {
                    echo json_encode(array('error' => 'Ya existe una reserva para este grupo en el horario seleccionado'));
                    exit;
                }
                //ejecutamos el metodo para crear la reserva
                $id_reserva = $reserva->crear();
                if ($id_reserva) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Reserva creada exitosamente"));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo crear la reserva"));
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    //caso para actualizar una reserva
    case 'PUT':

        $data = json_decode(file_get_contents("php://input")); //obtenemos los datos en JSON

        if (isset($data->id_reserva) && isset($data->id_aula) && isset($data->id_asignatura) && isset($data->id_grupo) && isset($data->fecha) && isset($data->hora_inicio) && isset($data->hora_fin)) {
            try {
                $reserva->id_reserva = $data->id_reserva;
                $reserva->id_usuario = $_SESSION['id_usuario'];
                $reserva->id_aula = $data->id_aula;
                $reserva->id_asignatura = $data->id_asignatura;
                $reserva->id_grupo = $data->id_grupo;
                $reserva->fecha = $data->fecha;
                $reserva->hora_inicio = $data->hora_inicio;
                $reserva->hora_fin = $data->hora_fin;

                //verificar solapamiento
                if (!$reserva->verificarSolapamiento()) {
                    echo json_encode(array('error' => 'Ya existe una reserva para este grupo en el horario seleccionado'));
                    exit;
                }
                //ejecutamos el metodo de actualizar
                if ($reserva->actualizar()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Reserva actualizada exitosamente"));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo actualizar la reserva"));
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error :" . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    //caso para eliminar una reserva
    case 'DELETE':

        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_reserva)) {
            $reserva->id_reserva = $data->id_reserva;
            if ($reserva->eliminar()) {
                http_response_code(200);
                echo json_encode(array("message" => "Reserva eliminada exitosamente"));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "No se pudo eliminar la reserva"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Metodo no permitido"));
        break;
}

?>