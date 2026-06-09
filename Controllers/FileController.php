<?php
require_once("BaseController.php");
require_once("Services/FileService.php");

class FileController extends BaseController 
{
    public function getFiles(){

        $this->validateQuery(["userId" => "required"]);

        echo json_encode(["message" => "this is the file route"]);
    }

    public function saveFile(){

        $this->validateFiles();
        
        $service = new FileService();

        $service->moveFiles($_FILES['file']);
    }
}

?>