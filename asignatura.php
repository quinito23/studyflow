<?php

class Asignatura
{
    private $conn;
    private $table_name = "asignatura";

    public $id_asignatura;
    public $nombre;
    public $descripcion;
    public $nivel;

    public $id_usuario;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //metodo para crear una nueva asignatura

    public function crear()
    {
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion, nivel, id_usuario) VALUES (:nombre, :descripcion, :nivel, :id_usuario)";
        $stmt = $this->conn->prepare($query);

        //limpiamos los datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->nivel = htmlspecialchars(strip_tags($this->nivel));
        $this->id_usuario = $this->id_usuario ? htmlspecialchars(strip_tags($this->id_usuario)) : null;

        //pasamos los datos a la consulta
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':nivel', $this->nivel);
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);

        //ejecutamos la consulta

        if ($stmt->execute()) {
            $this->id_asignatura = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    //metodo para leer todos las asignaturas
    public function leer_todos()
    {
        $query = "SELECT id_asignatura, nombre, descripcion, nivel FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        //ejecutamos la consulta
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

    //leer una asignatura especifica
    public function leer()
    {
        $query = "SELECT id_asignatura, nombre, descripcion, nivel FROM " . $this->table_name . " WHERE id_asignatura = :id_asignatura LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        //limpiamos los datos
        $this->id_asignatura = htmlspecialchars(strip_tags($this->id_asignatura));

        //pasamos los datos a la consulta
        $stmt->bindParam(':id_asignatura', $this->id_asignatura);
        //ejecutamos la consulta
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id_asignatura = $row['id_asignatura'];
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->nivel = $row['nivel'];
            return true;
        }
        return false;

    }

    //metodo para actualizar una asignatura
    public function actualizar()
    {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, descripcion = :descripcion, nivel = :nivel WHERE id_asignatura = :id_asignatura";
        $stmt = $this->conn->prepare($query);

        //hacemos la limpieza de parametros
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->nivel = htmlspecialchars(strip_tags($this->nivel));
        $this->id_asignatura = htmlspecialchars(strip_tags($this->id_asignatura));

        //pasamos los parametros a la consulta
        $stmt->bindParam(':id_asignatura', $this->id_asignatura);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':nivel', $this->nivel);

        //ejecutamos la consulta
        return $stmt->execute();
    }

    //metodo para eliminar una asignatura
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_asignatura = :id_asignatura";
        $stmt = $this->conn->prepare($query);

        //hacemos la limpieza de datos
        $this->id_asignatura = htmlspecialchars(strip_tags($this->id_asignatura));

        //pasamos los datos a la consulta
        $stmt->bindParam(':id_asignatura', $this->id_asignatura);

        //ejecutamos la consulta
        return $stmt->execute();
    }

    //metodo para obtener los grupos asociados a una asignatura 
    public function obtenerGrupos()
    {
        $query = "SELECT g.id_grupo, g.nombre, g.capacidad_maxima, g.id_asignatura, a.nombre_asignatura as nombre_asignatura, COUNT(ag.id_usuario) as numero_alumnos FROM grupo g LEFT JOIN alumno_grupo ag ON g.id_grupo = ag.id_grupo LEFT JOIN asignatura a ON g.id_asignatura = a.id_asignatura WHERE g.id_asignatura = :id_asignatura GROUP BY g.id_grupo";
        $stmt = $this->conn->prepare($query);

        //limpiamos los datos
        $this->id_asignatura = htmlspecialchars(strip_tags($this->id_asignatura));

        //pasamos los datos a la consulta
        $stmt->bindParam(':id_asignatura', $this->id_asignatura);

        //ejecutamos la consulta
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
}

?>