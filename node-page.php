<?php
require_once("Services/NodeServices.php");

if(!isset($_GET["user"])){
    die("no user");
}

$service = new NodeService();

$items = $service->getRootForUser($_GET["user"]);

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
  <?php foreach ($items as $item):
    $next = '?node=' . urlencode($item->id);
  ?>
    <a href="<?= $next ?>"><?= htmlspecialchars($item) ?></a>
  <?php endforeach; ?>
</body>
</html>