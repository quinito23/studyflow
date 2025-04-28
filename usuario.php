<?php

class Usuario
{
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

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function crearUsuario()
    {
        //antes de crear el usuario , vamos a verificar que el correo no exista ya en la base de datos
        $query = "SELECT correo FROM " . $this->table_name . " WHERE correo = :correo AND correo IS NOT NULL AND correo != ''";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();
        $correoExistente = $stmt->fetchColumn();
        if ($correoExistente !== false) {
            error_log("Correo duplicado");
            throw new Exception("El correo ya está registrado" . $correoExistente);
        }

        //insertamos al usuario en la tabla usuario
        $query = "INSERT INTO " . $this->table_name . " (nombre, apellidos, DNI, telefono, correo, contrasenia, fecha_nacimiento, rol) VALUES (:nombre, :apellidos, :DNI, :telefono, :correo, :contrasenia, :fecha_nacimiento, :rol)";
        $stmt = $this->conn->prepare($query);

        //limoieza de datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->DNI = htmlspecialchars(strip_tags($this->DNI));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->contrasenia = htmlspecialchars(strip_tags($this->contrasenia));
        $this->rol = htmlspecialchars(strip_tags($this->rol));
        $fecha_nacimiento = $this->fecha_nacimiento ?: null;

        //pasamos los valores a la consulta
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':DNI', $this->DNI);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':contrasenia', $this->contrasenia);
        $stmt->bindParam(':rol', $this->rol);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);


        if ($stmt->execute()) {
            $this->id_usuario = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function verificarLogin()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE correo = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->correo);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
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