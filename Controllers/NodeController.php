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

    public function getNodes(){

        $this->validateQuery(["userId" => "required"]);

        echo json_encode(["message" => "this is the file route"]);
    }

    public function saveFile(){

        $this->validateFiles();

        $this->service->moveFiles($_FILES['file']);
    }

    public function createDirNode()
    {
        $this->validateQuery(['parentId'=> 'required', "nodeName" => "required"]);

        $created = $this->service->createDirNode($_POST["parentId"], $_POST["nodName"]);

        if($created){
            exit(json_encode(["status" => "success", "message"=> "node created!"]));
        };

        exit(json_encode(["status"=> "failure","message"=> "could not create node!"]));
    }
}

?>