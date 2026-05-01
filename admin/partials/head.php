<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --bg: #0f0f12; --surface: #17171c; --surface2: #1e1e26; --surface3: #252530;
    --border: rgba(255,255,255,0.08); --border2: rgba(255,255,255,0.14);
    --accent: #6c63ff; --accent2: #8b85ff;
    --text: #e8e8f0; --text2: #9090a8; --text3: #5c5c70;
    --green: #3ecf8e; --red: #f87171; --amber: #fbbf24; --info: #60a5fa;
  }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }

  /* Sidebar */
  .sidebar {
    width: 230px; flex-shrink: 0; background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column;
    position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;
  }
  .sidebar-header {
    padding: 18px 18px 14px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
  }
  .sidebar-logo {
    width: 30px; height: 30px; border-radius: 6px; background: var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: white; flex-shrink: 0;
  }
  .sidebar-brand { font-size: 14px; font-weight: 600; }
  .sidebar-brand-sub { font-size: 11px; color: var(--text3); }
  .sidebar-nav { padding: 10px; flex: 1; overflow-y: auto; }
  .nav-section { font-size: 10px; font-weight: 600; color: var(--text3);
    text-transform: uppercase; letter-spacing: 0.08em;
    padding: 0 8px; margin: 12px 0 5px; }
  .nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 10px; border-radius: 7px;
    text-decoration: none; font-size: 13px; color: var(--text2);
    margin-bottom: 2px; transition: all 0.15s;
  }
  .nav-item:hover { background: var(--surface2); color: var(--text); }
  .nav-item.active { background: rgba(108,99,255,0.15); color: var(--accent2); }
  .nav-icon { width: 16px; text-align: center; font-size: 14px; }
  .sidebar-footer { padding: 12px; border-top: 1px solid var(--border); }
  .user-row {
    display: flex; align-items: center; gap: 10px; padding: 8px 10px;
    border-radius: 7px;
  }
  .user-av {
    width: 28px; height: 28px; border-radius: 50%; background: var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 600; color: white; flex-shrink: 0;
  }
  .user-name { font-size: 13px; font-weight: 500; flex: 1; }
  .user-role { font-size: 11px; color: var(--text3); }
  .logout-link { font-size: 12px; color: var(--text3); text-decoration: none; padding: 4px; }
  .logout-link:hover { color: var(--red); }

  /* Main */
  .main { margin-left: 230px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
  .topbar {
    background: var(--surface); border-bottom: 1px solid var(--border);
    padding: 0 24px; height: 52px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 50;
  }
  .topbar-title { font-size: 15px; font-weight: 600; }
  .badge { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 500; }
  .badge-green { background: rgba(62,207,142,0.12); color: var(--green); }
  .badge-red { background: rgba(248,113,113,0.12); color: var(--red); }
  .badge-amber { background: rgba(251,191,36,0.12); color: var(--amber); }
  .badge-accent { background: rgba(108,99,255,0.15); color: var(--accent2); }

  .content { padding: 24px; flex: 1; }
  .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
  .page-header h2 { font-size: 18px; font-weight: 600; }

  /* Stats */
  .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 22px; }
  .stat-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 18px;
  }
  .stat-label { font-size: 12px; color: var(--text2); margin-bottom: 8px; }
  .stat-value { font-size: 28px; font-weight: 600; margin-bottom: 4px; }
  .stat-sub { font-size: 12px; color: var(--text3); }

  /* Cards */
  .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
  .card-header {
    padding: 14px 16px; border-bottom: 1px solid var(--border);
    font-size: 13px; font-weight: 600;
    display: flex; align-items: center; justify-content: space-between;
  }
  .card-link { font-size: 12px; color: var(--accent2); text-decoration: none; }
  .card-link:hover { text-decoration: underline; }

  /* Quick buttons */
  .quick-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 12px; border-radius: 8px;
    background: var(--surface2); border: 1px solid var(--border);
    text-decoration: none; color: var(--text);
    transition: border-color 0.15s;
  }
  .quick-btn:hover { border-color: var(--accent); }

  /* List items */
  .list-item {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 16px; border-bottom: 1px solid var(--border);
  }
  .list-item:last-child { border-bottom: none; }
  .list-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    background: var(--accent); display: flex; align-items: center;
    justify-content: center; font-size: 12px; font-weight: 600;
    color: white; flex-shrink: 0; text-transform: uppercase;
  }

  /* Table */
  .table-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  thead { background: var(--surface2); }
  th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600;
    color: var(--text3); text-transform: uppercase; letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border); }
  td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid var(--border); vertical-align: middle; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: rgba(255,255,255,0.02); }

  /* Buttons */
  .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 13px;
    border-radius: 7px; font-size: 13px; font-weight: 500; cursor: pointer;
    border: none; text-decoration: none; transition: all 0.15s; }
  .btn-sm { padding: 5px 10px; font-size: 12px; }
  .btn-accent { background: var(--accent); color: white; }
  .btn-accent:hover { background: var(--accent2); }
  .btn-surface { background: var(--surface2); color: var(--text); border: 1px solid var(--border2); }
  .btn-surface:hover { background: var(--surface3); }
  .btn-danger { background: rgba(248,113,113,0.12); color: var(--red); border: 1px solid rgba(248,113,113,0.2); }
  .btn-danger:hover { background: rgba(248,113,113,0.22); }

  /* Forms */
  .form-group { margin-bottom: 16px; }
  .form-label { display: block; font-size: 13px; color: var(--text2); margin-bottom: 7px; }
  .form-input, .form-select, .form-textarea {
    width: 100%; padding: 10px 13px;
    background: var(--surface2); border: 1px solid var(--border2);
    border-radius: 7px; color: var(--text); font-family: inherit; font-size: 14px;
    outline: none; transition: border-color 0.2s;
  }
  .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--accent); }
  .form-textarea { resize: vertical; min-height: 100px; }
  .form-select { appearance: none; cursor: pointer; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

  /* Alerts */
  .alert { padding: 11px 15px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
  .alert-success { background: rgba(62,207,142,0.1); border: 1px solid rgba(62,207,142,0.3); color: var(--green); }
  .alert-error { background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: var(--red); }

  .empty-state { text-align: center; padding: 40px; color: var(--text3); font-size: 14px; }
</style>
