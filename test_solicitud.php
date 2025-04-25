<?php
include_once 'DBConnection.php';
include_once 'anonimo.php';
include_once 'solicitud.php';

header("Content-Type: application/json; charset=UTF-8");

// Activamos errores PDO
$database = new DBConnection();
$db = $database->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Paso 1: Crear un anónimo para obtener un id_anonimo válido
try {
    $anonimo = new Anonimo($db);
    $anonimo->correo = 'test_solicitud@test.com';
    $anonimo->contrasenia = 'secreta123';
    $anonimo->nombre = 'Usuario';
    $anonimo->apellidos = 'Solicitud';
    $anonimo->telefono = '987654321';

    $id_anonimo = $anonimo->crear();
    if ($id_anonimo) {
        echo json_encode(["message" => "Anónimo creado con ID: $id_anonimo"]);
    } else {
        echo json_encode(["message" => "No se pudo crear el Anónimo"]);
        exit;
    }
} catch (PDOException $e) {
    error_log("Error PDO (Anónimo): " . $e->getMessage());
    echo json_encode(["error" => "Error en la base de datos al crear el anónimo: " . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    error_log("Error general (Anónimo): " . $e->getMessage());
    echo json_encode(["error" => "Error general al crear el anónimo: " . $e->getMessage()]);
    exit;
}

// Paso 2: Crear una solicitud usando el id_anonimo
try {
    $solicitud = new Solicitud($db);
    $solicitud->id_anonimo = $id_anonimo;
    $solicitud->estado = 'pendiente';
    $solicitud->fecha_realizacion = date('Y-m-d'); // Fecha actual (YYYY-MM-DD)
    $solicitud->rol_propuesto = 'alumno';

    $id_solicitud = $solicitud->crear();
    if ($id_solicitud) {
        echo json_encode(["message" => "Solicitud creada con ID: $id_solicitud"]);
    } else {
        echo json_encode(["message" => "No se pudo crear la Solicitud"]);
    }
} catch (PDOException $e) {
    error_log("Error PDO (Solicitud): " . $e->getMessage());
    echo json_encode(["error" => "Error en la base de datos al crear la solicitud: " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error general (Solicitud): " . $e->getMessage());
    echo json_encode(["error" => "Error general al crear la solicitud: " . $e->getMessage()]);
}

?>