<?php

require_once("Core/config.php");
require_once("Helpers/FileHelper.php");
require_once("Repos/NodeRepository.php");

class NodeService
{
    private NodeRepository $repo;

    public function __construct()
    {
        $this->repo = new NodeRepository();
    }


    public function moveFiles(array $files, string $storage_path) : bool
    {
        try
        {

            for($i= 0; $i < count($files['tmp_name']); $i++){
                $target_path = $storage_path . DIRECTORY_SEPARATOR .  $files['name'][$i];
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

    public function uploadFiles(array $files, array $data) : bool
    {
        $parentData = $this->repo->getNodeById($data['parentId']);

     
        $storage_path = $parentData['storage_path'];

        $data['storage_path'] = $storage_path;
   
        if(!$this->moveFiles($files, $storage_path)){
            return false;
        }

        if(!$this->createFileNodes($files, $data)){
            return false;
        }

        return true;
    }

    public function getRootForUser($userId) : array
    {
        $nodes = $this->repo->getUserRootNodeChildren($userId);
        return $nodes;
    }

    public function createDirNode(array $data) : bool
    {
        //get the parent to find the storage path
        $parentData = $this->repo->getNodeById($data['parent_id']);


        $data['storage_path'] = $parentData['storage_path'] . DIRECTORY_SEPARATOR . $data['name'];

        if(!mkdir($data['storage_path'])){
            return false;
        }
        
        return $this->repo->createDirNode($data);
    }

    public function createFileNodes(array $files, array $data) : bool
    {
        $nodesData = $this->prepareNodesData($files, $data);
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

    public function deleteNode(int $nodeId) : bool
    {
        try
        {
            $node = $this->repo->getNodeById($nodeId);
            
            if(!$node){
                return false;
            }

            FileHelper::rrmdir($node["storage_path"]);

            $dbDeleted = $this->repo->deleteNodeAndChildren($nodeId);

            return $dbDeleted;
        }
        catch(\Exception $e){
            return false;
        }
    }

    public function prepareNodesData(array $files, array $data) : array
    {
        $nodesData = [];

        for($i= 0; $i < count($files['tmp_name']); $i++){

            $nodeData = [];
            $nodeData['name'] = $files['name'][$i];
            $nodeData['user_id'] = $data['userId'];
            $nodeData['parent_id'] = $data['parentId'];
            $nodeData['is_folder'] = 0;
            $nodeData['storage_path'] = $data['storage_path'] . DIRECTORY_SEPARATOR . $files['name'][$i];
            $nodeData['size'] = 0; //$file[''];
            $nodeData['created_at'] = time();

            $nodesData[] = $nodeData;
        }

        return $nodesData;
    } 

    public function updateNode(int $nodeId, array $data) : bool
    {
        //get the node to update
        $nodeToUpdate = $this->repo->getNodeById($nodeId);

        if(!$nodeToUpdate){
            return false;
        }

        try
        {
            $this->repo->BeginTransaction();

            $oldStoragePath = $nodeToUpdate['storage_path'];

            //create the new storage path
            $newStoragePath = $this->getNewStoragePath($nodeToUpdate['storage_path'], $data['name']);

            //set the new node name
            $nodeToUpdate['name'] = $data['name'];

            //set the new storage path
            $nodeToUpdate['storage_path'] = $newStoragePath;

            //update all nodes that start with this storage path
            if(!$this->repo->updateNodesStoragePaths($oldStoragePath, $newStoragePath)){
                $this->repo->rollbackTransaction();
                return false;
            }

            //update the node
            if(!$this->repo->updateNode($nodeId, $nodeToUpdate)){
                $this->repo->rollbackTransaction();
                return false;
            }

            if( !rename($oldStoragePath, $newStoragePath)){
                $this->repo->rollbackTransaction();
                return false;
            }

            $this->repo->Commit();

            return true;
        }
        catch(\Exception $e){
            $this->repo->RollbackTransaction();
            // var_dump($e);
            return false;
        }
    }

    private function getNewStoragePath(string $oldStoragePath, string $newName) : string
    {
        $exploded_path = explode(DIRECTORY_SEPARATOR, $oldStoragePath);

        $exploded_path[count($exploded_path) -1] = $newName;

        $new_path = implode(DIRECTORY_SEPARATOR, $exploded_path);

        return $new_path;
    }
}

?>