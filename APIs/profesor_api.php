<?php
session_start();//iniciamos la sesión para acceder a los datos almacenados del usuario y verificar su rol

//incluimos las clases necesarias
include_once '../DBConnection.php';
include_once '../clases/profesor.php';
include_once '../clases/usuario.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar acceso de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

// Obtenemos la conexión con la base de datos
$database = new DBConnection();
$db = $database->getConnection();

//Creamos un objeto de la clase profesor
$profesor = new Profesor($db);

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
    //Caso para obtener los profesores de la base de datos
    case 'GET':
        // Primero verifica si hay duplicados
        if (isset($_GET['check_duplicados'])) {
            try {
                $correo = isset($_GET['correo']) ? trim($_GET['correo']) : null;
                $contrasenia = isset($_GET['contrasenia']) ? trim($_GET['contrasenia']) : null;
                $DNI = isset($_GET['DNI']) ? trim($_GET['DNI']) : null;
                $id_usuario = isset($_GET['id_usuario']) ? trim($_GET['id_usuario']) : null;

                $duplicados = verificarDuplicados($db, $correo, $contrasenia, $DNI, $id_usuario);

                if (!empty($duplicados)) {
                    echo json_encode(array("message" => "Datos duplicados encontrados", "duplicados" => $duplicados));
                } else {
                    echo json_encode(array("message" => "No se encontraron duplicados"));
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array("message" => "Error al verificar duplicados: " . $e->getMessage()));
            }
            exit;
        }
        // obtenemos un alumno específico
        if (isset($_GET['id'])) {
            $profesor->id_usuario = $_GET['id'];
            if ($profesor->leer()) {
                $profesor_data = array(
                    "id_usuario" => $profesor->id_usuario,
                    "correo" => $profesor->correo,
                    "contrasenia" => $profesor->contrasenia,
                    "nombre" => $profesor->nombre,
                    "apellidos" => $profesor->apellidos,
                    "DNI" => $profesor->DNI,
                    "telefono" => $profesor->telefono,
                    "fecha_nacimiento" => $profesor->fecha_nacimiento,
                    "rol" => $profesor->rol,
                    "sueldo" => $profesor->sueldo,
                    "jornada" => $profesor->jornada,
                    "fecha_inicio_contrato" => $profesor->fecha_inicio_contrato,
                    "fecha_fin_contrato" => $profesor->fecha_fin_contrato,
                    "asignaturas" => $profesor->asignaturas
                );
                http_response_code(200);
                echo json_encode($profesor_data);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Profesor no encontrado"));
            }
        } else {
            // si no esta asignado el parametro id_usuario , se obtienen todos los alumnos llamando al metodo leer todos
            $result = $profesor->leer_todos();
            http_response_code(200);
            echo json_encode($result);
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

                $profesor->correo = $data->correo;
                $profesor->contrasenia = $data->contrasenia;
                $profesor->nombre = $data->nombre;
                $profesor->apellidos = $data->apellidos;
                $profesor->DNI = $data->DNI;
                $profesor->telefono = $data->telefono;
                $profesor->fecha_nacimiento = $data->fecha_nacimiento;
                $profesor->rol = $data->rol ?? 'profesor';
                $profesor->sueldo = $data->sueldo ?? null;
                $profesor->jornada = $data->jornada ?? null;
                $profesor->fecha_inicio_contrato = $data->fecha_inicio_contrato ?? null;
                $profesor->fecha_fin_contrato = $data->fecha_fin_contrato ?? null;
                $asignaturas = isset($data->asignaturas) ? $data->asignaturas : [];

                //creamos el alumno llamando al metodo de la clase
                $id_profesor = $profesor->crear($asignaturas);

                if ($id_profesor) {
                    $db->commit();
                    http_response_code(200);
                    echo json_encode(array("message" => "Profesor creado exitosamente", "id_usuario" => $id_profesor));
                } else {
                    $db->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo crear el profesor"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "Error al crear el profesor: " . $e->getMessage()));
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

                $profesor->id_usuario = $data->id_usuario;
                $profesor->correo = $data->correo;
                $profesor->contrasenia = $data->contrasenia;
                $profesor->nombre = $data->nombre;
                $profesor->apellidos = $data->apellidos;
                $profesor->DNI = $data->DNI;
                $profesor->telefono = $data->telefono;
                $profesor->fecha_nacimiento = $data->fecha_nacimiento;
                $profesor->rol = $data->rol ?? 'profesor';
                $profesor->sueldo = $data->sueldo ?? null;
                $profesor->jornada = $data->jornada ?? null;
                $profesor->fecha_inicio_contrato = $data->fecha_inicio_contrato ?? null;
                $profesor->fecha_fin_contrato = $data->fecha_fin_contrato ?? null;
                $asignaturas = isset($data->asignaturas) ? $data->asignaturas : [];

                //ejecutamos el metodo actualizar
                if ($profesor->actualizar($asignaturas)) {
                    $db->commit();
                    http_response_code(200);
                    echo json_encode(array("message" => "Profesor actualizado exitosamente"));
                } else {
                    $db->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo actualizar el profesor"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "Error al actualizar el profesor: " . $e->getMessage()));
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

                $profesor->id_usuario = $data->id_usuario;

                if ($profesor->eliminar()) {
                    $db->commit();
                    http_response_code(200);
                    echo json_encode(array("message" => "Profesor eliminado exitosamente"));
                } else {
                    $db->rollBack();
                    http_response_code(500);
                    echo json_encode(array("message" => "No se pudo eliminar el profesor"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(array("message" => "Error al eliminar el profesor: " . $e->getMessage()));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    default:
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>