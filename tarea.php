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
    public $id_asignatura;
    public $id_grupo;
    public $estado; // Added to store the computed estado

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para obtener tareas por asignatura
    public function obtenerPorAsignatura($id_asignatura, $id_usuario = null)
    {
        if (!is_numeric($id_asignatura) || (int) $id_asignatura <= 0) {
            return [];
        }

        try {
            $currentTime = date('Y-m-d H:i:s');
            $query = "SELECT t.id_tarea, t.id_usuario, t.descripcion, t.fecha_creacion, t.fecha_entrega, 
                      CASE WHEN t.fecha_entrega < :currentTime THEN 'pasada' ELSE 'activa' END AS estado, 
                      a.nombre AS asignatura, g.nombre AS grupo, u.nombre AS profesor 
                      FROM " . $this->table_name . " t 
                      JOIN tarea_asignatura ta ON t.id_tarea = ta.id_tarea 
                      JOIN tarea_grupo tg ON t.id_tarea = tg.id_tarea 
                      JOIN asignatura a ON ta.id_asignatura = a.id_asignatura 
                      JOIN grupo g ON tg.id_grupo = g.id_grupo 
                      JOIN usuario u ON t.id_usuario = u.id_usuario 
                      JOIN alumno_grupo ag ON tg.id_grupo = ag.id_grupo 
                      JOIN alumno al ON ag.id_usuario = al.id_usuario 
                      WHERE ta.id_asignatura = :id_asignatura";

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

            $tareas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tareas[] = [
                    "id_tarea" => $row['id_tarea'],
                    "descripcion" => $row['descripcion'],
                    "fecha_creacion" => $row['fecha_creacion'],
                    "fecha_entrega" => $row['fecha_entrega'],
                    "estado" => $row['estado'],
                    "asignatura" => $row['asignatura'],
                    "grupo" => $row['grupo'],
                    "profesor" => $row['profesor']
                ];
            }
            return $tareas;
        } catch (PDOException $e) {
            error_log("Error en obtenerPorAsignatura: " . $e->getMessage());
            return [];
        }
    }

    // Método para calcular el estado de la tarea
    protected function calcularEstado()
    {
        $fechaEntrega = strtotime($this->fecha_entrega);
        $fechaActual = time();
        return $fechaEntrega < $fechaActual ? 'pasada' : 'activa';
    }

    // Método para crear una nueva tarea
    public function crear()
    {
        $this->conn->beginTransaction();

        try {
            $query = "INSERT INTO " . $this->table_name . " (id_usuario, descripcion, fecha_creacion, fecha_entrega) 
                      VALUES (:id_usuario, :descripcion, :fecha_creacion, :fecha_entrega)";
            $stmt = $this->conn->prepare($query);

            $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
            $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
            $this->fecha_creacion = date('Y-m-d H:i:s');
            $this->fecha_entrega = htmlspecialchars(strip_tags($this->fecha_entrega));

            $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':fecha_creacion', $this->fecha_creacion);
            $stmt->bindParam(':fecha_entrega', $this->fecha_entrega);

            if ($stmt->execute()) {
                $id_tarea = $this->conn->lastInsertId();

                // Insertar en tarea_asignatura
                $query_asig = "INSERT INTO tarea_asignatura (id_tarea, id_asignatura) VALUES (:id_tarea, :id_asignatura)";
                $stmt_asig = $this->conn->prepare($query_asig);
                $stmt_asig->bindParam(':id_tarea', $id_tarea, PDO::PARAM_INT);
                $stmt_asig->bindParam(':id_asignatura', $this->id_asignatura, PDO::PARAM_INT);
                $stmt_asig->execute();

                // Insertar en tarea_grupo
                $query_grupo = "INSERT INTO tarea_grupo (id_tarea, id_grupo) VALUES (:id_tarea, :id_grupo)";
                $stmt_grupo = $this->conn->prepare($query_grupo);
                $stmt_grupo->bindParam(':id_tarea', $id_tarea, PDO::PARAM_INT);
                $stmt_grupo->bindParam(':id_grupo', $this->id_grupo, PDO::PARAM_INT);
                $stmt_grupo->execute();

                $this->conn->commit();
                return $id_tarea;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception("Error al crear la tarea: " . $e->getMessage());
        }
    }

    // Método para leer todas las tareas
    public function leer_todos($id_usuario = null)
    {
        $currentTime = date("Y-m-d H:i:s");
        $query = "SELECT t.id_tarea, t.id_usuario, t.descripcion, t.fecha_creacion, t.fecha_entrega, 
                  CASE WHEN t.fecha_entrega < :currentTime THEN 'pasada' ELSE 'activa' END AS estado, 
                  a.nombre AS asignatura, g.nombre AS grupo, u.nombre AS profesor 
                  FROM " . $this->table_name . " t 
                  JOIN tarea_asignatura ta ON t.id_tarea = ta.id_tarea 
                  JOIN tarea_grupo tg ON t.id_tarea = tg.id_tarea 
                  JOIN asignatura a ON ta.id_asignatura = a.id_asignatura 
                  JOIN grupo g ON tg.id_grupo = g.id_grupo 
                  JOIN usuario u ON t.id_usuario = u.id_usuario";
        if ($id_usuario !== null) {
            $query .= " WHERE t.id_usuario = :id_usuario";
        }
        $query .= " ORDER BY t.fecha_entrega DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':currentTime', $currentTime);
        if ($id_usuario !== null) {
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        }
        $stmt->execute();

        $tareas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tareas[] = [
                "id_tarea" => $row['id_tarea'],
                "id_usuario" => $row['id_usuario'],
                "descripcion" => $row['descripcion'],
                "fecha_creacion" => $row['fecha_creacion'],
                "fecha_entrega" => $row['fecha_entrega'],
                "estado" => $row['estado'],
                "asignatura" => $row['asignatura'],
                "grupo" => $row['grupo'],
                "profesor" => $row['profesor']
            ];
        }
        return $tareas;
    }

    // Método para leer una tarea específica
    public function leer()
    {
        $currentTime = date('Y-m-d H:i:s');
        $query = "SELECT t.id_tarea, t.id_usuario, t.descripcion, t.fecha_creacion, t.fecha_entrega, 
                  CASE WHEN t.fecha_entrega < :currentTime THEN 'pasada' ELSE 'activa' END AS estado, 
                  ta.id_asignatura, tg.id_grupo 
                  FROM " . $this->table_name . " t 
                  JOIN tarea_asignatura ta ON t.id_tarea = ta.id_tarea 
                  JOIN tarea_grupo tg ON t.id_tarea = tg.id_tarea 
                  WHERE t.id_tarea = :id_tarea LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->id_tarea = htmlspecialchars(strip_tags($this->id_tarea));
        $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
        $stmt->bindParam(':currentTime', $currentTime);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id_tarea = $row['id_tarea'];
            $this->id_usuario = $row['id_usuario'];
            $this->descripcion = $row['descripcion'];
            $this->fecha_creacion = $row['fecha_creacion'];
            $this->fecha_entrega = $row['fecha_entrega'];
            $this->estado = $row['estado']; // Assign computed estado
            $this->id_asignatura = $row['id_asignatura'];
            $this->id_grupo = $row['id_grupo'];
            return true;
        }
        return false;
    }

    // Método para actualizar una tarea
    public function actualizar()
    {
        $this->conn->beginTransaction();

        try {
            $query = "UPDATE " . $this->table_name . " SET descripcion = :descripcion, fecha_entrega = :fecha_entrega 
                      WHERE id_tarea = :id_tarea";
            $stmt = $this->conn->prepare($query);

            $this->id_tarea = htmlspecialchars(strip_tags($this->id_tarea));
            $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
            $this->fecha_entrega = htmlspecialchars(strip_tags($this->fecha_entrega));

            $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':fecha_entrega', $this->fecha_entrega);

            if ($stmt->execute()) {
                // Actualizar tarea_asignatura
                $query_asig = "UPDATE tarea_asignatura SET id_asignatura = :id_asignatura WHERE id_tarea = :id_tarea";
                $stmt_asig = $this->conn->prepare($query_asig);
                $stmt_asig->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
                $stmt_asig->bindParam(':id_asignatura', $this->id_asignatura, PDO::PARAM_INT);
                $stmt_asig->execute();

                // Actualizar tarea_grupo
                $query_grupo = "UPDATE tarea_grupo SET id_grupo = :id_grupo WHERE id_tarea = :id_tarea";
                $stmt_grupo = $this->conn->prepare($query_grupo);
                $stmt_grupo->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);
                $stmt_grupo->bindParam(':id_grupo', $this->id_grupo, PDO::PARAM_INT);
                $stmt_grupo->execute();

                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception("Error al actualizar la tarea: " . $e->getMessage());
        }
    }

    // Método para eliminar una tarea
    public function eliminar()
    {
        $this->conn->beginTransaction();

        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id_tarea = :id_tarea";
            $stmt = $this->conn->prepare($query);
            $this->id_tarea = htmlspecialchars(strip_tags($this->id_tarea));
            $stmt->bindParam(':id_tarea', $this->id_tarea, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception("Error al eliminar la tarea: " . $e->getMessage());
        }
    }
}
?>