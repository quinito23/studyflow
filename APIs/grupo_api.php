<?php
session_start(); //inicio de la sesión para acceder a los datos alamacenados del usuario que inicia sesion para verificar su rol

//incluimos las clases necesarias
include_once '../DBConnection.php';
include_once '../clases/grupo.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//establecemos la conexion con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase grupo, pasandole al constructor la conexión con la base de datos
$grupo = new Grupo($db);

// obtenemos el metodo HTTP de la solicitud que se realiza desde el frontend
$method = $_SERVER['REQUEST_METHOD'];

//bloque para procesar la solicitud según el método HTTP
switch ($method) {
    //caso para obtener grupos en diferentes situaciones
    case 'GET':
        //obtener un grupo específico, a través de su ID
        if (isset($_GET['id'])) {
            $grupo->id_grupo = $_GET['id'];
            if ($grupo->leer()) {
                //devolvemos un array con los datoss del grupo en JSON
                echo json_encode(array(
                    "id_grupo" => $grupo->id_grupo,
                    "nombre" => $grupo->nombre,
                    "capacidad_maxima" => $grupo->capacidad_maxima,
                    "id_asignatura" => $grupo->id_asignatura,
                    "nombre_asignatura" => $grupo->nombre_asignatura,
                    "numero_alumnos" => $grupo->numero_alumnos
                ));
            } else {
                echo json_encode(array("message" => "Grupo no encontrado"));
            }
        } else if (isset($_GET['id_asignatura'])) {
            // Filtrar grupos por asignatura (id_asignatura)
            $id_asignatura = $_GET['id_asignatura'];
            try {
                $query = "SELECT g.id_grupo, g.nombre, g.capacidad_maxima, g.id_asignatura, 
                          a.nombre AS nombre_asignatura, 
                          (SELECT COUNT(*) FROM alumno_grupo ag WHERE ag.id_grupo = g.id_grupo) AS numero_alumnos 
                          FROM grupo g 
                          JOIN asignatura a ON g.id_asignatura = a.id_asignatura 
                          WHERE g.id_asignatura = :id_asignatura";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
                $stmt->execute();
                $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($grupos);// devolvemos un array con los grupos filtrados por asignatura en JSON
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error al listar grupos: " . $e->getMessage()));
            }
        } else {
            //listar todos los grupos disponibles según la fecha y hora de reservas, para evitar solapamientos
            $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
            $hora_inicio = isset($_GET['hora_inicio']) ? $_GET['hora_inicio'] : null;
            $hora_fin = isset($_GET['hora_fin']) ? $_GET['hora_fin'] : null;
            $id_reserva = isset($_GET['id_reserva']) ? $_GET['id_reserva'] : null; // Obtener id_reserva

            try {
                if ($fecha && $hora_inicio && $hora_fin) {
                    //si existen los parametros, ejecutamos el método para obtener los grupos disponibles
                    $grupos = $grupo->leerDisponibles($fecha, $hora_inicio, $hora_fin, $id_reserva); // Pasar id_reserva
                } else {
                    //si no se pasan parametros ejecutamos el metodo para obtener todos los grupos en la base de datos
                    $grupos = $grupo->leer_todos();
                }
                echo json_encode($grupos);//devolvemos el array en JSON
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error al listar grupos: " . $e->getMessage()));
            }
        }
        break;

    //caso para manejar la creación de un nuevo grupo
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));//obtenemos los datos enviados en JSON
        if (!isset($data->nombre) || !isset($data->capacidad_maxima) || !isset($data->id_asignatura)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }
        $grupo->nombre = $data->nombre;
        $grupo->capacidad_maxima = $data->capacidad_maxima;
        $grupo->id_asignatura = $data->id_asignatura;
        try {
            //ejecutamos el metodo crear de la clase grupo 
            if ($grupo->crear()) {
                echo json_encode(array("message" => "Grupo creado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo crear el grupo"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al crear grupo: " . $e->getMessage()));
        }
        break;

    //caso para actualizar un grupo
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'));
        if (!isset($data->id_grupo) || !isset($data->nombre) || !isset($data->capacidad_maxima) || !isset($data->id_asignatura)) {
            echo json_encode(array('message' => 'Faltan datos requeridos'));
            exit;
        }
        $grupo->id_grupo = $data->id_grupo;
        $grupo->nombre = $data->nombre;
        $grupo->capacidad_maxima = $data->capacidad_maxima;
        $grupo->id_asignatura = $data->id_asignatura;
        try {
            if ($grupo->actualizar()) {
                echo json_encode(array("message" => "Grupo actualizado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo actualizar el grupo"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al actualizar grupo: " . $e->getMessage()));
        }
        break;

    //caso para eliminar un grupo
    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'));
        if (!isset($data->id_grupo)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        $grupo->id_grupo = $data->id_grupo;
        try {
            if ($grupo->eliminar()) {
                echo json_encode(array("message" => "Grupo eliminado exitosamente"));
            } else {
                echo json_encode(array("message" => "No se pudo eliminar el grupo"));
            }
        } catch (Exception $e) {
            echo json_encode(array("message" => "Error al eliminar el grupo: " . $e->getMessage()));
        }
        break;

    default:
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>