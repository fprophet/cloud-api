<?php
require_once("Core/router.php");

require_once("Controllers/NodeController.php");
require_once("Controllers/UserController.php");

$router = new Router();

$router->get("/files", [NodeController::class, "getNodes"]);
$router->get("/users", [UserController::class, "getUsers"]);


$router->post("/upload", [NodeController::class, "uploadFiles"]);
$router->post("/files", [NodeController::class, "createDirNode"]);
$router->post("/users", [UserController::class, "addUsers"]);

$router->delete("/files/{id}", [NodeController::class, "deleteNode"]);

$router->patch("/files/{nodeId}", [NodeController::class, "updateNode"]);

$router->resolve();

?>