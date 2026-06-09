<?php

class User
{
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public int $created_at;
    public string $root_directory;
    public int $root_node_id;

    public static function fromDbRow(array $row) : User
    {
        $user = new User();
        $user->id = $row["id"];
        $user->name = $row["name"];
        $user->email = $row["email"];
        $user->password = $row["password"];
        $user->created_at = $row["created_at"];
        $user->root_directory = $row["root_directory"];
        $user->root_node_id = $row["root_node_id"];

        return $user;
    }
}

?>