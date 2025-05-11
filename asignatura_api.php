<?php
session_start();

include_once 'DBConnection.php';
include_once 'asignatura.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar acceso de administrador para operaciones de escritura
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador')) {
    http_response_code(403);
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

$database = new DBConnection();
$db = $database->getConnection();

$asignatura = new Asignatura($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
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
                echo json_encode($asignaturas);
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
                http_response_code(200);
                echo json_encode($asignaturas);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error al listar asignaturas no asignadas: " . $e->getMessage()));
            }
        } elseif (isset($_GET['id'])) {
            $asignatura->id_asignatura = $_GET['id'];
            if ($asignatura->leer()) {
                http_response_code(200);
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

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->nombre) || !isset($data->descripcion) || !isset($data->nivel)) {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        try {
            $db->beginTransaction();

            $asignatura->nombre = htmlspecialchars(strip_tags($data->nombre));
            $asignatura->descripcion = htmlspecialchars(strip_tags($data->descripcion));
            $asignatura->nivel = htmlspecialchars(strip_tags($data->nivel));
            $asignatura->id_usuario = isset($data->id_usuario) && $data->id_usuario != '' ? $data->id_usuario : null;

            if ($asignatura->crear()) {
                $db->commit();
                http_response_code(201);
                echo json_encode(array("message" => "Asignatura creada exitosamente", "id_asignatura" => $db->lastInsertId()));
            } else {
                $db->rollBack();
                http_response_code(400);
                echo json_encode(array("message" => "No se pudo crear la asignatura"));
            }
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => "Error al crear la asignatura: " . $e->getMessage()));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id_asignatura) || !isset($data->nombre) || !isset($data->descripcion) || !isset($data->nivel)) {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        try {
            $db->beginTransaction();

            $asignatura->id_asignatura = htmlspecialchars(strip_tags($data->id_asignatura));
            $asignatura->nombre = htmlspecialchars(strip_tags($data->nombre));
            $asignatura->descripcion = htmlspecialchars(strip_tags($data->descripcion));
            $asignatura->nivel = htmlspecialchars(strip_tags($data->nivel));
            $asignatura->id_usuario = isset($data->id_usuario) && $data->id_usuario != '' ? $data->id_usuario : null;

            if ($asignatura->actualizar()) {
                $db->commit();
                http_response_code(200);
                echo json_encode(array("message" => "Asignatura actualizada exitosamente"));
            } else {
                $db->rollBack();
                http_response_code(400);
                echo json_encode(array("message" => "No se pudo actualizar la asignatura"));
            }
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(array("message" => "Error al actualizar la asignatura: " . $e->getMessage()));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id_asignatura)) {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        try {
            $db->beginTransaction();

            $asignatura->id_asignatura = htmlspecialchars(strip_tags($data->id_asignatura));

            if ($asignatura->eliminar()) {
                $db->commit();
                http_response_code(200);
                echo json_encode(array("message" => "Asignatura eliminada exitosamente"));
            } else {
                $db->rollBack();
                http_response_code(400);
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