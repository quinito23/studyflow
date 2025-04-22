<?php
include_once 'usuario.php';

    class Profesor extends Usuario{

        private $conn;
        private $table_name = "profesor";

        public $sueldo;
        public $jornada;
        public $fecha_inicio_contrato;
        public $fecha_fin_contrato;

        public function __construct($db){
            $this->conn = $db;
            //llamamos al constructor de la clase padre (usuario)

            parent::__construct($db);

        }

        //metodo para crear el nuevo profesor

        public function crear() {
            // primero insertamos los datos en la tabla usuario
            $query = "INSERT INTO usuario (nombre, apellidos, telefono, correo, contrasenia, fecha_nacimiento, rol, DNI) VALUES (:nombre, :apellidos, :telefono, :correo, :contrasenia, :fecha_nacimiento, :rol, :DNI)";
            $stmt = $this->conn->prepare($query);
        
            //hacemos limpieza de datos
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
            $stmt->bindParam(':contrasenia', $this->contrasenia);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':rol', $this->rol);
            $stmt->bindParam(':DNI', $this->DNI);
        
            if ($stmt->execute()) {
                //obtener el id del usuario que se acaba de insertar
                $this->id_usuario = $this->conn->lastInsertId();
        
                //insertamos los datos restantes en la tabla profesor
                $query = "INSERT INTO " . $this->table_name . " (id_usuario, sueldo, jornada, fecha_inicio_contrato, fecha_fin_contrato) VALUES (:id_usuario, :sueldo, :jornada, :fecha_inicio_contrato, :fecha_fin_contrato)";
                $stmt = $this->conn->prepare($query);
        
                // hacemos la limpieza de datos
                $this->jornada = htmlspecialchars(strip_tags($this->jornada));
                $this->fecha_inicio_contrato = $this->fecha_inicio_contrato ?: null;
                $this->fecha_fin_contrato = $this->fecha_fin_contrato ?: null;
        
                //pasamos los parametros a la consulta
                $stmt->bindParam(':id_usuario', $this->id_usuario);
                $stmt->bindParam(':sueldo', $this->sueldo);
                $stmt->bindParam(':jornada', $this->jornada);
                $stmt->bindParam(':fecha_inicio_contrato', $this->fecha_inicio_contrato);
                $stmt->bindParam(':fecha_fin_contrato', $this->fecha_fin_contrato);
        
                if ($stmt->execute()) {
                    return true; // devolvemos el id del usuario que acabamos de introducir
                }
            }
            return false;
        }
        //leer todos los profesores
        public function leer_todos(){
            $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.DNI, u.telefono, u.correo, u.contrasenia, u.fecha_nacimiento, u.rol, p.sueldo, p.jornada, p.fecha_inicio_contrato, p.fecha_fin_contrato FROM usuario u LEFT JOIN " . $this->table_name . " p ON u.id_usuario = p.id_usuario WHERE u.rol = 'profesor' ORDER BY u.id_usuario DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $profesores = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $profesor = array(
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
                    "fecha_fin_contrato" => $row['fecha_fin_contrato']
                );
                array_push($profesores, $profesor);
            }
            return $profesores;
        }

        public function leer(){
            $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.DNI, u.telefono, u.correo, u.contrasenia, u.fecha_nacimiento, u.rol, p.sueldo, p.jornada, p.fecha_inicio_contrato, p.fecha_fin_contrato FROM usuario u LEFT JOIN " . $this->table_name . " p ON u.id_usuario = p.id_usuario WHERE u.id_usuario = :id_usuario AND u.rol = 'profesor' LIMIT 0,1";
            $stmt = $this->conn->prepare($query);
            
            $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

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
                $this->sueldo = $row['sueldo'];
                $this->jornada = $row['jornada'];
                $this->fecha_inicio_contrato = $row['fecha_inicio_contrato'];
                $this->fecha_fin_contrato = $row['fecha_fin_contrato'];
                return true;

            }
            return false;
        }

        //funcion para actualizar un profesor

        public function actualizar(){
            //primero actualizamos los datos en la tabla usuario
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

            //le pasamos los datos

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':DNI', $this->DNI);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':contrasenia', $this->contrasenia);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':rol', $this->rol);
            $stmt->bindParam(':id_usuario', $this->id_usuario);

            if($stmt->execute()){
                // si se introducen los datos en usuario , ahora metemos los de profesor

                $query = "UPDATE " . $this->table_name . " SET sueldo = :sueldo, jornada = :jornada, fecha_inicio_contrato = :fecha_inicio_contrato, fecha_fin_contrato = :fecha_fin_contrato WHERE id_usuario = :id_usuario";

                $stmt = $this->conn->prepare($query);

                //limpiamos los datos
       
                $this->jornada = htmlspecialchars(strip_tags($this->jornada));
                $sueldo = $this->sueldo ?: null;
                $fecha_inicio_contrato = $this->fecha_inicio_contrato ?: null;
                $fecha_fin_contrato = $this->fecha_fin_contrato ?: null;

                //pasamos los valores a la consulta

                $stmt->bindParam(':sueldo', $sueldo);
                $stmt->bindParam(':jornada', $this->jornada);
                $stmt->bindParam(':fecha_inicio_contrato', $fecha_inicio_contrato);
                $stmt->bindParam(':fecha_fin_contrato', $fecha_fin_contrato);
                $stmt->bindParam(':id_usuario', $this->id_usuario);

                if($stmt->execute()){
                    return true;
                }

            }
            return false;
        }
        
        //funcion para eliminar un profesor

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