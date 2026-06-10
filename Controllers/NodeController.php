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

    public function uploadFiles() : void
    {
        $files = $this->validateFiles();

        $params = $this->validateQuery(['parentId' => 'required', 'userId' => 'requried']);

        if(!$this->service->uploadFiles($files, $params["POST"])) {
            $this->exitWithStatus('failure',' could not upload files');
        }

        $this->exitWithStatus('success','files uploaded!');
    }

    public function createDirNode() : void
    {
        $data = $this->getRequestData();

        //valdiate data and escape it
        $created = $this->service->createDirNode($data);

        if($created){
            $this->exitWithStatus("success", "node created!");
        };

        $this->exitWithStatus("failure", "could not create node!");
    }

    public function deleteNode(array $params) : void
    {
        $this->validateParams($params);

        $deleted = $this->service->deleteNode((int)$params["id"]);

        if($deleted){
            $this->exitWithStatus("success","node deleted");
        }

        $this->exitWithStatus("failure","cannot delete node");
        
    }

    public function updateNode(array $params) : void
    {
        $this->validateParams($params);

        $data = $this->getRequestData(['name' => 'required']);

        if( $this->service->updateNode($params["nodeId"], $data)){
            $this->exitWithStatus("success","node update!");
        }

        $this->exitWithStatus("failure","could not update node!");
    }

}

?>