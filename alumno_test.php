<?php
include_once 'DBConnection.php';
include_once 'alumno.php';

header("Content-Type: application/json; charset=UTF-8");

// Activamos errores PDO
$database = new DBConnection();
$db = $database->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Creamos una instancia
$alumno = new Alumno($db);

// ID del alumno a actualizar (debe existir en la base de datos)
$alumno->id_usuario = 6; // Cambia este ID según un registro existente en tu tabla usuario

// Datos de prueba para la actualización
$alumno->correo = 'alumno.actualizado@example.com';
$alumno->contrasenia = 'newpassword123';
$alumno->nombre = 'Ana María';
$alumno->apellidos = 'Gómez Fernández';
$alumno->DNI = '87654321B';
$alumno->telefono = '987654321';
$alumno->fecha_nacimiento = '2000-02-15';
$alumno->rol = 'alumno';

// Intentamos actualizar
try {
    echo "Intentando actualizar alumno...\n";
    $result = $alumno->actualizar();
    if ($result) {
        echo json_encode(["message" => "Alumno con ID: {$alumno->id_usuario} actualizado exitosamente"]);
    } else {
        echo json_encode(["message" => "No se pudo actualizar el alumno"]);
    }
} catch (PDOException $e) {
    error_log("Error PDO: " . $e->getMessage() . " (Código: " . $e->getCode() . ")");
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage() . " (Código: " . $e->getCode() . ")"]);
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    echo json_encode(["error" => "Error general: " . $e->getMessage()]);
}
?>