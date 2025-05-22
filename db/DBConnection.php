<?php

class DBConnection
{
    private $host = 'localhost';
    private $dbname = 'studyflow';
    private $username = 'studyflowuser';
    private $passwd = '1234';
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO(dsn: "mysql:host=$this->host;dbname=$this->dbname", username: $this->username, password: $this->passwd);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}





?>