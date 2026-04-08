<?php
require_once __DIR__ . '/auth_check.php';
require_auth();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>13Partners Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
      :root {
        --bg: #0a0e27; --bg2: #111638; --card: #161b42;
        --accent: #00d4ff; --accent2: #7c3aed;
        --grad: linear-gradient(135deg, #00d4ff, #7c3aed);
        --text: #ffffff; --text2: #a0a3bd;
        --border: rgba(255,255,255,0.08);
        --green: #22c55e; --red: #ef4444; --yellow: #f59e0b;
      }
      body { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; padding: 20px; }
      .dash-container { max-width: 1400px; margin: 0 auto; }
      .dash-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
      .dash-title { font-size: 28px; font-weight: 700; background: var(--grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
      .dash-subtitle { color: var(--text2); font-size: 13px; margin-top: 4px; }
      .dash-nav { display: flex; align-items: center; gap: 16px; }
      .dash-link { color: var(--accent); text-decoration: none; font-size: 14px; font-weight: 500; }
      .dash-link:hover { text-decoration: underline; }
      .btn-logout { padding: 8px 18px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25); color: #ef4444; font-size: 13px; font-weight: 600; font-family: inherit; border-radius: 8px; cursor: pointer; text-decoration: none; transition: background 0.2s; }
      .btn-logout:hover { background: rgba(239,68,68,0.2); }
      .admin-badge { color: var(--text2); font-size: 13px; }

      .tabs { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
      .tab-btn { padding: 10px 28px; background: var(--card); border: 1px solid var(--border); color: var(--text2); font-size: 14px; font-weight: 500; font-family: inherit; cursor: pointer; border-radius: 8px; transition: all 0.2s; }
      .tab-btn:hover { background: var(--bg2); color: var(--text); }
      .tab-btn.active { background: var(--grad); color: #fff; border-color: transparent; }

      .controls { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; align-items: center; }
      .control-label { color: var(--text2); font-size: 13px; font-weight: 500; }
      .date-input, .select-input { padding: 10px 14px; background: var(--card); border: 1px solid var(--border); color: var(--text); border-radius: 8px; font-size: 13px; font-family: inherit; outline: none; transition: border 0.2s; }
      .date-input:focus, .select-input:focus { border-color: var(--accent); }
      .btn-refresh { padding: 10px 24px; background: var(--grad); border: none; color: #fff; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; border-radius: 8px; transition: opacity 0.2s, transform 0.2s; }
      .btn-refresh:hover { opacity: 0.9; transform: translateY(-1px); }
      .btn-refresh:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

      .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
      .stat-card { background: var(--card); border: 1px solid var(--border); padding: 20px 24px; border-radius: 12px; text-align: center; }
      .stat-card .num { font-size: 32px; font-weight: 700; color: var(--accent); }
      .stat-card .lbl { color: var(--text2); font-size: 12px; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

      .tab-pane { display: none; }
      .tab-pane.active { display: block; }

      .table-wrap { background: var(--card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
      .table-scroll { overflow-x: auto; }
      table { width: 100%; border-collapse: collapse; min-width: 700px; }
      th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); font-size: 13px; }
      th { background: rgba(255,255,255,0.02); color: var(--accent); font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; white-space: nowrap; }
      tr:hover td { background: rgba(255,255,255,0.02); }
      td { color: var(--text2); }

      .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
      .badge-new { background: rgba(0,212,255,0.15); color: var(--accent); }
      .badge-ftd { background: rgba(34,197,94,0.15); color: var(--green); }
      .badge-status { background: rgba(245,158,11,0.15); color: var(--yellow); }
      .badge-reject { background: rgba(239,68,68,0.15); color: var(--red); }

      .empty-state { text-align: center; padding: 60px 20px; color: var(--text2); }
      .empty-state .icon { font-size: 40px; margin-bottom: 12px; }
      .loading-state { text-align: center; padding: 60px 20px; color: var(--text2); }
      .spinner { width: 36px; height: 36px; border: 3px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin 0.7s linear infinite; margin: 0 auto 12px; }
      @keyframes spin { to { transform: rotate(360deg); } }

      .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; justify-content: center; align-items: center; padding: 20px; }
      .modal-overlay.active { display: flex; }
      .modal-box { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 28px; max-width: 500px; width: 100%; max-height: 80vh; overflow-y: auto; }
      .modal-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
      .modal-top h2 { font-size: 18px; color: var(--accent); }
      .modal-close { background: none; border: none; color: var(--text2); font-size: 22px; cursor: pointer; padding: 4px 8px; }
      .modal-close:hover { color: #fff; }
      .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
      .detail-row:last-child { border-bottom: none; }
      .detail-key { color: var(--text2); }
      .detail-val { color: #fff; font-weight: 500; text-align: right; word-break: break-all; }

      .pagination { display: flex; gap: 8px; margin-top: 16px; align-items: center; justify-content: center; }
      .page-btn { padding: 8px 16px; background: var(--card); border: 1px solid var(--border); color: var(--text2); font-size: 13px; font-family: inherit; cursor: pointer; border-radius: 6px; transition: all 0.2s; }
      .page-btn:hover { background: var(--bg2); color: var(--text); }
      .page-btn.active { background: var(--grad); color: #fff; border-color: transparent; }
      .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
      .page-info { color: var(--text2); font-size: 13px; }

      .settings-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 32px; max-width: 500px; }
      .settings-title { font-size: 20px; font-weight: 700; color: var(--accent); margin-bottom: 6px; }
      .settings-desc { color: var(--text2); font-size: 13px; margin-bottom: 24px; }
      .settings-card .form-group { margin-bottom: 16px; }
      .settings-card .form-group label { display: block; font-size: 13px; font-weight: 500; color: var(--text2); margin-bottom: 6px; }
      .settings-card .form-group input { width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 8px; color: #fff; font-size: 14px; font-family: inherit; outline: none; transition: border 0.2s; }
      .settings-card .form-group input:focus { border-color: var(--accent); }
      .settings-card .form-group input::placeholder { color: #555e80; }
      .password-wrap { position: relative; }
      .password-wrap input { padding-right: 60px; }
      .toggle-pw { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--accent); font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; }
      .settings-msg { padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; text-align: center; }
      .settings-msg.success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: var(--green); }
      .settings-msg.error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: var(--red); }
    </style>
    <script src="/navbar.js" defer></script>
  </head>
  <body>
    <div class="dash-container">
      <div class="dash-header">
        <div>
          <h1 class="dash-title">13Partners Leads</h1>
          <p class="dash-subtitle">Source: QuakeAI &bull; User ID: 1021 &bull; CRM: crm.13partners.net</p>
        </div>
        <div class="dash-nav">
          <span class="admin-badge">Logged in as <strong><?= htmlspecialchars($_SESSION['partners13_admin_user'] ?? 'admin') ?></strong></span>
          <a href="index.html" class="dash-link">&larr; Landing Page</a>
          <a href="login.php?logout=1" class="btn-logout">Logout</a>
        </div>
      </div>

      <div class="tabs">
        <button class="tab-btn active" data-tab="all">All Leads</button>
        <button class="tab-btn" data-tab="new">New</button>
        <button class="tab-btn" data-tab="ftd">FTD</button>
        <button class="tab-btn" data-tab="rejected">Rejected</button>
        <button class="tab-btn" data-tab="settings">Settings</button>
      </div>

      <div class="controls">
        <span class="control-label">From:</span>
        <input type="date" class="date-input" id="dateStart" />
        <span class="control-label">To:</span>
        <input type="date" class="date-input" id="dateEnd" />
        <button class="btn-refresh" id="refreshBtn">Refresh</button>
      </div>

      <div class="stats">
        <div class="stat-card"><div class="num" id="totalCount">-</div><div class="lbl">Total Leads</div></div>
        <div class="stat-card"><div class="num" id="newCount">-</div><div class="lbl">New</div></div>
        <div class="stat-card"><div class="num" id="ftdCount">-</div><div class="lbl">FTD</div></div>
        <div class="stat-card"><div class="num" id="rejectedCount">-</div><div class="lbl">Rejected</div></div>
      </div>

      <!-- All leads -->
      <div class="tab-pane active" id="pane-all">
        <div class="table-wrap"><div class="table-scroll">
          <table>
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Country</th><th>Status</th><th>Landing</th><th>Details</th></tr></thead>
            <tbody id="allTable"><tr><td colspan="8" class="loading-state"><div class="spinner"></div>Loading...</td></tr></tbody>
          </table>
        </div></div>
        <div class="pagination" id="paginationAll"></div>
      </div>

      <!-- New leads -->
      <div class="tab-pane" id="pane-new">
        <div class="table-wrap"><div class="table-scroll">
          <table>
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Country</th><th>Landing</th><th>Details</th></tr></thead>
            <tbody id="newTable"><tr><td colspan="7" class="loading-state"><div class="spinner"></div>Loading...</td></tr></tbody>
          </table>
        </div></div>
      </div>

      <!-- FTD -->
      <div class="tab-pane" id="pane-ftd">
        <div class="table-wrap"><div class="table-scroll">
          <table>
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Country</th><th>FTD Date</th><th>Details</th></tr></thead>
            <tbody id="ftdTable"><tr><td colspan="7" class="loading-state"><div class="spinner"></div>Loading...</td></tr></tbody>
          </table>
        </div></div>
      </div>

      <!-- Rejected -->
      <div class="tab-pane" id="pane-rejected">
        <div class="table-wrap"><div class="table-scroll">
          <table>
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Country</th><th>Reason</th><th>Details</th></tr></thead>
            <tbody id="rejectedTable"><tr><td colspan="7" class="loading-state"><div class="spinner"></div>Loading...</td></tr></tbody>
          </table>
        </div></div>
      </div>

      <!-- Settings -->
      <div class="tab-pane" id="pane-settings">
        <div class="settings-card">
          <h2 class="settings-title">Change Login Credentials</h2>
          <p class="settings-desc">Update your 13Partners dashboard username and password.</p>
          <div id="settingsMsg"></div>
          <form id="settingsForm" autocomplete="off">
            <div class="form-group">
              <label for="currentPassword">Current Password</label>
              <div class="password-wrap">
                <input type="password" id="currentPassword" placeholder="Enter current password" required>
                <button type="button" class="toggle-pw" data-target="currentPassword">Show</button>
              </div>
            </div>
            <div class="form-group">
              <label for="newUsername">New Username</label>
              <input type="text" id="newUsername" placeholder="Enter new username" required minlength="3">
            </div>
            <div class="form-group">
              <label for="newPassword">New Password</label>
              <div class="password-wrap">
                <input type="password" id="newPassword" placeholder="Enter new password (min 4 chars)" required minlength="4">
                <button type="button" class="toggle-pw" data-target="newPassword">Show</button>
              </div>
            </div>
            <button type="submit" class="btn-refresh" id="saveCredsBtn">Save Changes</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal-overlay" id="modal">
      <div class="modal-box">
        <div class="modal-top">
          <h2>Lead Details</h2>
          <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      let allData = [];
      let currentTab = 'all';
      let currentPage = 1;
      let lastPage = 1;

      const dateStart = document.getElementById('dateStart');
      const dateEnd = document.getElementById('dateEnd');
      const refreshBtn = document.getElementById('refreshBtn');
      const modal = document.getElementById('modal');
      const modalBody = document.getElementById('modalBody');
      const modalClose = document.getElementById('modalClose');

      // Default: last 30 days to today
      const today = new Date().toISOString().split('T')[0];
      const thirtyAgo = new Date(Date.now() - 30 * 86400000).toISOString().split('T')[0];
      dateStart.value = thirtyAgo;
      dateEnd.value = today;

      // Tab switching
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          currentTab = btn.dataset.tab;
          document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
          document.getElementById('pane-' + currentTab).classList.add('active');
        });
      });

      // Modal
      modalClose.addEventListener('click', () => modal.classList.remove('active'));
      modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });

      function showModal(record) {
        const fields = Object.entries(record).map(([k, v]) => [k, resolveValue(v)]);
        modalBody.innerHTML = fields.map(([k, v]) =>
          '<div class="detail-row"><span class="detail-key">' + escapeHTML(k) + '</span><span class="detail-val">' + escapeHTML(v) + '</span></div>'
        ).join('');
        modal.classList.add('active');
      }

      function escapeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
      }

      // Safely resolve any value to a display string
      // Handles objects like {name: "New", id: 1}, arrays, nulls, etc.
      function resolveValue(val) {
        if (val === null || val === undefined) return '—';
        if (typeof val === 'string') return val;
        if (typeof val === 'number' || typeof val === 'boolean') return String(val);
        if (Array.isArray(val)) return val.map(resolveValue).join(', ');
        if (typeof val === 'object') {
          if (val.name) return String(val.name);
          if (val.title) return String(val.title);
          if (val.label) return String(val.label);
          if (val.value !== undefined) return String(val.value);
          if (val.text) return String(val.text);
          if (val.status) return String(val.status);
          const entries = Object.entries(val).filter(([,v]) => v !== null && v !== undefined);
          if (entries.length > 0) return entries.map(([k,v]) => k + ': ' + resolveValue(v)).join(', ');
          return '—';
        }
        return String(val);
      }

      // Status classification helpers
      function getStatusClass(status) {
        if (!status) return 'badge-status';
        const s = resolveValue(status).toLowerCase();
        if (s === 'ftd' || s === 'deposit' || s === 'converted') return 'badge-ftd';
        if (s === 'new' || s === 'pending' || s === 'callback') return 'badge-new';
        if (s === 'rejected' || s === 'decline' || s === 'trash' || s === 'invalid') return 'badge-reject';
        return 'badge-status';
      }

      function isFTD(d) {
        const s = resolveValue(d.status || d.lead_status || '').toLowerCase();
        return s === 'ftd' || s === 'deposit' || s === 'converted';
      }

      function isRejected(d) {
        const s = resolveValue(d.status || d.lead_status || '').toLowerCase();
        return s === 'rejected' || s === 'decline' || s === 'trash' || s === 'invalid';
      }

      function isNew(d) {
        return !isFTD(d) && !isRejected(d);
      }

      // Fetch Leads
      async function fetchData(page) {
        if (page) currentPage = page;

        refreshBtn.disabled = true;
        refreshBtn.textContent = 'Loading...';

        ['allTable', 'newTable', 'ftdTable', 'rejectedTable'].forEach(id => {
          const cols = id === 'allTable' ? 8 : 7;
          document.getElementById(id).innerHTML =
            '<tr><td colspan="' + cols + '" class="loading-state"><div class="spinner"></div>Loading...</td></tr>';
        });

        const payload = {
          page: currentPage,
          per_page: 1000,
        };

        if (dateStart.value) payload.date_start = dateStart.value;
        if (dateEnd.value) payload.date_end = dateEnd.value;

        try {
          const res = await fetch('leads_proxy.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
          });

          if (res.status === 401 || res.status === 403) {
            window.location.href = 'login.php';
            return;
          }

          const result = await res.json();
          console.log('13Partners API response:', result);

          // 13Partners response formats:
          // Paginated: { data: [...], current_page, last_page, total, per_page }
          // Or: { data: { data: [...], current_page, last_page } }
          // Or direct array: [...]
          if (result.data && Array.isArray(result.data)) {
            allData = result.data;
            currentPage = result.current_page || 1;
            lastPage = result.last_page || 1;
          } else if (result.data && result.data.data && Array.isArray(result.data.data)) {
            allData = result.data.data;
            currentPage = result.data.current_page || result.current_page || 1;
            lastPage = result.data.last_page || result.last_page || 1;
          } else if (Array.isArray(result)) {
            allData = result;
            lastPage = 1;
          } else if (result.success === false) {
            console.error('API error:', result);
            allData = [];
            lastPage = 1;
          } else {
            // Try to find any array in the response
            const keys = Object.keys(result);
            const arrKey = keys.find(k => Array.isArray(result[k]));
            if (arrKey) {
              allData = result[arrKey];
            } else {
              console.warn('Unexpected response structure:', result);
              allData = [];
            }
            lastPage = 1;
          }
        } catch (err) {
          console.error('Fetch error:', err);
          allData = [];
        }

        renderAll();
        refreshBtn.disabled = false;
        refreshBtn.textContent = 'Refresh';
      }

      function renderAll() {
        const newLeads = allData.filter(d => isNew(d));
        const ftdLeads = allData.filter(d => isFTD(d));
        const rejectedLeads = allData.filter(d => isRejected(d));

        document.getElementById('totalCount').textContent = allData.length;
        document.getElementById('newCount').textContent = newLeads.length;
        document.getElementById('ftdCount').textContent = ftdLeads.length;
        document.getElementById('rejectedCount').textContent = rejectedLeads.length;

        // All table
        renderTable('allTable', allData, 8, (d, i) => {
          const name = escapeHTML(d.full_name || d.name || ((d.first_name || '') + ' ' + (d.last_name || '')).trim() || '—');
          const date = escapeHTML(d.created_at || d.date || '—');
          const email = escapeHTML(d.email || '—');
          const phone = escapeHTML(d.phone || '—');
          const country = escapeHTML(d.country || '—');
          const status = resolveValue(d.status || d.lead_status || '—');
          const landing = escapeHTML(d.landing_name || d.landing || '—');
          return '<tr><td>' + date + '</td><td>' + name + '</td><td>' + email + '</td><td>' + phone + '</td><td>' + country + '</td>' +
            '<td><span class="badge ' + getStatusClass(status) + '">' + escapeHTML(status) + '</span></td>' +
            '<td>' + landing + '</td>' +
            '<td><button class="btn-refresh" style="padding:5px 12px;font-size:11px" onclick="showModal(allData[' + i + '])">View</button></td></tr>';
        });

        // New leads table
        renderTable('newTable', newLeads, 7, (d) => {
          const idx = allData.indexOf(d);
          const name = escapeHTML(d.full_name || d.name || ((d.first_name || '') + ' ' + (d.last_name || '')).trim() || '—');
          const date = escapeHTML(d.created_at || d.date || '—');
          const email = escapeHTML(d.email || '—');
          const phone = escapeHTML(d.phone || '—');
          const country = escapeHTML(d.country || '—');
          const landing = escapeHTML(d.landing_name || d.landing || '—');
          return '<tr><td>' + date + '</td><td>' + name + '</td><td>' + email + '</td><td>' + phone + '</td><td>' + country + '</td>' +
            '<td>' + landing + '</td>' +
            '<td><button class="btn-refresh" style="padding:5px 12px;font-size:11px" onclick="showModal(allData[' + idx + '])">View</button></td></tr>';
        });

        // FTD table
        renderTable('ftdTable', ftdLeads, 7, (d) => {
          const idx = allData.indexOf(d);
          const name = escapeHTML(d.full_name || d.name || ((d.first_name || '') + ' ' + (d.last_name || '')).trim() || '—');
          const date = escapeHTML(d.created_at || d.date || '—');
          const email = escapeHTML(d.email || '—');
          const phone = escapeHTML(d.phone || '—');
          const country = escapeHTML(d.country || '—');
          const ftdDate = escapeHTML(d.ftd_date || d.deposit_date || '—');
          return '<tr><td>' + date + '</td><td>' + name + '</td><td>' + email + '</td><td>' + phone + '</td><td>' + country + '</td>' +
            '<td>' + ftdDate + '</td>' +
            '<td><button class="btn-refresh" style="padding:5px 12px;font-size:11px" onclick="showModal(allData[' + idx + '])">View</button></td></tr>';
        });

        // Rejected table
        renderTable('rejectedTable', rejectedLeads, 7, (d) => {
          const idx = allData.indexOf(d);
          const name = escapeHTML(d.full_name || d.name || ((d.first_name || '') + ' ' + (d.last_name || '')).trim() || '—');
          const date = escapeHTML(d.created_at || d.date || '—');
          const email = escapeHTML(d.email || '—');
          const phone = escapeHTML(d.phone || '—');
          const country = escapeHTML(d.country || '—');
          const reason = escapeHTML(d.reject_reason || d.comment || d.description || '—');
          return '<tr><td>' + date + '</td><td>' + name + '</td><td>' + email + '</td><td>' + phone + '</td><td>' + country + '</td>' +
            '<td>' + reason + '</td>' +
            '<td><button class="btn-refresh" style="padding:5px 12px;font-size:11px" onclick="showModal(allData[' + idx + '])">View</button></td></tr>';
        });

        // Pagination
        renderPagination();
      }

      function renderTable(tableId, data, cols, rowFn) {
        const tbody = document.getElementById(tableId);
        if (data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="' + cols + '" class="empty-state"><div class="icon">&#128196;</div>No leads found for this date range.</td></tr>';
          return;
        }
        tbody.innerHTML = data.map((d, i) => rowFn(d, i)).join('');
      }

      function renderPagination() {
        const container = document.getElementById('paginationAll');
        if (lastPage <= 1) {
          container.innerHTML = '';
          return;
        }
        let html = '<button class="page-btn" onclick="fetchData(' + (currentPage - 1) + ')"' + (currentPage <= 1 ? ' disabled' : '') + '>&laquo; Prev</button>';
        html += '<span class="page-info">Page ' + currentPage + ' of ' + lastPage + '</span>';
        html += '<button class="page-btn" onclick="fetchData(' + (currentPage + 1) + ')"' + (currentPage >= lastPage ? ' disabled' : '') + '>Next &raquo;</button>';
        container.innerHTML = html;
      }

      // Event listeners
      refreshBtn.addEventListener('click', () => fetchData(1));
      dateStart.addEventListener('change', () => fetchData(1));
      dateEnd.addEventListener('change', () => fetchData(1));
      fetchData(1);

      // Settings: password toggle
      document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
          const input = document.getElementById(btn.dataset.target);
          if (input.type === 'password') { input.type = 'text'; btn.textContent = 'Hide'; }
          else { input.type = 'password'; btn.textContent = 'Show'; }
        });
      });

      // Settings: change credentials
      document.getElementById('settingsForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const msgEl = document.getElementById('settingsMsg');
        const btn = document.getElementById('saveCredsBtn');
        btn.disabled = true;
        btn.textContent = 'Saving...';
        msgEl.innerHTML = '';

        const payload = {
          current_password: document.getElementById('currentPassword').value,
          new_username: document.getElementById('newUsername').value.trim(),
          new_password: document.getElementById('newPassword').value,
        };

        try {
          const res = await fetch('change_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
          });
          const result = await res.json();
          if (result.success) {
            msgEl.innerHTML = '<div class="settings-msg success">Credentials updated successfully!</div>';
            document.getElementById('settingsForm').reset();
            const badge = document.querySelector('.admin-badge strong');
            if (badge) badge.textContent = payload.new_username;
          } else {
            msgEl.innerHTML = '<div class="settings-msg error">' + escapeHTML(result.error || 'Failed to update.') + '</div>';
          }
        } catch {
          msgEl.innerHTML = '<div class="settings-msg error">Connection error. Please try again.</div>';
        }
        btn.disabled = false;
        btn.textContent = 'Save Changes';
      });
    </script>
  </body>
</html>
