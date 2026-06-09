<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Upload</title>
  <style>
    body { font-family: sans-serif; display: flex; justify-content: center; padding: 3rem; background: #f5f5f5; }
    .box { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 2rem; width: 100%; max-width: 400px; }
    h2 { margin: 0 0 1.5rem; font-size: 1.1rem; }
    label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 0.4rem; }
    input[type="file"] { display: block; width: 100%; margin-bottom: 1.25rem; }
    button { background: #111; color: #fff; border: none; border-radius: 6px; padding: 0.6rem 1.25rem; cursor: pointer; font-size: 0.9rem; }
    button:hover { background: #333; }
  </style>
</head>
<body>
  <div class="box">
    <h2>Upload a file</h2>
    <form action="/cloud-api/files" method="POST" multiple enctype="multipart/form-data">
      <label for="file">Choose file</label>
      <input type="file" id="file" name="file[]" required multiple/>
      <button type="submit">Upload</button>
    </form>
  </div>
</body>
</html>