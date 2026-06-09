<?php
require_once("BaseController.php");
require_once("Services/NodeService.php");

class NodeController extends BaseController 
{
    private NodeService $service;

    public function __construct()
    {
        $this->service = new NodeService();
    }

    public function getNodes() : void
    {

        $params = $this->validateQuery(["userId" => "required"]);

        $this->exitWithStatus("success", "this is the file route");
    }

    public function saveFiles() : void
    {

        $files = $this->validateFiles();

        $params = $this->validateQuery(['parentId' => 'required', 'userId' => 'requried']);

        if(!$this->service->moveFiles($files, $params["POST"]["userId"])){
            $this->exitWithStatus('failure','could not upload files!');
        }

        if(!$this->service->createFileNodes($files, $params["POST"]['parentId'], $params["POST"]['userId'])){
            $this->exitWithStatus('failure','could not create nodes!');
        }

        $this->exitWithStatus('success','files uploaded!');
    }

    public function createDirNode() : void
    {
        $params = $this->validateQuery(['parentId'=> 'required', 'userId' => 'requried', "nodeName" => "required"]);

        $created = $this->service->createDirNode($params["POST"]["parentId"], $params["POST"]['userId'], $params["POST"]["nodName"]);

        if($created){
            $this->exitWithStatus("success", "node created!");
        };

        $this->exitWithStatus("failure", "could not create node!");
    }

    public function deleteNode() : void
    {
        $params = $this->validateQuery(["nodeId"=> "required",]);
    }
}

?>