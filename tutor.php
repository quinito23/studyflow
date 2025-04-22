<?php



    class Tutor {
        private $conn;
        private $table_name = "tutor_legal";

        public $id_tutor;
        public $nombre;
        public $apellidos;
        public $telefono;
        
        public function __construct($db){
            $this->conn = $db; 
        }

        // metodo para crear un nuevo tutor

        public function crear(){

            //insertamos los datos en la tabla tutor
            $query = "INSERT INTO " . $this->table_name . " (nombre, apellidos, telefono) VALUE (:nombre, :apellidos, :telefono)";
            $stmt = $this->conn->prepare($query);

            //hacemos la limpieza de datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
            $this->telefono = htmlspecialchars(strip_tags($this->telefono));

            //le pasamos los parametros a la consulta
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':telefono', $this->telefono);

            if($stmt->execute()){
                return true;
            }
            return false;
        }

        public function leer_todos(){
            $query = "SELECT id_tutor, nombre, apellidos, telefono FROM " . $this->table_name . " ORDER BY id_tutor DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $tutores = array();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $tutor = array(
                    "id_tutor" => $row['id_tutor'],
                    "nombre" => $row['nombre'],
                    "apellidos" => $row['apellidos'],
                    "telefono" => $row['telefono']
                );
                array_push($tutores, $tutor);
            }
            return $tutores;

        }

        //leer un tutor

        public function leer(){

            $query = "SELECT id_tutor, nombre, apellidos, telefono FROM " . $this->table_name . " WHERE id_tutor = :id_tutor LIMIT 0,1";
            $stmt = $this->conn->prepare($query);

            //limpieza de datos
            $this->id_tutor = htmlspecialchars(strip_tags($this->id_tutor));
            //pasamos los datos a la consulta
            $stmt->bindParam(':id_tutor', $this->id_tutor);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row){
                $this->id_tutor = $row['id_tutor'];
                $this->nombre = $row['nombre'];
                $this->apellidos = $row['apellidos'];
                $this->telefono = $row['telefono'];
                return true;
            }
            return false;
        }

        //funcion para actualizar un tutor

        public function actualizar(){

            $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, apellidos = :apellidos, telefono = :telefono WHERE id_tutor = :id_tutor";

            $stmt = $this->conn->prepare($query);

            //hacemos limpieza de datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
            $this->telefono = htmlspecialchars(strip_tags($this->telefono));
            $this->id_tutor = htmlspecialchars(strip_tags($this->id_tutor));

            // le pasamos los datos a la consulta
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':id_tutor', $this->id_tutor);
            

            if($stmt->execute()){
                return true;
            }
            return false;


        }
        public function eliminar(){
            $query = "DELETE FROM " . $this->table_name . " WHERE id_tutor = :id_tutor";

            $stmt = $this->conn->prepare($query);

            $this->id_tutor = htmlspecialchars(strip_tags($this->id_tutor));

            $stmt->bindParam(':id_tutor', $this->id_tutor);

            return $stmt->execute();

        }
    }



?>