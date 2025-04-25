<?php

class Solicitud{
    private $conn;
    private $table_name = "solicitud";

    public $id_solicitud;
    public $id_anonimo;
    public $estado;
    public $fecha_realizacion;
    public $rol_propuesto;

    public function __construct($db){
        $this->conn = $db;
    }

    public function crear(){
        // creamos la consulta
        $query = "INSERT INTO " . $this->table_name . " (id_anonimo, estado, fecha_realizacion, rol_propuesto) VALUES (:id_anonimo, :estado, :fecha_realizacion, :rol_propuesto)";
        $stmt = $this->conn->prepare($query);

        //limpiamos los datos
        $this->id_anonimo = htmlspecialchars(strip_tags($this->id_anonimo));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->fecha_realizacion = htmlspecialchars(strip_tags($this->fecha_realizacion));
        $this->rol_propuesto = htmlspecialchars(strip_tags($this->rol_propuesto));

        //pasamos los parametros a la consulta
        $stmt->bindParam(':id_anonimo', $this->id_anonimo);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':fecha_realizacion', $this->fecha_realizacion);
        $stmt->bindParam(':rol_propuesto', $this->rol_propuesto);

        //ejecutamos la consulta
        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;

    }
}


?>