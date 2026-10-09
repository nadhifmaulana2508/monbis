<section id="eventThemePage" class="event-theme-page">
  <div class="event-admin-header">
    <div class="event-admin-title">
      <div class="event-admin-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 4h10a2 2 0 0 1 2 2v3.5a2 2 0 0 1-.586 1.414l-5.5 5.5a2 2 0 0 1-2.828 0l-4.5-4.5A2 2 0 0 1 5 10.5V6a2 2 0 0 1 2-2Z"/><path d="M12 8h.01M4 20h16"/></svg>
      </div>
      <div>
        <h1>Setting Event</h1>
        <p>Atur tampilan event dan pemberitahuan Monbis untuk pengguna.</p>
      </div>
    </div>
    <button type="button" class="event-admin-refresh" onclick="eventAdminRefreshAll()">Refresh</button>
  </div>

  <div class="event-admin-tabs" role="tablist" aria-label="Pengaturan event Monbis">
    <button type="button" class="event-tab is-active" data-event-tab="theme" role="tab" aria-selected="true" onclick="eventAdminSwitchTab('theme')">Tampilan Event</button>
    <button type="button" class="event-tab" data-event-tab="notification" role="tab" aria-selected="false" onclick="eventAdminSwitchTab('notification')">Pemberitahuan</button>
  </div>

  <div id="eventThemeTab" class="event-admin-tab">
  <div class="event-admin-grid">
    <form id="eventThemeForm" class="event-admin-card" enctype="multipart/form-data">
      <input type="hidden" id="eventId" name="id">
      <div class="event-card-heading">
        <span>Konfigurasi Event</span>
        <button type="button" onclick="eventAdminReset()">Baru</button>
      </div>

      <label class="event-field">
        <span>Nama Event</span>
        <input id="eventName" name="event_name" type="text" maxlength="120" placeholder="Contoh: HUT BKK Jateng">
      </label>

      <div class="event-form-row">
        <label class="event-field">
          <span>Mulai</span>
          <input id="eventStart" name="start_date" type="date">
        </label>
        <label class="event-field">
          <span>Selesai</span>
          <input id="eventEnd" name="end_date" type="date">
        </label>
      </div>

      <div class="event-form-row">
        <label class="event-field">
          <span>Aksen</span>
          <input id="eventAccent" name="accent_color" type="color" value="#2563eb">
        </label>
        <label class="event-field">
          <span>Header</span>
          <input id="eventHeader" name="header_bg" type="color" value="#ffffff">
        </label>
      </div>

      <div class="event-form-row">
        <label class="event-field">
          <span>Sidebar</span>
          <input id="eventSidebar" name="sidebar_bg" type="color" value="#ffffff">
        </label>
        <label class="event-field">
          <span>Text</span>
          <input id="eventText" name="text_color" type="color" value="#0f172a">
        </label>
      </div>

      <label class="event-field">
        <span>Text Sidebar</span>
        <input id="eventSidebarText" name="sidebar_text" type="color" value="#334155">
      </label>

      <label class="event-field">
        <span>Font</span>
        <select id="eventFont" name="font_family">
          <option value="Inter, system-ui, sans-serif">Inter</option>
          <option value="Poppins, system-ui, sans-serif">Poppins</option>
          <option value="Nunito, system-ui, sans-serif">Nunito</option>
          <option value="Montserrat, system-ui, sans-serif">Montserrat</option>
          <option value="Arial, system-ui, sans-serif">Arial</option>
        </select>
      </label>

      <label class="event-field">
        <span>Gambar Event</span>
        <input id="eventImage" name="event_image" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      </label>

      <div class="event-form-row">
        <label class="event-field">
          <span>Tampilan Gambar</span>
          <select id="eventImageFit" name="image_fit">
            <option value="cover">Cover</option>
            <option value="contain">Contain</option>
          </select>
        </label>
        <label class="event-field">
          <span>Posisi Gambar</span>
          <select id="eventImagePosition" name="image_position">
            <option value="center">Tengah</option>
            <option value="top center">Atas</option>
            <option value="bottom center">Bawah</option>
            <option value="center left">Kiri</option>
            <option value="center right">Kanan</option>
          </select>
        </label>
      </div>

      <label class="event-check">
        <input id="eventActive" name="is_active" type="checkbox" value="1" checked>
        <span>Event aktif</span>
      </label>

      <label class="event-check">
        <input id="eventRemoveImage" name="remove_image" type="checkbox" value="1">
        <span>Hapus gambar lama</span>
      </label>

      <div class="event-actions">
        <button type="submit" class="event-save">Simpan Event</button>
      </div>
      <div id="eventAdminMessage" class="event-message"></div>
    </form>

    <div class="event-admin-card event-preview-card">
      <div class="event-card-heading">
        <span>Preview</span>
      </div>
      <div id="eventPreview" class="event-preview">
        <div class="event-preview-sidebar">
          <div class="event-preview-logo"></div>
          <div class="event-preview-line w1"></div>
          <div class="event-preview-line w2"></div>
          <div class="event-preview-line w3"></div>
          <div id="eventPreviewPromo" class="event-preview-promo">Sidebar event</div>
        </div>
        <div class="event-preview-main">
          <div class="event-preview-nav">
            <span id="eventPreviewName">Event Monbis</span>
            <b></b>
          </div>
          <div class="event-preview-content">
            <div></div><div></div><div></div>
          </div>
        </div>
      </div>
      <p class="event-hint">Preview hanya gambaran warna. Tampilan asli mengikuti layout navbar dan sidebar aplikasi.</p>
    </div>
  </div>

  <div class="event-admin-card event-list-card">
    <div class="event-card-heading">
      <span>Daftar Event</span>
      <small id="eventCount">0 event</small>
    </div>
    <div id="eventList" class="event-list"></div>
  </div>
  </div>

  <div id="eventNotificationTab" class="event-admin-tab hidden">
    <div class="event-admin-grid event-notification-grid">
      <form id="notificationForm" class="event-admin-card">
        <input type="hidden" id="notificationId" name="id">
        <div class="event-card-heading">
          <span>Konfigurasi Pemberitahuan</span>
          <button type="button" onclick="notificationAdminReset()">Baru</button>
        </div>

        <label class="event-field">
          <span>Nama Konfigurasi</span>
          <input id="notificationName" name="notification_name" type="text" maxlength="120" placeholder="Contoh: Rilis Monbis v1.1">
        </label>

        <label class="event-field">
          <span>Judul Modal</span>
          <input id="notificationTitle" name="title" type="text" maxlength="180" placeholder="Perubahan Signifikan MONBIS v1.1">
        </label>

        <label class="event-field">
          <span>Isi Pemberitahuan</span>
          <textarea id="notificationMessage" name="message" rows="7" maxlength="3000" placeholder="Tulis ringkasan perubahan yang ingin disampaikan..."></textarea>
        </label>

        <div class="event-form-row">
          <label class="event-field">
            <span>Mulai Tampil</span>
            <input id="notificationStart" name="start_date" type="date">
          </label>
          <label class="event-field">
            <span>Selesai Tampil</span>
            <input id="notificationEnd" name="end_date" type="date">
          </label>
        </div>

        <label class="event-check">
          <input id="notificationActive" name="is_active" type="checkbox" value="1" checked>
          <span>Pemberitahuan aktif</span>
        </label>

        <label class="event-check">
          <input id="notificationAfterLogin" name="show_after_login" type="checkbox" value="1" checked>
          <span>Tampilkan setelah login</span>
        </label>

        <p class="event-hint">Periode default dibuat 7 hari kalender. Matikan centang aktif jika ingin menyembunyikan modal tanpa menghapus konfigurasi.</p>
        <div class="event-actions">
          <button type="submit" class="event-save">Simpan Pemberitahuan</button>
        </div>
        <div id="notificationAdminMessage" class="event-message"></div>
      </form>

      <div class="event-admin-card event-notification-guide">
        <div class="event-card-heading"><span>Catatan Penggunaan</span></div>
        <div class="notification-guide-item"><b>Aktif / nonaktif</b><span>Hanya satu pemberitahuan aktif yang ditampilkan kepada pengguna.</span></div>
        <div class="notification-guide-item"><b>Setelah login</b><span>Modal muncul sekali setiap login baru selama tanggal tampil masih berlaku.</span></div>
        <div class="notification-guide-item"><b>Periode</b><span>Di luar tanggal mulai dan selesai, pemberitahuan tidak akan muncul.</span></div>
      </div>
    </div>

    <div class="event-admin-card event-list-card">
      <div class="event-card-heading">
        <span>Daftar Pemberitahuan</span>
        <small id="notificationCount">0 pemberitahuan</small>
      </div>
      <div id="notificationList" class="event-list notification-list"></div>
    </div>
  </div>
</section>

<style>
  .event-theme-page {
    display:flex;
    flex-direction:column;
    gap:14px;
    color:#0f172a;
    font-family:Inter,system-ui,sans-serif;
  }
  .event-admin-header,
  .event-admin-card {
    border:1px solid #dbe3ee;
    border-radius:14px;
    background:#fff;
    box-shadow:0 10px 28px rgba(15,23,42,.06);
  }
  .event-admin-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:16px;
  }
  .event-admin-tabs {
    display:flex;
    align-items:center;
    gap:6px;
    width:max-content;
    max-width:100%;
    padding:4px;
    border:1px solid #dbe3ee;
    border-radius:12px;
    background:#f8fafc;
  }
  .event-tab {
    min-height:34px;
    padding:0 13px;
    border:1px solid transparent;
    border-radius:9px;
    background:transparent;
    color:#64748b;
    font-size:11px;
    font-weight:900;
    cursor:pointer;
  }
  .event-tab.is-active {
    border-color:#bfdbfe;
    background:#eff6ff;
    color:#2563eb;
    box-shadow:0 3px 8px rgba(37,99,235,.08);
  }
  .event-admin-tab.hidden { display:none; }
  .event-admin-title { display:flex; align-items:center; gap:12px; min-width:0; }
  .event-admin-title h1 { margin:0; font-size:22px; font-weight:950; letter-spacing:-.02em; }
  .event-admin-title p { margin:3px 0 0; color:#64748b; font-size:12px; font-weight:650; }
  .event-admin-icon {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:42px;
    height:42px;
    border-radius:12px;
    background:#2563eb;
    color:#fff;
    box-shadow:0 10px 20px rgba(37,99,235,.22);
  }
  .event-admin-icon svg { width:22px; height:22px; stroke-width:2.2; }
  .event-admin-refresh,
  .event-card-heading button,
  .event-save {
    height:38px;
    border:0;
    border-radius:10px;
    cursor:pointer;
    font-weight:900;
  }
  .event-admin-refresh {
    padding:0 14px;
    background:#eff6ff;
    color:#2563eb;
    border:1px solid #bfdbfe;
  }
  .event-admin-grid {
    display:grid;
    grid-template-columns:minmax(320px,420px) minmax(0,1fr);
    gap:14px;
  }
  .event-admin-card { padding:14px; min-width:0; }
  .event-card-heading {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:12px;
    padding-bottom:10px;
    border-bottom:1px solid #e2e8f0;
    color:#0f172a;
    font-size:14px;
    font-weight:950;
  }
  .event-card-heading button {
    padding:0 11px;
    background:#f8fafc;
    color:#2563eb;
    border:1px solid #dbe3ee;
    font-size:11px;
  }
  .event-card-heading small { color:#64748b; font-size:11px; }
  .event-form-row { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
  .event-field { display:flex; flex-direction:column; gap:6px; margin-bottom:10px; }
  .event-field span,
  .event-check span {
    color:#64748b;
    font-size:10px;
    font-weight:900;
    letter-spacing:.04em;
    text-transform:uppercase;
  }
  .event-field input,
  .event-field select,
  .event-field textarea {
    width:100%;
    border:1px solid #cbd5e1;
    border-radius:10px;
    background:#fff;
    color:#0f172a;
    padding:0 10px;
    font-size:13px;
    font-weight:750;
    outline:none;
  }
  .event-field input,
  .event-field select { height:40px; }
  .event-field textarea { min-height:142px; padding:10px; resize:vertical; line-height:1.55; }
  .event-field input[type="color"] { padding:4px; }
  .event-field input[type="file"] { padding:8px 10px; height:auto; }
  .event-field input:focus,
  .event-field select:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
  .event-check {
    display:flex;
    align-items:center;
    gap:8px;
    margin:8px 0;
  }
  .event-check input { width:16px; height:16px; accent-color:#2563eb; }
  .event-actions { display:flex; justify-content:flex-end; margin-top:12px; }
  .event-save { padding:0 16px; background:#059669; color:#fff; }
  .event-message { min-height:20px; margin-top:10px; color:#64748b; font-size:12px; font-weight:700; }
  .event-message.is-error { color:#dc2626; }
  .event-notification-guide { align-self:start; }
  .notification-guide-item {
    display:flex;
    flex-direction:column;
    gap:4px;
    padding:11px 0;
    border-bottom:1px solid #e2e8f0;
  }
  .notification-guide-item:last-child { border-bottom:0; }
  .notification-guide-item b { color:#0f172a; font-size:12px; }
  .notification-guide-item span { color:#64748b; font-size:11px; line-height:1.55; }
  .event-preview {
    display:grid;
    grid-template-columns:150px 1fr;
    overflow:hidden;
    min-height:280px;
    border:1px solid #dbe3ee;
    border-radius:16px;
    background:#f8fafc;
  }
  .event-preview-sidebar { padding:16px; background:var(--preview-sidebar,#fff); color:var(--preview-sidebar-text,#334155); }
  .event-preview-logo { width:38px; height:38px; border-radius:12px; background:var(--preview-accent,#2563eb); margin-bottom:18px; }
  .event-preview-line { height:12px; border-radius:999px; background:currentColor; opacity:.2; margin-bottom:12px; }
  .event-preview-line.w1 { width:100%; }
  .event-preview-line.w2 { width:75%; }
  .event-preview-line.w3 { width:88%; }
  .event-preview-promo {
    display:flex;
    align-items:flex-end;
    min-height:84px;
    margin-top:22px;
    padding:10px;
    border-radius:14px;
    background:
      linear-gradient(180deg, rgba(15,23,42,.05), rgba(15,23,42,.55)),
      var(--preview-promo-image, linear-gradient(135deg, rgba(37,99,235,.24), rgba(14,165,233,.18)));
    background-size:var(--preview-image-fit, cover);
    background-position:var(--preview-image-position, center);
    background-repeat:no-repeat;
    color:#fff;
    font-size:10px;
    font-weight:950;
    overflow:hidden;
  }
  .event-preview-main { background:#fff; }
  .event-preview-nav {
    display:flex;
    align-items:center;
    justify-content:space-between;
    height:58px;
    padding:0 16px;
    background:var(--preview-header,#fff);
    color:var(--preview-text,#0f172a);
    border-bottom:1px solid #e2e8f0;
    font-weight:950;
  }
  .event-preview-nav b { width:34px; height:34px; border-radius:999px; background:var(--preview-accent,#2563eb); opacity:.22; }
  .event-preview-content { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; padding:18px; }
  .event-preview-content div { height:86px; border:1px solid #e2e8f0; border-radius:13px; background:#f8fafc; }
  .event-hint { margin:10px 0 0; color:#64748b; font-size:12px; font-weight:650; }
  .event-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
  .event-row {
    display:grid;
    grid-template-columns:48px minmax(0,1fr) auto;
    gap:10px;
    align-items:center;
    padding:10px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#f8fafc;
  }
  .event-row img,
  .event-row__swatch { width:48px; height:48px; border-radius:12px; object-fit:cover; background:var(--row-accent,#2563eb); }
  .event-row strong { display:block; color:#0f172a; font-size:13px; font-weight:950; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .event-row span { display:block; margin-top:2px; color:#64748b; font-size:11px; font-weight:700; }
  .event-row small { display:block; margin-top:4px; color:#94a3b8; font-size:10px; line-height:1.4; }
  .notification-row { grid-template-columns:48px minmax(0,1fr) auto; }
  .event-row__actions { display:flex; gap:6px; }
  .event-row__actions button {
    height:32px;
    padding:0 10px;
    border:1px solid #dbe3ee;
    border-radius:9px;
    background:#fff;
    color:#2563eb;
    font-size:11px;
    font-weight:900;
    cursor:pointer;
  }
  .event-row__actions button.danger { color:#dc2626; }
  :root[data-monbis-theme="dark"] .event-theme-page { color:#e5e7eb; }
  :root[data-monbis-theme="dark"] .event-admin-header,
  :root[data-monbis-theme="dark"] .event-admin-card {
    background:#111827;
    border-color:#334155;
    box-shadow:0 18px 32px rgba(0,0,0,.25);
  }
  :root[data-monbis-theme="dark"] .event-card-heading,
  :root[data-monbis-theme="dark"] .event-admin-title h1,
  :root[data-monbis-theme="dark"] .event-row strong { color:#f8fafc; border-color:#334155; }
  :root[data-monbis-theme="dark"] .event-admin-title p,
  :root[data-monbis-theme="dark"] .event-field span,
  :root[data-monbis-theme="dark"] .event-check span,
  :root[data-monbis-theme="dark"] .event-hint,
  :root[data-monbis-theme="dark"] .event-row span { color:#94a3b8; }
  :root[data-monbis-theme="dark"] .event-field input,
  :root[data-monbis-theme="dark"] .event-field select,
  :root[data-monbis-theme="dark"] .event-field textarea,
  :root[data-monbis-theme="dark"] .event-row__actions button {
    background:#0b1220;
    border-color:#475569;
    color:#e5e7eb;
  }
  :root[data-monbis-theme="dark"] .event-preview,
  :root[data-monbis-theme="dark"] .event-row { background:#0b1220; border-color:#334155; }
  :root[data-monbis-theme="dark"] .event-admin-tabs { background:#0b1220; border-color:#334155; }
  :root[data-monbis-theme="dark"] .event-tab { color:#94a3b8; }
  :root[data-monbis-theme="dark"] .event-tab.is-active { background:#172554; border-color:#3b82f6; color:#bfdbfe; }
  :root[data-monbis-theme="dark"] .notification-guide-item { border-color:#334155; }
  :root[data-monbis-theme="dark"] .notification-guide-item b { color:#f8fafc; }
  :root[data-monbis-theme="dark"] .notification-guide-item span { color:#94a3b8; }
  :root[data-monbis-theme="dark"] .event-preview-main,
  :root[data-monbis-theme="dark"] .event-preview-content div { background:#111827; border-color:#334155; }
  @media (max-width:980px) {
    .event-admin-grid { grid-template-columns:1fr; }
    .event-list { grid-template-columns:1fr; }
  }
  @media (max-width:640px) {
    .event-admin-header { align-items:flex-start; flex-direction:column; }
    .event-admin-tabs { width:100%; }
    .event-tab { flex:1; padding:0 8px; }
    .event-form-row { grid-template-columns:1fr; }
    .event-preview { grid-template-columns:92px 1fr; min-height:220px; }
    .event-preview-content { grid-template-columns:1fr; }
    .event-preview-content div { height:46px; }
    .event-row { grid-template-columns:42px minmax(0,1fr); }
    .event-row__actions { grid-column:1 / -1; justify-content:flex-end; }
  }
</style>

<script>
(function () {
  const API = './api/event_theme/';
  const ids = {
    id:'eventId',
    name:'eventName',
    start:'eventStart',
    end:'eventEnd',
    accent:'eventAccent',
    header:'eventHeader',
    sidebar:'eventSidebar',
    text:'eventText',
    sidebarText:'eventSidebarText',
    font:'eventFont',
    imageFit:'eventImageFit',
    imagePosition:'eventImagePosition',
    active:'eventActive',
    removeImage:'eventRemoveImage',
    image:'eventImage',
    notificationId:'notificationId',
    notificationName:'notificationName',
    notificationTitle:'notificationTitle',
    notificationMessage:'notificationMessage',
    notificationStart:'notificationStart',
    notificationEnd:'notificationEnd',
    notificationActive:'notificationActive',
    notificationAfterLogin:'notificationAfterLogin'
  };
  let rows = [];
  let notificationRows = [];

  function el(id) { return document.getElementById(id); }
  function user() {
    const current = (window.getUser && window.getUser()) || window.__USER || null;
    if (current && typeof current === 'object') return current;
    try {
      const stored = JSON.parse(localStorage.getItem('dpk_user') || 'null');
      return stored && typeof stored === 'object' ? stored : {};
    } catch (error) {
      return {};
    }
  }
  function adminId() {
    const u = user();
    const keys = ['id_peg', 'idPeg', 'id_pegawai', 'idPegawai', 'employee_id'];
    for (const key of keys) {
      const value = String(u?.[key] || '').trim();
      if (value === '102-119') return value;
    }
    return '';
  }
  async function waitForAdminId(timeoutMs = 3500) {
    const started = Date.now();
    let id = adminId();
    while (!id && Date.now() - started < timeoutMs) {
      await new Promise(resolve => setTimeout(resolve, 150));
      id = adminId();
    }
    return id;
  }
  function isAdmin() {
    return adminId() === '102-119';
  }
  function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch]));
  }
  function msg(text, error) {
    const box = el('eventAdminMessage');
    if (!box) return;
    box.textContent = text || '';
    box.classList.toggle('is-error', !!error);
  }
  function notificationMsg(text, error) {
    const box = el('notificationAdminMessage');
    if (!box) return;
    box.textContent = text || '';
    box.classList.toggle('is-error', !!error);
  }
  function localDate(offset = 0) {
    const date = new Date();
    date.setHours(12, 0, 0, 0);
    date.setDate(date.getDate() + offset);
    return date.toISOString().slice(0, 10);
  }
  function assetUrl(path) {
    if (!path) return '';
    if (/^https?:\/\//i.test(path)) return path;
    return './' + String(path).replace(/^\.?\//, '');
  }

  function collectPreview() {
    return {
      event_name: el(ids.name)?.value || 'Event Monbis',
      accent_color: el(ids.accent)?.value || '#2563eb',
      header_bg: el(ids.header)?.value || '#ffffff',
      sidebar_bg: el(ids.sidebar)?.value || '#ffffff',
      text_color: el(ids.text)?.value || '#0f172a',
      sidebar_text: el(ids.sidebarText)?.value || '#334155',
      font_family: el(ids.font)?.value || 'Inter, system-ui, sans-serif',
      image_fit: el(ids.imageFit)?.value || 'cover',
      image_position: el(ids.imagePosition)?.value || 'center'
    };
  }

  function renderPreview() {
    const data = collectPreview();
    const preview = el('eventPreview');
    if (!preview) return;
    preview.style.setProperty('--preview-accent', data.accent_color);
    preview.style.setProperty('--preview-header', data.header_bg);
    preview.style.setProperty('--preview-sidebar', data.sidebar_bg);
    preview.style.setProperty('--preview-text', data.text_color);
    preview.style.setProperty('--preview-sidebar-text', data.sidebar_text);
    preview.style.setProperty('--preview-image-fit', data.image_fit);
    preview.style.setProperty('--preview-image-position', data.image_position);
    preview.style.fontFamily = data.font_family;
    el('eventPreviewName').textContent = data.event_name;
  }

  function renderPreviewImage() {
    const preview = el('eventPreview');
    const input = el(ids.image);
    if (!preview || !input || !input.files || !input.files[0]) {
      preview?.style.removeProperty('--preview-promo-image');
      return;
    }
    const reader = new FileReader();
    reader.onload = () => {
      preview.style.setProperty('--preview-promo-image', 'url("' + String(reader.result || '') + '")');
    };
    reader.readAsDataURL(input.files[0]);
  }

  function fillForm(data) {
    el(ids.id).value = data?.id || '';
    el(ids.name).value = data?.event_name || '';
    el(ids.start).value = data?.start_date || '';
    el(ids.end).value = data?.end_date || '';
    el(ids.accent).value = data?.accent_color || '#2563eb';
    el(ids.header).value = data?.header_bg || '#ffffff';
    el(ids.sidebar).value = data?.sidebar_bg || '#ffffff';
    el(ids.text).value = data?.text_color || '#0f172a';
    el(ids.sidebarText).value = data?.sidebar_text || '#334155';
    el(ids.font).value = data?.font_family || 'Inter, system-ui, sans-serif';
    el(ids.imageFit).value = data?.image_fit || 'cover';
    el(ids.imagePosition).value = data?.image_position || 'center';
    el(ids.active).checked = data ? Number(data.is_active) === 1 : true;
    el(ids.removeImage).checked = false;
    if (el(ids.image)) el(ids.image).value = '';
    el('eventPreview')?.style.removeProperty('--preview-promo-image');
    renderPreview();
  }

  window.eventAdminReset = function () {
    fillForm(null);
    msg('');
  };

  window.eventAdminSwitchTab = function (tab) {
    const isNotification = tab === 'notification';
    el('eventThemeTab')?.classList.toggle('hidden', isNotification);
    el('eventNotificationTab')?.classList.toggle('hidden', !isNotification);
    document.querySelectorAll('[data-event-tab]').forEach(button => {
      const active = button.dataset.eventTab === tab;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-selected', active ? 'true' : 'false');
    });
  };

  function fillNotification(data) {
    const defaults = {
      id:'',
      notification_name:'Rilis Monbis v1.1',
      title:'Perubahan Signifikan MONBIS v1.1',
      message:'MONBIS v1.1 telah hadir dengan pembaruan tampilan, filter navbar, laporan saldo bank dan baki debet, serta pengalaman yang lebih responsif di berbagai perangkat.\n\nSilakan jelajahi menu-menu terbaru dan gunakan tombol informasi jika membutuhkan panduan.',
      start_date:localDate(0),
      end_date:localDate(6),
      is_active:1,
      show_after_login:1
    };
    const value = data || defaults;
    el(ids.notificationId).value = value.id || '';
    el(ids.notificationName).value = value.notification_name || defaults.notification_name;
    el(ids.notificationTitle).value = value.title || defaults.title;
    el(ids.notificationMessage).value = value.message || defaults.message;
    el(ids.notificationStart).value = value.start_date || defaults.start_date;
    el(ids.notificationEnd).value = value.end_date || defaults.end_date;
    el(ids.notificationActive).checked = data ? Number(value.is_active) === 1 : true;
    el(ids.notificationAfterLogin).checked = data ? Number(value.show_after_login) === 1 : true;
  }

  window.notificationAdminReset = function () {
    fillNotification(null);
    notificationMsg('');
  };

  window.notificationAdminEdit = function (id) {
    const row = notificationRows.find(item => Number(item.id) === Number(id));
    if (!row) return;
    fillNotification(row);
    window.eventAdminSwitchTab('notification');
    window.scrollTo({ top:0, behavior:'smooth' });
  };

  window.notificationAdminDelete = async function (id) {
    if (!confirm('Hapus pemberitahuan ini?')) return;
    const currentAdminId = await waitForAdminId();
    try {
      const res = await fetch(API + 'notification_delete', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Employee-Id':currentAdminId },
        body:JSON.stringify({ id, id_peg:currentAdminId })
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal hapus pemberitahuan');
      notificationMsg('Pemberitahuan berhasil dihapus.');
      await notificationAdminLoad();
      window.MonbisAnnouncement?.refresh?.();
    } catch (error) {
      notificationMsg(error.message, true);
    }
  };

  window.notificationAdminActivate = async function (id) {
    const row = notificationRows.find(item => Number(item.id) === Number(id));
    if (!row) return;
    const currentAdminId = await waitForAdminId();
    try {
      const payload = {
        id:Number(row.id),
        id_peg:currentAdminId,
        notification_name:row.notification_name || 'Pemberitahuan Monbis',
        title:row.title || 'Perubahan Monbis',
        message:row.message || '',
        is_active:'1',
        show_after_login:Number(row.show_after_login) === 1 ? '1' : '0',
        start_date:row.start_date || '',
        end_date:row.end_date || ''
      };
      const res = await fetch(API + 'notification_save', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Employee-Id':currentAdminId },
        body:JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal mengaktifkan pemberitahuan');
      notificationMsg('Pemberitahuan berhasil diaktifkan.');
      fillNotification(json.data);
      await notificationAdminLoad();
      window.MonbisAnnouncement?.refresh?.();
    } catch (error) {
      notificationMsg(error.message, true);
    }
  };

  window.eventAdminEdit = function (id) {
    const row = rows.find(item => Number(item.id) === Number(id));
    if (!row) return;
    fillForm(row);
    window.scrollTo({ top:0, behavior:'smooth' });
  };

  window.eventAdminDelete = async function (id) {
    if (!confirm('Hapus event ini?')) return;
    const currentAdminId = await waitForAdminId();
    try {
      const res = await fetch(API + 'delete', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Employee-Id':currentAdminId },
        body:JSON.stringify({ id, id_peg:currentAdminId })
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal hapus');
      msg('Event berhasil dihapus.');
      await eventAdminLoad();
      window.MonbisEventTheme?.refresh?.();
    } catch (error) {
      msg(error.message, true);
    }
  };

  window.eventAdminActivate = async function (id) {
    const row = rows.find(item => Number(item.id) === Number(id));
    if (!row) return;
    const currentAdminId = await waitForAdminId();
    try {
      const payload = {
        id:Number(row.id),
        id_peg:currentAdminId,
        event_name:row.event_name || 'Event Monbis',
        is_active:'1',
        start_date:row.start_date || '',
        end_date:row.end_date || '',
        accent_color:row.accent_color || '#2563eb',
        header_bg:row.header_bg || '#ffffff',
        sidebar_bg:row.sidebar_bg || '#ffffff',
        text_color:row.text_color || '#0f172a',
        sidebar_text:row.sidebar_text || '#334155',
        font_family:row.font_family || 'Inter, system-ui, sans-serif',
        image_fit:row.image_fit || 'cover',
        image_position:row.image_position || 'center',
        image_path:row.image_path || ''
      };
      const res = await fetch(API + 'save', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Employee-Id':currentAdminId },
        body:JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal aktifkan event');
      msg('Event berhasil diaktifkan.');
      await eventAdminLoad();
      window.MonbisEventTheme?.refresh?.();
    } catch (error) {
      msg(error.message, true);
    }
  };

  function renderList() {
    const box = el('eventList');
    const count = el('eventCount');
    if (count) count.textContent = rows.length + ' event';
    if (!box) return;
    if (!rows.length) {
      box.innerHTML = '<div class="event-row"><div class="event-row__swatch"></div><div><strong>Belum ada event</strong><span>Buat event pertama untuk mengubah navbar dan sidebar.</span></div></div>';
      return;
    }
    box.innerHTML = rows.map(row => {
      const img = assetUrl(row.image_path);
      const media = img
        ? '<img src="' + esc(img) + '" alt="">'
        : '<div class="event-row__swatch" style="--row-accent:' + esc(row.accent_color || '#2563eb') + '"></div>';
      const period = (row.start_date || '-') + ' s/d ' + (row.end_date || '-');
      const active = Number(row.is_active) === 1 ? 'Aktif' : 'Nonaktif';
      const activateBtn = Number(row.is_active) === 1
        ? ''
        : '<button type="button" onclick="eventAdminActivate(' + Number(row.id) + ')">Aktifkan</button>';
      return '<div class="event-row">' + media +
        '<div><strong>' + esc(row.event_name) + '</strong><span>' + esc(period) + ' - ' + active + '</span></div>' +
        '<div class="event-row__actions">' + activateBtn + '<button type="button" onclick="eventAdminEdit(' + Number(row.id) + ')">Edit</button><button type="button" class="danger" onclick="eventAdminDelete(' + Number(row.id) + ')">Hapus</button></div>' +
      '</div>';
    }).join('');
  }

  function renderNotificationList() {
    const box = el('notificationList');
    const count = el('notificationCount');
    if (count) count.textContent = notificationRows.length + ' pemberitahuan';
    if (!box) return;
    if (!notificationRows.length) {
      box.innerHTML = '<div class="event-row"><div class="event-row__swatch"></div><div><strong>Belum ada pemberitahuan</strong><span>Buat pengumuman pertama untuk pengguna Monbis.</span></div></div>';
      return;
    }
    box.innerHTML = notificationRows.map(row => {
      const active = Number(row.is_active) === 1 ? 'Aktif' : 'Nonaktif';
      const login = Number(row.show_after_login) === 1 ? 'Setelah login' : 'Tidak otomatis';
      const period = (row.start_date || '-') + ' s/d ' + (row.end_date || '-');
      const preview = String(row.message || '').replace(/\s+/g, ' ').slice(0, 110);
      const activateBtn = Number(row.is_active) === 1
        ? ''
        : '<button type="button" onclick="notificationAdminActivate(' + Number(row.id) + ')">Aktifkan</button>';
      return '<div class="event-row notification-row">' +
        '<div class="event-row__swatch" style="--row-accent:' + esc('#2563eb') + '"></div>' +
        '<div><strong>' + esc(row.title || row.notification_name) + '</strong><span>' + esc(period + ' - ' + active + ' - ' + login) + '</span><small>' + esc(preview) + '</small></div>' +
        '<div class="event-row__actions">' + activateBtn + '<button type="button" onclick="notificationAdminEdit(' + Number(row.id) + ')">Edit</button><button type="button" class="danger" onclick="notificationAdminDelete(' + Number(row.id) + ')">Hapus</button></div>' +
      '</div>';
    }).join('');
  }

  window.eventAdminLoad = async function () {
    const currentAdminId = await waitForAdminId();
    if (!currentAdminId) {
      el('eventThemePage').innerHTML = '<div class="event-admin-card"><strong>Akses ditolak</strong><p class="event-hint">Menu ini khusus user id_peg 102-119.</p></div>';
      return;
    }
    try {
      const res = await fetch(API + 'list', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Employee-Id':currentAdminId },
        body:JSON.stringify({ id_peg:currentAdminId })
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal memuat event');
      rows = Array.isArray(json.data) ? json.data : [];
      renderList();
    } catch (error) {
      msg(error.message, true);
    }
  };

  window.notificationAdminLoad = async function () {
    const currentAdminId = await waitForAdminId();
    if (!currentAdminId) return;
    try {
      const res = await fetch(API + 'notification_list', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Employee-Id':currentAdminId },
        body:JSON.stringify({ id_peg:currentAdminId })
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal memuat pemberitahuan');
      notificationRows = Array.isArray(json.data) ? json.data : [];
      renderNotificationList();
    } catch (error) {
      notificationMsg(error.message, true);
    }
  };

  async function submitForm(event) {
    event.preventDefault();
    const currentAdminId = await waitForAdminId();
    if (!currentAdminId) {
      msg('Akses ditolak.', true);
      return;
    }
    const form = el('eventThemeForm');
    const data = new FormData(form);
    data.set('id_peg', currentAdminId);
    data.set('is_active', el(ids.active).checked ? '1' : '0');
    data.set('remove_image', el(ids.removeImage).checked ? '1' : '0');
    try {
      msg('Menyimpan event...');
      const res = await fetch(API + 'save', {
        method:'POST',
        headers:{ 'X-Employee-Id':currentAdminId },
        body:data
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal simpan');
      msg('Event berhasil disimpan.');
      fillForm(json.data);
      await eventAdminLoad();
      window.MonbisEventTheme?.refresh?.();
    } catch (error) {
      msg(error.message, true);
    }
  }

  async function submitNotification(event) {
    event.preventDefault();
    const currentAdminId = await waitForAdminId();
    if (!currentAdminId) {
      notificationMsg('Akses ditolak.', true);
      return;
    }
    const payload = {
      id:el(ids.notificationId)?.value || '',
      id_peg:currentAdminId,
      notification_name:el(ids.notificationName)?.value.trim() || 'Pemberitahuan Monbis',
      title:el(ids.notificationTitle)?.value.trim() || '',
      message:el(ids.notificationMessage)?.value.trim() || '',
      start_date:el(ids.notificationStart)?.value || '',
      end_date:el(ids.notificationEnd)?.value || '',
      is_active:el(ids.notificationActive)?.checked ? '1' : '0',
      show_after_login:el(ids.notificationAfterLogin)?.checked ? '1' : '0'
    };
    try {
      notificationMsg('Menyimpan pemberitahuan...');
      const res = await fetch(API + 'notification_save', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-Employee-Id':currentAdminId },
        body:JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.status !== 200) throw new Error(json.message || 'Gagal menyimpan pemberitahuan');
      notificationMsg('Pemberitahuan berhasil disimpan.');
      fillNotification(json.data);
      await notificationAdminLoad();
      window.MonbisAnnouncement?.refresh?.();
    } catch (error) {
      notificationMsg(error.message, true);
    }
  }

  window.eventAdminRefreshAll = function () {
    eventAdminLoad();
    notificationAdminLoad();
  };

  document.addEventListener('DOMContentLoaded', () => {
    Object.values(ids).forEach(id => {
      const node = el(id);
      if (node) node.addEventListener('input', renderPreview);
      if (node) node.addEventListener('change', renderPreview);
    });
    el(ids.image)?.addEventListener('change', renderPreviewImage);
    el('eventThemeForm')?.addEventListener('submit', submitForm);
    el('notificationForm')?.addEventListener('submit', submitNotification);
    fillForm(null);
    fillNotification(null);
    eventAdminLoad();
    notificationAdminLoad();
  });
})();
</script>
