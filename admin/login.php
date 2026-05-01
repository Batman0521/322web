<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
require_once '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_user'] = $username;
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = 'Нэвтрэх нэр эсвэл нууц үг буруу байна';
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Нэвтрэх</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0f0f12; --surface: #17171c; --surface2: #1e1e26;
    --border: rgba(255,255,255,0.08); --border2: rgba(255,255,255,0.14);
    --accent: #6c63ff; --accent2: #8b85ff;
    --text: #e8e8f0; --text2: #9090a8; --text3: #5c5c70;
    --red: #f87171; --font: 'Segoe UI', Arial, sans-serif;
  }
  body { font-family: var(--font); background: var(--bg); color: var(--text);
    min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .card {
    width: 400px; background: var(--surface);
    border: 1px solid var(--border2); border-radius: 16px; padding: 40px;
  }
  .logo { display: flex; align-items: center; gap: 10px; margin-bottom: 32px; }
  .logo-icon {
    width: 36px; height: 36px; border-radius: 8px; background: var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 700; color: white;
  }
  .logo-name { font-size: 16px; font-weight: 600; }
  .logo-sub { font-size: 12px; color: var(--text3); }
  h1 { font-size: 22px; font-weight: 600; margin-bottom: 6px; }
  .sub { font-size: 14px; color: var(--text2); margin-bottom: 28px; }
  .hint {
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 8px; padding: 10px 14px;
    font-size: 12px; color: var(--text3); margin-bottom: 20px; line-height: 1.6;
  }
  .hint code { color: var(--text2); background: rgba(255,255,255,0.06); padding: 1px 5px; border-radius: 4px; }
  .group { margin-bottom: 16px; }
  label { display: block; font-size: 13px; color: var(--text2); margin-bottom: 7px; }
  input {
    width: 100%; padding: 11px 14px;
    background: var(--surface2); border: 1px solid var(--border2);
    border-radius: 8px; color: var(--text); font-size: 14px;
    font-family: var(--font); outline: none; transition: border-color 0.2s;
  }
  input:focus { border-color: var(--accent); }
  .error {
    background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3);
    border-radius: 8px; padding: 10px 14px;
    font-size: 13px; color: var(--red); margin-bottom: 16px;
  }
  button {
    width: 100%; padding: 12px; background: var(--accent); color: white;
    border: none; border-radius: 8px; font-size: 14px; font-weight: 600;
    cursor: pointer; margin-top: 4px; transition: background 0.2s;
  }
  button:hover { background: var(--accent2); }
  .back { text-align: center; margin-top: 16px; }
  .back a { font-size: 13px; color: var(--text3); text-decoration: none; }
  .back a:hover { color: var(--text2); }
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">A</div>
    <div>
      <div class="logo-name">AdminCMS</div>
      <div class="logo-sub">Удирдлагын систем</div>
    </div>
  </div>
  <h1>Нэвтрэх</h1>
  <p class="sub">Системд нэвтрэхийн тулд мэдээллээ оруулна уу</p>
  <div class="hint">
    Нэвтрэх нэр: <code>admin</code> &nbsp;|&nbsp; Нууц үг: <code>admin123</code>
  </div>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="group">
      <label>Нэвтрэх нэр</label>
      <input type="text" name="username" value="admin" required>
    </div>
    <div class="group" style="margin-bottom:20px">
      <label>Нууц үг</label>
      <input type="password" name="password" value="admin123" required>
    </div>
    <button type="submit">Нэвтрэх →</button>
  </form>
  <div class="back"><a href="../index.php">← Нүүр хуудас руу буцах</a></div>
</div>
</body>
</html>
