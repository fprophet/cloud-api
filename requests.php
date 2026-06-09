<?php
require_once("Core/router.php");

require_once("Controllers/FileController.php");
require_once("Controllers/UserController.php");

$router = new Router();

$router->get("/files",[FileController::class, "getFiles"]);
$router->get("/users",[UserController::class, "getUsers"]);

$router->post("/files",[FileController::class, "saveFile"]);
$router->post("/users",[UserController::class, "addUsers"]);


$router->resolve();


?>