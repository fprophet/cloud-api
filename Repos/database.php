<?php

    class DatabaseConnection
    {
        private $_dbConnection;

        private PDO $pdo;

        public function __construct(){
            $this->pdo = new PDO(
                "mysql:host=localhost;dbname=filemanager",
                "root",
                "muielacai",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

        }

        public function GetConnection() : PDO{
            return $this->pdo;
        }

        public function Query($string) : bool{
            return true;
        }


    }

?>