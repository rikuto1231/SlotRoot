<?php

class DatabaseConnection {
    private $host;
    private $dbname;
    private $user;
    private $pass;
    private $pdo;
    private $charset;

    public function __construct($host, $dbname, $user, $pass, $charset = 'utf8') {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->pass = $pass;
        $this->charset = $charset;
    }

    public function connect() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=' . $this->charset;
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('接続エラー: ' . $e->getMessage());
        }
    }

    public function getPDO() {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }
}

function getDatabaseConnection() {
    $host = 'mysql212.phy.lolipop.lan';
    $dbname = 'LAA1517437-development';
    $user = 'LAA1517437';
    $pass = 'pass1015';

    $dbConnection = new DatabaseConnection($host, $dbname, $user, $pass);
    return $dbConnection->getPDO();
}

?>
