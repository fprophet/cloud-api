<?php

require_once("Core/config.php");
require_once("Repos/NodeRepository.php");

class NodeService
{
    private NodeRepository $repo;

    public function __construct()
    {
        $this->repo = new NodeRepository();
    }


    public function moveFiles(array $files, int $userId) : bool
    {
        try
        {
            $nodeData = $this->repo->getUserRootDir($userId);

            if(!$nodeData){
                throw new Exception("Root directory for user not found!");
            }

            for($i= 0; $i < count($files['tmp_name']); $i++){
                $target_path = UPLOADS . DIRECTORY_SEPARATOR . $nodeData["name"] . DIRECTORY_SEPARATOR .  $files['name'][$i];
                if(!move_uploaded_file($files['tmp_name'][$i], $target_path)){
                    throw new Exception('Could not save file: ' . $files['name'][$i]);
                }
            }

            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public function getRootForUser($userId) : array
    {
        $nodes = $this->repo->getUserRootNodeChildren($userId);
        return $nodes;
    }

    public function createDirNode(int $parentId, int $userId, string $name) : bool
    {
        return $this->repo->createDirNode($parentId, $userId, $name);
    }

    public function createFileNodes(array $files, int $parentId, int $userId) : bool
    {
        $nodesData = $this->getNodesData($files, $parentId, $userId);
        return $this->repo->createFileNodes($nodesData);
    }

    public function getParentWithChildren(int $parentId) : array
    {
        $nodesData = $this->repo->getParentWithChildren($parentId);

        $nodes = [];

        foreach($nodesData as $nodeData){
            $nodes[] = Node::fromDbRow($nodeData);
        }
        
        return $nodes;
    }

    public function getNodesData(array $files, int $parentId, int $userId) : array
    {
        $data = [];

        for($i= 0; $i < count($files['tmp_name']); $i++){

            $nodeData = [];
            $nodeData['name'] = $files['name'][$i];
            $nodeData['user_id'] = $userId;
            $nodeData['parent_id'] = $parentId;
            $nodeData['is_folder'] = 0;
            $nodeData['storage_path'] = 'test';
            $nodeData['size'] = 0; //$file[''];
            $nodeData['created_at'] = time();

            $data[] = $nodeData;
        }
        return $data;
    } 
}

?>