<?php

require_once("Core/config.php");

class FileService
{
    public function moveFiles($files){

        for($i= 0; $i < count($files['tmp_name']); $i++){

            if(!move_uploaded_file($files['tmp_name'][$i], UPLOADS . $files['name'][$i])){
                throw new Exception('Could not save file: ' . $files['name'][$i]);
            }
        }
    }
}

?>