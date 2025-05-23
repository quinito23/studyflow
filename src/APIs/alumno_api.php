<?php
session_start();//iniciamos la sesión para acceder a los datos almacenados del usuario y verificar su rol

//incluimos las clases necesarias
include_once '../../db/DBConnection.php';
include_once '../models/alumno.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar acceso de administrador y alumno
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'alumno' && $_SESSION['rol'] !== 'administrador')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

// Obtenemos la conexión con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//Creamos un objeto de la clase alumno
$alumno = new Alumno($db);

// Almacenamos el método HTTP de la petición
$method = $_SERVER['REQUEST_METHOD'];

//función que verifica si existen valores duplicados para los campos correo, contraseña y DNI a la hora de crear un nuevo alumno
function verificarDuplicados($db, $correo, $contrasenia, $DNI, $id_usuario = null)
{
    $duplicados = [];

    // Verificar correo
    if ($correo) {
        $query = "SELECT COUNT(*) AS count FROM usuario WHERE correo = :correo";
        if ($id_usuario) {
            $query .= " AND id_usuario != :id_usuario";
        }
        $query .= " UNION ALL SELECT COUNT(*) AS count FROM anonimo WHERE correo = :correo";
        if($id_usuario){
            $query .= " AND (id_usuario IS NULL OR id_usuario != :id_usuario)";
        }
        $stmt = $db->prepare($query);
        $stmt->bindParam(':correo', $correo);
        if ($id_usuario) {
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        }
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (array_sum(array_column($resultados, 'count')) > 0) {
            $duplicados[] = "correo";
        }
    }

    // Verificar contraseña
    if ($contrasenia) {
        $query = "SELECT COUNT(*) AS count FROM usuario WHERE contrasenia = :contrasenia";
        if ($id_usuario) {
            $query .= " AND id_usuario != :id_usuario";
        }
        $query .= " UNION ALL SELECT COUNT(*) AS count FROM anonimo WHERE contrasenia = :contrasenia";
        if($id_usuario){
            $query .= " AND (id_usuario IS NULL OR id_usuario != :id_usuario)";
        }
        $stmt = $db->prepare($query);
        $stmt->bindParam(':contrasenia', $contrasenia);
        if ($id_usuario) {
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        }
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (array_sum(array_column($resultados, 'count')) > 0) {
            $duplicados[] = "contrasenia";
        }
    }

    // Verificar DNI
    if ($DNI) {
        $query = "SELECT COUNT(*) AS count FROM usuario WHERE DNI = :DNI";
        if ($id_usuario) {
            $query .= " AND id_usuario != :id_usuario";
        }
        $query .= " UNION ALL SELECT COUNT(*) AS count FROM anonimo WHERE DNI = :DNI";
        if($id_usuario){
            $query .= " AND (id_usuario IS NULL OR id_usuario != :id_usuario)";
        }
        $stmt = $db->prepare($query);
        $stmt->bindParam(':DNI', $DNI);
        if ($id_usuario) {
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        }
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (array_sum(array_column($resultados, 'count')) > 0) {
            $duplicados[] = "DNI";
        }
    }
    //devolver el array de duplicados
    return $duplicados;
}

//bloque para procesar la solicitud según el método HTTP
switch ($method) {

    //Caso para obtener los alumnos
    case 'GET':
        // Primero verifica si hay duplicados
        if (isset($_GET['check_duplicados'])) {
            try {
                $correo = isset($_GET['correo']) ? trim($_GET['correo']) : null;
                $contrasenia = isset($_GET['contrasenia']) ? trim($_GET['contrasenia']) : null;
                $DNI = isset($_GET['DNI']) ? trim($_GET['DNI']) : null;
                $id_usuario = isset($_GET['id_usuario']) ? trim($_GET['id_usuario']) : null;

                $duplicados = verificarDuplicados($db, $correo, $contrasenia, $DNI, $id_usuario);

                
                    
                echo json_encode(array("message" => "Datos duplicados encontrados", "duplicados" => $duplicados));
                
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error al verificar duplicados: " . $e->getMessage()));
            }
            exit;
        }

        //obtiene las asignaturas asociadas a un alumno
        if (isset($_GET['asignaturas']) && $_GET['asignaturas'] == 1) {
            if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] != 'alumno' && $_SESSION['rol'] != 'administrador')) {
                echo json_encode(array("message" => "Acceso denegado"));
                exit;
            }
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;

            if (!$id_usuario || $id_usuario != $_SESSION['id_usuario']) {
                echo json_encode(array("message" => "ID de usuario no válido"));
                exit;
            }
            //llama al metodo de la clase
            $asignaturas = $alumno->obtenerAsignaturas($id_usuario);
            http_response_code(200);
            echo json_encode($asignaturas);
        } else {
            // obtenemos un alumno específico
            if (isset($_GET['id'])) {
                $alumno->id_usuario = $_GET['id'];
                if ($alumno->leer()) {
                    $alumno_data = array(
                        "id_usuario" => $alumno->id_usuario,
                        "correo" => $alumno->correo,
                        "contrasenia" => $alumno->contrasenia,
                        "nombre" => $alumno->nombre,
                        "apellidos" => $alumno->apellidos,
                        "DNI" => $alumno->DNI,
                        "telefono" => $alumno->telefono,
                        "fecha_nacimiento" => $alumno->fecha_nacimiento,
                        "rol" => $alumno->rol,
                        "tutores" => $alumno->tutores,
                        "grupos" => $alumno->grupos
                    );
                    http_response_code(200);
                    echo json_encode($alumno_data);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Alumno no encontrado"));
                }
            } else {
                // si no esta asignado el parametro id_usuario , se obtienen todos los alumnos llamando al metodo leer todos
                $result = $alumno->leer_todos();
                http_response_code(200);
                echo json_encode($result);
            }
        }
        break;

    //caso para crear un alumno 
    case 'POST':
        //obtenemos los datos en JSON
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) && isset($data->apellidos)) {
            try {
                $db->beginTransaction();


                // Verificar duplicados
                $duplicados = verificarDuplicados($db, $data->correo, $data->contrasenia, $data->DNI, null);
                if (!empty($duplicados)) {
                    echo json_encode(array("message" => "Datos duplicados", "duplicados" => $duplicados));
                    exit;
                }

                $alumno->correo = $data->correo;
                $alumno->contrasenia = $data->contrasenia;
                $alumno->nombre = $data->nombre;
                $alumno->apellidos = $data->apellidos;
                $alumno->DNI = $data->DNI;
                $alumno->telefono = $data->telefono;
                $alumno->fecha_nacimiento = $data->fecha_nacimiento;
                $alumno->rol = $data->rol ?? 'alumno';
                $tutores = isset($data->tutores) ? $data->tutores : [];
                $grupos = isset($data->grupos) ? $data->grupos : [];
                //creamos el alumno llamando al metodo de la clase
                $id_alumno = $alumno->crear($tutores, $grupos);


                if ($id_alumno) {
                    $db->commit();
                    http_response_code(200);
                    echo json_encode(array("message" => "Alumno creado exitosamente", "id_usuario" => $id_alumno));
                } else {
                    $db->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo crear el alumno"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "Error al crear el alumno: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    //caso para actualizar un profesor
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_usuario) && isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) && isset($data->apellidos)) {
            try {
                $db->beginTransaction();

                // Verificar duplicados
                $duplicados = verificarDuplicados($db, $data->correo, $data->contrasenia, $data->DNI, $data->id_usuario);
                if (!empty($duplicados)) {
                    echo json_encode(array("message" => "Datos duplicados", "duplicados" => $duplicados));
                    exit;
                }

                $alumno->id_usuario = $data->id_usuario;
                $alumno->correo = $data->correo;
                $alumno->contrasenia = $data->contrasenia;
                $alumno->nombre = $data->nombre;
                $alumno->apellidos = $data->apellidos;
                $alumno->DNI = $data->DNI;
                $alumno->telefono = $data->telefono;
                $alumno->fecha_nacimiento = $data->fecha_nacimiento;
                $alumno->rol = $data->rol ?? 'alumno';
                $tutores = isset($data->tutores) ? $data->tutores : [];
                $grupos = isset($data->grupos) ? $data->grupos : [];

                //ejecutamos el metodo actualizar
                if ($alumno->actualizar($tutores, $grupos)) {
                    $db->commit();
                    http_response_code(200);
                    echo json_encode(array("message" => "Alumno actualizado exitosamente"));
                } else {
                    $db->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo actualizar el alumno"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "Error al actualizar el alumno: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    //caso para eliminar un alumno
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_usuario)) {
            try {
                $db->beginTransaction();

                $alumno->id_usuario = $data->id_usuario;

                if ($alumno->eliminar()) {
                    $db->commit();
                    http_response_code(200);
                    echo json_encode(array("message" => "Alumno eliminado exitosamente"));
                } else {
                    $db->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo eliminar el alumno"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "Error al eliminar el alumno: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>