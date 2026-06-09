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


    public function moveFiles($files){

        for($i= 0; $i < count($files['tmp_name']); $i++){

            if(!move_uploaded_file($files['tmp_name'][$i], UPLOADS . $files['name'][$i])){
                throw new Exception('Could not save file: ' . $files['name'][$i]);
            }
        }
    }

    public function getRootForUser($userId) : array
    {
        $nodes = $this->repo->getUserRootNodeChildren($userId);
        return $nodes;
    }

    public function createDirNode(int $parentId, string $name) : bool
    {
        return $this->repo->createDirNode($parentId, $name);
    }
}

?>