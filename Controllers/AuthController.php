<?php

class AuthController extends BaseController
{
    private UserService $service;

    public function __construct(){
        $this->service = new UserService();
    }

    public function login() : void
    {
        $data = $this->getRequestData();

        if( !$data ){
            $this->exitWithStatus("failure","invalid data received!");
        }

        $user = $this->service->getUserByEmail($data["email"]);

        if( !$user ){
            $this->exitWithStatus("failure","could not find user!");
        }

        $hashedRequestPassword = password_hash($data["password"], PASSWORD_DEFAULT);
        if( !password_verify($data["password"], $user->password) ){
            $this->exitWithStatus("failure","passwords dont match!");
        }

        $this->exitWithStatus("success","", [$user]);
    }
}

?>