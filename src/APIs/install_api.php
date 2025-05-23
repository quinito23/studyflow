<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

include_once '../../db/DBConnection.php';
include_once '../models/Install.php';

// Prevent access if already installed
if (file_exists('../../db/DBConnection.php')) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "La aplicación ya está instalada"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->host) || !isset($data->dbname) || !isset($data->username)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos"]);
    exit;
}

$install = new Install();
if (!$install->testConnection($data->host, $data->dbname, $data->username, $data->password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No se pudo conectar a la base de datos: " . $install->getError()]);
    exit;
}

if (!$install->executeSqlFile('../../sql/studyflow.sql')) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al ejecutar studyflow.sql: " . $install->getError()]);
    exit;
}

if (!$install->generateConfig($data->host, $data->dbname, $data->username, $data->password)) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "No se pudo generar DBConnection.php"]);
    exit;
}

// Eliminar archivos de instalación (opcional)
@unlink('../views/install.php');
@unlink('install_api.php');
@unlink('../models/Install.php');
@unlink('../../sql/studyflow.sql');

http_response_code(200);
echo json_encode(["success" => true, "message" => "Instalación completada con éxito"]);