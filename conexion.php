<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    private $charset;

    public function __construct() {
       
        $this->host = 'localhost';
        $this->db_name = 'nombre_base_datos';
        $this->username = 'usuario_db';
        $this->password = 'contraseña_segura';
        $this->charset = 'utf8mb4';
    }

    public function conectar() {
        $this->conn = null;

        try {
            
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $opciones);
            
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $e) {
            
            error_log("Error de conexión: " . $e->getMessage());
          
            die("Error al conectar con la base de datos. Por favor intente más tarde.");
        }

        return $this->conn;
    }

    public function desconectar() {
        $this->conn = null;
    }
}

?>