<?php

include_once 'DBConnection.php';
include_once 'anonimo.php';
include_once 'solicitud.php';
include_once 'usuario.php';
include_once 'profesor.php';
include_once 'alumno.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

//conexion con la base de datos

$database = new DBConnection();
$db = $database->getConnection();

$anonimo = new Anonimo($db);
$solicitud = new Solicitud($db);
$usuario = new Usuario($db);
$profesor = new Profesor($db);
$alumno = new Alumno($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if(isset($_GET['getAsignaturas'])){
            $id_solicitud = intval($_GET['getAsignaturas']);
            $asignaturas = $solicitud->obtenerAsignaturas($id_solicitud);
            echo json_encode($asignaturas);
        }else{
            //Listamos todas las solicitudes con los datos del anonimo
            try {
                $query = "SELECT s.id_solicitud, s.id_anonimo, s.estado, s.fecha_realizacion, s.rol_propuesto, a.nombre, a.apellidos, a.correo FROM solicitud s JOIN anonimo a ON s.id_anonimo = a.id_anonimo";
    
                $stmt = $db->prepare($query);
                $stmt->execute();
                $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($solicitudes);
            } catch (Exception $e) {
                error_log("Error al listar solicitudes: " . $e->getMessage());
                echo json_encode(array("message" => "Error al listar solicitudes: " . $e->getMessage()));
            }
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->accion) || !isset($data->id_solicitud)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }

        $accion = $data->accion;
        $idSolicitud = $data->id_solicitud;

        try {
            $db->beginTransaction();

            if ($accion === 'aceptar') {
                //obtener los datos de la solicitud y del anonimo que la ha hecho
                $query = "SELECT s.id_anonimo, s.rol_propuesto, a.correo, a.contrasenia, a.nombre, a.apellidos, a.telefono FROM solicitud s JOIN anonimo a ON s.id_anonimo = a.id_anonimo WHERE s.id_solicitud = :id_solicitud";
                $stmt = $db->prepare($query);

                $stmt->bindParam(':id_solicitud', $idSolicitud);
                $stmt->execute();
                $solicitudData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$solicitudData) {
                    throw new Exception("Solicitud no encontrada");
                }

                //validamos que el correo no sea NULL ni vacio 
                if (empty($solicitudData['correo'])) {
                    throw new Exception("El correo de la solicitud no puede estar vacio");
                }

                //creamos el usuario usando el metodo crear de la clase usuario
                $usuario->nombre = $solicitudData['nombre'];
                $usuario->apellidos = $solicitudData['apellidos'];
                $usuario->DNI = null;
                $usuario->telefono = $solicitudData['telefono'];
                $usuario->correo = $solicitudData['correo'];
                $usuario->contrasenia = $solicitudData['contrasenia'];
                $usuario->fecha_nacimiento = null;
                $usuario->rol = $solicitudData['rol_propuesto'];


                // Ejecutar la inserción
                if (!$usuario->crearUsuario()) {
                    throw new Exception("No se pudo crear el usuario");
                }

                // Obtener el ID del usuario recién creado
                $id_usuario = $usuario->id_usuario;


                // y según el rol , lo agregamos a alumno o profesor

                if ($solicitudData['rol_propuesto'] === 'alumno') {
                    $alumno->id_usuario = $id_usuario;
                    $alumno->correo = $solicitudData['correo'];
                    $alumno->contrasenia = $solicitudData['contrasenia'];
                    $alumno->nombre = $solicitudData['nombre'];
                    $alumno->apellidos = $solicitudData['apellidos'];
                    $alumno->telefono = $solicitudData['telefono'];
                    $alumno->rol = 'alumno';
                    $alumno->DNI = null;
                    $alumno->fecha_nacimiento = null;
                    $tutores = []; // No asignamos tutores inicialmente

                    if(isset($data->grupos) && is_array($data->grupos) && !empty($data->grupos)){
                        foreach($data->grupos as $id_grupo){
                            $query = "INSERT INTO alumno_grupo(id_usuario, id_grupo) VALUES (:id_usuario, :id_grupo)";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                            $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
                            $stmt->execute();

                        }
                    }

                    if (!$alumno->crear($tutores)) {
                        throw new Exception("No se pudo crear el alumno");
                    }
                } elseif ($solicitudData['rol_propuesto'] === 'profesor') {
                    $profesor->id_usuario = $id_usuario;
                    $profesor->correo = $solicitudData['correo'];
                    $profesor->contrasenia = $solicitudData['contrasenia'];
                    $profesor->nombre = $solicitudData['nombre'];
                    $profesor->apellidos = $solicitudData['apellidos'];
                    $profesor->telefono = $solicitudData['telefono'];
                    $profesor->rol = 'profesor';
                    $profesor->DNI = null;
                    $profesor->fecha_nacimiento = null;
                    $profesor->sueldo = null; // Valores por defecto
                    $profesor->jornada = null;
                    $profesor->fecha_inicio_contrato = null;
                    $profesor->fecha_fin_contrato = null;
                    if (!$profesor->crear()) {
                        throw new Exception("No se pudo crear el profesor");
                    }
                }

                //actualizamos el estado de la solicitud a 'aceptado'
                $query = "UPDATE solicitud SET estado = 'aceptado' WHERE id_solicitud = :id_solicitud";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_solicitud', $idSolicitud);
                $stmt->execute();

                $db->commit();
                echo json_encode(array("message" => "Solicitud aceptada exitosamente"));


            } elseif ($accion === 'rechazar') {
                // actualizamos el estado de la solicitud a rechazado
                $query = "UPDATE solicitud SET estado = 'rechazado' WHERE id_solicitud = :id_solicitud";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_solicitud', $idSolicitud);
                $stmt->execute();

                $db->commit();
                echo json_encode(array("message" => "Solicitud rechazada exitosamente"));
            } else {
                echo json_encode(array("message" => "Acción no valida"));
            }
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error al procesar la solicitud: " . $e->getMessage());
            echo json_encode(array("message" => "Error al procesar la solicitud: " . $e->getMessage()));
        }
        break;
    default:
        echo json_encode(array("message" => "Metodo no permitido"));
        break;
}



?>