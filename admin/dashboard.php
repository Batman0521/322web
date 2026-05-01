<?php require_once 'auth.php'; ?>
<?php
$total_menus   = $conn->query("SELECT COUNT(*) c FROM menus")->fetch_assoc()['c'];
$total_news    = $conn->query("SELECT COUNT(*) c FROM news")->fetch_assoc()['c'];
$total_contact = $conn->query("SELECT COUNT(*) c FROM contacts")->fetch_assoc()['c'];
$unread        = $conn->query("SELECT COUNT(*) c FROM contacts WHERE is_read=0")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<title>Admin — Дашбоард</title>
<?php include 'partials/head.php'; ?>
</head>
<body>
<?php include 'partials/sidebar.php'; ?>
<div class="main">
  <?php include 'partials/topbar.php'; ?>
  <div class="content">
    <div class="page-header">
      <h2>Дашбоард</h2>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Нийт цэс</div>
        <div class="stat-value"><?= $total_menus ?></div>
        <div class="stat-sub">Идэвхтэй цэс</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Нийт мэдээ</div>
        <div class="stat-value"><?= $total_news ?></div>
        <div class="stat-sub">Нийтлэгдсэн</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Холбоо барих</div>
        <div class="stat-value"><?= $total_contact ?></div>
        <div class="stat-sub">Нийт мессеж</div>
      </div>
      <div class="stat-card" style="border-color:<?= $unread > 0 ? 'rgba(251,191,36,0.4)' : 'var(--border)' ?>">
        <div class="stat-label">Уншаагүй</div>
        <div class="stat-value" style="color:<?= $unread > 0 ? 'var(--amber)' : 'var(--text)' ?>"><?= $unread ?></div>
        <div class="stat-sub">Шинэ мессеж</div>
      </div>
    </div>

    <div class="grid2">
      <div class="card">
        <div class="card-header">
          <span>Шуурхай үйлдэл</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:16px">
          <a href="menus.php" class="quick-btn">
            <span style="font-size:18px">☰</span>
            <div>
              <div style="font-size:13px;font-weight:600">Цэс нэмэх</div>
              <div style="font-size:11px;color:var(--text3)">Навигаци удирдах</div>
            </div>
          </a>
          <a href="news.php" class="quick-btn">
            <span style="font-size:18px">◉</span>
            <div>
              <div style="font-size:13px;font-weight:600">Мэдээ нэмэх</div>
              <div style="font-size:11px;color:var(--text3)">Агуулга удирдах</div>
            </div>
          </a>
          <a href="contacts.php" class="quick-btn">
            <span style="font-size:18px">✉</span>
            <div>
              <div style="font-size:13px;font-weight:600">Мессежүүд</div>
              <div style="font-size:11px;color:var(--text3)"><?= $unread ?> уншаагүй</div>
            </div>
          </a>
          <a href="../index.php" target="_blank" class="quick-btn">
            <span style="font-size:18px">↗</span>
            <div>
              <div style="font-size:13px;font-weight:600">Сайт харах</div>
              <div style="font-size:11px;color:var(--text3)">Нүүр хуудас</div>
            </div>
          </a>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><span>Сүүлийн мессежүүд</span><a href="contacts.php" class="card-link">Бүгдийг харах</a></div>
        <?php
        $recent = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 4");
        if ($recent && $recent->num_rows > 0):
          while ($c = $recent->fetch_assoc()):
        ?>
        <div class="list-item">
          <div class="list-avatar"><?= mb_substr($c['name'],0,1) ?></div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:500;color:var(--text)"><?= htmlspecialchars($c['name']) ?></div>
            <div style="font-size:12px;color:var(--text3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars(mb_substr($c['message'],0,40)) ?>...</div>
          </div>
          <?php if (!$c['is_read']): ?>
            <span class="badge badge-amber">Шинэ</span>
          <?php endif; ?>
        </div>
        <?php endwhile; else: ?>
        <div class="empty-state">Мессеж байхгүй байна</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
