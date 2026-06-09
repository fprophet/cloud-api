<?php
require_once("database.php");

    class BaseRepository{
        protected PDO $pdo;


        public function __construct(
        ){
            $dbConnection =  new DatabaseConnection();
        
            $this->pdo = $dbConnection->GetConnection();
        }

        
        public function BeginTransaction() {
            return $this->pdo->beginTransaction();
        }

        public function RollbackTransaction(){
            return $this->pdo->rollBack();
        }

        public function Commit(){
            return $this->pdo->commit();
        }
    }

?>