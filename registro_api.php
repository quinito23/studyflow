<?php

include_once 'DBConnection.php';
include_once 'anonimo.php';
include_once 'solicitud.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Desactivar la visualización de errores en pantalla
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Obtenemos la conexión con la base de datos
try {
    $database = new DBConnection();
    $db = $database->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(array("message" => "Error de conexión con la base de datos"));
    exit;
}

// Creamos instancias de las clases Anonimo y Solicitud
$anonimo = new Anonimo($db);
$solicitud = new Solicitud($db);

// Almacenamos el método HTTP de la petición
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        // Verificamos que todos los datos requeridos estén presentes
        if (isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) && 
            isset($data->apellidos) && isset($data->telefono) && isset($data->rol_propuesto)) {

            try {
                // Iniciamos una transacción
                $db->beginTransaction();

                // Creamos el anónimo
                $anonimo->correo = $data->correo;
                $anonimo->contrasenia = $data->contrasenia;
                $anonimo->nombre = $data->nombre;
                $anonimo->apellidos = $data->apellidos;
                $anonimo->telefono = $data->telefono;

                $id_anonimo = $anonimo->crear();
                error_log("ID Anónimo creado: " . $id_anonimo);

                if (!$id_anonimo) {
                    throw new Exception("No se pudo crear el anónimo");
                }

                // Creamos la solicitud asociada
                $solicitud->id_anonimo = $id_anonimo;
                $solicitud->estado = "pendiente";
                $solicitud->fecha_realizacion = date('Y-m-d'); // Fecha actual
                $solicitud->rol_propuesto = $data->rol_propuesto;

                $id_solicitud = $solicitud->crear();
                error_log("ID Solicitud creada: " . ($id_solicitud ? $id_solicitud : "Fallo al crear solicitud"));

                if (!$id_solicitud) {
                    throw new Exception("No se pudo crear la solicitud");
                }

                //Asociamos las asignaturas a la solicitud
                if(isset($data->asignaturas) && is_array($data->asignaturas) && !empty($data->asignaturas)){
                    $query = "INSERT INTO solicitud_asignatura (id_solicitud, id_asignatura) VALUES (:id_solicitud, :id_asignatura)";
                    $stmt = $db->prepare($query);

                    foreach($data->asignaturas as $id_asignatura){
                        $stmt->bindParam(':id_solicitud', $id_solicitud, PDO::PARAM_INT);
                        $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
                        if(!$stmt->execute()){
                            throw new Exception("Error al asociar asignatura a la solicitud");
                        }
                    }
                }

                // Confirmamos la transacción
                $db->commit();

                echo json_encode(array(
                    "message" => "Registro completado exitosamente",
                    "id_anonimo" => $id_anonimo,
                    "id_solicitud" => $id_solicitud
                ));

            } catch (Exception $e) {
                // Revertimos la transacción en caso de error
                $db->rollBack();
                error_log("Error durante el registro: " . $e->getMessage());
                echo json_encode(array("message" => "Error al completar el registro: " . $e->getMessage()));
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