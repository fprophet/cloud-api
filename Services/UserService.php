<?php

require_once("Repos/UserRepository.php");
require_once("Core/config.php");



class UserService 
{
    private UserRepository $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();
    }

    public function getUsers() : array
    {
        return $this->repo->getUsers();
    }

    public function addUser(array $userData) : int
    {
        $userData['hashed_password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        $userData['created_at'] = time();

        try
        {
            $this->repo->BeginTransaction();
            $userId = $this->repo->addUser($userData);

            $rootDirectory = $userId  . "_" . $userData["created_at"];

            var_dump(UPLOADS . DIRECTORY_SEPARATOR . $rootDirectory);
            if( is_dir(UPLOADS . DIRECTORY_SEPARATOR . $rootDirectory) ){
                throw new \Exception("A directory already exists for this user!");
            }

            if( !mkdir(UPLOADS . DIRECTORY_SEPARATOR . $rootDirectory, 0, true) ){
                throw new \Exception("Could not create directory for user!");
            }

            if( !$this->repo->updateUserRootDirectory($userId, $rootDirectory) ){
                throw new \Exception("Could not update user directory path");
            }

            $this->repo->Commit();

            return $userId;
        }
        catch( \Exception $e ){

            if( is_dir(UPLOADS . DIRECTORY_SEPARATOR . $rootDirectory) ){
                rmdir(UPLOADS . DIRECTORY_SEPARATOR . $rootDirectory);
            }

            $this->repo->rollbackTransaction();

            return -1;
        }
    }
}

?>