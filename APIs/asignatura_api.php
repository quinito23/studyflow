<?php

//iniciamos la sesión para acceder a los datos almacenados del usuario y verificar su rol
session_start();
//incluimos las clases necesarias
include_once '../DBConnection.php';
include_once '../clases/asignatura.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar acceso de administrador para operaciones de escritura
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}
// Obtenemos la conexión con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//creamos una instancia de la clase asignatura, pasandole al constructor la conexión a la base de datos
$asignatura = new Asignatura($db);

// Almacenamos el método HTTP de la petición
$method = $_SERVER['REQUEST_METHOD'];

//bloque para procesar la solicitud según el método HTTP
switch ($method) {
    case 'GET':
        //Listamos las asignaturas que no estén asignadas aún a ningún grupo
        if (isset($_GET['sin_grupo'])) {
            // Devolver asignaturas no asignadas a ningún grupo
            try {
                $query = "SELECT id_asignatura, nombre, descripcion, nivel, id_usuario 
                          FROM asignatura 
                          WHERE id_asignatura NOT IN (SELECT id_asignatura FROM grupo WHERE id_asignatura IS NOT NULL)
                          ORDER BY nombre ASC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $asignaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
                echo json_encode($asignaturas); //devolvemos la lista de asingaturas en formato JSON
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error al listar asignaturas sin grupo: " . $e->getMessage()));
            }
        } elseif (isset($_GET['no_asignadas'])) {
            // Devolver solo asignaturas no asignadas a profesores
            try {
                $query = "SELECT id_asignatura, nombre, descripcion, nivel, id_usuario 
                          FROM asignatura 
                          WHERE id_usuario IS NULL 
                          ORDER BY nombre ASC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $asignaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                //devolvemos la lista de asignaturas
                http_response_code(200);
                echo json_encode($asignaturas);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error al listar asignaturas no asignadas: " . $e->getMessage()));
            }
            //Para obtener una asignatura específica por su id , se usapara editar una asigntura o para ver su información
        } elseif (isset($_GET['id'])) {
            $asignatura->id_asignatura = $_GET['id'];
            if ($asignatura->leer()) {
                http_response_code(200);
                //devolvemos una lista con todos los datos de la asignatura
                echo json_encode(array(
                    "id_asignatura" => $asignatura->id_asignatura,
                    "nombre" => $asignatura->nombre,
                    "descripcion" => $asignatura->descripcion,
                    "nivel" => $asignatura->nivel,
                    "id_usuario" => $asignatura->id_usuario
                ));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Asignatura no encontrada"));
            }

        } elseif (isset($_GET['id_usuario'])) {
            //devolver las asignaturas asignadas a un profesor específico
            $id_usuario = $_GET['id_usuario'];
            try {
                $query = "SELECT a.id_asignatura, a.nombre, a.descripcion, a.nivel, a.id_usuario, u.nombre AS profesor 
                          FROM asignatura a 
                          LEFT JOIN usuario u ON a.id_usuario = u.id_usuario 
                          WHERE a.id_usuario = :id_usuario";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->execute();
                $asignaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
                echo json_encode($asignaturas);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error al listar asignaturas: " . $e->getMessage()));
            }
        } else {
            // obtener todas las asignaturas de la base de datos
            try {
                $asignaturas = $asignatura->leer_todos();
                http_response_code(200);
                echo json_encode($asignaturas);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error al listar las asignaturas: " . $e->getMessage()));
            }
        }
        break;

    //caso para manejar la creación de una asignatura    
    case 'POST':
        $data = json_decode(file_get_contents("php://input")); //obtenemos los datos enviados en formato JSON
        if (!isset($data->nombre) || !isset($data->descripcion) || !isset($data->nivel)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        try {
            //iniciamos una transacción
            $db->beginTransaction();

            //limpieza de datos para prevenir inyección SQL
            $asignatura->nombre = htmlspecialchars(strip_tags($data->nombre));
            $asignatura->descripcion = htmlspecialchars(strip_tags($data->descripcion));
            $asignatura->nivel = htmlspecialchars(strip_tags($data->nivel));
            // le asignamos el valor del id del profesor si existe
            $asignatura->id_usuario = isset($data->id_usuario) && $data->id_usuario != '' ? $data->id_usuario : null;

            //se ejecuta el metodo crear de la clase , para crear la asignatura en la base de datos
            if ($asignatura->crear()) {
                $db->commit();
                http_response_code(200);
                echo json_encode(array("message" => "Asignatura creada exitosamente", "id_asignatura" => $db->lastInsertId()));
            } else {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "No se pudo crear la asignatura"));
            }
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => "Error al crear la asignatura: " . $e->getMessage()));
        }
        break;
    //caso para actualizar una asignatura existente
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));//obtenemos los datos enviados en formato JSON
        if (!isset($data->id_asignatura) || !isset($data->nombre) || !isset($data->descripcion) || !isset($data->nivel)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        try {
            $db->beginTransaction();

            //limpieza de datos para evitar inyecciones
            $asignatura->id_asignatura = htmlspecialchars(strip_tags($data->id_asignatura));
            $asignatura->nombre = htmlspecialchars(strip_tags($data->nombre));
            $asignatura->descripcion = htmlspecialchars(strip_tags($data->descripcion));
            $asignatura->nivel = htmlspecialchars(strip_tags($data->nivel));
            $asignatura->id_usuario = isset($data->id_usuario) && $data->id_usuario != '' ? $data->id_usuario : null;
            //ejecutamos el metodo actualiza de la clase asignatura, para actualizar la asignatura con los nuevos datos
            if ($asignatura->actualizar()) {
                $db->commit();
                http_response_code(200);
                echo json_encode(array("message" => "Asignatura actualizada exitosamente"));
            } else {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "No se pudo actualizar la asignatura"));
            }
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => "Error al actualizar la asignatura: " . $e->getMessage()));
        }
        break;

    //caso para eliminar una asignatura de la base de datos
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input")); //obtenemos los datos enviados en formato JSON
        if (!isset($data->id_asignatura)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        try {
            $db->beginTransaction();

            $asignatura->id_asignatura = htmlspecialchars(strip_tags($data->id_asignatura));
            //ejecutamos el método para eliminar la asignatura
            if ($asignatura->eliminar()) {
                $db->commit();
                http_response_code(200);
                echo json_encode(array("message" => "Asignatura eliminada exitosamente"));
            } else {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "No se pudo eliminar la asignatura"));
            }
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => "Error al eliminar la asignatura: " . $e->getMessage()));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>