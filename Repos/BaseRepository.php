<?php
require_once("database.php");

    class BaseRepository{
        protected PDO $pdo;

        protected DatabaseConnection $db;

        public function __construct(
        ){
            $this->db =  new DatabaseConnection();
        
            $this->pdo = $this->db->GetConnection();
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