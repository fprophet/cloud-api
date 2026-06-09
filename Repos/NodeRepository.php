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

    public function createDirNode(int $parentId, int $userId, string $name) : bool
    {
        $query = $this->pdo->prepare("insert into Nodes (name, user_id, is_folder, parent_id, storage_path, size, created_at)
            VALUES (:name, :user_id, :is_folder, :parent_id, :storage_path, :size, :created_at)");

        $query->execute(
            [
                "name"=> $name,
                'user_id' => $userId,
                'is_folder' => true,
                'parent_id' => $parentId,
                'storage_path' => 'test',
                'size' => 0,
                'created_at' => time(),
            ]);

        return $query->rowCount() > 0;
    }

    public function createFileNodes(array $nodeData) : bool
    {
        $this->pdo->beginTransaction();

        $query = $this->pdo->prepare("INSERT INTO nodes (name, user_id, is_folder, parent_id, storage_path, size, created_at) 
        VALUES (:name, :user_id, :is_folder, :parent_id, :storage_path, :size, :created_at)");

        foreach ($nodeData as $item) {
            $query->execute([
                'name'      => $item['name'],
                'user_id' => $item['user_id'],
                'is_folder' => $item['is_folder'],
                'parent_id' => $item['parent_id'],
                'size'      => $item['size'],
                'storage_path' => $item['storage_path'],
                'created_at'=> $item['created_at'],
            ]);
        }
    
        $this->pdo->commit();

        return $query->rowCount() > 0;
    }

    public function getUserRootDir(int $userId) : array|false
    {
        $query = $this->pdo->prepare('select name from nodes where parent_id is null and user_id = :user_id');
        $query->execute(['user_id' => $userId]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getParentWithChildren(int $parentId): array|false
    {
        $nodes = [];

        $parentQuery = $this->pdo->prepare('SELECT * FROM nodes WHERE id = :parent_id');
        $parentQuery->execute(['parent_id' => $parentId]);
        $parent = $parentQuery->fetch(PDO::FETCH_ASSOC);

        if (!$parent) return false;

        $nodes[] = $parent;

        $childQuery = $this->pdo->prepare('SELECT * FROM nodes WHERE parent_id = :parent_id');
        $childQuery->execute(['parent_id' => $parentId]);

        while ($child = $childQuery->fetch(PDO::FETCH_ASSOC)){
            $nodes[] = $child;
        }

        return $nodes;
    }

}
?>