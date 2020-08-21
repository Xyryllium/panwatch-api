<?php
//used to get mysql database connection
class Database
{

    //database credentials
    // private $host = "sql203.epizy.com";
    // private $dbName = "epiz_26551805_panwatch";
    // private $username = "epiz_26551805";
    // private $password = "0vDESY4bdT";
    private $host = "localhost";
    private $dbName = "panwatch";
    private $username = "root";
    private $password = "";
    private $charset = 'utf8mb4';
    public $conn;

    //get database connection
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName . ";charset=" . $this->charset, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}