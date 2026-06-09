<?php
require_once("Services/NodeService.php");
require_once("Services/UserService.php");


if(!isset($_GET["user"])){
    die("no user");
}

$userServices = new UserService();

$user = $userServices->getUser($_GET["user"]);

if($user === null){
    die("user not found!");
}

$service = new NodeService();

if(isset($_POST["new-node"])){
    $service->createRootChildNode($user->root_node_id, $_POST["node-name"]);
}

$items = $service->getRootForUser($user->id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Files</title>
  <style>
    body { font-family: sans-serif; padding: 2rem; }
    a { display: block; padding: 4px 0; text-decoration: none; color: #1a0dab; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
<form action="/cloud-api/user-root.php?user=<?= $user->id ?>" method="POST">
  <input type="text" name="node-name" placeholder="Type here..." />
  <button type="submit" name="new-node">Submit</button>
</form>

  <hr />

  <?php if(count($items) < 1): ?>
    <p>No items</p>
  <?php else: ?>
    <?php foreach ($items as $item): ?>
      <a href="?node=<?= urlencode($item->id) ?>"><?= htmlspecialchars($item->name) ?></a>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>