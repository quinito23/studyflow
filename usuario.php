<?php

class Usuario{
    private $conn;
    private $table_name = "usuario";

    public $id_usuario;
    public $nombre;
    public $apellidos;
    public $DNI;
    public $telefono;
    public $correo;
    public $contrasenia;
    public $fecha_nacimiento;
    public $rol;

    public function __construct($db){
        $this->conn = $db;
    }


    public function verificarLogin(){
        $query = "SELECT * FROM " . $this->table_name . " WHERE correo = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->correo);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row){
            $this->id_usuario = $row['id_usuario'];
            $this->nombre = $row['nombre'];
            $this->apellidos = $row['apellidos'];
            $this->telefono = $row['telefono'];
            $this->correo = $row['correo'];
            $this->contrasenia = $row['contrasenia'];
            $this->fecha_nacimiento = $row['fecha_nacimiento'];
            $this->rol = $row['rol'];
            return true;

        }
        return false;

    }
}


?>