<?php
include_once 'usuario.php';

class Profesor extends Usuario
{
    private $conn;
    private $table_name = "profesor";

    public $sueldo;
    public $jornada;
    public $fecha_inicio_contrato;
    public $fecha_fin_contrato;
    public $asignaturas; // Contenedor para asignaturas, similar a tutores/grupos en Alumno

    public function __construct($db)
    {
        $this->conn = $db;
        parent::__construct($db);
    }

    public function crear($asignaturas = [])
    {
        // Si no hay id_usuario preexistente, crear el usuario base
        if (empty($this->id_usuario)) {
            if (!$this->crearUsuario()) {
                throw new Exception("No se pudo crear el usuario para el profesor");
            }
        }

        // Validar id_usuario
        if (empty($this->id_usuario)) {
            throw new Exception("Error en Profesor::crear: id_usuario está vacío");
        }

        // Verificar si el usuario ya es profesor
        $query = "SELECT id_usuario FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->execute();
        if ($stmt->fetch()) {
            throw new Exception("El usuario ya está registrado como profesor");
        }

        // Insertar en tabla profesor
        $query = "INSERT INTO " . $this->table_name . " (id_usuario, sueldo, jornada, fecha_inicio_contrato, fecha_fin_contrato) 
                  VALUES (:id_usuario, :sueldo, :jornada, :fecha_inicio_contrato, :fecha_fin_contrato)";
        $stmt = $this->conn->prepare($query);

        // Limpieza de datos
        $this->jornada = htmlspecialchars(strip_tags($this->jornada ?? ''));
        $sueldo = $this->sueldo ?: null;
        $fecha_inicio_contrato = $this->fecha_inicio_contrato ?: null;
        $fecha_fin_contrato = $this->fecha_fin_contrato ?: null;

        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':sueldo', $sueldo);
        $stmt->bindParam(':jornada', $this->jornada);
        $stmt->bindParam(':fecha_inicio_contrato', $fecha_inicio_contrato);
        $stmt->bindParam(':fecha_fin_contrato', $fecha_fin_contrato);

        if (!$stmt->execute()) {
            return false;
        }

        // Asignar asignaturas al profesor
        foreach ($asignaturas as $id_asignatura) {
            // Verificar si la asignatura ya está asignada o no existe
            $query = "SELECT id_asignatura FROM asignatura WHERE id_asignatura = :id_asignatura AND (id_usuario IS NULL OR id_usuario = 0)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
            $stmt->execute();
            if (!$stmt->fetch()) {
                error_log("No se puede asignar la asignatura $id_asignatura porque ya está asignada o no existe");
                continue;
            }

            // Asignar la asignatura
            $query = "UPDATE asignatura SET id_usuario = :id_usuario WHERE id_asignatura = :id_asignatura";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                return false;
            }
        }

        return $this->id_usuario;
    }

    public function leer_todos()
    {
        $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.DNI, u.telefono, u.correo, u.contrasenia, u.fecha_nacimiento, u.rol, 
                         p.sueldo, p.jornada, p.fecha_inicio_contrato, p.fecha_fin_contrato 
                  FROM usuario u 
                  LEFT JOIN " . $this->table_name . " p ON u.id_usuario = p.id_usuario 
                  WHERE u.rol = 'profesor' 
                  ORDER BY u.id_usuario DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $profesores = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $asignaturas = $this->obtenerAsignaturas($row['id_usuario']);
            $profesores[] = array(
                "id_usuario" => $row['id_usuario'],
                "nombre" => $row['nombre'],
                "apellidos" => $row['apellidos'],
                "DNI" => $row['DNI'],
                "telefono" => $row['telefono'],
                "correo" => $row['correo'],
                "contrasenia" => $row['contrasenia'],
                "fecha_nacimiento" => $row['fecha_nacimiento'],
                "rol" => $row['rol'],
                "sueldo" => $row['sueldo'],
                "jornada" => $row['jornada'],
                "fecha_inicio_contrato" => $row['fecha_inicio_contrato'],
                "fecha_fin_contrato" => $row['fecha_fin_contrato'],
                "asignaturas" => $asignaturas
            );
        }
        return $profesores;
    }

    public function leer()
    {
        $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.DNI, u.telefono, u.correo, u.contrasenia, u.fecha_nacimiento, u.rol, 
                         p.sueldo, p.jornada, p.fecha_inicio_contrato, p.fecha_fin_contrato 
                  FROM usuario u 
                  LEFT JOIN " . $this->table_name . " p ON u.id_usuario = p.id_usuario 
                  WHERE u.id_usuario = :id_usuario AND u.rol = 'profesor' 
                  LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_usuario = $row['id_usuario'];
            $this->nombre = $row['nombre'];
            $this->apellidos = $row['apellidos'];
            $this->DNI = $row['DNI'];
            $this->telefono = $row['telefono'];
            $this->correo = $row['correo'];
            $this->contrasenia = $row['contrasenia'];
            $this->fecha_nacimiento = $row['fecha_nacimiento'];
            $this->rol = $row['rol'];
            $this->sueldo = $row['sueldo'];
            $this->jornada = $row['jornada'];
            $this->fecha_inicio_contrato = $row['fecha_inicio_contrato'];
            $this->fecha_fin_contrato = $row['fecha_fin_contrato'];
            $this->asignaturas = $this->obtenerAsignaturas($this->id_usuario);
            return true;
        }
        return false;
    }

    public function actualizar($asignaturas = [])
    {
        $query = "UPDATE usuario SET nombre = :nombre, apellidos = :apellidos, DNI = :DNI, telefono = :telefono, correo = :correo, 
                         contrasenia = :contrasenia, fecha_nacimiento = :fecha_nacimiento, rol = :rol 
                  WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->DNI = htmlspecialchars(strip_tags($this->DNI));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->contrasenia = htmlspecialchars(strip_tags($this->contrasenia));
        $this->rol = htmlspecialchars(strip_tags($this->rol));
        $fecha_nacimiento = $this->fecha_nacimiento ?: null;

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':DNI', $this->DNI);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':contrasenia', $this->contrasenia);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':rol', $this->rol);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        if ($stmt->execute()) {
            $query = "UPDATE " . $this->table_name . " SET sueldo = :sueldo, jornada = :jornada, 
                            fecha_inicio_contrato = :fecha_inicio_contrato, fecha_fin_contrato = :fecha_fin_contrato 
                      WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($query);

            $this->jornada = htmlspecialchars(strip_tags($this->jornada ?? ''));
            $sueldo = $this->sueldo ?: null;
            $fecha_inicio_contrato = $this->fecha_inicio_contrato ?: null;
            $fecha_fin_contrato = $this->fecha_fin_contrato ?: null;

            $stmt->bindParam(':sueldo', $sueldo);
            $stmt->bindParam(':jornada', $this->jornada);
            $stmt->bindParam(':fecha_inicio_contrato', $fecha_inicio_contrato);
            $stmt->bindParam(':fecha_fin_contrato', $fecha_fin_contrato);
            $stmt->bindParam(':id_usuario', $this->id_usuario);

            if ($stmt->execute()) {
                // Eliminar asignaturas existentes
                $query = "UPDATE asignatura SET id_usuario = NULL WHERE id_usuario = :id_usuario";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_usuario', $this->id_usuario);
                $stmt->execute();

                // Asignar nuevas asignaturas
                foreach ($asignaturas as $id_asignatura) {
                    $query = "SELECT id_asignatura FROM asignatura WHERE id_asignatura = :id_asignatura AND (id_usuario IS NULL OR id_usuario = 0)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
                    $stmt->execute();
                    if (!$stmt->fetch()) {
                        error_log("No se puede asignar la asignatura $id_asignatura porque ya está asignada o no existe");
                        continue;
                    }

                    $query = "UPDATE asignatura SET id_usuario = :id_usuario WHERE id_asignatura = :id_asignatura";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
                    $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
                    $stmt->execute();
                }
                return true;
            }
        }
        return false;
    }

    public function eliminar()
    {
        $query = "DELETE FROM usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        return $stmt->execute();
    }

    private function obtenerAsignaturas($id_usuario)
    {
        $query = "SELECT id_asignatura, nombre, descripcion, nivel 
                  FROM asignatura 
                  WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $asignaturas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $asignaturas[] = array(
                "id_asignatura" => $row['id_asignatura'],
                "nombre" => $row['nombre'],
                "descripcion" => $row['descripcion'],
                "nivel" => $row['nivel']
            );
        }
        return $asignaturas;
    }
}
?>