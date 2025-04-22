<?php
include_once 'DBConnection.php';
include_once 'profesor.php';

header("Content-Type: application/json; charset=UTF-8");

// Activamos errores PDO
$database = new DBConnection();
$db = $database->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Creamos una instancia
$profesor = new Profesor($db);

// Datos de prueba
$profesor->correo = 'test@example.com';
$profesor->contrasenia = '123456';
$profesor->nombre = 'Test';
$profesor->apellidos = 'User';
$profesor->DNI = '12345678Z';
$profesor->telefono = '123456789';
$profesor->fecha_nacimiento = '1990-01-01';
$profesor->rol = 'profesor';
$profesor->sueldo = '2000';
$profesor->jornada = 'completa';
$profesor->fecha_inicio_contrato = '2024-09-01';
$profesor->fecha_fin_contrato = '2025-09-01';



// Intentamos insertar
try {
    echo "Intentando crear profesor...\n";
    $id = $profesor->crear();
    if ($id) {
        echo json_encode(["message" => "Profesor creado con ID: $id"]);
    } else {
        echo json_encode(["message" => "No se pudo crear el profesor"]);
    }
} catch (PDOException $e) {
    error_log("Error PDO: " . $e->getMessage() . " (Código: " . $e->getCode() . ")");
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage() . " (Código: " . $e->getCode() . ")"]);
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    echo json_encode(["error" => "Error general: " . $e->getMessage()]);
}
?>