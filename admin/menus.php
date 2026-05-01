<?php require_once 'auth.php'; ?>
<?php
$msg = $err = '';

// CREATE / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($conn->real_escape_string($_POST['name'] ?? ''));
    $url    = trim($conn->real_escape_string($_POST['url'] ?? ''));
    $order  = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $id     = (int)($_POST['id'] ?? 0);

    if (!$name || !$url) {
        $err = 'Нэр болон URL заавал оруулна уу';
    } elseif ($id) {
        $conn->query("UPDATE menus SET name='$name', url='$url', sort_order=$order, is_active=$active WHERE id=$id");
        $msg = '"' . $name . '" цэс амжилттай засагдлаа';
    } else {
        $conn->query("INSERT INTO menus (name, url, sort_order, is_active) VALUES ('$name','$url',$order,$active)");
        $msg = '"' . $name . '" цэс амжилттай нэмэгдлээ';
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM menus WHERE id=$did");
    $msg = 'Цэс устгагдлаа';
}

// EDIT prefill
$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit = $conn->query("SELECT * FROM menus WHERE id=$eid")->fetch_assoc();
}

$menus = [];
$res = $conn->query("SELECT * FROM menus ORDER BY sort_order ASC");
while ($row = $res->fetch_assoc()) $menus[] = $row;
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<title>Цэс удирдах</title>
<?php include 'partials/head.php'; ?>
</head>
<body>
<?php include 'partials/sidebar.php'; ?>
<div class="main">
  <?php include 'partials/topbar.php'; ?>
  <div class="content">
    <div class="page-header">
      <h2>Цэс удирдах</h2>
    </div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <div class="grid2" style="align-items:start">

      <!-- Form -->
      <div class="card">
        <div class="card-header"><?= $edit ? '✎ Цэс засах' : '＋ Шинэ цэс нэмэх' ?></div>
        <div style="padding:20px">
          <form method="POST">
            <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
            <div class="form-group">
              <label class="form-label">Цэсийн нэр *</label>
              <input class="form-input" name="name" placeholder="Жишээ: Нүүр хуудас" value="<?= htmlspecialchars($edit['name'] ?? '') ?>" required>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">URL *</label>
                <input class="form-input" name="url" placeholder="#skills" value="<?= htmlspecialchars($edit['url'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Дараалал</label>
                <input class="form-input" type="number" name="sort_order" min="0" value="<?= $edit['sort_order'] ?? count($menus)+1 ?>">
              </div>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:8px">
              <input type="checkbox" name="is_active" id="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?> style="width:auto">
              <label for="is_active" style="margin:0;cursor:pointer">Идэвхтэй</label>
            </div>
            <div style="display:flex;gap:10px">
              <button type="submit" class="btn btn-accent"><?= $edit ? 'Хадгалах' : '＋ Нэмэх' ?></button>
              <?php if ($edit): ?><a href="menus.php" class="btn btn-surface">Болих</a><?php endif; ?>
            </div>
          </form>
        </div>
      </div>

      <!-- Table -->
      <div>
        <div class="table-wrap">
          <?php if ($menus): ?>
          <table>
            <thead><tr>
              <th>#</th><th>Нэр</th><th>URL</th><th>Дараалал</th><th>Төлөв</th><th>Үйлдэл</th>
            </tr></thead>
            <tbody>
            <?php foreach ($menus as $m): ?>
            <tr>
              <td style="color:var(--text3);font-size:12px"><?= $m['id'] ?></td>
              <td style="font-weight:500"><?= htmlspecialchars($m['name']) ?></td>
              <td style="color:var(--info);font-size:12px"><?= htmlspecialchars($m['url']) ?></td>
              <td><?= $m['sort_order'] ?></td>
              <td>
                <?= $m['is_active']
                  ? '<span class="badge badge-green">● Идэвхтэй</span>'
                  : '<span class="badge badge-red">● Идэвхгүй</span>' ?>
              </td>
              <td>
                <div style="display:flex;gap:6px">
                  <a href="menus.php?edit=<?= $m['id'] ?>" class="btn btn-sm btn-surface">Засах</a>
                  <a href="menus.php?delete=<?= $m['id'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('<?= htmlspecialchars($m['name']) ?> цэсийг устгах уу?')">Устгах</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?>
            <div class="empty-state">Цэс байхгүй байна. Шинэ цэс нэмнэ үү.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
