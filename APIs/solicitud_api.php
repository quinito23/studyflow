<?php
//incluimos las clases necesarias para la conexión a la base de datos y manejar las solicitudes
include_once '../DBConnection.php';
include_once '../clases/anonimo.php';
include_once '../clases/solicitud.php';
include_once '../clases/usuario.php';
include_once '../clases/profesor.php';
include_once '../clases/alumno.php';
// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

//conexion con la base de datos

$database = new DBConnection();
$db = $database->getConnection();

//creamos objetos de las clases que vamos a manejar
$anonimo = new Anonimo($db);
$solicitud = new Solicitud($db);
$usuario = new Usuario($db);
$profesor = new Profesor($db);
$alumno = new Alumno($db);

// Almacenamos el método HTTP de la petición
$method = $_SERVER['REQUEST_METHOD'];

//bloque para procesar la solicitud según el método HTTP
switch ($method) {
    case 'GET':
        //Para obtener las asignaturas asociadas a una solicitud 
        if (isset($_GET['getAsignaturas'])) {
            $id_solicitud = intval($_GET['getAsignaturas']);
            $asignaturas = $solicitud->obtenerAsignaturas($id_solicitud);//se llama al método para obtener las asignaturas asociadas a la solicitud de la clase solicitud
            echo json_encode($asignaturas);// devolvemos las asignaturas en formato json
        } else {
            //Listamos todas las solicitudes con los datos del anonimo
            try {
                $query = "SELECT s.id_solicitud, s.id_anonimo, s.estado, s.fecha_realizacion, s.rol_propuesto, a.nombre, a.apellidos, a.correo, a.DNI FROM solicitud s JOIN anonimo a ON s.id_anonimo = a.id_anonimo";

                $stmt = $db->prepare($query);
                $stmt->execute();
                $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($solicitudes);// DEvuelve las solicitudes en JSON
            } catch (Exception $e) {
                error_log("Error al listar solicitudes: " . $e->getMessage());
                echo json_encode(array("message" => "Error al listar solicitudes: " . $e->getMessage()));
            }
        }
        break;
    //caso para aceptar o rechazar una solicitud
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->accion) || !isset($data->id_solicitud)) {
            echo json_encode(array("message" => "Faltan datos requeridos"));
            exit;
        }
        // almadenamos la acción seleccionada , que será aceptar o rechazar
        $accion = $data->accion;
        //almacenamos el id de la solicitud que manejamos
        $idSolicitud = $data->id_solicitud;

        try {
            $db->beginTransaction();

            //para la acción de aceptar la solicitud
            if ($accion === 'aceptar') {
                //obtener los datos de la solicitud y del anonimo que la ha hecho
                $query = "SELECT s.id_anonimo, s.rol_propuesto, a.correo, a.contrasenia, a.nombre, a.apellidos, a.telefono, a.DNI, a.fecha_nacimiento FROM solicitud s JOIN anonimo a ON s.id_anonimo = a.id_anonimo WHERE s.id_solicitud = :id_solicitud";
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
                $usuario->DNI = $solicitudData['DNI'];
                $usuario->telefono = $solicitudData['telefono'];
                $usuario->correo = $solicitudData['correo'];
                $usuario->contrasenia = $solicitudData['contrasenia'];
                $usuario->fecha_nacimiento = $solicitudData['fecha_nacimiento'];
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
                    $alumno->DNI = $solicitudData['DNI'];
                    $alumno->fecha_nacimiento = $solicitudData['fecha_nacimiento'];
                    $tutores = []; // No asignamos tutores inicialmente
                    $grupos = isset($data->grupos) ? $data->grupos : [];

                    if (!$alumno->crear($tutores, $grupos)) {
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
                    $profesor->DNI = $solicitudData['DNI'];
                    $profesor->fecha_nacimiento = $solicitudData['fecha_nacimiento'];
                    $profesor->sueldo = null; // Valores por defecto
                    $profesor->jornada = null;
                    $profesor->fecha_inicio_contrato = null;
                    $profesor->fecha_fin_contrato = null;
                    $asignaturas = $solicitud->obtenerAsignaturas($idSolicitud);
                    if (!$profesor->crear($asignaturas)) {
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
            echo json_encode(array("message" => "Error al procesar la solicitud: " . $e->getMessage()));
        }
        break;
    default:
        echo json_encode(array("message" => "Metodo no permitido"));
        break;
}



?>