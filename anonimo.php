<?php

class Anonimo{
    private $conn;
    private $table_name = "anonimo";

    public $id_anonimo;
    public $correo;
    public $contrasenia;
    public $nombre;
    public $apellidos;
    public $telefono;

    public function __construct($db){
        $this->conn = $db;
    }

    //metodo para crear un nuevo anonimo

    public function crear(){
        // consulta para introducir los datos en la tabla
        $query = "INSERT INTO " . $this->table_name . " (correo, contrasenia, nombre, apellidos, telefono) VALUES (:correo, :contrasenia, :nombre, :apellidos, :telefono)";
        $stmt = $this->conn->prepare($query);
        
        //limpiamos los datos
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->contrasenia = htmlspecialchars(strip_tags($this->contrasenia));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        //pasamos los parametros a la consulta
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':contrasenia', $this->contrasenia);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':telefono', $this->telefono);

        //ejecutamos la consulta
        if($stmt->execute()){
            return $this->conn->lastInsertId();
        }
        return false;

    }
}

?>