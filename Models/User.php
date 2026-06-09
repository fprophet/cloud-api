<?php

class User
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $created_at;
    public $root_directory;

    public static function fromDbRow(array $row) : User
    {
        $user = new User();
        $user->id = $row["id"];
        $user->name = $row["name"];
        $user->email = $row["email"];
        $user->password = $row["password"];
        $user->created_at = $row["created_at"];
        $user->root_directory = $row["root_directory"];

        return $user;
    }
}

?>