<?php
//incluimos las clases necesarias
include_once '../DBConnection.php';
include_once '../clases/anonimo.php';
include_once '../clases/solicitud.php';

// Configuración de cabeceras para la API REST
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Desactivar la visualización de errores en pantalla
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Obtenemos la conexión con la base de datos

$database = new DBConnection();
$db = $database->getConnection();



// Creamos instancias de las clases Anonimo y Solicitud
$anonimo = new Anonimo($db);
$solicitud = new Solicitud($db);

// Almacenamos el método HTTP de la petición
$method = $_SERVER['REQUEST_METHOD'];
//bloque para procesar la solicitud según el método HTTP
switch ($method) {
    case 'GET':
        // creamos el caso GET que usamos para verificar que al hacer un registro no hayan campos clave duplicdos
        if (isset($_GET['correo']) || isset($_GET['contrasenia']) || isset($_GET['DNI'])) {
            try {
                $correo = isset($_GET['correo']) ? trim($_GET['correo']) : null;
                $contrasenia = isset($_GET['contrasenia']) ? trim($_GET['contrasenia']) : null;
                $DNI = isset($_GET['DNI']) ? trim($_GET['DNI']) : null;

                $duplicados = [];

                //verificar si el correo esta duplicado
                if ($correo) {
                    $query = "SELECT COUNT(*) as count FROM usuario WHERE correo = :correo UNION ALL SELECT COUNT(*) as count FROM anonimo WHERE correo = :correo";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':correo', $correo);
                    $stmt->execute();

                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $total = array_sum(array_column($resultados, 'count'));
                    if ($total > 0) {
                        $duplicados[] = "correo";
                    }
                }

                //verificar si la contraseña ya existe
                if ($contrasenia) {
                    $query = "SELECT COUNT(*) as count FROM usuario WHERE contrasenia = :contrasenia UNION ALL SELECT COUNT(*) as count FROM anonimo WHERE contrasenia = :contrasenia";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':contrasenia', $contrasenia);
                    $stmt->execute();

                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $total = array_sum(array_column($resultados, 'count'));
                    if ($total > 0) {
                        $duplicados[] = "contrasenia";
                    }
                }

                //verificar si el DNI ya existe
                if ($DNI) {
                    $query = "SELECT COUNT(*) as count FROM usuario WHERE DNI = :DNI UNION ALL SELECT COUNT(*) as count FROM anonimo WHERE DNI = :DNI";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':DNI', $DNI);
                    $stmt->execute();

                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $total = array_sum(array_column($resultados, 'count'));
                    if ($total > 0) {
                        $duplicados[] = "DNI";
                    }
                }

                if (!empty($duplicados)) {
                    echo json_encode(array("message" => "Datos duplicados encontrados", "duplicados" => $duplicados));
                } else {
                    echo json_encode(array("message" => "No se encontraron duplicados"));
                }
            } catch (Exception $e) {
                echo json_encode(array("message" => "Error al verificar duplicados"));
            }
        } else {
            echo json_encode(array("message" => "Parámetros requeridos faltantes"));
        }
        break;




    case 'POST':
        //manejamos la creación de un nuevo usuario anónimo y se crea una solicitud
        $data = json_decode(file_get_contents("php://input"));

        // Verificamos que todos los datos requeridos estén presentes
        if (
            isset($data->correo) && isset($data->contrasenia) && isset($data->nombre) &&
            isset($data->apellidos) && isset($data->telefono) && isset($data->rol_propuesto)
        ) {

            try {

                $duplicados = [];

                //verificar si el correo esta duplicado

                $query = "SELECT COUNT(*) as count FROM usuario WHERE correo = :correo UNION ALL SELECT COUNT(*) as count FROM anonimo WHERE correo = :correo";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':correo', $data->correo);
                $stmt->execute();

                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $total = array_sum(array_column($resultados, 'count'));
                if ($total > 0) {
                    $duplicados[] = "correo";
                }


                //verificar si la contraseña ya existe

                $query = "SELECT COUNT(*) as count FROM usuario WHERE contrasenia = :contrasenia UNION ALL SELECT COUNT(*) as count FROM anonimo WHERE contrasenia = :contrasenia";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':contrasenia', $data->contrasenia);
                $stmt->execute();

                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $total = array_sum(array_column($resultados, 'count'));

                if ($total > 0) {
                    $duplicados[] = "contrasenia";
                }


                //verificar si el DNI ya existe
                if (!empty($data->DNI)) {
                    $query = "SELECT COUNT(*) as count FROM usuario WHERE DNI = :DNI UNION ALL SELECT COUNT(*) as count FROM anonimo WHERE DNI = :DNI";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':DNI', $data->DNI);
                    $stmt->execute();

                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $total = array_sum(array_column($resultados, 'count'));
                    if ($total > 0) {
                        $duplicados[] = "DNI";
                    }
                }

                if (!empty($duplicados)) {
                    echo json_encode(array("message" => "Datos duplicados encontrados", "duplicados" => $duplicados));
                    exit;
                }


                // Iniciamos una transacción
                $db->beginTransaction();

                // Creamos el anónimo
                $anonimo->correo = $data->correo;
                $anonimo->contrasenia = $data->contrasenia;
                $anonimo->nombre = $data->nombre;
                $anonimo->apellidos = $data->apellidos;
                $anonimo->telefono = $data->telefono;
                $anonimo->DNI = $data->DNI;
                $anonimo->fecha_nacimiento = $data->fecha_nacimiento;

                //ejecutamos el metodo de la clase para crear el anonimo en  la base de datos
                $id_anonimo = $anonimo->crear();
                error_log("ID Anónimo creado: " . $id_anonimo);

                if (!$id_anonimo) {
                    throw new Exception("No se pudo crear el anónimo");
                }

                // Creamos la solicitud asociada
                $solicitud->id_anonimo = $id_anonimo;
                $solicitud->estado = "pendiente";
                $solicitud->fecha_realizacion = date('Y-m-d'); // Fecha actual
                $solicitud->rol_propuesto = $data->rol_propuesto;

                //ejecutamos el metodo de la clase crear 
                $id_solicitud = $solicitud->crear();
                error_log("ID Solicitud creada: " . ($id_solicitud ? $id_solicitud : "Fallo al crear solicitud"));

                if (!$id_solicitud) {
                    throw new Exception("No se pudo crear la solicitud");
                }

                //Asociamos las asignaturas seleccionadas a la solicitud creada
                if (isset($data->asignaturas) && is_array($data->asignaturas) && !empty($data->asignaturas)) {
                    $query = "INSERT INTO solicitud_asignatura (id_solicitud, id_asignatura) VALUES (:id_solicitud, :id_asignatura)";
                    $stmt = $db->prepare($query);

                    foreach ($data->asignaturas as $id_asignatura) {
                        $stmt->bindParam(':id_solicitud', $id_solicitud, PDO::PARAM_INT);
                        $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
                        if (!$stmt->execute()) {
                            throw new Exception("Error al asociar asignatura a la solicitud");
                        }
                    }
                }

                // Confirmamos la transacción
                $db->commit();

                echo json_encode(array(
                    "message" => "Registro completado exitosamente",
                    "id_anonimo" => $id_anonimo,
                    "id_solicitud" => $id_solicitud
                ));

            } catch (Exception $e) {
                // Revertimos la transacción en caso de error
                $db->rollBack();
                error_log("Error durante el registro: " . $e->getMessage());
                echo json_encode(array("message" => "Error al completar el registro: " . $e->getMessage()));
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