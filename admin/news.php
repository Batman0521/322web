<?php require_once 'auth.php'; ?>
<?php
$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = trim($conn->real_escape_string($_POST['title'] ?? ''));
    $content   = trim($conn->real_escape_string($_POST['content'] ?? ''));
    $category  = $conn->real_escape_string($_POST['category'] ?? 'Мэдээ');
    $published = isset($_POST['is_published']) ? 1 : 0;
    $id        = (int)($_POST['id'] ?? 0);

    if (!$title || !$content) {
        $err = 'Гарчиг болон агуулга заавал оруулна уу';
    } elseif ($id) {
        $conn->query("UPDATE news SET title='$title', content='$content', category='$category', is_published=$published WHERE id=$id");
        $msg = '"' . $title . '" амжилттай засагдлаа';
    } else {
        $conn->query("INSERT INTO news (title, content, category, is_published) VALUES ('$title','$content','$category',$published)");
        $msg = '"' . $title . '" амжилттай нэмэгдлээ';
    }
}

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM news WHERE id=$did");
    $msg = 'Мэдээ устгагдлаа';
}

$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit = $conn->query("SELECT * FROM news WHERE id=$eid")->fetch_assoc();
}

$filter = $_GET['cat'] ?? '';
$where = $filter ? "WHERE category='" . $conn->real_escape_string($filter) . "'" : '';
$items = [];
$res = $conn->query("SELECT * FROM news $where ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) $items[] = $row;

$categories = ['Мэдээ','Төсөл','Зар','Блог'];
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<title>Мэдээ / Төсөл удирдах</title>
<?php include 'partials/head.php'; ?>
</head>
<body>
<?php include 'partials/sidebar.php'; ?>
<div class="main">
  <?php include 'partials/topbar.php'; ?>
  <div class="content">
    <div class="page-header">
      <h2>Мэдээ / Төсөл удирдах</h2>
    </div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <!-- Form -->
    <div class="card" style="margin-bottom:20px">
      <div class="card-header"><?= $edit ? '✎ Мэдээ засах' : '＋ Шинэ мэдээ / төсөл нэмэх' ?></div>
      <div style="padding:20px">
        <form method="POST">
          <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
          <div class="form-group">
            <label class="form-label">Гарчиг *</label>
            <input class="form-input" name="title" placeholder="Мэдээ эсвэл төслийн гарчиг" value="<?= htmlspecialchars($edit['title'] ?? '') ?>" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Ангилал</label>
              <select class="form-select" name="category">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat ?>" <?= ($edit['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;gap:8px;padding-bottom:1px">
              <input type="checkbox" name="is_published" id="pub" value="1" <?= (!$edit || $edit['is_published']) ? 'checked' : '' ?> style="width:auto">
              <label for="pub" style="margin:0;cursor:pointer;font-size:13px;color:var(--text2)">Нийтлэх</label>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Агуулга *</label>
            <textarea class="form-textarea" name="content" rows="5" placeholder="Мэдээний агуулгыг бичнэ үү..." required><?= htmlspecialchars($edit['content'] ?? '') ?></textarea>
          </div>
          <div style="display:flex;gap:10px">
            <button type="submit" class="btn btn-accent"><?= $edit ? 'Хадгалах' : '＋ Нэмэх' ?></button>
            <?php if ($edit): ?><a href="news.php" class="btn btn-surface">Болих</a><?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <!-- Filter tabs -->
    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
      <a href="news.php" class="btn btn-sm <?= !$filter ? 'btn-accent' : 'btn-surface' ?>">Бүгд</a>
      <?php foreach ($categories as $cat): ?>
        <a href="news.php?cat=<?= urlencode($cat) ?>" class="btn btn-sm <?= $filter===$cat ? 'btn-accent' : 'btn-surface' ?>"><?= $cat ?></a>
      <?php endforeach; ?>
    </div>

    <!-- Table -->
    <div class="table-wrap">
      <?php if ($items): ?>
      <table>
        <thead><tr>
          <th>#</th><th>Гарчиг</th><th>Ангилал</th><th>Агуулга</th><th>Төлөв</th><th>Огноо</th><th>Үйлдэл</th>
        </tr></thead>
        <tbody>
        <?php foreach ($items as $n): ?>
        <tr>
          <td style="color:var(--text3);font-size:12px"><?= $n['id'] ?></td>
          <td style="font-weight:500;max-width:160px"><?= htmlspecialchars($n['title']) ?></td>
          <td><span class="badge badge-accent"><?= $n['category'] ?></span></td>
          <td style="color:var(--text2);font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= htmlspecialchars(mb_substr($n['content'], 0, 60)) ?>...
          </td>
          <td>
            <?= $n['is_published']
              ? '<span class="badge badge-green">● Нийтлэгдсэн</span>'
              : '<span class="badge badge-amber">● Ноорог</span>' ?>
          </td>
          <td style="font-size:12px;color:var(--text3)"><?= date('Y-m-d', strtotime($n['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="news.php?edit=<?= $n['id'] ?>" class="btn btn-sm btn-surface">Засах</a>
              <a href="news.php?delete=<?= $n['id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('<?= htmlspecialchars($n['title']) ?> мэдээг устгах уу?')">Устгах</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <div class="empty-state">Мэдээ байхгүй байна. Шинэ мэдээ нэмнэ үү.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
