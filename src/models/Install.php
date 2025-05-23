<?php
class Install
{
    private $conn;
    private $error;

    public function testConnection($host, $dbname, $username, $password)
    {
        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 1049) { // Base de datos no existe
                try {
                    $this->conn = new PDO("mysql:host=$host", $username, $password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
                    $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    return true;
                } catch (PDOException $e) {
                    $this->error = $e->getMessage();
                    return false;
                }
            }
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function executeSqlFile($sqlFile)
    {
        if (!file_exists($sqlFile)) {
            $this->error = "El archivo $sqlFile no se encuentra";
            return false;
        }

        try {
            $sql = file_get_contents($sqlFile);
            $sql = preg_replace('/--.*?\n/', '', $sql); // Eliminar comentarios
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Eliminar comentarios multilínea
            // Hashear la contraseña del administrador
            $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
            $sql = str_replace("'admin'", "'$hashedPassword'", $sql);
            $sql = trim($sql);

            $queries = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($queries as $query) {
                if (!empty($query)) {
                    $this->conn->exec($query);
                }
            }
            return true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function generateConfig($host, $dbname, $username, $password)
    {
        $host = htmlspecialchars(strip_tags($host));
        $dbname = htmlspecialchars(strip_tags($dbname));
        $username = htmlspecialchars(strip_tags($username));
        $password = htmlspecialchars(strip_tags($password));

        $template = <<<EOD
<?php
class DBConnection
{
    private \$host = '$host';
    private \$dbname = '$dbname';
    private \$username = '$username';
    private \$passwd = '$password';
    private \$conn;

    public function __construct()
    {
        try {
            \$this->conn = new PDO(dsn: "mysql:host=\$this->host;dbname=\$this->dbname", username: \$this->username, password: \$this->passwd);
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException \$e) {
            echo "Connection failed: " . \$e->getMessage();
        }
    }

    public function getConnection()
    {
        return \$this->conn;
    }
}
?>
EOD;

        return file_put_contents('../../db/DBConnection.php', $template) !== false;
    }

    public function getError()
    {
        return $this->error;
    }
}