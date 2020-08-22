<?php
//used to get mysql database connection
class Database
{

    //database credentials
    //production
    // private $host = "remotemysql.com";
    // private $dbName = "lxvQFMRUq9";
    // private $username = "lxvQFMRUq9";
    // private $password = "LaNDjGu7p1";

    //local dev
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