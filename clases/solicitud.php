<?php

class Solicitud
{
    private $conn; //conexión con la base de datos
    private $table_name = "solicitud"; //nombre de la tabla

    public $id_solicitud;
    public $id_anonimo;
    public $estado;
    public $fecha_realizacion;
    public $rol_propuesto;

    public function __construct($db)
    { //constructor que recibe la conexión con la base de datos
        $this->conn = $db;
    }

    //metodo para crear una nueva solicitud en la base de datos, asociada al id del anonimo que la ha realiado al enviar el formulario de registro
    public function crear()
    {
        // Creamos la consulta
        $query = "INSERT INTO " . $this->table_name . " (id_anonimo, estado, fecha_realizacion, rol_propuesto) VALUES (:id_anonimo, :estado, :fecha_realizacion, :rol_propuesto)";
        $stmt = $this->conn->prepare($query);

        // Limpiamos los datos
        $this->id_anonimo = htmlspecialchars(strip_tags($this->id_anonimo));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->fecha_realizacion = htmlspecialchars(strip_tags($this->fecha_realizacion));
        $this->rol_propuesto = htmlspecialchars(strip_tags($this->rol_propuesto));

        // Pasamos los parámetros a la consulta
        $stmt->bindParam(':id_anonimo', $this->id_anonimo, PDO::PARAM_INT); // Especificamos que es un entero
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':fecha_realizacion', $this->fecha_realizacion);
        $stmt->bindParam(':rol_propuesto', $this->rol_propuesto);

        // Ejecutamos la consulta
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        // Si falla, lanzamos una excepción
        $errorInfo = $stmt->errorInfo();
        error_log("Error al crear la solicitud: " . $errorInfo[2]);
        throw new Exception("Error al crear la solicitud");
    }

    //metodo para obtener las asignaturas de la solicitud y mostrarlas en el modal al aceptar la solicitud
    public function obtenerAsignaturas($id_solicitud)
    {
        //creamos la consulta para obtener las asignaturas asociadas a la solicitud
        $query = "SELECT a.id_asignatura, a.nombre FROM asignatura a JOIN solicitud_asignatura sa ON a.id_asignatura = sa.id_asignatura WHERE sa.id_solicitud = :id_solicitud";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_solicitud', $id_solicitud, PDO::PARAM_INT);
        $stmt->execute();
        //devolvemos un array con las asignaturas
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>