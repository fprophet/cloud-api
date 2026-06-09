<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create user</title>
  <style>
    body { font-family: sans-serif; display: flex; justify-content: center; padding: 3rem; background: #f5f5f5; }
    .box { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 2rem; width: 100%; max-width: 420px; }
    h2 { margin: 0 0 1.5rem; font-size: 1.1rem; }
    .field { margin-bottom: 1.1rem; }
    label { display: block; font-size: 0.85rem; color: #555; margin-bottom: 0.3rem; }
    input, select { width: 100%; padding: 0.5rem 0.7rem; border: 1px solid #ccc; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; }
    input:focus, select:focus { outline: none; border-color: #888; }
    button { width: 100%; background: #111; color: #fff; border: none; border-radius: 6px; padding: 0.65rem; font-size: 0.95rem; cursor: pointer; margin-top: 0.5rem; }
    button:hover { background: #333; }
  </style>
</head>
<body>
  <div class="box">
    <h2>Create user</h2>
    <form action="/cloud-api/users" method="POST">
      <div class="field">
        <label for="name">Full name</label>
        <input type="text" id="name" name="name" placeholder="John Doe" required />
      </div>
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="john@example.com" required />
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>

      <button type="submit">Create user</button>
    </form>
  </div>
</body>
</html>