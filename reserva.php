<?php


class Reserva
{
    private $conn;
    private $table_name = "reserva";

    public $id_reserva;
    public $id_usuario;
    public $id_aula;
    public $id_asignatura;
    public $id_grupo;
    public $fecha;
    public $hora_inicio;
    public $hora_fin;
    public $estado;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    //metodo para verificar disponibilidad del aula considerando solo reservas activas
    protected function verificarDisponibilidad()
    {
        //calcularemos de mamera dinamica que reservas estan activas basandonos en la fecha y hora actual
        $currentTime = date('Y-m-d H:i:s');
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_aula = :id_aula AND fecha = :fecha AND (CONCAT(fecha, ' ', hora_fin) > :currentTime) AND NOT (hora_fin <= :hora_inicio OR :hora_fin <= hora_inicio) AND id_reserva != :id_reserva";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':hora_inicio', $this->hora_inicio);
        $stmt->bindParam(':hora_fin', $this->hora_fin);
        $stmt->bindParam(':currentTime', $currentTime);
        $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    //Metodo para verificar que no se puedan solapar las reservas
    public function verificarSolapamiento()
    {
        if (!$this->id_grupo || !$this->fecha || !$this->hora_inicio || !$this->hora_fin) {
            return true;
        }
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_grupo = :id_grupo AND fecha = :fecha AND NOT (hora_fin <= :hora_inicio OR :hora_fin <= hora_inicio)";

        if ($this->id_reserva) {
            $query .= " AND id_reserva != :id_reserva";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_grupo', $this->id_grupo, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':hora_inicio', $this->hora_inicio);
        $stmt->bindParam(':hora_fin', $this->hora_fin);
        if ($this->id_reserva) {
            $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    //metodo para obtener las reservas activas por asignatura
    public function obtenerPorAsignatura($id_asignatura, $id_usuario = null)
    {
        // Validar que id_asignatura sea un entero
        if (!is_numeric($id_asignatura) || (int) $id_asignatura <= 0) {
            return [];
        }

        try {
            $currentTime = date('Y-m-d H:i:s');
            $query = "SELECT r.id_reserva, r.id_usuario, r.id_aula, r.id_asignatura, r.id_grupo, r.fecha, r.hora_inicio, r.hora_fin, 
                     CASE WHEN CONCAT(r.fecha, ' ', r.hora_fin) < :currentTime THEN 'pasada' ELSE 'activa' END AS estado, 
                     a.nombre AS aula, asig.nombre AS asignatura, g.nombre AS grupo, u.nombre AS profesor 
                     FROM " . $this->table_name . " r 
                     JOIN aula a ON r.id_aula = a.id_aula 
                     JOIN asignatura asig ON r.id_asignatura = asig.id_asignatura 
                     JOIN grupo g ON r.id_grupo = g.id_grupo 
                     JOIN usuario u ON r.id_usuario = u.id_usuario 
                     JOIN alumno_grupo ag ON r.id_grupo = ag.id_grupo 
                     JOIN alumno al ON ag.id_usuario = al.id_usuario 
                     WHERE r.id_asignatura = :id_asignatura 
                     AND CONCAT(r.fecha, ' ', r.hora_fin) >= :currentTime";

            if ($id_usuario) {
                $query .= " AND ag.id_usuario = :id_usuario";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
            $stmt->bindParam(':currentTime', $currentTime);
            if ($id_usuario) {
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            }
            $stmt->execute();

            $reservas = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reservas[] = array(
                    "id_reserva" => $row['id_reserva'],
                    "fecha" => $row['fecha'],
                    "hora_inicio" => $row['hora_inicio'],
                    "hora_fin" => $row['hora_fin'],
                    "estado" => $row['estado'],
                    "aula" => $row['aula'],
                    "asignatura" => $row['asignatura'],
                    "grupo" => $row['grupo'],
                    "profesor" => $row['profesor']
                );
            }
            return $reservas;
        } catch (PDOException $e) {
            error_log("Error en obtenerPorAsignatura: " . $e->getMessage());
            return [];
        }
    }



    //metodo para calcular el estado de la reserva
    protected function calcularEstado()
    {
        $fechaHoraFin = strtotime($this->fecha . ' ' . $this->hora_fin);
        $fechaActual = time();
        return $fechaHoraFin < $fechaActual ? 'pasada' : 'activa';
    }


    //metodo para crear una nueva reserva
    public function crear()
    {


        //verificar la disponibilidad del aula
        if (!$this->verificarDisponibilidad()) {
            throw new Exception("El aula no esta disponible en ese horario");
        }

        //iniciamos una transaccion
        $this->conn->beginTransaction();

        $query = "INSERT INTO " . $this->table_name . " (id_usuario, id_aula, id_asignatura, id_grupo, fecha, hora_inicio, hora_fin, estado) VALUES (:id_usuario, :id_aula, :id_asignatura, :id_grupo, :fecha, :hora_inicio, :hora_fin, :estado)";
        $stmt = $this->conn->prepare($query);

        //hacemos la limpieza de datos
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->id_aula = htmlspecialchars(strip_tags($this->id_aula));
        $this->id_asignatura = htmlspecialchars(strip_tags($this->id_asignatura));
        $this->id_grupo = htmlspecialchars(strip_tags($this->id_grupo));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->hora_inicio = htmlspecialchars(strip_tags($this->hora_inicio));
        $this->hora_fin = htmlspecialchars(strip_tags($this->hora_fin));
        $this->estado = $this->calcularEstado();

        //pasamos los parametros a la consulta
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);
        $stmt->bindParam(':id_asignatura', $this->id_asignatura, PDO::PARAM_INT);
        $stmt->bindParam(':id_grupo', $this->id_grupo, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':hora_inicio', $this->hora_inicio);
        $stmt->bindParam(':hora_fin', $this->hora_fin);
        $stmt->bindParam(':estado', $this->estado);

        if ($stmt->execute()) {
            //actualizar el estado del aula a reservada
            $id_reserva = $this->conn->lastInsertId();
            $this->conn->commit();
            return $id_reserva;
        } else {
            $this->conn->rollBack();
            return false;
        }


    }



    //metodo para leer las reservas de un usuario
    public function leer_todos($id_usuario = null)
    {


        $currentTime = date("Y-m-d H:i:s");
        $query = "SELECT r.id_reserva, r.id_usuario, r.id_aula, r.id_asignatura, r.id_grupo, r.fecha, r.hora_inicio, r.hora_fin, CASE WHEN CONCAT(r.fecha, ' ', r.hora_fin) < :currentTime THEN 'pasada' ELSE 'activa' END AS estado, a.nombre AS aula , asig.nombre AS asignatura, g.nombre AS grupo, u.nombre AS profesor FROM " . $this->table_name . " r JOIN aula a ON r.id_aula = a.id_aula JOIN asignatura asig ON r.id_asignatura = asig.id_asignatura JOIN grupo g ON r.id_grupo = g.id_grupo JOIN usuario u ON r.id_usuario = u.id_usuario ";
        if ($id_usuario !== null) {
            $query .= "     WHERE r.id_usuario = :id_usuario";
        }
        $query .= " ORDER BY r.fecha DESC, r.hora_inicio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':currentTime', $currentTime);
        if ($id_usuario !== null) {
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        }
        $stmt->execute();

        $reservas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reserva = array(
                "id_reserva" => $row['id_reserva'],
                "id_usuario" => $row['id_usuario'],
                "id_aula" => $row['id_aula'],
                "id_asignatura" => $row['id_asignatura'],
                "id_grupo" => $row['id_grupo'],
                "fecha" => $row['fecha'],
                "hora_inicio" => $row['hora_inicio'],
                "hora_fin" => $row['hora_fin'],
                "estado" => $row['estado'],
                "aula" => $row['aula'],
                "asignatura" => $row['asignatura'],
                "grupo" => $row['grupo'],
                "profesor" => $row['profesor']

            );
            array_push($reservas, $reserva);
        }
        return $reservas;

    }

    //metodo para leer una reserva especifica
    public function leer()
    {
        $currentTime = date('Y-m-d H:i:s');
        $query = "SELECT r.id_reserva, r.id_usuario, r.id_aula, r.id_asignatura, r.id_grupo, r.fecha, r.hora_inicio, r.hora_fin, CASE WHEN CONCAT(r.fecha, ' ', r.hora_fin) < :currentTime THEN 'pasada' ELSE 'activa' END AS estado, a.nombre AS aula, asig.nombre AS asignatura, g.nombre AS grupo, u.nombre AS profesor FROM " . $this->table_name . " r JOIN aula a ON r.id_aula = a.id_aula JOIN asignatura asig ON r.id_asignatura = asig.id_asignatura JOIN grupo g ON r.id_grupo = g.id_grupo JOIN usuario u ON r.id_usuario = u.id_usuario WHERE r.id_reserva = :id_reserva LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->id_reserva = htmlspecialchars(strip_tags($this->id_reserva));
        $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);
        $stmt->bindParam(':currentTime', $currentTime);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id_reserva = $row['id_reserva'];
            $this->id_usuario = $row['id_usuario'];
            $this->id_aula = $row['id_aula'];
            $this->id_asignatura = $row['id_asignatura'];
            $this->id_grupo = $row['id_grupo'];
            $this->fecha = $row['fecha'];
            $this->hora_inicio = $row['hora_inicio'];
            $this->hora_fin = $row['hora_fin'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    //metodo para actualizar una reserva
    public function actualizar()
    {


        //verificar la disponibilidad del aula
        if (!$this->verificarDisponibilidad()) {
            throw new Exception("El aula no esta disponible en ese horario");
        }


        $query = "UPDATE " . $this->table_name . " SET id_aula = :id_aula, id_asignatura = :id_asignatura, id_grupo = :id_grupo, fecha = :fecha, hora_inicio = :hora_inicio, hora_fin = :hora_fin, estado = :estado WHERE id_reserva = :id_reserva";
        $stmt = $this->conn->prepare($query);

        //limpieza de datos
        $this->id_reserva = htmlspecialchars(strip_tags($this->id_reserva));
        $this->id_aula = htmlspecialchars(strip_tags($this->id_aula));
        $this->id_asignatura = htmlspecialchars(strip_tags($this->id_asignatura));
        $this->id_grupo = htmlspecialchars(strip_tags($this->id_grupo));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->hora_inicio = htmlspecialchars(strip_tags($this->hora_inicio));
        $this->hora_fin = htmlspecialchars(strip_tags($this->hora_fin));
        $this->estado = $this->calcularEstado();

        //pasamos los parametros a la consulta
        $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);
        $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);
        $stmt->bindParam(':id_asignatura', $this->id_asignatura, PDO::PARAM_INT);
        $stmt->bindParam(':id_grupo', $this->id_grupo, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':hora_inicio', $this->hora_inicio);
        $stmt->bindParam(':hora_fin', $this->hora_fin);
        $stmt->bindParam(':estado', $this->estado);

        return $stmt->execute();
    }

    //metodo para eliminar una reserva
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_reserva = :id_reserva";
        $stmt = $this->conn->prepare($query);
        $this->id_reserva = htmlspecialchars(strip_tags($this->id_reserva));
        $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>