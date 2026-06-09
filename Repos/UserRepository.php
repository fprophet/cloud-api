<?php
require_once("BaseRepository.php");
require_once("Models/User.php");

class UserRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUsers() : array
    {
        $query = $this->pdo->prepare("Select * from users;");
        $query->execute();

        $users = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC))
        {
            $users[] = User::fromDbRow($row);
        }
        
        return $users;
    }

    public function addUser(array $data) : int
    {
        $query = $this->pdo->prepare("insert into users
                (name, email, password, created_at)  
                values (:name, :email, :password, :created_at)");

        $query->execute([
            'name' => $data['name'],
            'email'=> $data['email'],
            'password'=> $data['hashed_password'],
            'created_at'=> $data['created_at'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }


    public function updateUserRootDirectory(int $id, string  $rootDir) : bool
    {
        $query = $this->pdo->prepare('update users set root_directory = :root_directory where id = :id');
        $query->execute(['root_directory' => $rootDir, 'id'=> $id]);

        return (bool)$query->rowCount();
    }
}

?>