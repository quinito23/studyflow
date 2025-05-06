<?php

class Grupo
{
    private $conn;
    private $table_name = "grupo";

    public $id_grupo;
    public $nombre;
    public $capacidad_maxima;
    public $id_asignatura;
    public $nombre_asignatura;
    public $numero_alumnos;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //metodo para crear un nuevo grupo
    public function crear()
    {
        $query = "INSERT INTO " . $this->table_name . " (nombre, capacidad_maxima, id_asignatura) VALUES (:nombre, :capacidad_maxima, :id_asignatura)";
        $stmt = $this->conn->prepare($query);

        //hacemos la limpieza de datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->capacidad_maxima = (int) $this->capacidad_maxima;
        $this->id_asignatura = (int) $this->id_asignatura;

        //pasamos los paarametros a la consulta
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':capacidad_maxima', $this->capacidad_maxima);
        $stmt->bindParam(':id_asignatura', $this->id_asignatura);

        //ejecutamos la conssulta
        if ($stmt->execute()) {
            $this->id_grupo = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    //metodo para leer todos los grupos y mostrarlos en la tabla
    public function leer_todos()
    {
        $query = "SELECT g.id_grupo, g.nombre, g.capacidad_maxima, g.id_asignatura, a.nombre as nombre_asignatura, COUNT(ag.id_usuario) as numero_alumnos FROM " . $this->table_name . " g LEFT JOIN alumno_grupo ag ON g.id_grupo = ag.id_grupo LEFT JOIN asignatura a ON g.id_asignatura = a.id_asignatura GROUP BY g.id_grupo ORDER BY g.nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        //gestionamos los grupos
        $grupos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $grupos[] = array(
                "id_grupo" => $row['id_grupo'],
                "nombre" => $row['nombre'],
                "capacidad_maxima" => $row['capacidad_maxima'],
                "id_asignatura" => $row['id_asignatura'],
                "nombre_asignatura" => $row['nombre_asignatura'],
                "numero_alumnos" => $row['numero_alumnos']
            );
        }
        return $grupos;
    }

    //metodo para leer todos los grupos disponibles en una franja horaria , para evitar reservas solapadas
    public function leerDisponibles($fecha, $hora_inicio, $hora_fin)
    {
        $query = "SELECT g.id_grupo, g.nombre, g.capacidad_maxima, g.id_asignatura, a.nombre as nombre_asignatura, COUNT(ag.id_usuario) as numero_alumnos FROM " . $this->table_name . " g LEFT JOIN alumno_grupo ag ON g.id_grupo = ag.id_grupo LEFT JOIN asignatura a ON g.id_asignatura = a.id_asignatura LEFT JOIN reserva r ON r.id_grupo = g.id_grupo AND r.fecha = :fecha AND NOT (r.hora_fin <= :hora_inicio OR :hora_fin <= r.hora_inicio) AND r.estado IN ('activa', 'pendiente') WHERE r.id_reserva IS NULL GROUP BY g.id_grupo ORDER BY g.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->execute();

        $grupos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $grupos[] = array(
                "id_grupo" => $row['id_grupo'],
                "nombre" => $row['nombre'],
                "capacidad_maxima" => $row['capacidad_maxima'],
                "id_asignatura" => $row['id_asignatura'],
                "nombre_asignatura" => $row['nombre_asignatura'],
                "numero_alumnos" => $row['numero_alumnos']
            );
        }
        return $grupos;
    }

    //leer un grupo específico
    public function leer()
    {
        $query = "SELECT g.id_grupo, g.nombre, g.capacidad_maxima, g.id_asignatura, a.nombre as nombre_asignatura, COUNT(ag.id_usuario) as numero_alumnos FROM " . $this->table_name . " g LEFT JOIN alumno_grupo ag ON g.id_grupo = ag.id_grupo LEFT JOIN asignatura a ON g.id_asignatura = a.id_asignatura WHERE g.id_grupo = :id_grupo GROUP BY g.id_grupo LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        //limpieza de datos
        $this->id_grupo = htmlspecialchars(strip_tags($this->id_grupo));

        //le pasamos el parametro a la consulta
        $stmt->bindParam(":id_grupo", $this->id_grupo);
        //ejecutamos la consulta
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id_grupo = $row['id_grupo'];
            $this->nombre = $row['nombre'];
            $this->capacidad_maxima = $row['capacidad_maxima'];
            $this->id_asignatura = $row['id_asignatura'];
            $this->nombre_asignatura = $row['nombre_asignatura'];
            $this->numero_alumnos = $row['numero_alumnos'];
            return true;
        }
        return false;
    }

    //metodo para actualizar un grupo
    public function actualizar()
    {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, capacidad_maxima = :capacidad_maxima, id_asignatura = :id_asignatura WHERE id_grupo = :id_grupo";
        $stmt = $this->conn->prepare($query);

        //limpieza de parametros
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->capacidad_maxima = (int) $this->capacidad_maxima;
        $this->id_asignatura = (int) $this->id_asignatura;
        $this->id_grupo = htmlspecialchars(strip_tags($this->id_grupo));

        //pasamos los paarametros a la consulta
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':capacidad_maxima', $this->capacidad_maxima);
        $stmt->bindParam(':id_asignatura', $this->id_asignatura);
        $stmt->bindParam(':id_grupo', $this->id_grupo);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //metodo para eliminar un grupo
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_grupo = :id_grupo";
        $stmt = $this->conn->prepare($query);

        //limpieza de datos
        $this->id_grupo = htmlspecialchars(strip_tags($this->id_grupo));
        // le pasamos los parametros a la consulta
        $stmt->bindParam(':id_grupo', $this->id_grupo);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    //metodo para obtener los alumnos de un grupo
    public function obtenerAlumnos()
    {
        $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.correo FROM usuario u INNER JOIN alumno_grupo ag ON u.id_usuario = ag.id_usuario WHERE ag.id_grupo = :id_grupo AND u.rol = 'alumno'";
        $stmt = $this->conn->prepare($query);

        //limpieza de datos
        $this->id_grupo = htmlspecialchars(strip_tags($this->id_grupo));
        //le pasamos los datos a la consulta
        $stmt->bindParam(':id_grupo', $this->id_grupo);
        //ejecutamos la consulta
        $stmt->execute();

        $alumnos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $alumnos[] = array(
                "id_usuario" => $row['id_usuario'],
                "nombre" => $row['nombre'],
                "apellidos" => $row['apellidos'],
                "correo" => $row['correo']
            );
        }
        return $alumnos;
    }
}

?>