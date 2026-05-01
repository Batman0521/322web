<?php
require_once 'config/db.php';

// Fetch active menus
$menus = [];
$res = $conn->query("SELECT * FROM menus WHERE is_active=1 ORDER BY sort_order ASC");
if ($res) while ($row = $res->fetch_assoc()) $menus[] = $row;

// Fetch published projects/news
$projects = [];
$res = $conn->query("SELECT * FROM news WHERE is_published=1 AND category='Төсөл' ORDER BY created_at DESC");
if ($res) while ($row = $res->fetch_assoc()) $projects[] = $row;

$news_list = [];
$res = $conn->query("SELECT * FROM news WHERE is_published=1 AND category='Мэдээ' ORDER BY created_at DESC LIMIT 3");
if ($res) while ($row = $res->fetch_assoc()) $news_list[] = $row;

// Handle contact form
$contact_success = false;
$contact_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($conn->real_escape_string($_POST['name'] ?? ''));
    $email   = trim($conn->real_escape_string($_POST['email'] ?? ''));
    $message = trim($conn->real_escape_string($_POST['message'] ?? ''));
    if ($name && $email && $message) {
        $conn->query("INSERT INTO contacts (name, email, message) VALUES ('$name','$email','$message')");
        $contact_success = true;
    } else {
        $contact_error = 'Бүх талбарыг бөглөнө үү.';
    }
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Миний Портфолио</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:      #0d1b2a;
    --bg2:     #152238;
    --bg3:     #1a2d45;
    --card:    #1e3451;
    --gold:    #ffd700;
    --gold2:   #ffdd33;
    --text:    #e8eaf0;
    --text2:   #9fb3c8;
    --border:  rgba(255,215,0,0.25);
  }

  html { scroll-behavior: smooth; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); }

  /* NAV */
  nav {
    background: var(--bg2);
    border-bottom: 1px solid var(--border);
    position: sticky; top: 0; z-index: 100;
    padding: 0 40px;
    display: flex; justify-content: center; align-items: center;
    height: 52px; gap: 40px;
  }
  nav a {
    color: var(--gold); font-weight: 700; font-size: 14px;
    text-decoration: none; letter-spacing: 0.03em;
    transition: opacity 0.2s;
  }
  nav a:hover { opacity: 0.75; }

  /* HERO */
  .hero {
    background: var(--bg2);
    text-align: center;
    padding: 80px 40px;
  }
  .hero h1 {
    font-size: 42px; font-weight: 800;
    color: var(--gold); margin-bottom: 16px;
    letter-spacing: -0.01em;
  }
  .hero p { font-size: 16px; color: var(--text2); max-width: 500px; margin: 0 auto; }

  /* SECTIONS */
  section { padding: 60px 40px; background: var(--bg2); margin-bottom: 8px; }
  section h2 {
    text-align: center; font-size: 20px; font-weight: 700;
    color: var(--text); margin-bottom: 36px;
  }

  /* SKILLS */
  .skills-grid {
    display: flex; flex-wrap: wrap;
    justify-content: center; gap: 16px;
    max-width: 900px; margin: 0 auto;
  }
  .skill-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 28px 36px;
    font-size: 15px; font-weight: 700;
    color: var(--gold);
    min-width: 120px; text-align: center;
    transition: transform 0.2s, border-color 0.2s;
  }
  .skill-card:hover { transform: translateY(-3px); border-color: var(--gold); }

  /* PROJECTS */
  .projects-list {
    max-width: 860px; margin: 0 auto;
    display: flex; flex-direction: column; gap: 16px;
  }
  .project-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 28px 32px;
    text-align: center;
    transition: border-color 0.2s;
  }
  .project-card:hover { border-color: var(--gold); }
  .project-card h3 { color: var(--gold); font-size: 16px; font-weight: 700; margin-bottom: 8px; }
  .project-card p { color: var(--text2); font-size: 14px; line-height: 1.6; }
  .project-card .tag {
    display: inline-block; margin-top: 10px;
    background: rgba(255,215,0,0.12); color: var(--gold);
    font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px;
    border: 1px solid var(--border);
  }

  /* NEWS */
  .news-grid {
    max-width: 900px; margin: 0 auto;
    display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;
  }
  .news-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 10px; padding: 22px;
    transition: border-color 0.2s;
  }
  .news-card:hover { border-color: var(--gold); }
  .news-card h3 { color: var(--gold); font-size: 14px; font-weight: 700; margin-bottom: 8px; }
  .news-card p { color: var(--text2); font-size: 13px; line-height: 1.6; }
  .news-card .date { font-size: 11px; color: #5a7a9a; margin-top: 10px; }

  /* CONTACT */
  .contact-form {
    max-width: 520px; margin: 0 auto;
    display: flex; flex-direction: column; gap: 14px;
  }
  .contact-form label { font-size: 14px; color: var(--text2); margin-bottom: 4px; display: block; }
  .contact-form input,
  .contact-form textarea {
    width: 100%; padding: 13px 16px;
    background: transparent;
    border: 1.5px solid var(--gold);
    border-radius: 8px; color: var(--text);
    font-size: 14px; font-family: inherit; outline: none;
    transition: border-color 0.2s;
  }
  .contact-form input:focus,
  .contact-form textarea:focus { border-color: var(--gold2); }
  .contact-form textarea { min-height: 110px; resize: vertical; }
  .contact-form button {
    padding: 14px; background: var(--gold); color: #0d1b2a;
    border: none; border-radius: 8px;
    font-size: 15px; font-weight: 800;
    cursor: pointer; letter-spacing: 0.05em;
    transition: background 0.2s;
  }
  .contact-form button:hover { background: var(--gold2); }

  .alert-success {
    background: rgba(62,207,142,0.12); border: 1px solid rgba(62,207,142,0.4);
    color: #3ecf8e; padding: 12px 16px; border-radius: 8px;
    font-size: 14px; text-align: center;
  }
  .alert-error {
    background: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.3);
    color: #f87171; padding: 12px 16px; border-radius: 8px;
    font-size: 14px; text-align: center;
  }

  /* FOOTER */
  footer {
    background: var(--bg2);
    border-top: 1px solid var(--border);
    text-align: center; padding: 20px;
    font-size: 13px; color: var(--gold);
  }

  /* EMPTY STATE */
  .empty { text-align: center; color: var(--text2); padding: 32px; font-size: 14px; }
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <?php if ($menus): ?>
    <?php foreach ($menus as $m): ?>
      <a href="<?= htmlspecialchars($m['url']) ?>"><?= htmlspecialchars($m['name']) ?></a>
    <?php endforeach; ?>
  <?php else: ?>
    <a href="#skills">Миний ур чадвар</a>
    <a href="#projects">Бүтээсэн төсөл</a>
    <a href="#contact">Холбоо барих</a>
  <?php endif; ?>
</nav>

<!-- HERO -->
<div class="hero">
  <h1>Сайн уу! [Таны нэр]</h1>
  <p>Таны ур чадвар, төслүүдийг танилцуулах миний вэб сайт.</p>
</div>

<!-- SKILLS -->
<section id="skills">
  <h2>Миний Ур Чадварууд</h2>
  <div class="skills-grid">
    <?php
    $skills = ['HTML','CSS','JavaScript','React','Tailwind CSS','PHP'];
    foreach ($skills as $s): ?>
      <div class="skill-card"><?= $s ?></div>
    <?php endforeach; ?>
  </div>
</section>

<!-- PROJECTS -->
<section id="projects">
  <h2>Бүтээсэн төсөл</h2>
  <div class="projects-list">
    <?php if ($projects): ?>
      <?php foreach ($projects as $p): ?>
        <div class="project-card">
          <h3><?= htmlspecialchars($p['title']) ?></h3>
          <p><?= htmlspecialchars($p['content']) ?></p>
          <span class="tag"><?= htmlspecialchars($p['category']) ?></span>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty">Одоогоор төсөл байхгүй байна</div>
    <?php endif; ?>
  </div>
</section>

<!-- NEWS -->
<?php if ($news_list): ?>
<section id="news">
  <h2>Сүүлийн мэдээ</h2>
  <div class="news-grid">
    <?php foreach ($news_list as $n): ?>
      <div class="news-card">
        <h3><?= htmlspecialchars($n['title']) ?></h3>
        <p><?= htmlspecialchars(mb_substr($n['content'], 0, 100)) ?>...</p>
        <div class="date"><?= date('Y-m-d', strtotime($n['created_at'])) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- CONTACT -->
<section id="contact">
  <h2>Холбоо барих</h2>
  <?php if ($contact_success): ?>
    <div class="alert-success" style="max-width:520px;margin:0 auto 20px">Таны мессеж амжилттай илгээгдлээ!</div>
  <?php endif; ?>
  <?php if ($contact_error): ?>
    <div class="alert-error" style="max-width:520px;margin:0 auto 20px"><?= $contact_error ?></div>
  <?php endif; ?>
  <form class="contact-form" method="POST">
    <div>
      <label>Таны нэр:</label>
      <input type="text" name="name" placeholder="" required>
    </div>
    <div>
      <label>И-мэйл хаяг:</label>
      <input type="email" name="email" placeholder="" required>
    </div>
    <div>
      <label>Танилцуулга:</label>
      <textarea name="message" required></textarea>
    </div>
    <button type="submit" name="contact_submit">ИЛГЭЭХ</button>
  </form>
</section>

<!-- FOOTER -->
<footer>© 2025 танилцуулга вэб</footer>

</body>
</html>
