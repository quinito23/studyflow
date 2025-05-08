<?php

class Tarea
{
    private $conn;
    private $table_name = "tarea";

    public $id_tarea;
    public $id_usuario;
    public $descripcion;
    public $fecha_creacion;
    public $fecha_entrega;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function crear()
    {
        $query = "INSERT INTO " . $this->table_name . " (id_usuario, descripcion, fecha_creacion, fecha_entrega) VALUES (:id_usuario, :descripcion, :fecha_creacion, :fecha_entrega)";
        $stmt = $this->conn->prepare($query);

        //limpiamos los parametros antes de pasarlos a la consulta
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->fecha_creacion = htmlspecialchars(strip_tags($this->fecha_creacion));
        $this->fecha_entrega = htmlspecialchars(strip_tags($this->fecha_entrega));

        //pasamos los parametros a la consulta
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':fecha_creacion', $this->fecha_creacion);
        $stmt->bindParam(':fecha_entrega', $this->fecha_entrega);

        if ($stmt->execute()) {
            $this->id_tarea = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    //asignar tarea a asignaturas
    public function asignarAsignaturas($id_asignatura)
    {
        if (!$this->id_tarea) {
            return false;
        }

        //primero eliminamos la asignacion con la anterior asignatura si existe
        $delete_query = "DELETE FROM tarea_asignatura WHERE id_tarea = :id_tarea";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
        $delete_stmt->execute();

        //despues insertamos la nueva relacion
        $query = "INSERT INTO tarea_asignatura (id_tarea, id_asignatura) VALUES (:id_tarea, :id_asignatura)";
        $stmt = $this->conn->query($query);

        $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
        $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
        return $stmt->execute();

    }

    //metodo para asignar las tareas a los grupos
    public function asignarGrupos($id_grupo)
    {
        if (!$this->id_tarea) {
            return false;
        }

        //eliminamos la relación anterior si existe
        $delete_query = "DELETE FROM tarea_grupo WHERE id_tarea = :id_tarea";
        $delete_stmt = $this->conn->prepare($delete_query);

        $delete_stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
        $delete_stmt->execute();

        //despues insertamos la nueva relación
        $query = "INSERT INTO tarea_grupo (id_tarea, id_grupo) VALUES (:id_tarea, :id_grupo)";
        $stmt = $this->conn->query($query);

        $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        return $stmt->execute();

    }

    //leer todas las tareas
    public function leer_todos()
    {
        $query = "SELECT t.id_tarea, t.id_usuario, t.descripcion, t.fecha_creacion, t.fecha_entrega, u.nombre AS profesor, a.nombre AS asignatura, g.nombre AS grupo FROM " . $this->table_name . " t JOIN usuario u ON t.id_usuario = u.id_usuario JOIN tarea_asignatura ta ON t.id_tarea = ta.id_tarea JOIN asignatura a ON ta.id_asignatura = a.id_asignatura JOIN tarea_grupo tg ON t.id_grupo = g.id_grupo ORDER BY t.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $tareas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tareas[] = array(
                "id_tarea" => $row['id_tarea'],
                "id_usuario" => $row['id_usuario'],
                "profesor" => $row['profesor'],
                "descripcion" => $row['descripcion'],
                "fecha_creacion" => $row['fecha_creacion'],
                "fecha_entrega" => $row['fecha_entrega'],
                "asignatrua" => $row['asignatura'],
                "grupo" => $row['grupo'],

            );
        }
        return $tareas;
    }

    //leer tareas por asignatura
    public function obtenerPorAsignatura($id_asignatura)
    {
        $query = "SELECT t.id_tarea, t.id_usuario, t.descripcion, t.fecha_creacion, t.fecha_entrega, u.nombre AS profesor, g.nombre AS grupo FROM " . $this->table_name . " t JOIN tarea_asignatura ta ON t.id_tarea = ta.id_tarea JOIN usuario u ON t.id_usuario = u.id_usuario JOIN tarea_grupo tg ON t.id_tarea = tg.id_tarea JOIN grupo g ON tg.id_grupo = g.id_grupo WHERE ta.id_asignatura = :id_asignatura ORDER BY t.fecha_entrega ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_asignatura', $id_asignatura, PDO::PARAM_INT);
        $stmt->execute();

        $tareas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tareas[] = array(
                "id_tarea" => $row['id_tarea'],
                "id_usuario" => $row['id_usuario'],
                "profesor" => $row['profesor'],
                "descripcion" => $row['descripcion'],
                "fecha_creacion" => $row['fecha_creacion'],
                "fecha_entrega" => $row['fecha_entrega'],
                "grupo" => $row['grupo']
            );
        }
        return $tareas;
    }

    //leer tareas por grupo
    public function obtenerPorGrupo($id_grupo)
    {
        $query = "SELECT t.id_tarea, t.id_usuario, t.descripcion, t.fecha_creacion, t.fecha_entrega, u.nombre AS profesor, a.nombre AS asignatura FROM " . $this->table_name . " t JOIN tarea_grupo tg ON t.id_tarea = tg.id_tarea JOIN usuario u ON t.id_usuario = u.id_usuario JOIN tarea_asignatura ta ON t.id_tarea = ta.id_tarea JOIN asignatura a ON ta.id_asignatura = a.id_asignatura WHERE tg.id_grupo = :id_grupo ORDER BY t.fecha_entrega ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);

        $tareas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tareas[] = array(
                "id_tarea" => $row['id_tarea'],
                "id_usuario" => $row['id_usuario'],
                "profesor" => $row['profesor'],
                "descripcion" => $row['descripcion'],
                "fecha_creacion" => $row['fecha_creacion'],
                "fecha_entrega" => $row['fecha_entrega'],
                "asignatura" => $row['asignatura']

            );
        }
        return $tareas;
    }

    //leer una tarea especifica
    public function leer()
    {
        $query = "SELECT t.id_tarea, t.id_usuario, t.descripcion, t.fecha_creacion, t.fecha_entrega, u.nombre AS profesor, a.id_asignatura, g.id_grupo FROM " . $this->table_name . " t JOIN usuario ON t.id_usuario = u.id_usuario JOIN tarea_asignatura ta ON t.id_tarea = ta.id_tarea JOIN asignatura a ON ta.id_asignatura = a.id_asignatura JOIN tarea_grupo tg ON t.id_tarea = tg.id_tarea JOIN grupo g ON tg.id_grupo = g.id_grupo WHERE t.id_tarea = :id_tarea LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        //limpiamos datos antes de pasarlos a la consulta
        $this->id_tarea = htmlspecialchars(strip_tags($this->id_tarea));

        //lo pasamos a la consulta
        $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_tarea = $row['id_tarea'];
            $this->id_usuario = $row['id_usuario'];
            $this->descripcion = $row['descripcion'];
            $this->fecha_creacion = $row['fecha_creacion'];
            $this->fecha_entrega = $row['fecha_entrega'];
            return true;
        }
        return false;
    }

    //metodo para actualizar una tarea
    public function actualizar()
    {
        $query = "UPDATE " . $this->table_name . " SET id_usuario = :id_usuario, descripcion = :descripcion, fecha_creacion = :fecha_creacion, fecha_entrega = :fecha_entrega WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($query);
        //hacemos limpieza de datos
        $this->id_tarea = htmlspecialchars(strip_tags($this->id_tarea));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->fecha_creacion = htmlspecialchars(strip_tags($this->fecha_creacion));
        $this->fecha_entrega = htmlspecialchars(strip_tags($this->fecha_entrega));

        //pasamos los datos a la consulta
        $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':fecha_creacion', $this->fecha_creacion);
        $stmt->bindParam(':fecha_entrega', $this->fecha_entrega);

        return $stmt->execute();
    }

    //eliminar una tarea
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($query);
        //limpiamos el dato
        $this->id_tarea = htmlspecialchars(strip_tags($this->id_tarea));
        //pasamos el dato a la consulta
        $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }
        return false;

    }
}

?>