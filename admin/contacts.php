<?php require_once 'auth.php'; ?>
<?php
$msg = '';

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM contacts WHERE id=$did");
    $msg = 'Мессеж устгагдлаа';
}

if (isset($_GET['read'])) {
    $rid = (int)$_GET['read'];
    $conn->query("UPDATE contacts SET is_read=1 WHERE id=$rid");
}

// Mark all read
if (isset($_GET['readall'])) {
    $conn->query("UPDATE contacts SET is_read=1");
    $msg = 'Бүх мессеж уншсан гэж тэмдэглэгдлээ';
}

$filter = $_GET['filter'] ?? '';
$where = $filter === 'unread' ? 'WHERE is_read=0' : '';
$contacts = [];
$res = $conn->query("SELECT * FROM contacts $where ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) $contacts[] = $row;

$unread = $conn->query("SELECT COUNT(*) c FROM contacts WHERE is_read=0")->fetch_assoc()['c'];
$total  = $conn->query("SELECT COUNT(*) c FROM contacts")->fetch_assoc()['c'];

// View single
$view = null;
if (isset($_GET['view'])) {
    $vid = (int)$_GET['view'];
    $view = $conn->query("SELECT * FROM contacts WHERE id=$vid")->fetch_assoc();
    if ($view) $conn->query("UPDATE contacts SET is_read=1 WHERE id=$vid");
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<title>Холбоо барих мессежүүд</title>
<?php include 'partials/head.php'; ?>
<style>
  .msg-detail {
    background: var(--surface2); border: 1px solid var(--border2);
    border-radius: 10px; padding: 24px; margin-bottom: 20px;
  }
  .msg-detail .from { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
  .msg-detail .email { font-size: 13px; color: var(--info); margin-bottom: 16px; }
  .msg-detail .body { font-size: 14px; color: var(--text2); line-height: 1.7; }
  .msg-detail .date { font-size: 12px; color: var(--text3); margin-top: 14px; }
</style>
</head>
<body>
<?php include 'partials/sidebar.php'; ?>
<div class="main">
  <?php include 'partials/topbar.php'; ?>
  <div class="content">
    <div class="page-header">
      <h2>Холбоо барих мессежүүд</h2>
      <div style="display:flex;gap:8px;align-items:center">
        <?php if ($unread > 0): ?>
          <span class="badge badge-amber"><?= $unread ?> уншаагүй</span>
          <a href="contacts.php?readall=1" class="btn btn-sm btn-surface">Бүгдийг уншсан болгох</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <?php if ($view): ?>
    <!-- Detail view -->
    <div class="msg-detail">
      <div class="from"><?= htmlspecialchars($view['name']) ?></div>
      <div class="email">✉ <?= htmlspecialchars($view['email']) ?></div>
      <div class="body"><?= nl2br(htmlspecialchars($view['message'])) ?></div>
      <div class="date"><?= date('Y-m-d H:i', strtotime($view['created_at'])) ?></div>
      <div style="margin-top:16px;display:flex;gap:8px">
        <a href="contacts.php" class="btn btn-surface btn-sm">← Буцах</a>
        <a href="contacts.php?delete=<?= $view['id'] ?>" class="btn btn-sm btn-danger"
           onclick="return confirm('Энэ мессежийг устгах уу?')">Устгах</a>
      </div>
    </div>
    <?php endif; ?>

    <!-- Filter -->
    <div style="display:flex;gap:8px;margin-bottom:14px">
      <a href="contacts.php" class="btn btn-sm <?= !$filter ? 'btn-accent' : 'btn-surface' ?>">Бүгд (<?= $total ?>)</a>
      <a href="contacts.php?filter=unread" class="btn btn-sm <?= $filter==='unread' ? 'btn-accent' : 'btn-surface' ?>">Уншаагүй (<?= $unread ?>)</a>
    </div>

    <!-- Table -->
    <div class="table-wrap">
      <?php if ($contacts): ?>
      <table>
        <thead><tr>
          <th>#</th><th>Нэр</th><th>И-мэйл</th><th>Мессеж</th><th>Огноо</th><th>Төлөв</th><th>Үйлдэл</th>
        </tr></thead>
        <tbody>
        <?php foreach ($contacts as $c): ?>
        <tr style="<?= !$c['is_read'] ? 'background:rgba(108,99,255,0.04)' : '' ?>">
          <td style="color:var(--text3);font-size:12px"><?= $c['id'] ?></td>
          <td style="font-weight:<?= !$c['is_read'] ? '600' : '400' ?>"><?= htmlspecialchars($c['name']) ?></td>
          <td style="color:var(--info);font-size:12px"><?= htmlspecialchars($c['email']) ?></td>
          <td style="color:var(--text2);font-size:12px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= htmlspecialchars(mb_substr($c['message'], 0, 60)) ?>...
          </td>
          <td style="font-size:12px;color:var(--text3)"><?= date('m-d H:i', strtotime($c['created_at'])) ?></td>
          <td>
            <?= !$c['is_read']
              ? '<span class="badge badge-amber">● Шинэ</span>'
              : '<span class="badge" style="color:var(--text3)">Уншсан</span>' ?>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="contacts.php?view=<?= $c['id'] ?>" class="btn btn-sm btn-surface">Харах</a>
              <a href="contacts.php?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Устгах уу?')">Устгах</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <div class="empty-state">Мессеж байхгүй байна</div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
