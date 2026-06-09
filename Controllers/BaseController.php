<?php

class BaseController
{
    protected function validateQuery(array $parameters ) : bool{

        $method = $_SERVER["REQUEST_METHOD"];

        foreach( $parameters as $key => $value ){
            $exploded_value = explode("|", $value );
            
            if( in_array("required", $exploded_value ) 
                && $method === "GET" && !isset($_GET[$key])){
                throw new \Exception("Required GET parameter not set: " . $key);
            }

            if( in_array("required", $exploded_value ) 
                && $method === "POST" && !isset($_POST[$key])){
                throw new \Exception("Required POST parameter not set: " . $key);
            }
        }

        return true;
    }

    protected function validateFiles() : bool
    {
        $files = $_FILES;
        
        if( !is_array($files) || count($files) < 1){
            throw new \Exception("No files were received!");
        }

        foreach( $files['file']['tmp_name'] as $tmp_path ){
            if( !is_uploaded_file( $tmp_path ) ){
                throw new \Exception("Invalid file received through post! ". $tmp_path );
            }
        }

        return true;
    }

}

?>