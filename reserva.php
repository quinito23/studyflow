<?php
session_start();

class Reserva{
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

    public function __construct($db){
        $this->conn = $db;
    }

    //metodo para verificar que el usuario es un profesor
    private function esProfesor($id_usuario){
        if(!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] != $id_usuario || $_SESSION['rol'] != 'profesor'){
            return false;
        }
        return true;
    }

    //metodo para verificar disponibilidad del aula considerando solo reservas activas
    private function verificarDisponibilidad(){
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_aula = :id_aula AND fecha = :fecha AND estado = 'activa' AND ((hora_inicio <= :hora_inicio AND hora_fin > :hora_inicio) OR (hora_inicio < :hora_fin AND hora_fin >= :hora_fin) OR 
                      (hora_inicio >= :hora_inicio AND hora_fin <= :hora_fin)) AND id_reserva != :id_reserva";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':hora_inicio', $this->hora_inicio);
        $stmt->bindParam(':hora_fin', $this->hora_fin);
        $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    //Metodo para verificar que un grupo no este asociado a otra asignatura con reserva activa
    private function verificarAsignacionGrupo(){
        if(!$this->id_grupo || !$this->id_asignatura) return true;

        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_grupo = :id_grupo AND id_asignatura != :id_asignatura AND estado = 'activa'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_grupo', $this->id_grupo, PDO::PARAM_INT);
        $stmt->bindParam('id_asignatura', $this->id_asignatura, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    //metodo para crear una nueva reserva
    public function crear(){
        //primero verificamos que el usuario que la crea es un profesor
        if(!$this->esProfesor($this->id_usuario)){
            throw new Exception("El usuario no es un profesor");
        }

        //verificar la disponibilidad del aula
        if(!$this->verificarAsignacionGrupo()){
            throw new Exception("El grupo ya esta asocado a otra asignatura en una reserva activa");
        }

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

        if($stmt->execute()){
            //actualizar el estado del aula a reservada
            $query = "UPDATE aula SET estado = 'reservada' WHERE id_aula = :id_aula";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);
            $stmt->execute();
            return $this->conn->lastInsertId();
        }
        return false;

    }

    //metodo para calcular el estado de la reserva
    private function calcularEstado(){
        $fechaHoraFin = strtotime($this->fecha . '' . $this->hora_fin);
        $fechaActual = time();
        return $fechaHoraFin < $fechaActual ? 'pasada' : 'activa';
    }

    //metodo para leer las reservas de un usuario
    public function leer_todos($id_usuario){
        //primero verificamos que el usuario es un profesor
        if(!$this->esProfesor($id_usuario)){
            throw new Exception("El usuario no es un profesor");
        }

        $query = "SELECT r.id_reserva, r.id_usuario, r.id_aula, r.id_asignatura, r.id_grupo, r.fecha, r.hora_inicio, r.fecha_fin, r.estado, a.nombre AS aula , asig.nombre AS asignatura, g.nombre AS grupo, u.nombre AS profesor FROM " . $this->table_name . " r JOIN aula a ON r.id_aula = a.id_aula JOIN asignatura asig ON r.id_asignatura = asig.id_asignatura JOIN grupo g ON r.id_grupo = g.id_grupo JOIN usuario u ON r.id_usuario = u.id_usuario WHERE r.id_usuario = :id_usuario ORDER BY r.fecha DESC, r.hora_inicio DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $reservas = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $reserva = array(
                "id_reserva" => $row['id_reserva'],
                "id_usuario" => $row['id_usuario'],
                "id_aula" => $row['id_aula'],
                "id_asignatura" => $row['id_asignatura'],
                "id_grupo" => $row['id_grupo'],
                "fecha" => $row['fecha'],
                "hora inicio" => $row['hora_inicio'],
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
    public function leer(){
        $query = "SELECT r.id_reserva, r.id_usuario, r.id_aula, r.id_asignatura, r.id_grupo, r.fecha, r.hora_inicio, r.hora_fin, r.estado, a.nombre AS aula, asig.nombre AS asignatura, g.nombre AS grupo, u.nombre AS profesor FROM " . $this->table_name . " r JOIN aula a ON r.id_aula = a.id_aula JOIN asignatura asig ON r.id_asignatura = asig.id_asignatura JOIN grupo g ON r.id_grupo = g.id_grupo JOIN usuario u ON r.id_usuario = u.id_usuario WHERE r.id_reserva = :id_reserva LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->id_reserva = htmlspecialchars(strip_tags($this->id_reserva));
        $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
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
    public function actualizar(){
        //verificar que el usuario sea un profesor
        if(!$this->esProfesor($this->id_usuario)){
            throw new Exception("El usuario no es un profesor");
        }

        //verificar la disponibilidad del aula
        if(!$this->verificarDisponibilidad()){
            throw new Exception("El aula no esta disponible en ese horario");
        }

        if(!$this->verificarAsignacionGrupo()){
            throw new Exception("El grupo ya esta asociado a otra asignatura en una reserva activa");
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
    public function eliminar(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id_reserva = :id_reserva";
        $stmt = $this->conn->prepare($query);
        $this->id_reserva = htmlspecialchars(strip_tags($this->id_reserva));
        $stmt->bindParam(':id_reserva', $this->id_reserva, PDO::PARAM_INT);

        if($stmt->execute()){
            //actualizar el estado de aula a libre
            $query = "UPDATE aula SET estado = 'libre' WHERE id_aula = :id_aula";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_aula', $this->id_aula, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        }
        return false;
    }
}

?>