<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
?>

<main id="auditLogPage" class="audit-log-page">
  <header class="audit-header">
    <div class="audit-heading">
      <span class="audit-heading__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l2.5 1.5"/><circle cx="12" cy="12" r="9"/><path d="M4 4v4h4"/></svg>
      </span>
      <div>
        <span class="audit-eyebrow">ADMINISTRASI</span>
        <h1>Audit Log Akses Menu</h1>
        <p>Riwayat menu, detail, update, dan download.</p>
      </div>
    </div>
    <div class="audit-header__actions">
      <div id="auditState" class="audit-state" role="status" aria-live="polite">Memuat data...</div>
      <button id="auditExportExcel" class="audit-export-button" type="button" aria-label="Export seluruh hasil ke Excel" title="Export seluruh hasil ke Excel" disabled>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14a2 2 0 0 0 2-2v-3"></path><path d="M3 16v3a2 2 0 0 0 2 2"></path></svg>
      </button>
    </div>
  </header>

  <section class="audit-panel audit-results" aria-label="Riwayat akses menu">
    <div class="audit-results__heading">
      <div><h2>Riwayat akses</h2><p id="auditSummary">Menyiapkan data...</p></div>
      <div class="audit-pagination" aria-label="Navigasi halaman">
        <button id="auditPrevious" class="audit-page-button" type="button" aria-label="Halaman sebelumnya" disabled>&#8249;</button>
        <span id="auditPageLabel">Halaman 1</span>
        <button id="auditNext" class="audit-page-button" type="button" aria-label="Halaman berikutnya" disabled>&#8250;</button>
      </div>
    </div>
    <div class="audit-table-wrap">
      <table class="audit-table">
        <thead><tr><th>ID pegawai</th><th>Nama pegawai</th><th>Halaman / ID data</th><th>Metode</th><th>Status HTTP</th><th>Alamat IP</th><th>Aktivitas</th><th>Waktu (WIB)</th></tr></thead>
        <tbody id="auditRows"><tr><td colspan="8" class="audit-empty">Memuat riwayat akses...</td></tr></tbody>
      </table>
    </div>
  </section>
</main>

<style>
  #auditLogPage{--audit-ink:#17324d;--audit-muted:#6e8294;--audit-line:#dce6ed;--audit-blue:#1565d8;color:var(--audit-ink);font-family:Roboto,"Segoe UI",Arial,sans-serif;padding:14px;min-height:100%}
  .audit-header,.audit-panel{border:1px solid var(--audit-line);border-radius:14px;background:#fff;box-shadow:0 5px 18px rgba(19,54,79,.055)}
  .audit-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 18px}.audit-heading{display:flex;align-items:center;gap:12px;min-width:0}.audit-heading__icon{display:grid;place-items:center;width:42px;height:42px;flex:0 0 auto;border-radius:11px;background:#e8f1ff;color:var(--audit-blue)}.audit-heading__icon svg{width:23px;height:23px}.audit-eyebrow{display:block;margin-bottom:4px;color:#5e7fa5;font-size:8px;font-weight:900;letter-spacing:.14em}.audit-heading h1{margin:0;font-size:20px;line-height:1.2;font-weight:900;letter-spacing:-.02em}.audit-heading p{margin:4px 0 0;color:var(--audit-muted);font-size:10px;font-weight:600}.audit-header__actions{display:flex;align-items:center;gap:9px}.audit-state{padding:6px 9px;border:1px solid #dce7ef;border-radius:7px;color:#668096;font-size:9px;font-weight:800;white-space:nowrap}.audit-state[data-state="success"]{border-color:#b7e6ce;background:#f0fff7;color:#087648}.audit-state[data-state="error"]{border-color:#fecaca;background:#fff5f5;color:#b42318}.audit-export-button{display:grid;place-items:center;width:42px;height:42px;border:0;border-radius:11px;background:#059669;color:#fff;cursor:pointer;transition:background .15s,transform .15s}.audit-export-button:hover:not(:disabled){background:#047857;transform:translateY(-1px)}.audit-export-button:disabled{opacity:.55;cursor:wait}.audit-export-button svg{width:19px;height:19px}
  .audit-panel{margin-top:12px;padding:13px}
  .audit-results__heading{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:1px 2px 11px}.audit-results__heading h2{margin:0;font-size:12px;font-weight:900}.audit-results__heading p{margin:4px 0 0;color:var(--audit-muted);font-size:9px;font-weight:650}.audit-pagination{display:flex;align-items:center;gap:8px;color:#5c7690;font-size:9px;font-weight:800;white-space:nowrap}.audit-page-button{display:grid;place-items:center;width:29px;height:29px;border:1px solid #cadbe8;border-radius:7px;background:#fff;color:#416078;font-size:18px;line-height:1;cursor:pointer}.audit-page-button:hover:not(:disabled){border-color:#8db3d5;background:#f2f8fd}.audit-page-button:disabled{opacity:.45;cursor:not-allowed}.audit-table-wrap{max-height:calc(100vh - 300px);min-height:260px;overflow:auto;border:1px solid var(--audit-line);border-radius:9px;scrollbar-color:#b3c5d1 #f4f7f9;scrollbar-width:thin}.audit-table{width:100%;min-width:1180px;border-collapse:separate;border-spacing:0}.audit-table th,.audit-table td{padding:9px 11px;border-right:1px solid var(--audit-line);border-bottom:1px solid var(--audit-line);text-align:left}.audit-table th:last-child,.audit-table td:last-child{border-right:0}.audit-table thead{position:sticky;top:0;z-index:2}.audit-table th{height:33px;background:#edf4fb;color:#45647d;font-size:9px;font-weight:900}.audit-table td{background:#fff;font-size:9px}.audit-table tbody tr:nth-child(even) td{background:#fbfdff}.audit-table tbody tr:hover td{background:#f2f8fe}.audit-table td:first-child,.audit-table td:nth-child(2){font-weight:800}.audit-empty{text-align:center!important;color:#7890a2!important;height:100px}
  .audit-filters{grid-template-columns:repeat(5,minmax(100px,1fr)) auto}.audit-action{display:inline-flex;align-items:center;max-width:260px;min-height:22px;padding:3px 7px;border:1px solid #cbdbe7;border-radius:999px;background:#f7fafd;color:#416078;font-size:8px;font-weight:900;white-space:normal;overflow-wrap:anywhere}.audit-action--detail{border-color:#bfdbfe;background:#eff6ff;color:#1d4ed8}.audit-action--update{border-color:#a7f3d0;background:#ecfdf5;color:#047857}.audit-action--download{border-color:#ddd6fe;background:#f5f3ff;color:#6d28d9}.audit-request-path{max-width:300px;overflow-wrap:anywhere}.audit-ip{white-space:nowrap;font-variant-numeric:tabular-nums}.audit-http-status{display:inline-flex;padding:4px 7px;border:1px solid #b7e6ce;border-radius:999px;background:#f0fff7;color:#087648;font-size:8px;font-weight:900;white-space:nowrap}.audit-http-status--client{border-color:#fde68a;background:#fffbeb;color:#92400e}.audit-http-status--server{border-color:#fecaca;background:#fff5f5;color:#b42318}
  @media(max-width:640px){#auditLogPage{padding:8px}.audit-header{align-items:flex-start;flex-direction:column;padding:13px}.audit-heading h1{font-size:18px}.audit-header__actions{width:100%;justify-content:space-between}.audit-state{white-space:normal}.audit-panel{padding:10px}.audit-results__heading{align-items:flex-start;flex-direction:column}.audit-pagination{align-self:flex-end}.audit-table-wrap{max-height:calc(100vh - 390px);min-height:240px}}
</style>

<script>
(() => {
  const API = './api/index.php?request=audit_log';
  const el = id => document.getElementById(id);
  const state = { page:1, totalPages:1, totalRows:0, exporting:false };
  const escapeHtml = value => String(value == null ? '' : value).replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const setState = (message, kind) => { el('auditState').textContent = message; el('auditState').dataset.state = kind || ''; };
  const filterPanel = el('auditFilters');
  const filterToggle = el('auditNavbarFilterToggle');

  function setFilterOpen(open) {
    filterPanel.classList.toggle('hidden', !open);
    filterPanel.classList.toggle('flex', open);
    filterToggle.classList.toggle('is-active', open);
    filterToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    filterToggle.setAttribute('aria-label', open ? 'Tutup filter Audit Log' : 'Buka filter Audit Log');
    filterToggle.setAttribute('title', open ? 'Tutup filter Audit Log' : 'Buka filter Audit Log');
  }

  async function fetchAuditData(params) {
    const response = await fetch(API + '&' + params.toString(), {credentials:'same-origin', headers:{'Accept':'application/json'}});
    const payload = await response.json().catch(() => ({}));
    if (!response.ok || Number(payload.status) >= 400) throw new Error(payload.message || 'Riwayat akses gagal dimuat.');
    return payload.data || {};
  }

  function showEmpty(message) {
    el('auditRows').innerHTML = '<tr><td colspan="8" class="audit-empty">' + escapeHtml(message) + '</td></tr>';
  }

  function formatDateWib(value) {
    const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}:\d{2}(?::\d{2})?)/);
    if (!match) return String(value || '');
    const utcValue = new Date(match[1] + '-' + match[2] + '-' + match[3] + 'T' + match[4] + 'Z');
    if (Number.isNaN(utcValue.getTime())) return String(value || '');
    const parts = new Intl.DateTimeFormat('en-GB', {
      timeZone:'Asia/Jakarta', year:'numeric', month:'2-digit', day:'2-digit',
      hour:'2-digit', minute:'2-digit', second:'2-digit', hourCycle:'h23'
    }).formatToParts(utcValue).reduce((result, part) => {
      if (part.type !== 'literal') result[part.type] = part.value;
      return result;
    }, {});
    return parts.day + '/' + parts.month + '/' + parts.year + ' ' + parts.hour + ':' + parts.minute + ':' + parts.second + ' WIB';
  }

  const displayDate = value => escapeHtml(formatDateWib(value));

  function renderRows(rows) {
    if (!rows.length) {
      showEmpty('Belum ada akses yang cocok dengan filter ini.');
      return;
    }
    el('auditRows').innerHTML = rows.map(row => {
      const actionNames = {menu_view:'Buka menu', detail:'Detail', update:'Update', download:'Download'};
      const actionType = row.action_type || 'menu_view';
      const actionLabel = row.action_label || actionNames[actionType] || 'Aktivitas';
      const requestPath = row.request_path || ('/' + String(row.feature_key || '').replace(/^page:/, '') + ' [page=' + String(row.feature_key || '').replace(/^page:/, '') + ']');
      const requestMethod = row.request_method || '-';
      const httpStatus = Number(row.http_status) || 0;
      const statusClass = httpStatus >= 500 ? ' audit-http-status--server' : (httpStatus >= 400 ? ' audit-http-status--client' : '');
      return '<tr><td>' + escapeHtml(row.employee_id) + '</td><td>' + escapeHtml(row.employee_name || '-') + '</td><td class="audit-request-path">' + escapeHtml(requestPath) + '</td><td>' + escapeHtml(requestMethod) + '</td><td>' + (httpStatus ? '<span class="audit-http-status' + statusClass + '">' + httpStatus + '</span>' : '-') + '</td><td class="audit-ip">' + escapeHtml(row.ip_address || '-') + '</td><td><span class="audit-action audit-action--' + escapeHtml(actionType) + '">' + escapeHtml(actionLabel) + '</span></td><td>' + displayDate(row.accessed_at) + '</td></tr>';
    }).join('');
  }

  function renderMenus(menus) {
    const select = el('auditMenu');
    const selected = select.value;
    select.innerHTML = '<option value="">Semua menu</option>' + menus.map(menu => '<option value="' + escapeHtml(menu.feature_key) + '">' + escapeHtml(menu.feature_label) + '</option>').join('');
    if (menus.some(menu => menu.feature_key === selected)) select.value = selected;
  }

  async function loadRows() {
    const form = el('auditFilters');
    const submit = form.querySelector('button[type="submit"]');
    const params = new URLSearchParams(new FormData(form));
    params.set('page', String(state.page));
    params.set('limit', '50');
    submit.disabled = true;
    el('auditPrevious').disabled = true;
    el('auditNext').disabled = true;
    setState('Memuat data...', '');
    showEmpty('Memuat riwayat akses...');
    try {
      const data = await fetchAuditData(params);
      const rows = Array.isArray(data.rows) ? data.rows : [];
      const pagination = data.pagination || {};
      renderMenus(Array.isArray(data.menus) ? data.menus : []);
      renderRows(rows);
      state.page = Number(pagination.page) || 1;
      state.totalPages = Number(pagination.total_pages) || 1;
      const total = Number(pagination.total) || 0;
      state.totalRows = total;
      el('auditSummary').textContent = total.toLocaleString('id-ID') + ' catatan ditemukan';
      el('auditPageLabel').textContent = 'Halaman ' + state.page + ' dari ' + state.totalPages;
      el('auditPrevious').disabled = state.page <= 1;
      el('auditNext').disabled = state.page >= state.totalPages;
      setState('Data berhasil dimuat', 'success');
    } catch (error) {
      showEmpty(error.message || 'Terjadi kesalahan saat memuat data.');
      el('auditSummary').textContent = 'Data belum tersedia';
      el('auditPageLabel').textContent = 'Halaman 1';
      setState(error.message || 'Gagal memuat data', 'error');
      state.totalRows = 0;
    } finally {
      submit.disabled = false;
      el('auditExportExcel').disabled = state.totalRows === 0 || state.exporting;
    }
  }

  async function exportExcel() {
    if (state.exporting) return;
    const button = el('auditExportExcel');
    state.exporting = true;
    button.disabled = true;
    setState('Menyiapkan file Excel...', '');
    try {
      const params = new URLSearchParams(new FormData(filterPanel));
      params.set('page', '1');
      params.set('limit', '100');
      const firstPage = await fetchAuditData(params);
      const pagination = firstPage.pagination || {};
      const rows = Array.isArray(firstPage.rows) ? [...firstPage.rows] : [];
      const totalPages = Math.max(1, Number(pagination.total_pages) || 1);
      for (let page = 2; page <= totalPages; page += 1) {
        params.set('page', String(page));
        const nextPage = await fetchAuditData(params);
        if (Array.isArray(nextPage.rows)) rows.push(...nextPage.rows);
      }
      if (!rows.length) {
        state.totalRows = 0;
        setState('Tidak ada data untuk diekspor', 'error');
        return;
      }
      state.totalRows = rows.length;

      const columns = ['ID pegawai', 'Nama pegawai', 'Halaman / ID data', 'Metode', 'Status HTTP', 'Alamat IP', 'Aktivitas', 'Waktu (WIB)'];
      const actionNames = {menu_view:'Buka menu', detail:'Detail', update:'Update', download:'Download'};
      const cell = value => `<td style="mso-number-format:'\\@'">${escapeHtml(value)}</td>`;
      const header = '<tr>' + columns.map(value => `<th>${escapeHtml(value)}</th>`).join('') + '</tr>';
      const body = rows.map(row => {
        const actionType = row.action_type || 'menu_view';
        const values = [row.employee_id, row.employee_name || '-', row.request_path || '', row.request_method || '-', row.http_status || '-', row.ip_address || '-', row.action_label || actionNames[actionType] || 'Aktivitas', formatDateWib(row.accessed_at)];
        return '<tr>' + values.map(cell).join('') + '</tr>';
      }).join('');
      const workbook = `<html><head><meta charset="utf-8"></head><body><table>${header}${body}</table></body></html>`;
      const blob = new Blob(['\ufeff', workbook], {type:'application/vnd.ms-excel;charset=utf-8;'});
      const link = document.createElement('a');
      const dateFrom = el('auditDateFrom').value.replace(/-/g, '');
      const dateTo = el('auditDateTo').value.replace(/-/g, '');
      link.href = URL.createObjectURL(blob);
      link.download = `Audit_Log_${dateFrom}_${dateTo}.xls`;
      document.body.appendChild(link);
      link.click();
      link.remove();
      setTimeout(() => URL.revokeObjectURL(link.href), 1000);
      setState(`Excel berhasil diekspor (${rows.length.toLocaleString('id-ID')} catatan)`, 'success');
    } catch (error) {
      setState(error.message || 'Export Excel gagal.', 'error');
    } finally {
      state.exporting = false;
      button.disabled = state.totalRows === 0;
    }
  }

  const jakartaToday = new Intl.DateTimeFormat('en-GB', {
    timeZone:'Asia/Jakarta', year:'numeric', month:'2-digit', day:'2-digit'
  }).formatToParts(new Date()).reduce((result, part) => {
    if (part.type !== 'literal') result[part.type] = Number(part.value);
    return result;
  }, {});
  const todayValue = String(jakartaToday.year) + '-' + String(jakartaToday.month).padStart(2, '0') + '-' + String(jakartaToday.day).padStart(2, '0');
  const fromValue = new Date(Date.UTC(jakartaToday.year, jakartaToday.month - 1, jakartaToday.day - 29)).toISOString().slice(0, 10);
  el('auditDateFrom').value = fromValue;
  el('auditDateTo').value = todayValue;
  filterToggle.addEventListener('click', event => {
    event.stopPropagation();
    setFilterOpen(filterPanel.classList.contains('hidden'));
  });
  el('auditFilterClose').addEventListener('click', () => setFilterOpen(false));
  document.addEventListener('click', event => {
    if (!filterPanel.classList.contains('hidden') && !filterPanel.contains(event.target) && !filterToggle.contains(event.target)) setFilterOpen(false);
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape') setFilterOpen(false); });
  filterPanel.addEventListener('submit', event => { event.preventDefault(); setFilterOpen(false); state.page = 1; loadRows(); });
  el('auditExportExcel').addEventListener('click', exportExcel);
  el('auditPrevious').addEventListener('click', () => { if (state.page > 1) { state.page -= 1; loadRows(); } });
  el('auditNext').addEventListener('click', () => { if (state.page < state.totalPages) { state.page += 1; loadRows(); } });
  filterToggle.hidden = false;
  loadRows();
})();
</script>
