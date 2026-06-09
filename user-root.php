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
    $service->createDirNode($user->root_node_id, $user->id, $_POST["node-name"]);
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
  <h1>ROOT</h1>
<form action="/cloud-api/user-root.php?user=<?= $user->id ?>" method="POST">
  <input type="text" name="node-name" placeholder="Type here..." />
  <button type="submit" name="new-node">Submit</button>
</form>

    <form action="/cloud-api/upload" method="POST" multiple enctype="multipart/form-data" style="margin-top:20px">
      <label for="file">Choose file</label>
      <input type="file" id="file" name="file[]" required multiple/>
      <input type="text" id="parentId" name="parentId" value="<?= $user->root_node_id?>" hidden/>
      <input type="text" id="userId" name="userId" value="<?= $user->id?>" hidden/>
      <button type="submit">Upload</button>
    </form>
  <hr />

  <?php if(count($items) < 1): ?>
    <p>No items</p>
  <?php else: ?>
    <?php foreach ($items as $item): ?>
      <a href="/cloud-api/node-page.php?node=<?= urlencode($item->id) ?>"><?= htmlspecialchars($item->name) ?></a>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>