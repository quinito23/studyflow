<?php
session_start();//inicio de la sesión

//incluimos las clases necesarias para la conexión a la base de datos y manejar las solicitudes
include_once '../DBConnection.php';
include_once '../clases/tarea.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar autenticación
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'alumno')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

// Conexión con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//creamos un objeto de la clase reserva
$tarea = new Tarea($db);

//obtenemos el método
$method = $_SERVER['REQUEST_METHOD'];

//bloque para procesar la solicitud según el método HTTP
switch ($method) {
    //caso para obtener las tareas
    case 'GET':
        if (isset($_GET['asignatura'])) {
            //filtradas por asignatura y opcionalmente también por usuario(para la pagina de los alumnos)
            if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' && $_SESSION['rol'] != 'administrador')) {
                echo json_encode(array('message' => 'Acceso denegado'));
                exit;
            }
            $id_asignatura = $_GET['asignatura'];
            //asignar id usuario y si no hay valor , null
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;
            $tareas = $tarea->obtenerPorAsignatura($id_asignatura, $id_usuario);
            http_response_code(200);
            echo json_encode($tareas);
        } else {
            //obtener una tarea específica si se pasa el id en la url
            if (isset($_GET['id'])) {
                $tarea->id_tarea = $_GET['id'];
                if ($tarea->leer()) {
                    $tarea_data = array(
                        "id_tarea" => $tarea->id_tarea,
                        "id_usuario" => $tarea->id_usuario,
                        "descripcion" => $tarea->descripcion,
                        "fecha_creacion" => $tarea->fecha_creacion,
                        "fecha_entrega" => $tarea->fecha_entrega,
                        "estado" => $tarea->estado, // Now safe to access
                        "id_asignatura" => $tarea->id_asignatura,
                        "id_grupo" => $tarea->id_grupo
                    );
                    http_response_code(200);
                    echo json_encode($tarea_data);
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Tarea no encontrada"));
                }
            } else {
                //si se pasa el parámetro todas en la solicitud entonces se obtienen  todas las tareas
                if (isset($_GET['todas']) && $_GET['todas'] == 1) {
                    $result = $tarea->leer_todos(null);

                } else {
                    $id_usuario = $_SESSION['id_usuario'];

                }
                echo json_encode($result);
            }
        }
        break;
    //caso para crear una tarea
    case 'POST':
        //obtener los datos en formato JSON
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->descripcion) && isset($data->fecha_entrega) && isset($data->id_asignatura) && isset($data->id_grupo)) {
            try {
                $tarea->id_usuario = $_SESSION['id_usuario'];
                $tarea->descripcion = $data->descripcion;
                $tarea->fecha_entrega = $data->fecha_entrega;
                $tarea->id_asignatura = $data->id_asignatura;
                $tarea->id_grupo = $data->id_grupo;
                //llamamos al metodo crear de la clase
                $id_tarea = $tarea->crear();
                if ($id_tarea) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Tarea creada exitosamente"));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo crear la tarea"));
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    //caso para actualizar una tarea
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_tarea) && isset($data->descripcion) && isset($data->fecha_entrega) && isset($data->id_asignatura) && isset($data->id_grupo)) {
            try {
                $tarea->id_tarea = $data->id_tarea;
                $tarea->descripcion = $data->descripcion;
                $tarea->fecha_entrega = $data->fecha_entrega;
                $tarea->id_asignatura = $data->id_asignatura;
                $tarea->id_grupo = $data->id_grupo;

                if ($tarea->actualizar()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Tarea actualizada exitosamente"));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo actualizar la tarea"));
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;
    //caso para eliminar una tarea
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_tarea)) {
            $tarea->id_tarea = $data->id_tarea;
            if ($tarea->eliminar()) {
                http_response_code(200);
                echo json_encode(array("message" => "Tarea eliminada exitosamente"));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "No se pudo eliminar la tarea"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>