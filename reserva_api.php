<?php
session_start();

include_once 'DBConnection.php';
include_once 'reserva.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//verificar autenticacion
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'administrador')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

//conexion con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

$reserva = new Reserva($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        //primero verificamos si se solicitan reservas por asignatura , para mostrarselas a los alumnos
        if (isset($_GET['asignatura'])) {
            //verificamos autenticacion y roles
            if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' && $_SESSION['rol'] != 'administrador')) {
                echo json_encode(array('message' => 'Acceso denegado'));
                exit;
            }
            $id_asignatura = $_GET['asiognatura'];
            $reservas = $reserva->obtenerPorAsignatura($id_asignatura);
            echo json_encode($reservas);
        } else {
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
                    echo json_encode($reserva_data);
                } else {
                    echo json_encode(array("message" => "Reserva no encontrada"));
                }
            } else {
                if (isset($_GET['todas']) && $_GET['todas'] == 1) {
                    $result = $reserva->leer_todos(null); // le pasamos null para obtener todas las reservas
                } else {
                    //tenemos que obtener el id_usuario de la sesion
                    $id_usuario = $_SESSION['id_usuario'];
                    $result = $reserva->leer_todos($id_usuario);
                }

                echo json_encode($result);
            }

        }
        break;



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

                $id_reserva = $reserva->crear();
                if ($id_reserva) {
                    echo json_encode(array("message" => "Reserva creada exitosamente"));
                } else {
                    echo json_encode(array("message" => "No se pudo crear la reserva"));
                }
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'PUT':
        //actualizar una reserva
        $data = json_decode(file_get_contents("php://input"));

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

                if ($reserva->actualizar()) {
                    echo json_encode(array("message" => "Reserva actualizada exitosamente"));
                } else {
                    echo json_encode(array("message" => "No se pudo actualizar la reserva"));
                }
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error :" . $e->getMessage()));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'DELETE':
        //eliminar una reserva
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_reserva)) {
            $reserva->id_reserva = $data->id_reserva;
            if ($reserva->eliminar()) {
                echo json_encode(array("message" => "Reserva eliminada exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo eliminar la reserva"));
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