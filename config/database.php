<?php
/**
 * Configuração do Banco de Dados
 * Sistema de Catálogo de Produtos de Limpeza
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_produtos_limpeza');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');

// Configurações gerais do sistema
define('SITE_NAME', 'Produtos de Limpeza');
define('WHATSAPP_NUMBER', '5511999999999'); // Altere para seu número
define('UPLOAD_PATH', 'assets/images/produtos/');
define('MAX_FILE_SIZE', 5242880); // 5MB

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;

    /**
     * Conexão com o banco de dados
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

// Função para conectar ao banco
function conectarBD() {
    $database = new Database();
    return $database->getConnection();
}
?>