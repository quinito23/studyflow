<?php

class Aula
{
    private $conn; //conexión a la base de datos
    private $table_name = "aula"; //nombre de la tabla en la base de datos
    public $id_aula;
    public $nombre;
    public $capacidad;
    public $equipamiento;

    public function __construct($db) //constructor de la clase que recibe la conexión a la base de datos
    {
        $this->conn = $db;
    }

    //Metodo para crear el aula
    public function crear()
    {
        $query = "INSERT INTO " . $this->table_name . " (nombre, capacidad, equipamiento) VALUES (:nombre, :capacidad, :equipamiento)";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars((strip_tags($this->nombre)));
        $this->capacidad = htmlspecialchars((strip_tags($this->capacidad)));
        $this->equipamiento = htmlspecialchars((strip_tags($this->equipamiento)));

        //pasamos los datos a la consulta
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':capacidad', $this->capacidad, PDO::PARAM_INT);
        $stmt->bindParam(':equipamiento', $this->equipamiento);

        if ($stmt->execute()) {
            $this->id_aula = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    //metodo para obtener todas las aulas
    public function leer_todos()
    {
        $query = "SELECT id_aula, nombre, capacidad, equipamiento FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $aulas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $aulas[] = array(
                "id_aula" => $row['id_aula'],
                "nombre" => $row['nombre'],
                "capacidad" => $row['capacidad'],
                "equipamiento" => $row['equipamiento']
            );
        }
        return $aulas;
    }

    //metodo para obtener un aula específica
    public function leer()
    {
        $query = "SELECT id_aula, nombre, capacidad, equipamiento FROM " . $this->table_name . " WHERE id_aula = :id_aula LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        //limpiamos parametro
        $this->id_aula = htmlspecialchars(strip_tags($this->id_aula));
        //pasamos el parametro a la consulta
        $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id_aula = $row['id_aula'];
            $this->nombre = $row['nombre'];
            $this->capacidad = $row['capacidad'];
            $this->equipamiento = $row['equipamiento'];
            return true;
        }
        return false;

    }

    //metodo para actualizar un aula
    public function actualizar()
    {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, capacidad = :capacidad, equipamiento = :equipamiento WHERE id_aula = :id_aula";
        $stmt = $this->conn->prepare($query);

        //hacemos limpieza de datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->capacidad = htmlspecialchars(strip_tags($this->capacidad));
        $this->equipamiento = htmlspecialchars(strip_tags($this->equipamiento));
        $this->id_aula = htmlspecialchars(strip_tags($this->id_aula));

        //pasamos los datos a la consulta
        $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':capacidad', $this->capacidad, PDO::PARAM_INT);
        $stmt->bindParam(':equipamiento', $this->equipamiento);

        return $stmt->execute();

    }

    //metodo para actualiza un aura
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_aula = :id_aula";
        $stmt = $this->conn->prepare($query);

        $this->id_aula = htmlspecialchars(strip_tags($this->id_aula));

        $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>