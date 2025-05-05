<?php

session_start();

include_once 'DBConnection.php';
include_once 'reserva.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//verificar la autenticacion
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'profesor') {
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

$aulas = array();

if ($fecha && $hora_inicio && $hora_fin) {
    //creamos una instancia de la clase Reserva para poder usar sus metodos verificarDisponibilidad y vaerificarAsignacionGrupo
    $reserva = new Reserva($db);

    // recogemos todas las aulas
    $query = "SELECT id_aula, nombre FROM aula";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $listaAulas = $stmt->fetchAll(PDO::FETCH_ASSOC); //array que contiene todas las aulas de la base de datos

    $aulas = array(); //array que contendra las aulas disponibles
    foreach ($listaAulas as $aula) {
        $reserva->id_aula = $aula['id_aula'];
        $reserva->fecha = $fecha;
        $reserva->hora_inicio = $hora_inicio;
        $reserva->hora_fin = $hora_fin;
        $reserva->id_reserva = 0; // para evitar comparar con la propia reserva , y asi evitar un fallo grave

        $disponible = $reserva->verificarDisponibilidad();

        //verificamos si el grupo ya tiene un reserva hecha para ese horario
        if ($id_grupo && $disponible) {
            $reserva->id_grupo = $id_grupo;
            $disponible = $reserva->verificarAsignacionGrupo();
        }

        if ($disponible) {
            $aulas[] = array(
                "id_aula" => $aula['id_aula'],
                "nombre" => $aula["nombre"]
            );
        }

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