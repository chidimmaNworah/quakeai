<?php
require_once __DIR__ . '/auth_check.php';
require_auth();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Altstream &mdash; Leads &amp; Conversions Dashboard</title>
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
      .dash-nav { display: flex; align-items: center; gap: 16px; }
      .dash-link { color: var(--accent); text-decoration: none; font-size: 14px; font-weight: 500; }
      .dash-link:hover { text-decoration: underline; }
      .btn-logout { padding: 8px 18px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25); color: #ef4444; font-size: 13px; font-weight: 600; font-family: inherit; border-radius: 8px; cursor: pointer; text-decoration: none; transition: background 0.2s; }
      .btn-logout:hover { background: rgba(239,68,68,0.2); }
      .admin-badge { color: var(--text2); font-size: 13px; }

      .tabs { display: flex; gap: 8px; margin-bottom: 20px; }
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
      .badge-reg { background: rgba(0,212,255,0.15); color: var(--accent); }
      .badge-conv { background: rgba(34,197,94,0.15); color: var(--green); }
      .badge-status { background: rgba(245,158,11,0.15); color: var(--yellow); }

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
    </style>
    <script src="/navbar.js" defer></script>
  </head>
  <body>
    <div class="dash-container">
      <div class="dash-header">
        <div>
          <h1 class="dash-title">Altstream &mdash; Leads &amp; Conversions</h1>
        </div>
        <div class="dash-nav">
          <span class="admin-badge">Logged in as <strong><?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?></strong></span>
          <a href="index.html" class="dash-link">&larr; Landing Page</a>
          <a href="login.php?logout=1" class="btn-logout">Logout</a>
        </div>
      </div>

      <div class="tabs">
        <button class="tab-btn active" data-tab="all">All</button>
        <button class="tab-btn" data-tab="registrations">Registrations</button>
        <button class="tab-btn" data-tab="deposits">Deposits</button>
      </div>

      <div class="controls">
        <span class="control-label">Date:</span>
        <input type="date" class="date-input" id="dateFilter" />
        <select class="select-input" id="typeFilter">
          <option value="3">Leads + Deposits</option>
          <option value="2">Only Leads</option>
          <option value="4">Only Deposits</option>
        </select>
        <button class="btn-refresh" id="refreshBtn">Refresh</button>
      </div>

      <div class="stats">
        <div class="stat-card"><div class="num" id="totalCount">-</div><div class="lbl">Total Records</div></div>
        <div class="stat-card"><div class="num" id="regCount">-</div><div class="lbl">Registrations</div></div>
        <div class="stat-card"><div class="num" id="ftdCount">-</div><div class="lbl">Deposits (FTD)</div></div>
      </div>

      <div class="tab-pane active" id="pane-all">
        <div class="table-wrap"><div class="table-scroll">
          <table>
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Country</th><th>Type</th><th>Status</th><th>Details</th></tr></thead>
            <tbody id="allTable"><tr><td colspan="8" class="loading-state"><div class="spinner"></div>Loading...</td></tr></tbody>
          </table>
        </div></div>
      </div>

      <div class="tab-pane" id="pane-registrations">
        <div class="table-wrap"><div class="table-scroll">
          <table>
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Country</th><th>Status</th><th>Details</th></tr></thead>
            <tbody id="regTable"><tr><td colspan="7" class="loading-state"><div class="spinner"></div>Loading...</td></tr></tbody>
          </table>
        </div></div>
      </div>

      <div class="tab-pane" id="pane-deposits">
        <div class="table-wrap"><div class="table-scroll">
          <table>
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Country</th><th>FTD Date</th><th>Details</th></tr></thead>
            <tbody id="ftdTable"><tr><td colspan="7" class="loading-state"><div class="spinner"></div>Loading...</td></tr></tbody>
          </table>
        </div></div>
      </div>
    </div>

    <div class="modal-overlay" id="modal">
      <div class="modal-box">
        <div class="modal-top">
          <h2>Record Details</h2>
          <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <div id="modalBody"></div>
      </div>
    </div>

    <script>
      let allData = [];
      let currentTab = 'all';

      const dateFilter = document.getElementById('dateFilter');
      const typeFilter = document.getElementById('typeFilter');
      const refreshBtn = document.getElementById('refreshBtn');
      const modal = document.getElementById('modal');
      const modalBody = document.getElementById('modalBody');
      const modalClose = document.getElementById('modalClose');

      dateFilter.value = new Date().toISOString().split('T')[0];

      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          currentTab = btn.dataset.tab;
          document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
          document.getElementById('pane-' + currentTab).classList.add('active');
        });
      });

      modalClose.addEventListener('click', () => modal.classList.remove('active'));
      modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });

      function showModal(record) {
        const fields = Object.entries(record).map(([k, v]) => [k, v ?? '—']);
        modalBody.innerHTML = fields.map(([k, v]) =>
          '<div class="detail-row"><span class="detail-key">' + escapeHTML(k) + '</span><span class="detail-val">' + escapeHTML(String(v)) + '</span></div>'
        ).join('');
        modal.classList.add('active');
      }

      function escapeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
      }

      function isDeposit(d) {
        return !!(d.ftd_date || d.ftdDate || d.deposit_date || d.is_ftd);
      }

      function isRegistration(d) {
        return !isDeposit(d);
      }

      async function fetchData() {
        refreshBtn.disabled = true;
        refreshBtn.textContent = 'Loading...';

        ['allTable', 'regTable', 'ftdTable'].forEach(id => {
          const cols = id === 'allTable' ? 8 : 7;
          document.getElementById(id).innerHTML =
            '<tr><td colspan="' + cols + '" class="loading-state"><div class="spinner"></div>Loading...</td></tr>';
        });

        const payload = {};
        if (dateFilter.value) payload.date = dateFilter.value;
        payload.type = typeFilter.value;

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

          // Trackbox may return data in various formats
          if (Array.isArray(result)) {
            allData = result;
          } else if (result.data && Array.isArray(result.data)) {
            allData = result.data;
          } else if (result.customers && Array.isArray(result.customers)) {
            allData = result.customers;
          } else {
            allData = [];
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
        const regs = allData.filter(d => isRegistration(d));
        const ftds = allData.filter(d => isDeposit(d));

        document.getElementById('totalCount').textContent = allData.length;
        document.getElementById('regCount').textContent = regs.length;
        document.getElementById('ftdCount').textContent = ftds.length;

        // All table
        renderTable('allTable', allData, 8, (d, i) => {
          const name = escapeHTML((d.firstname || d.first_name || '') + ' ' + (d.lastname || d.last_name || ''));
          const date = escapeHTML(d.created_at || d.registration_date || d.date || '—');
          const email = escapeHTML(d.email || '—');
          const phone = escapeHTML(d.phone || '—');
          const country = escapeHTML(d.country || d.countryCode || '—');
          const type = isDeposit(d) ? '<span class="badge badge-conv">Deposit</span>' : '<span class="badge badge-reg">Lead</span>';
          const status = d.status ? '<span class="badge badge-status">' + escapeHTML(d.status) + '</span>' : '—';
          return '<tr><td>' + date + '</td><td>' + name + '</td><td>' + email + '</td><td>' + phone + '</td><td>' + country + '</td><td>' + type + '</td><td>' + status + '</td>' +
            '<td><button class="btn-refresh" style="padding:5px 12px;font-size:11px" onclick="showModal(allData[' + i + '])">View</button></td></tr>';
        });

        // Registrations table
        renderTable('regTable', regs, 7, (d) => {
          const idx = allData.indexOf(d);
          const name = escapeHTML((d.firstname || d.first_name || '') + ' ' + (d.lastname || d.last_name || ''));
          const date = escapeHTML(d.created_at || d.registration_date || d.date || '—');
          const email = escapeHTML(d.email || '—');
          const phone = escapeHTML(d.phone || '—');
          const country = escapeHTML(d.country || d.countryCode || '—');
          const status = d.status ? '<span class="badge badge-status">' + escapeHTML(d.status) + '</span>' : '—';
          return '<tr><td>' + date + '</td><td>' + name + '</td><td>' + email + '</td><td>' + phone + '</td><td>' + country + '</td><td>' + status + '</td>' +
            '<td><button class="btn-refresh" style="padding:5px 12px;font-size:11px" onclick="showModal(allData[' + idx + '])">View</button></td></tr>';
        });

        // Deposits table
        renderTable('ftdTable', ftds, 7, (d) => {
          const idx = allData.indexOf(d);
          const name = escapeHTML((d.firstname || d.first_name || '') + ' ' + (d.lastname || d.last_name || ''));
          const date = escapeHTML(d.created_at || d.registration_date || d.date || '—');
          const email = escapeHTML(d.email || '—');
          const phone = escapeHTML(d.phone || '—');
          const country = escapeHTML(d.country || d.countryCode || '—');
          const ftdDate = escapeHTML(d.ftd_date || d.ftdDate || d.deposit_date || '—');
          return '<tr><td>' + date + '</td><td>' + name + '</td><td>' + email + '</td><td>' + phone + '</td><td>' + country + '</td><td>' + ftdDate + '</td>' +
            '<td><button class="btn-refresh" style="padding:5px 12px;font-size:11px" onclick="showModal(allData[' + idx + '])">View</button></td></tr>';
        });
      }

      function renderTable(tableId, data, cols, rowFn) {
        const tbody = document.getElementById(tableId);
        if (data.length === 0) {
          tbody.innerHTML = '<tr><td colspan="' + cols + '" class="empty-state"><div class="icon">&#128196;</div>No records found for this date.</td></tr>';
          return;
        }
        tbody.innerHTML = data.map((d, i) => rowFn(d, i)).join('');
      }

      refreshBtn.addEventListener('click', fetchData);
      dateFilter.addEventListener('change', fetchData);
      typeFilter.addEventListener('change', fetchData);
      fetchData();
    </script>
  </body>
</html>
