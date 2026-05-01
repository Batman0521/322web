<?php $current = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">A</div>
    <div>
      <div class="sidebar-brand">AdminCMS</div>
      <div class="sidebar-brand-sub">Портфолио систем</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Ерөнхий</div>
    <a href="dashboard.php" class="nav-item <?= $current==='dashboard.php'?'active':'' ?>">
      <span class="nav-icon">⊞</span> Дашбоард
    </a>
    <div class="nav-section">Удирдах</div>
    <a href="menus.php" class="nav-item <?= $current==='menus.php'?'active':'' ?>">
      <span class="nav-icon">☰</span> Цэс удирдах
    </a>
    <a href="news.php" class="nav-item <?= $current==='news.php'?'active':'' ?>">
      <span class="nav-icon">◉</span> Мэдээ / Төсөл
    </a>
    <a href="contacts.php" class="nav-item <?= $current==='contacts.php'?'active':'' ?>">
      <span class="nav-icon">✉</span> Холбоо барих
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-row">
      <div class="user-av"><?= mb_substr($_SESSION['admin_user'] ?? 'A', 0, 1) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></div>
        <div class="user-role">Системийн эрх</div>
      </div>
      <a href="logout.php" class="logout-link" title="Гарах">✕</a>
    </div>
  </div>
</aside>
