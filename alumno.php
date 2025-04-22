<?php

include_once 'usuario.php';

    class Alumno extends Usuario{
        private $conn;
        private $table_name = "alumno";
        
        public function __construct($db){
            $this->conn = $db;
            // llamamos al consructor de la clase padre (usuario)

            parent::__construct($db);
        }

        // metodo para crear un nuevo profesor

        public function crear(){

            //insertamos primero los datos en la tabla usuario
            $query = "INSERT INTO usuario (nombre, apellidos, telefono, correo, contrasenia, fecha_nacimiento, rol, DNI) VALUE (:nombre, :apellidos, :telefono, :correo, :contrasenia, :fecha_nacimiento, :rol, :DNI)";
            $stmt = $this->conn->prepare($query);

            //hacemos la limpieza de datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
            $this->telefono = htmlspecialchars(strip_tags($this->telefono));
            $this->correo = htmlspecialchars(strip_tags($this->correo));
            $this->contrasenia = htmlspecialchars(strip_tags($this->contrasenia));
            $this->rol = htmlspecialchars(strip_tags($this->rol));
            $this->DNI = htmlspecialchars(strip_tags($this->DNI));
            $fecha_nacimiento = $this->fecha_nacimiento ?: null;

            //le pasamos los parametros a la consulta
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':contrasenia' ,$this->contrasenia);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':rol', $this->rol);
            $stmt->bindParam(':DNI', $this->DNI);

            if($stmt->execute()){
                // aqui deberiamos hacer algo para poder elegir el aula y eso.
                return true;
            }
            return false;



        }

        public function leer_todos(){
            //aqui deberiamos hacer un join con otra tabla prbabkemente , pero de momento cogeremos solo los datos del usuario
            $query = "SELECT id_usuario, nombre, apellidos, DNI, telefono, correo, contrasenia, fecha_nacimiento, rol FROM usuario WHERE rol = 'alumno' ORDER BY id_usuario DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $alumnos = array();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $alumno = array(
                    "id_usuario" => $row['id_usuario'],
                    "nombre" => $row['nombre'],
                    "apellidos" => $row['apellidos'],
                    "DNI" => $row['DNI'],
                    "telefono" => $row['telefono'],
                    "correo" => $row['correo'],
                    "contrasenia" => $row['contrasenia']
                );
                array_push($alumnos, $alumno);
            }
            return $alumnos;

        }

        //leer un alumno

        public function leer(){

            $query = "SELECT id_usuario, nombre, apellidos, DNI, telefono, correo, contrasenia, fecha_nacimiento, rol FROM usuario WHERE id_usuario = :id_usuario AND rol = 'alumno' LIMIT 0,1";
            $stmt = $this->conn->prepare($query);

            //limpieza de datos
            $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
            //pasamos los datos a la consulta
            $stmt->bindParam(':id_usuario', $this->id_usuario);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row){
                $this->id_usuario = $row['id_usuario'];
                $this->nombre = $row['nombre'];
                $this->apellidos = $row['apellidos'];
                $this->DNI = $row['DNI'];
                $this->telefono = $row['telefono'];
                $this->correo = $row['correo'];
                $this->contrasenia = $row['contrasenia'];
                $this->fecha_nacimiento = $row['fecha_nacimiento'];
                $this->rol = $row['rol'];
                return true;
            }
            return false;
        }

        //funcion para actualizar un alumno

        public function actualizar(){

            //primero actualizamos los datos de la tabla usuario
            $query = "UPDATE usuario SET nombre = :nombre, apellidos = :apellidos, DNI = :DNI, telefono = :telefono, correo = :correo, contrasenia = :contrasenia, fecha_nacimiento = :fecha_nacimiento, rol = :rol WHERE id_usuario = :id_usuario";

            $stmt = $this->conn->prepare($query);

            //hacemos limpieza de datos
            
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
            $this->DNI = htmlspecialchars(strip_tags($this->DNI));
            $this->telefono = htmlspecialchars(strip_tags($this->telefono));
            $this->correo = htmlspecialchars(strip_tags($this->correo));
            $this->contrasenia = htmlspecialchars(strip_tags($this->contrasenia));
            $this->rol = htmlspecialchars(strip_tags($this->rol));
            $fecha_nacimiento = $this->fecha_nacimiento ?: null;
            $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

            // le pasamos los datos a la consulta
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':DNI', $this->DNI);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':contrasenia' ,$this->contrasenia);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':rol', $this->rol);
            $stmt->bindParam('id_usuario', $this->id_usuario);
            

            if($stmt->execute()){
                //aqui pondriamos otra consulta para actualizar datos referentes a otras tablas , pero de momento no
                return true;
            }
            return false;


        }
        public function eliminar(){
            // como en este caso hemos introducido la sentencia ON DELETE CASCADE en el codigo sql , no hace falta hacer una consulta para cada tabla

            $query = "DELETE FROM usuario WHERE id_usuario = :id_usuario";

            $stmt = $this->conn->prepare($query);

            $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

            $stmt->bindParam(':id_usuario', $this->id_usuario);

            return $stmt->execute();

        }
    }



?>