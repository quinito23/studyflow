<?php
include_once 'DBConnection.php';
include_once 'anonimo.php';

header("Content-Type: application/json; charset=UTF-8");

// Activamos errores PDO
$database = new DBConnection();
$db = $database->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Creamos una instancia de Anonimo
$anonimo = new Anonimo($db);

// Datos de prueba (ajústalos si quieres)
$anonimo->correo = 'anonimo@test.com';
$anonimo->contrasenia = 'secreta123';
$anonimo->nombre = 'Usuario';
$anonimo->apellidos = 'Anónimo';
$anonimo->telefono = '123456789';

// Intentamos insertar
try {
    $id = $anonimo->crear();
    if ($id) {
        echo json_encode(["message" => "Anonimo creado con ID: $id"]);
    } else {
        echo json_encode(["message" => "No se pudo crear el Anonimo"]);
    }
} catch (PDOException $e) {
    error_log("Error PDO: " . $e->getMessage());
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    echo json_encode(["error" => "Error general: " . $e->getMessage()]);
}
?>