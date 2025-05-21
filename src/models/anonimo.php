<?php

class Anonimo
{
    private $conn; //conexión con la base de datos
    private $table_name = "anonimo"; //nombre de la tabla

    public $id_anonimo;
    public $correo;
    public $contrasenia;
    public $nombre;
    public $apellidos;
    public $telefono;
    public $DNI;
    public $fecha_nacimiento;

    public function __construct($db)
    { //constructor que recibe la conexión con la base de datos
        $this->conn = $db;
    }

    //metodo para crear un usuario anónimo en la base de datos
    public function crear()
    {
        // Definimos la consulta para ingresar al anonimo en la base de datos
        $query = "INSERT INTO " . $this->table_name . " (correo, contrasenia, nombre, apellidos, telefono, DNI, fecha_nacimiento) VALUES (:correo, :contrasenia, :nombre, :apellidos, :telefono, :DNI, :fecha_nacimiento)";
        $stmt = $this->conn->prepare($query);
        //limpieza de datos para evitar inyecciones
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->contrasenia = htmlspecialchars(strip_tags($this->contrasenia));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->DNI = htmlspecialchars(strip_tags($this->DNI));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));

        // Asociamos los valores a los parámetros de la consulta
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':contrasenia', $this->contrasenia);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':DNI', $this->DNI);
        $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);

        //ejecutamos la consulta
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        $errorInfo = $stmt->errorInfo();
        error_log("Error al crear el anónimo: " . $errorInfo[2]);
        throw new Exception("Error al crear el anónimo");
    }
}

?>