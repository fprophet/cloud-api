<?php
require_once("BaseController.php");
require_once("Services/UserService.php");

class UserController extends BaseController
{
    public function getUsers(){

        $service = new UserService();
        $users = $service->getUsers();
        echo json_encode(["status"=> "success","message"=> $users]);
    }


    public function addUsers(){
        
        $this->validateQuery(['name' => 'required', 'email'=> 'required','password'=> 'required']);

        $userData = $_POST;

        $service = new UserService();

        $userId = $service->addUser($userData);

        if($userId > 0){
            echo json_encode(['status'=> 'success','message'=> 'user created!']);
        }else{
            echo json_encode(['status'=> 'error','message'=> 'failed to created user']);
        }
    }
}

?>