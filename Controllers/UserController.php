<?php
require_once("BaseController.php");
require_once("Services/UserService.php");

class UserController extends BaseController
{
    public function getUsers() : void
    {

        $service = new UserService();
        $users = $service->getUsers();
        $this->exitWithStatus("success", "", $users);
    }


    public function addUsers() : void
    {
        
        $params = $this->validateQuery(['name' => 'required', 'email'=> 'required','password'=> 'required']);

        $service = new UserService();

        $userId = $service->addUser($params["POST"]);

        if($userId > 0){
            $this->exitWithStatus('success', 'user created!');
        }else{
            $this->exitWithStatus('error', 'failed to created user');
        }
    }
}

?>