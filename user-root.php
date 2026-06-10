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

$items = $service->getParentWithChildren($user->root_node_id);
$parent = null;

if( count($items) > 0){
  $parent = array_shift($items);
}


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
Show more18:30Claude responded: Wire up the fetch calls in saveEdit and deleteItem to your actual endpoints — I left TODO comments where they go.php<style>
  .list { list-style: none; padding: 0; margin: 1rem 0; }
  .list-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; border: 1px solid #eee; border-radius: 6px; margin-bottom: 0.4rem; }
  .list-item a { flex: 1; text-decoration: none; color: #1a0dab; }
  .list-item a:hover { text-decoration: underline; }
  .list-item input { flex: 1; padding: 0.2rem 0.4rem; font-size: 0.95rem; border: 1px solid #ccc; border-radius: 4px; }
  .btn { padding: 0.25rem 0.6rem; font-size: 0.8rem; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; background: #fff; }
  .btn:hover { background: #f0f0f0; }
  .btn-delete { color: #c00; border-color: #f5c0c0; }
  .btn-delete:hover { background: #fff0f0; }
  .btn-save { color: #090; border-color: #b0deb0; }
  .btn-save:hover { background: #f0fff0; }
</style>
<body>
  <h1><?= $parent !== null ? $parent->name : 'unkwown';?></h1>
<form id="directory">
  <input type="text" name="nodeName" placeholder="Folder name..." />
  <button type="submit">Create</button>
</form>

<form style="margin-top:20px" id="files">
  <label for="file">Choose file</label>
  <input type="file" id="file" name="file[]" required multiple />
  <button type="submit">Upload</button>
</form>
<hr/>

<?php if(count($items) < 1): ?>
  <p>No items</p>
<?php else: ?>
  <ul class="list">
    <?php foreach ($items as $item): ?>
      <li class="list-item" data-id="<?= $item->id ?>">
        <a href="/cloud-api/node-page.php?node=<?= urlencode($item->id) ?>"><?= htmlspecialchars($item->name) ?></a>
        <input type="text" value="<?= htmlspecialchars($item->name) ?>" style="display:none" />
        <button class="btn" onclick="startEdit(this)">rename</button>
        <button class="btn btn-save" onclick="saveEdit(this)" style="display:none">save</button>
        <button class="btn" onclick="cancelEdit(this)" style="display:none">cancel</button>
        <button class="btn btn-delete" onclick="deleteItem(this)">delete</button>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<script>
  function startEdit(btn) {
    const li = btn.closest('.list-item');
    li.querySelector('a').style.display = 'none';
    li.querySelector('input').style.display = '';
    li.querySelector('input').focus();
    btn.style.display = 'none';
    li.querySelector('.btn-save').style.display = '';
    li.querySelectorAll('.btn')[2].style.display = ''; // cancel
  }

  function cancelEdit(btn) {
    const li = btn.closest('.list-item');
    const a = li.querySelector('a');
    li.querySelector('input').value = a.textContent;
    li.querySelector('input').style.display = 'none';
    a.style.display = '';
    li.querySelector('.btn-save').style.display = 'none';
    btn.style.display = 'none';
    li.querySelectorAll('.btn')[0].style.display = ''; // rename
  }

  function saveEdit(btn) {
    const li = btn.closest('.list-item');
    const input = li.querySelector('input');
    const newName = input.value.trim();
    if (!newName) return;

    const id = li.dataset.id;
    // TODO: call your update endpoint
    fetch(`/cloud-api/files/${id}`, { method: 'PATCH', body: JSON.stringify({ name: newName }) })

    li.querySelector('a').textContent = newName;
    cancelEdit(btn);
  }

  function deleteItem(btn) {
    const li = btn.closest('.list-item');
    const id = li.dataset.id;
    if (!confirm('Delete this item?')) return;

    // TODO: call your delete endpoint
    fetch(`/cloud-api/files/${id}`, { method: 'DELETE' })

    li.remove();
  }

document.querySelector('#directory').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.querySelector('[name="nodeName"]').value.trim();
    const parent_id = <?= $parent->id ?>;
    const user_id   = <?= $parent->user_id ?>;

    if (!name) return;

    fetch('/cloud-api/files', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, parent_id, user_id })
    })
    .then(res => res.json())
    .then(data => console.log(data))
    .catch(err => console.error(err));
});
  
  document.querySelector('#files').addEventListener('submit', function(e) {
    e.preventDefault();

    const files    = document.querySelector('#file').files;
    const parentId = <?= $parent->id ?>;
    const userId   = <?= $parent->user_id ?>;

    if (!files.length) return;

    const formData = new FormData();
    formData.append('parentId', parentId);
    formData.append('userId', userId);

    Array.from(files).forEach(file => {
        formData.append('file[]', file);
    });

    fetch('/cloud-api/upload', {
        method: 'POST',
        body: formData  
    })
    .then(res => res.json())
    .then(data => console.log(data))
    .catch(err => console.error(err));
});
</script>
</body>
</html>