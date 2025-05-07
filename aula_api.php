<?php

session_start();

include_once 'DBConnection.php';
include_once 'reserva.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//verificar la autenticacion
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'administrador')) {
    echo json_encode(array("message" => "Acceso denegado. Debes ser un profesor"));
    exit;
}

//creamos la conexion con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//obtener los parametros de horario y grupo desde el frontend
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
$hora_inicio = isset($_GET['hora_inicio']) ? $_GET['hora_inicio'] : null;
$hora_fin = isset($_GET['hora_fin']) ? $_GET['hora_fin'] : null;
$id_grupo = isset($_GET['id_grupo']) ? $_GET['id_grupo'] : null;
$id_asignatura = isset($_GET['id_asignatura']) ? $_GET['id_asignatura'] : null;

$aulas = array();

if ($fecha && $hora_inicio && $hora_fin) {

    //validamos que la hora del final sea mayor a la hora de inicio
    if (strtotime($fecha . ' ' . $hora_fin) <= strtotime($fecha . ' ' . $hora_inicio)) {
        echo json_encode(array('message' => 'La hora de finalizacion debe ser posterior a la hora de inicio'));
        exit;
    }

    //consulta para obtener las aulas disponibles
    $currentTime = date('Y-m-d H:i:s');
    $query = "SELECT a.id_aula, a.nombre FROM aula a LEFT JOIN reserva r ON a.id_aula = r.id_aula AND r.fecha = :fecha AND (CONCAT(r.fecha, ' ', r.hora_fin) > :currentTime) AND NOT (r.hora_fin <= :hora_inicio OR :hora_fin <= r.hora_inicio) WHERE r.id_aula IS NULL";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora_inicio', $hora_inicio);
    $stmt->bindParam(':hora_fin', $hora_fin);
    $stmt->bindParam(':currentTime', $currentTime);
    $stmt->execute();
    $listaAulas = $stmt->fetchAll(PDO::FETCH_ASSOC); //array que contiene todas las aulas de la base de datos

    $aulas = array(); //array que contendra las aulas disponibles
    foreach ($listaAulas as $aula) {
        $aulas[] = array(
            "id_aula" => $aula['id_aula'],
            "nombre" => $aula["nombre"]
        );
    }
} else {
    //si no se proporcionan horarios , devolver todas las aulas
    $query = "SELECT id_aula, nombre FROM aula";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode($aulas);
?>