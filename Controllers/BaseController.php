<?php

class BaseController
{
    protected function validateQuery(array $parameters ) : array{

        $method = $_SERVER["REQUEST_METHOD"];

        $params["GET"] = [];
        $params["POST"] = [];

        foreach( $parameters as $key => $value ){
            $exploded_value = explode("|", $value );
            
            if( $method === "GET"){
                if( in_array("required", $exploded_value ) 
                    && !isset($_GET[$key])){
                    //exit with error code
                    throw new \Exception("Required GET parameter not set: " . $key);
                }else
                {
                    $params["GET"][$key] = $_GET[$key];
                }
            }
                    
            if($method === "POST"){
                if( in_array("required", $exploded_value ) 
                    && !isset($_POST[$key])){
                    //exit with error code
                    throw new \Exception("Required POST parameter not set: " . $key);
                }else
                {
                    $params["POST"][$key] = $_POST[$key];
                }
            }
        }

        return $params;
    }

    protected function getRequestData(array $validationParameters = []) : array|bool
    {
        $data = json_decode(file_get_contents("php://input"), true);

        //sanitize the data
        foreach( $data as $key => $value ){
            $data[$key] = $this->sanitize($value);
        }

        //if no custom validation require return data
        if( empty($validationParameters) ){
            return $data;
        }

        //if custom validation, check if the required parameters are set in the request body
        foreach( $validationParameters as $key => $value ){
            $exploded_value = explode("|", $value );
            if( in_array("required", $exploded_value ) 
                && !isset($data[$key])){
                //exit with error code
                throw new \Exception("Required parameter not set in the request body: " . $key);
            }
        }

        return $data;
    }

    protected function validateFiles() : array
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

        return $files["file"];
    }

    protected function exitWithStatus(string $status, string $message = "", array $objects = []) : void
    {
        exit(json_encode(["status" => $status,"message"=> $message, "data" => $objects]));
    }

    protected function validateParams(array &$params) : void
    {
        if (!is_array($params) || count($params) < 1) {
            throw new \Exception("No params received");
        }

        foreach ($params as $key => $value) {
            $params[$key] = $this->sanitize($value);
        }
    }

    protected function sanitize(string $value) : string
    {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return $value;
    }
}

?>