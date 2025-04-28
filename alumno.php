<?php

include_once 'usuario.php';

class Alumno extends Usuario
{
    private $conn;
    private $table_name = "alumno";

    //creamos la propiedad tutores para almacenar los tutores asignados al alumno
    // No corresponde a una columna en la tabla alumno sino que es simplemente un contenedor de los tutores para hacer su manejo más sencillo
    public $tutores;

    public function __construct($db)
    {
        $this->conn = $db;
        // llamamos al consructor de la clase padre (usuario)

        parent::__construct($db);
    }

    // metodo para crear un nuevo profesor

    public function crear($tutores)
    {

        $id_usuario = $this->id_usuario;
        if (empty($id_usuario)) {
            throw new Exception("Error en Alumno::crear(): id_usuario está vacío");

        }
        foreach ($tutores as $id_tutor) {
            $query = "INSERT INTO alumno_tutor (id_usuario,id_tutor) VALUES (:id_usuario, :id_tutor)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':id_tutor', $id_tutor);
            if (!$stmt->execute()) {
                return false;
            }

        }
        return $id_usuario;
    }

    public function leer_todos()
    {
        //aqui deberiamos hacer un join con otra tabla prbabkemente , pero de momento cogeremos solo los datos del usuario
        $query = "SELECT id_usuario, nombre, apellidos, DNI, telefono, correo, contrasenia, fecha_nacimiento, rol FROM usuario WHERE rol = 'alumno' ORDER BY id_usuario DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $alumnos = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //obtenemos el id del usuario para luego obtener los tutores asignados
            $id_usuario = $row['id_usuario'];
            //obtenemos los tutores asignados
            $tutores = $this->obtenerTutores($id_usuario);
            $alumno = array(
                "id_usuario" => $row['id_usuario'],
                "nombre" => $row['nombre'],
                "apellidos" => $row['apellidos'],
                "DNI" => $row['DNI'],
                "telefono" => $row['telefono'],
                "correo" => $row['correo'],
                "contrasenia" => $row['contrasenia'],
                "tutores" => $tutores
            );
            array_push($alumnos, $alumno);
        }
        return $alumnos;

    }

    //leer un alumno

    public function leer()
    {

        $query = "SELECT id_usuario, nombre, apellidos, DNI, telefono, correo, contrasenia, fecha_nacimiento, rol FROM usuario WHERE id_usuario = :id_usuario AND rol = 'alumno' LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        //limpieza de datos
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        //pasamos los datos a la consulta
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_usuario = $row['id_usuario'];
            $this->nombre = $row['nombre'];
            $this->apellidos = $row['apellidos'];
            $this->DNI = $row['DNI'];
            $this->telefono = $row['telefono'];
            $this->correo = $row['correo'];
            $this->contrasenia = $row['contrasenia'];
            $this->fecha_nacimiento = $row['fecha_nacimiento'];
            $this->rol = $row['rol'];
            $this->tutores = $this->obtenerTutores($this->id_usuario);
            return true;
        }
        return false;
    }

    //metodo para obtener los tutores asignados al alumno
    private function obtenerTutores($id_usuario)
    {
        $query = "SELECT t.id_tutor, t.nombre, t.apellidos, t.telefono FROM tutor_legal t INNER JOIN alumno_tutor at ON t.id_tutor = at.id_tutor WHERE at.id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        $tutores = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // por cada tutor lo agregamos como un array al final del array tutores
            $tutores[] = array("id_tutor" => $row['id_tutor'], "nombre" => $row['nombre'], "apellidos" => $row['apellidos'], "telefono" => $row['telefono']);
        }
        return $tutores;
    }

    //funcion para actualizar un alumno

    public function actualizar($tutores)
    {

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
        $stmt->bindParam(':contrasenia', $this->contrasenia);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':rol', $this->rol);
        $stmt->bindParam('id_usuario', $this->id_usuario);


        if ($stmt->execute()) {
            //primero eliminamos las relaciones existentes que hay entre alumno y tutor
            $query = "DELETE FROM alumno_tutor WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuario', $this->id_usuario);
            $stmt->execute();

            // y en segundo lugar introducimos las nuevas relaciones
            foreach ($tutores as $id_tutor) {
                $query = "INSERT INTO alumno_tutor (id_usuario, id_tutor) VALUES (:id_usuario, :id_tutor)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_usuario', $this->id_usuario);
                $stmt->bindParam(':id_tutor', $id_tutor);
                $stmt->execute();
            }

            return true;
        }
        return false;


    }
    public function eliminar()
    {
        // como en este caso hemos introducido la sentencia ON DELETE CASCADE en el codigo sql , no hace falta hacer una consulta para cada tabla

        $query = "DELETE FROM usuario WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

        $stmt->bindParam(':id_usuario', $this->id_usuario);

        return $stmt->execute();

    }
}



?>