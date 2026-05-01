<?php
$titles = [
  'dashboard.php' => 'Дашбоард',
  'menus.php'     => 'Цэс удирдах',
  'news.php'      => 'Мэдээ / Төсөл удирдах',
  'contacts.php'  => 'Холбоо барих мессежүүд',
];
$title = $titles[basename($_SERVER['PHP_SELF'])] ?? 'Admin';
?>
<div class="topbar">
  <div class="topbar-title"><?= $title ?></div>
  <div style="display:flex;align-items:center;gap:10px">
    <span class="badge badge-green">● Онлайн</span>
    <a href="../index.php" target="_blank" class="btn btn-surface btn-sm">↗ Сайт харах</a>
  </div>
</div>
