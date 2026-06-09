<?php
require_once("BaseRepository.php");
require_once("Models/User.php");

class UserRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUser(int $id) : array|false
    {
        $query = $this->pdo->prepare("select * from users where id = :id");
        $query->execute(["id" => $id]);

        $user = $query->fetch(PDO::FETCH_ASSOC);

        return $user;
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


    public function updateUserRootDirectory(int $id, int $nodeId, string  $rootDir) : bool
    {
        $query = $this->pdo->prepare('update users set root_directory = :root_directory, root_node_id = :root_node_id where id = :id');
        $query->execute(['root_directory' => $rootDir, 'id'=> $id, 'root_node_id'=> $nodeId]);

        return (bool)$query->rowCount();
    }


    public function createRootNode(string $root, int $userId) : int
    {
        $query = $this->pdo->prepare("insert into Nodes (name, user_id, is_folder, parent_id, storage_path, size, created_at)
            VALUES (:name, :user_id, :is_folder, :parent_id, :storage_path, :size, :created_at)");

        $query->execute(
            [
                'name' => $root,
                'user_id' => $userId,
                'is_folder' => true,
                'parent_id' => NULL,
                'storage_path' => 'test',
                'size' => 0,
                'created_at' => time(),
            ]);

        return (int)$this->pdo->lastInsertId();
    }
}

?>