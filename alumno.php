<?php

include_once 'usuario.php';

class Alumno extends Usuario
{
    private $conn;
    private $table_name = "alumno";

    //creamos la propiedad tutores para almacenar los tutores asignados al alumno
    // No corresponde a una columna en la tabla alumno sino que es simplemente un contenedor de los tutores para hacer su manejo más sencillo
    public $tutores;
    public $grupos;

    public function __construct($db)
    {
        $this->conn = $db;
        // llamamos al consructor de la clase padre (usuario)

        parent::__construct($db);
    }

    // metodo para crear un nuevo profesor

    public function crear($tutores, $grupos = [])
    {
        //insertar en la tabla alumno
        $query = "INSERT INTO " . $this->table_name . " (id_usuario) VALUES (:id_usuario)";
        $stmt = $this->conn->prepare($query);

        $id_usuario = $this->id_usuario;
        if (empty($id_usuario)) {
            throw new Exception("Error en Alumno::crear(): id_usuario está vacío");
        }

        $stmt->bindParam(":id_usuario", $id_usuario);

        if (!$stmt->execute()) {
            return false;
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

        //insertamos la relacion del alumno con los grupos en la tabla alumno_grupo
        foreach ($grupos as $id_grupo) {
            // creamos una consulta para verificar la capacidad del grupo y si ha llegado a su limite
            $query = "SELECT COUNT(id_usuario) as numero_alumnos, g.capacidad_maxima FROM alumno_grupo ag JOIN grupo g ON ag.id_grupo = g.id_grupo WHERE ag.id_grupo = :id_grupo";
            $stmt = $this->conn->prepare($query);
            //le pasamos el parametro a la consulta
            $stmt->bindParam('id_grupo', $id_grupo);
            //ejecutamos la consulta
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['numero_alumnos'] >= $row['capacidad_maxima']) {
                error_log("No se puede asignar el alumno al grupo por que excede su capacidad maxima");
                continue;
            }

            // ahora si insertamos el alumno en el grupo
            $query = "INSERT INTO alumno_grupo (id_usuario, id_grupo) VALUES (:id_usuario, :id_grupo)";
            $stmt = $this->conn->prepare($query);
            //pasamos los parametros a la consulta
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':id_grupo', $id_grupo);
            //ejecutamos la consulta
            $stmt->execute();

        }
        return $id_usuario;
    }


    public function leer_todos()
    {
        //aqui deberiamos hacer un join con otra tabla prbabkemente , pero de momento cogeremos solo los datos del usuario
        $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.DNI, u.telefono, u.correo, u.contrasenia, u.fecha_nacimiento, u.rol FROM usuario u INNER JOIN " . $this->table_name . " a ON u.id_usuario = a.id_usuario WHERE u.rol = 'alumno' ORDER BY u.id_usuario DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $alumnos = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //obtenemos el id del usuario para luego obtener los tutores asignados
            $id_usuario = $row['id_usuario'];
            //obtenemos los tutores asignados
            $tutores = $this->obtenerTutores($id_usuario);
            $grupos = $this->obtenerGrupos($id_usuario);
            $alumno = array(
                "id_usuario" => $row['id_usuario'],
                "nombre" => $row['nombre'],
                "apellidos" => $row['apellidos'],
                "DNI" => $row['DNI'],
                "telefono" => $row['telefono'],
                "correo" => $row['correo'],
                "contrasenia" => $row['contrasenia'],
                "tutores" => $tutores,
                "grupos" => $grupos
            );
            array_push($alumnos, $alumno);
        }
        return $alumnos;

    }

    //leer un alumno

    public function leer()
    {

        $query = "SELECT u.id_usuario, u.nombre, u.apellidos, u.DNI, u.telefono, u.correo, u.contrasenia, u.fecha_nacimiento, u.rol FROM usuario u INNER JOIN " . $this->table_name . " a ON u.id_usuario = a.id_usuario WHERE u.id_usuario = :id_usuario AND u.rol = 'alumno' LIMIT 0,1";
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
            $this->grupos = $this->obtenerGrupos($this->id_usuario);
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

    //metodo para obtener los grupos a los que pertenece el alumno
    public function obtenerGrupos($id_usuario)
    {
        $query = "SELECT g.id_grupo, g.nombre, g.capacidad_maxima, a.id_asignatura, a.nombre AS nombre_asignatura, COUNT(ag2.id_usuario) as numero_alumnos FROM grupo g INNER JOIN alumno_grupo ag ON g.id_grupo = ag.id_grupo LEFT JOIN alumno_grupo ag2 ON g.id_grupo = ag2.id_grupo LEFT JOIN asignatura a ON g.id_asignatura = a.id_asignatura WHERE ag.id_usuario = :id_usuario GROUP BY g.id_grupo, g.nombre, g.capacidad_maxima, a.id_asignatura, a.nombre";
        $stmt = $this->conn->prepare($query);
        //le pasamos los datos a la consulta
        $stmt->bindParam(':id_usuario', $id_usuario);
        //ejecutaomos la consulta
        $stmt->execute();

        $grupos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $grupos[] = array(
                "id_grupo" => $row['id_grupo'],
                "nombre" => $row['nombre'],
                "capacidad_maxima" => $row['capacidad_maxima'],
                "id_asignatura" => $row['id_asignatura'],
                "nombre_asignatura" => $row['nombre_asignatura'],
                "numero_alumnos" => $row['numero_alumnos']
            );
        }
        return $grupos;
    }

    //metodo para obtener las asignaturas asociadaas al alumno a traves de los grupos
    public function obtenerAsignaturas($id_usuario)
    {
        $asignaturas = array();
        $grupos = $this->obtenerGrupos($id_usuario);

        foreach ($grupos as $grupo) {
            $query = "SELECT id_asignatura, nombre, descripcion, nivel FROM asignatura WHERE id_asignatura = :id_asignatura";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_asignatura', $grupo['id_asignatura'], PDO::PARAM_INT);
            $stmt->execute();
            $asignatura = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($asignatura) {
                $asignaturas[] = $asignatura;
            }
        }
        return $asignaturas;
    }

    //funcion para actualizar un alumno

    public function actualizar($tutores, $grupos = [])
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

            //Eliminamos las relaciones exitentes entre grupo y alumno
            $query = "DELETE FROM alumno_grupo WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($query);
            //le pasamos los parametros
            $stmt->bindParam('id_usuario', $this->id_usuario);
            $stmt->execute();

            //hacemos lo mismo para grupos
            foreach ($grupos as $id_grupo) {
                $query = "SELECT COUNT(id_usuario) as numero_alumnos, g.capacidad_maxima FROM alumno_grupo ag JOIN grupo g ON ag.id_grupo = g.id_grupo WHERE ag.id_grupo = :id_grupo";
                $stmt = $this->conn->prepare($query);
                //le pasamos los parametros a la consulta
                $stmt->bindParam(':id_grupo', $id_grupo);
                //ejecutamos la consulta
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row && $row['numero_alumnos'] >= $row['capacidad_maxima']) {
                    error_log("No se puede asignar el alumno al grupo por exceder el maximo permitido");
                    continue;
                }

                // lo insertamos
                $query = "INSERT INTO alumno_grupo (id_usuario, id_grupo) VALUES (:id_usuario, :id_grupo) ";
                $stmt = $this->conn->prepare($query);
                //pasamos loa parametros a la consulta
                $stmt->bindParam(':id_usuario', $this->id_usuario);
                $stmt->bindParam(':id_grupo', $id_grupo);
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