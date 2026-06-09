<?php

require_once("BaseRepository.php");
require_once("Models/Node.php");


class NodeRepository extends BaseRepository 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getNodeChildren($nodeId) : array
    {
        $query = $this->pdo->prepare("select * from nodes where parent_id = :id");

        $query->execute(['parent_id' => $nodeId] );


        $nodes = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $nodes[] = Node::fromDbRow( $row );
        }

        return $nodes;
    }

    public function getUserRootNodeChildren($userId) : array
    {
        $query = $this->pdo->prepare("SELECT c.*
            FROM nodes c
            JOIN nodes r ON c.parent_id = r.id
            WHERE r.name LIKE CONCAT(:userId, '_%')");
            
        $query->execute(['userId'=> $userId] );
        
        $nodes = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
        
            $nodes[] = Node::fromDbRow( $row );
        }

        return $nodes;
    }

    public function createDirNode(int $parentId, string $name) : bool
    {
        $query = $this->pdo->prepare("insert into Nodes (name, is_folder, parent_id, storage_path, size, created_at)
            VALUES (:name, :is_folder, :parent_id, :storage_path, :size, :created_at)");

        $query->execute(
            [
                "name"=> $name,
                'is_folder' => true,
                'parent_id' => $parentId,
                'storage_path' => 'test',
                'size' => 0,
                'created_at' => time(),
            ]);

        return $query->rowCount() > 0;
    }

}
?>