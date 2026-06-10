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

    public function createDirNode($data) : bool
    {
        $query = $this->pdo->prepare("insert into Nodes (name, user_id, is_folder, parent_id, storage_path, size, created_at)
            VALUES (:name, :user_id, :is_folder, :parent_id, :storage_path, :size, :created_at)");

        $query->execute(
            [
                "name"=> $data['name'],
                'user_id' => $data['user_id'],
                'is_folder' => true,
                'parent_id' => $data['parent_id'],
                'storage_path' => $data['storage_path'],
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

    public function deleteNodeAndChildren(int $nodeId) : bool
    {
        $query = $this->pdo->prepare('delete from nodes where id = :id');
        $query->execute(['id'=> $nodeId]);

        return $query->rowCount() > 0;
    }

    public function getNodeById(int $nodeId) : array|false
    {
        $query = $this->pdo->prepare('select * from nodes where id = :node_id');
        $query->execute(['node_id'=> $nodeId]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function updateNode(int $nodeId, array $data) : bool
    {
        $query = $this->pdo->prepare('update nodes set name = :name, storage_path = :storage_path where id = :node_id');

        $query->execute(['node_id'=> $nodeId, 'name'=> $data['name'], 'storage_path' => $data['storage_path']]);

        return $query->rowCount() > 0;
    }

    public function updateNodesStoragePaths(string $oldPath, string $newPath) : bool
    {

        $escapedOld = str_replace('\\', '\\\\', $oldPath);
        $escapedNew = str_replace('\\', '\\\\', $newPath);

        //first count how many paths are there to be replaced
        $countQuery = $this->pdo->prepare('select count(storage_path) as total from nodes where storage_path like :like_path');
        $countQuery->execute(['like_path'=> $escapedOld . "%"]);

        $result = $countQuery->fetch(PDO::FETCH_ASSOC);
        $totalToReplace = $result['total'];


        if ($totalToReplace < 1) {
            return true;
        }

        $replaceQuery = $this->pdo->prepare("update nodes 
                            set storage_path = REPLACE(storage_path, :old_path, :new_path)
                            where storage_path like :like_path");

        $replaceQuery->execute(["old_path" => $oldPath, "new_path"=> $newPath, "like_path" => $escapedOld . "%"]);

        return $replaceQuery->rowCount() === $totalToReplace;
    }

}
?>