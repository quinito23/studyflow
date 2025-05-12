<?php
session_start();

include_once 'DBConnection.php';
include_once 'alumno.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Verificar acceso de administrador
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'alumno' && $_SESSION['rol'] !== 'administrador')) {
    echo json_encode(array("message" => "Acceso denegado"));
    exit;
}

$database = new DBConnection();
$db = $database->getConnection();

$alumno = new Alumno($db);

$method = $_SERVER['REQUEST_METHOD'];

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

    return $duplicados;
}

switch ($method) {
    case 'GET':
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
                echo json_encode(array("message" => "Error al verificar duplicados: " . $e->getMessage()));
            }
            exit;
        }
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

            $asignaturas = $alumno->obtenerAsignaturas($id_usuario);
            echo json_encode($asignaturas);
        } else {
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
                        "tutores" => $alumno->tutores
                    );
                    echo json_encode($alumno_data);
                } else {
                    echo json_encode(array("message" => "Alumno no encontrado"));
                }
            } else {
                $result = $alumno->leer_todos();
                echo json_encode($result);
            }
        }
        break;

    case 'POST':
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

                $id_alumno = $alumno->crear($tutores, $grupos);

                if ($id_alumno) {
                    $db->commit();
                    echo json_encode(array("message" => "Alumno creado exitosamente", "id_usuario" => $id_alumno));
                } else {
                    $db->rollBack();
                    echo json_encode(array("message" => "No se pudo crear el alumno"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(array("message" => "Error al crear el alumno: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

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

                if ($alumno->actualizar($tutores, $grupos)) {
                    $db->commit();
                    echo json_encode(array("message" => "Alumno actualizado exitosamente"));
                } else {
                    $db->rollBack();
                    echo json_encode(array("message" => "No se pudo actualizar el alumno"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(array("message" => "Error al actualizar el alumno: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id_usuario)) {
            try {
                $db->beginTransaction();

                $alumno->id_usuario = $data->id_usuario;

                if ($alumno->eliminar()) {
                    $db->commit();
                    echo json_encode(array("message" => "Alumno eliminado exitosamente"));
                } else {
                    $db->rollBack();
                    echo json_encode(array("message" => "No se pudo eliminar el alumno"));
                }
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(array("message" => "Error al eliminar el alumno: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("message" => "Faltan datos requeridos"));
        }
        break;

    default:
        echo json_encode(array("message" => "Método no permitido"));
        break;
}
?>