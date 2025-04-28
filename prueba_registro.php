<?php

include_once 'DBConnection.php';
include_once 'anonimo.php';
include_once 'solicitud.php';

header("Content-Type: application/json; charset=UTF-8");

// Activamos errores PDO
try {
    $database = new DBConnection();
    $db = $database->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    error_log("Error de conexión: " . $e->getMessage());
    echo json_encode(["error" => "Error de conexión con la base de datos: " . $e->getMessage()]);
    exit;
}

// Función auxiliar para realizar una prueba de registro
function testRegistro($db, $data, $testName) {
    echo "=== $testName ===\n";
    
    $anonimo = new Anonimo($db);
    $solicitud = new Solicitud($db);

    try {
        // Iniciamos una transacción
        $db->beginTransaction();

        // Creamos el anónimo
        $anonimo->correo = $data["correo"];
        $anonimo->contrasenia = $data["contrasenia"];
        $anonimo->nombre = $data["nombre"];
        $anonimo->apellidos = $data["apellidos"];
        $anonimo->telefono = $data["telefono"];

        $id_anonimo = $anonimo->crear();

        if (!$id_anonimo) {
            throw new Exception("No se pudo crear el anónimo");
        }

        // Creamos la solicitud asociada
        $solicitud->id_anonimo = $id_anonimo;
        $solicitud->estado = "pendiente";
        $solicitud->fecha_realizacion = date('Y-m-d'); // Fecha actual
        $solicitud->rol_propuesto = $data["rol_propuesto"];

        $id_solicitud = $solicitud->crear();

        if (!$id_solicitud) {
            throw new Exception("No se pudo crear la solicitud");
        }

        // Confirmamos la transacción
        $db->commit();

        echo json_encode([
            "message" => "Registro completado exitosamente",
            "id_anonimo" => $id_anonimo,
            "id_solicitud" => $id_solicitud
        ]) . "\n";

    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Error PDO ($testName): " . $e->getMessage());
        echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]) . "\n";
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error general ($testName): " . $e->getMessage());
        echo json_encode(["error" => "Error general: " . $e->getMessage()]) . "\n";
    }
    echo "\n";
}

// Prueba 1: Registro con datos válidos
$data_valid = [
    "correo" => "test" . time() . "@example.com", // Correo único con timestamp
    "contrasenia" => "password123",
    "nombre" => "Juan",
    "apellidos" => "Pérez",
    "telefono" => "1234567890",
    "rol_propuesto" => "alumno"
];
testRegistro($db, $data_valid, "Prueba 1: Registro con datos válidos");

?>