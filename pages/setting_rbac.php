<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
?>

<div id="rbacPage" class="rbac-page">
  <section class="rbac-header">
    <div class="rbac-heading">
      <div class="rbac-heading__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.4 2.8 8.3 7 10 4.2-1.7 7-5.6 7-10V6l-7-3Z"/><path d="m9.3 12 1.8 1.8 3.8-4"/></svg>
      </div>
      <div>
        <span class="rbac-eyebrow">ADMINISTRASI AKSES</span>
        <h1>Matrix Akses Menu</h1>
        <p>Atur akses dengan prioritas pegawai, jabatan, lalu divisi.</p>
      </div>
    </div>
    <div class="rbac-state" id="rbacState" role="status">Memuat data...</div>
  </section>

  <section class="rbac-panel" aria-label="Pengaturan akses menu">
    <div class="rbac-tabs" role="tablist" aria-label="Mode pengaturan akses">
      <button type="button" class="rbac-tab" data-rbac-mode="user" role="tab" aria-selected="false">Per Pegawai</button>
      <button type="button" class="rbac-tab is-active" data-rbac-mode="role" role="tab" aria-selected="true">Per Jabatan</button>
      <button type="button" class="rbac-tab" data-rbac-mode="division" role="tab" aria-selected="false">Per Divisi</button>
    </div>

    <div class="rbac-toolbar">
      <div class="rbac-toolbar__fields rbac-role-fields">
        <label class="rbac-field">
          <span>Jabatan</span>
          <div class="rbac-combobox" id="rbacRoleCombo">
            <input id="rbacRoleSearch" class="rbac-combobox__input" type="search" placeholder="Cari jabatan..." autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="rbacRoleOptions" disabled>
            <select id="rbacRoleSelect" hidden aria-hidden="true"></select>
            <div id="rbacRoleOptions" class="rbac-combobox__options" role="listbox" hidden></div>
          </div>
        </label>
        <label class="rbac-field rbac-field--small">
          <span>Kantor</span>
          <select id="rbacOfficeSelect" disabled>
            <option value="*">Semua kantor</option>
          </select>
        </label>
      </div>
      <div class="rbac-toolbar__fields rbac-division-fields" hidden>
        <label class="rbac-field rbac-field--wide">
          <span>Divisi</span>
          <select id="rbacDivisionSelect" disabled></select>
        </label>
      </div>
      <div class="rbac-toolbar__fields rbac-user-fields" hidden>
        <label class="rbac-field rbac-field--wide">
          <span>Cari pegawai</span>
          <input id="rbacUserSearch" type="search" placeholder="Nama atau ID pegawai" autocomplete="off">
        </label>
        <div class="rbac-selection-info" id="rbacSelectionInfo">Belum ada pegawai dipilih</div>
      </div>
    </div>

    <div class="rbac-user-picker" id="rbacUserPicker" hidden>
      <div class="rbac-user-picker__head"><strong>Pilih satu atau beberapa ID pegawai</strong><button type="button" id="rbacToggleUsers" class="rbac-link-button">Pilih semua hasil</button></div>
      <div class="rbac-user-list" id="rbacUserList"></div>
    </div>

    <div class="rbac-note" id="rbacRoleNote">Akses jabatan adalah lapisan kedua. Jika belum disimpan, pegawai mengikuti akses divisinya.</div>
    <div class="rbac-note" id="rbacDivisionNote" hidden>Akses divisi menjadi dasar. Jabatan menimpa divisi, lalu pengaturan pegawai menjadi prioritas paling tinggi.</div>
    <div class="rbac-note" id="rbacUserNote" hidden>Matrix menampilkan akses efektif pegawai. Simpan untuk membuat override pegawai; reset mengembalikan ke jabatan dan divisi.</div>

    <div class="rbac-table-wrap">
      <table class="rbac-table">
        <thead><tr><th>Fitur / halaman</th><th>Kelompok</th><th class="rbac-access-col">Akses</th></tr></thead>
        <tbody id="rbacFeatureBody"><tr><td colspan="3" class="rbac-empty">Menyiapkan daftar fitur...</td></tr></tbody>
      </table>
    </div>

    <div class="rbac-footer">
      <span id="rbacFeatureCount">0 fitur</span>
      <div class="rbac-footer__actions">
        <button type="button" class="rbac-button rbac-button--danger" id="rbacResetUsers" hidden>Kembalikan ke jabatan</button>
        <button type="button" class="rbac-button rbac-button--primary" id="rbacSave">Simpan akses</button>
      </div>
    </div>
  </section>
  <div id="rbacToastRegion" class="rbac-toast-region" aria-live="polite" aria-atomic="true"></div>
</div>

<style>
  #rbacPage{--rbac-ink:#17324d;--rbac-muted:#6e8294;--rbac-line:#dce6ed;--rbac-blue:#1565d8;--rbac-soft:#f5f9fc;color:var(--rbac-ink);font-family:Roboto,"Segoe UI",Arial,sans-serif;padding:14px;min-height:100%;}
  .rbac-header,.rbac-panel{border:1px solid var(--rbac-line);background:#fff;border-radius:14px;box-shadow:0 5px 18px rgba(19,54,79,.055)}
  .rbac-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 18px}.rbac-heading{display:flex;align-items:center;gap:12px;min-width:0}.rbac-heading__icon{display:grid;place-items:center;width:42px;height:42px;flex:0 0 auto;border-radius:11px;background:#e8f1ff;color:var(--rbac-blue)}.rbac-heading__icon svg{width:23px;height:23px}.rbac-eyebrow{display:block;margin-bottom:4px;color:#5e7fa5;font-size:8px;font-weight:900;letter-spacing:.14em}.rbac-heading h1{margin:0;font-size:20px;line-height:1.2;font-weight:900;letter-spacing:-.02em}.rbac-heading p{margin:4px 0 0;color:var(--rbac-muted);font-size:10px;font-weight:600}.rbac-state{padding:6px 9px;border:1px solid #dce7ef;border-radius:7px;color:#668096;font-size:9px;font-weight:800;white-space:nowrap}.rbac-state[data-state="success"]{border-color:#b7e6ce;background:#f0fff7;color:#087648}.rbac-state[data-state="error"]{border-color:#fecaca;background:#fff5f5;color:#b42318}
  .rbac-panel{margin-top:12px;padding:13px}.rbac-tabs{display:flex;gap:5px;border-bottom:1px solid var(--rbac-line);margin:-1px -1px 13px;padding:0 1px}.rbac-tab{min-height:38px;padding:0 13px;border:0;border-bottom:2px solid transparent;background:transparent;color:#6c8295;font:800 10px Roboto,"Segoe UI",sans-serif;cursor:pointer}.rbac-tab:hover{color:var(--rbac-blue)}.rbac-tab.is-active{border-bottom-color:var(--rbac-blue);color:var(--rbac-blue)}
  .rbac-toolbar{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap}.rbac-toolbar__fields{display:flex;align-items:flex-end;gap:8px;min-width:0}.rbac-field{display:flex;flex-direction:column;gap:5px;min-width:270px}.rbac-field--small{min-width:150px}.rbac-field--wide{min-width:290px}.rbac-field span{padding-left:1px;color:#627c93;font-size:8px;font-weight:900;letter-spacing:.06em;text-transform:uppercase}.rbac-field select,.rbac-field input{height:36px;min-width:0;border:1px solid #cbdbe7;border-radius:8px;padding:0 10px;background:#fbfdff;color:#173b5b;font:750 10px Roboto,"Segoe UI",sans-serif;outline:0}.rbac-field select:focus,.rbac-field input:focus{border-color:#6ea5e7;box-shadow:0 0 0 3px rgba(21,101,216,.1)}.rbac-button{min-height:36px;padding:0 11px;border:1px solid transparent;border-radius:8px;font:850 10px Roboto,"Segoe UI",sans-serif;cursor:pointer;transition:background .15s,border-color .15s,color .15s}.rbac-button:disabled{opacity:.55;cursor:not-allowed}.rbac-button--quiet{border-color:#cadbe8;background:#fff;color:#416078}.rbac-button--quiet:hover{border-color:#8db3d5;background:#f2f8fd}.rbac-button--primary{background:var(--rbac-blue);color:#fff;box-shadow:0 4px 10px rgba(21,101,216,.18)}.rbac-button--primary:hover{background:#0e56bd}.rbac-button--danger{border-color:#f1b8b8;background:#fff7f7;color:#b42318}.rbac-button--danger:hover{background:#fff0f0}
  .rbac-combobox{position:relative;width:100%}.rbac-combobox__input{width:100%;height:36px;box-sizing:border-box}.rbac-combobox__options{position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:20;max-height:260px;overflow:auto;border:1px solid #cbdbe7;border-radius:8px;background:#fff;box-shadow:0 10px 24px rgba(19,54,79,.14);padding:4px}.rbac-combobox__option{display:block;width:100%;border:0;border-radius:6px;background:transparent;color:#173b5b;text-align:left;padding:9px 10px;font:750 10px Roboto,"Segoe UI",sans-serif;cursor:pointer}.rbac-combobox__option:hover,.rbac-combobox__option:focus{background:#edf4fb;color:var(--rbac-blue);outline:0}.rbac-combobox__empty{padding:9px 10px;color:#7890a2;font-size:9px}
  .rbac-note{margin:11px 0;padding:9px 10px;border-left:3px solid #91b7df;background:#f5f9fd;color:#567188;font-size:9px;line-height:1.45}.rbac-user-picker{margin-top:12px;border:1px solid var(--rbac-line);border-radius:9px;background:#fbfdff}.rbac-user-picker__head{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 10px;border-bottom:1px solid var(--rbac-line);font-size:10px}.rbac-link-button{border:0;background:transparent;color:var(--rbac-blue);font:800 9px Roboto,"Segoe UI",sans-serif;cursor:pointer}.rbac-user-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:4px;max-height:190px;overflow:auto;padding:7px}.rbac-user-option{display:flex;align-items:center;gap:8px;min-width:0;padding:8px;border:1px solid transparent;border-radius:7px;background:#fff;cursor:pointer}.rbac-user-option:hover{border-color:#cfe0ed;background:#f6faff}.rbac-user-option input{width:16px;height:16px;flex:0 0 auto;accent-color:var(--rbac-blue)}.rbac-user-option__text{display:flex;flex-direction:column;min-width:0}.rbac-user-option strong,.rbac-user-option small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.rbac-user-option strong{font-size:9px}.rbac-user-option small{color:#7890a2;font-size:8px}.rbac-selection-info{padding-bottom:9px;color:#5c7690;font-size:9px;font-weight:750;white-space:nowrap}
  .rbac-table-wrap{max-height:calc(100vh - 330px);min-height:290px;overflow:auto;border:1px solid var(--rbac-line);border-radius:9px;scrollbar-color:#b3c5d1 #f4f7f9;scrollbar-width:thin}.rbac-table{width:100%;min-width:560px;border-collapse:separate;border-spacing:0;table-layout:fixed}.rbac-table th,.rbac-table td{border-right:1px solid var(--rbac-line);border-bottom:1px solid var(--rbac-line)}.rbac-table th:last-child,.rbac-table td:last-child{border-right:0}.rbac-table thead{position:sticky;top:0;z-index:2}.rbac-table th{height:33px;padding:6px 9px;background:#edf4fb;color:#45647d;text-align:left;font-size:9px;font-weight:900}.rbac-table th:nth-child(1){width:52%}.rbac-table th:nth-child(2){width:34%}.rbac-table th:nth-child(3){width:14%;text-align:center}.rbac-table td{height:35px;padding:6px 9px;background:#fff;font-size:9px}.rbac-table tbody tr:nth-child(even) td{background:#fbfdff}.rbac-table tbody tr:hover td{background:#f2f8fe}.rbac-table td:first-child{font-weight:800}.rbac-table td:nth-child(2){color:#6c8498;font-size:8px}.rbac-access-col{text-align:center!important}.rbac-check{display:grid;place-items:center}.rbac-check input{width:17px;height:17px;accent-color:var(--rbac-blue);cursor:pointer}.rbac-module-row td{background:#eaf3ff!important;color:#234d78;font-weight:900}.rbac-module-toggle{display:flex;align-items:center;gap:8px;cursor:pointer}.rbac-module-toggle input{width:17px;height:17px;accent-color:var(--rbac-blue);cursor:pointer}.rbac-module-toggle__text{display:flex;flex-direction:column;gap:2px}.rbac-module-toggle__text strong{font-size:9px}.rbac-module-toggle__text small{color:#6c8498;font-size:8px;font-weight:700}.rbac-module-status{color:#52708a;font-size:8px;font-weight:850}.rbac-feature-name{padding-left:24px!important}.rbac-empty{text-align:center!important;color:#7890a2!important;height:120px!important}.rbac-footer{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 2px 1px;color:#6c8498;font-size:9px;font-weight:800}.rbac-footer__actions{display:flex;gap:7px;flex-wrap:wrap;justify-content:flex-end}
  .rbac-toast-region{position:fixed;right:18px;bottom:18px;z-index:1200;display:grid;gap:8px;width:min(340px,calc(100vw - 36px));pointer-events:none}.rbac-toast{border:1px solid #dce6ed;border-left:3px solid #6c8498;border-radius:9px;background:#fff;color:#17324d;box-shadow:0 10px 28px rgba(19,54,79,.16);padding:11px 13px;font-size:10px;font-weight:750;line-height:1.45;opacity:0;transform:translateY(8px);transition:opacity .16s ease,transform .16s ease}.rbac-toast.is-visible{opacity:1;transform:translateY(0)}.rbac-toast--success{border-left-color:#0b8f61}.rbac-toast--error{border-left-color:#c0392b;color:#8d241b}.rbac-toast--info{border-left-color:var(--rbac-blue)}
  @media(max-width:700px){#rbacPage{padding:8px}.rbac-header{align-items:flex-start;flex-direction:column;padding:13px}.rbac-heading h1{font-size:18px}.rbac-state{align-self:stretch;text-align:left}.rbac-panel{padding:10px}.rbac-toolbar__fields{width:100%;flex-direction:column;align-items:stretch}.rbac-field,.rbac-field--small,.rbac-field--wide{width:100%;min-width:0}.rbac-user-list{grid-template-columns:1fr}.rbac-table-wrap{max-height:calc(100vh - 410px);min-height:260px}.rbac-footer{align-items:flex-start;flex-direction:column}.rbac-footer__actions{width:100%}.rbac-footer__actions .rbac-button{flex:1}}
  @media(max-width:400px){.rbac-tab{padding:0 9px;font-size:9px}.rbac-table{min-width:500px}.rbac-table th:nth-child(1){width:48%}.rbac-table th:nth-child(2){width:38%}}
</style>

<script>
(() => {
  const API = './api/index.php?request=rbac';
  const el = id => document.getElementById(id);
  const state = { mode:'role', menus:[], roles:[], roleOptions:[], divisions:[], users:[], selectedUsers:new Set(), currentKeys:new Set(), roleMatrix:null, userMatrix:null };
  const MATRIX_VISIBLE_FEATURES = new Set(['page:update_flowpar', 'page:tv_cabang', 'page:tv']);
  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const token = () => { try { const local = localStorage.getItem('dpk_token'); if (local) return local; } catch (error) {} const match = document.cookie.match(/(?:^|;\s*)sso_token=([^;]+)/); return match ? decodeURIComponent(match[1]) : ''; };
  const headers = () => { const value = token(); return { 'Content-Type':'application/json', ...(value ? {Authorization:`Bearer ${value}`} : {}) }; };
  const setState = (text, status = '') => { const node = el('rbacState'); node.textContent = text; node.dataset.state = status; };
  const showToast = (message, type = 'info') => {
    const region = el('rbacToastRegion');
    if (!region || !message) return;
    const toast = document.createElement('div');
    toast.className = `rbac-toast rbac-toast--${type}`;
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
    toast.textContent = message;
    region.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('is-visible'));
    window.setTimeout(() => {
      toast.classList.remove('is-visible');
      window.setTimeout(() => toast.remove(), 180);
    }, 4200);
  };
  const selectedRole = () => { const role = state.roles.find(item => `${item.job_position}|${item.group_jabatan}` === el('rbacRoleSelect').value); return role || state.roles[0] || {job_position:'',group_jabatan:'',kode:'*'}; };
  const selectedUserIds = () => [...state.selectedUsers];
  const featureKeys = () => [...el('rbacFeatureBody').querySelectorAll('input[data-feature-key]:checked')].map(input => input.dataset.featureKey);
  const isVisibleMatrixFeature = menu => {
    const moduleName = String(menu.module_name || '').trim().toLowerCase();
    return moduleName !== 'lainnya' || MATRIX_VISIBLE_FEATURES.has(String(menu.feature_key || '').trim().toLowerCase());
  };

  async function post(body) {
    const response = await fetch(API, {method:'POST', credentials:'same-origin', headers:headers(), body:JSON.stringify(body)});
    const raw = await response.text();
    let json = {};
    try { json = raw ? JSON.parse(raw) : {}; } catch (error) {}
    if (!response.ok || Number(json.status) >= 400) throw new Error(json.message || `Permintaan RBAC gagal (${response.status}).`);
    return json.data || {};
  }

  const roleLabel = item => `${item.job_position}${item.group_jabatan ? ` - ${item.group_jabatan}` : ''}`;

  function renderRoleOptions(query = '', open = false) {
    const options = el('rbacRoleOptions');
    const normalizedQuery = String(query || '').trim().toLowerCase();
    const filtered = state.roleOptions.filter(item => !normalizedQuery || roleLabel(item).toLowerCase().includes(normalizedQuery));
    options.innerHTML = filtered.length
      ? filtered.map(item => `<button type="button" class="rbac-combobox__option" role="option" data-role-value="${escapeHtml(item.value)}">${escapeHtml(roleLabel(item))}</button>`).join('')
      : '<div class="rbac-combobox__empty">Jabatan tidak ditemukan.</div>';
    options.hidden = !open;
    el('rbacRoleSearch').setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function chooseRole(value) {
    const option = state.roleOptions.find(item => item.value === value);
    if (!option) return;
    el('rbacRoleSelect').value = option.value;
    el('rbacRoleSearch').value = roleLabel(option);
    renderRoleOptions('', false);
    loadScopeMatrix();
  }

  function renderRoles() {
    const select = el('rbacRoleSelect');
    const uniqueRoles = [...new Map(state.roles.map(item => [`${item.job_position}|${item.group_jabatan}`, item])).values()];
    state.roleOptions = uniqueRoles.map(item => ({...item, value:`${item.job_position}|${item.group_jabatan}`}));
    select.innerHTML = state.roleOptions.map(item => `<option value="${escapeHtml(item.value)}">${escapeHtml(roleLabel(item))}</option>`).join('');
    select.disabled = !state.roleOptions.length;
    const selected = state.roleOptions[0];
    if (selected) {
      select.value = selected.value;
      el('rbacRoleSearch').value = roleLabel(selected);
    }
    el('rbacRoleSearch').disabled = !state.roleOptions.length;
    renderRoleOptions();
  }

  function renderDivisions() {
    const select = el('rbacDivisionSelect');
    select.innerHTML = state.divisions.map(item => `<option value="${escapeHtml(item.unit_kerja)}">${escapeHtml(item.unit_kerja)}</option>`).join('');
    select.disabled = !state.divisions.length;
  }

  function renderUsers() {
    const query = String(el('rbacUserSearch').value || '').trim().toLowerCase();
    const rows = state.users.filter(user => !query || [user.employee_id,user.full_name,user.job_position,user.branch_name].some(value => String(value || '').toLowerCase().includes(query)));
    el('rbacUserList').innerHTML = rows.length ? rows.map(user => {
      const id = String(user.employee_id);
      return `<label class="rbac-user-option"><input type="checkbox" data-user-id="${escapeHtml(id)}" ${state.selectedUsers.has(id) ? 'checked' : ''}><span class="rbac-user-option__text"><strong>${escapeHtml(user.full_name || id)}</strong><small>${escapeHtml(id)} · ${escapeHtml(user.job_position || 'Jabatan belum diisi')}</small></span></label>`;
    }).join('') : '<div class="rbac-empty">Pegawai tidak ditemukan.</div>';
    updateSelectionInfo();
  }

  function updateSelectionInfo() {
    const count = state.selectedUsers.size;
    el('rbacSelectionInfo').textContent = count ? `${count} pegawai dipilih` : 'Belum ada pegawai dipilih';
  }

  function renderMatrix(rows = []) {
    const granted = new Set(rows.filter(row => Number(row.can_view) === 1).map(row => row.feature_key));
    state.currentKeys = granted;
    const groups = new Map();
    state.menus.forEach(menu => {
      const moduleName = String(menu.module_name || 'Lainnya');
      if (!groups.has(moduleName)) groups.set(moduleName, []);
      groups.get(moduleName).push(menu);
    });
    let markup = '';
    groups.forEach((menus, moduleName) => {
      const visibleMenus = menus.filter(isVisibleMatrixFeature);
      const grantedCount = visibleMenus.filter(menu => granted.has(menu.feature_key)).length;
      const moduleHidden = visibleMenus.length ? '' : ' hidden';
      markup += `<tr class="rbac-module-row"${moduleHidden}><td colspan="2"><label class="rbac-module-toggle"><input type="checkbox" data-module-toggle data-module-key="${escapeHtml(moduleName)}" ${visibleMenus.length > 0 && grantedCount === visibleMenus.length ? 'checked' : ''} aria-label="Akses semua menu ${escapeHtml(moduleName)}"><span class="rbac-module-toggle__text"><strong>Menu ${escapeHtml(moduleName)}</strong><small>${visibleMenus.length} halaman</small></span></label></td><td class="rbac-access-col"><span class="rbac-module-status" data-module-status="${escapeHtml(moduleName)}">${grantedCount}/${visibleMenus.length}</span></td></tr>`;
      markup += menus.map(menu => {
        const visible = isVisibleMatrixFeature(menu);
        return `<tr${visible ? '' : ' hidden'}><td class="rbac-feature-name">${escapeHtml(menu.feature_label)}</td><td>${escapeHtml(moduleName)}</td><td class="rbac-access-col"><label class="rbac-check"><input type="checkbox" data-feature-key="${escapeHtml(menu.feature_key)}" data-module-key="${escapeHtml(moduleName)}" data-matrix-visible="${visible ? '1' : '0'}" ${granted.has(menu.feature_key) ? 'checked' : ''} aria-label="Akses ${escapeHtml(menu.feature_label)}"></label></td></tr>`;
      }).join('');
    });
    el('rbacFeatureBody').innerHTML = markup || '<tr><td colspan="3" class="rbac-empty">Daftar fitur belum tersedia.</td></tr>';
    const visibleCount = state.menus.filter(isVisibleMatrixFeature).length;
    el('rbacFeatureCount').textContent = `${visibleCount} fitur ditampilkan dari ${state.menus.length}`;
    syncModuleToggles();
  }

  function syncModuleToggles() {
    el('rbacFeatureBody').querySelectorAll('input[data-module-toggle]').forEach(toggle => {
      const moduleKey = toggle.dataset.moduleKey;
      const children = [...el('rbacFeatureBody').querySelectorAll('input[data-feature-key]')].filter(input => input.dataset.moduleKey === moduleKey && input.dataset.matrixVisible !== '0');
      const checkedCount = children.filter(input => input.checked).length;
      toggle.checked = children.length > 0 && checkedCount === children.length;
      toggle.indeterminate = checkedCount > 0 && checkedCount < children.length;
      const status = [...el('rbacFeatureBody').querySelectorAll('[data-module-status]')].find(node => node.dataset.moduleStatus === moduleKey);
      if (status) status.textContent = `${checkedCount}/${children.length}`;
    });
  }

  async function loadScopeMatrix() {
    if (state.mode === 'division') {
      const unit = el('rbacDivisionSelect').value;
      if (!unit) { renderMatrix([]); return; }
      setState('Memuat matrix divisi...');
      const data = await post({type:'matrix', scope:'role', scope_type:'divisi', unit_kerja:unit, kode_kantor:'*'});
      renderMatrix(data.rows || []);
      setState('Siap disimpan', 'success');
      return;
    }
    const role = selectedRole();
    if (!role.job_position) { renderMatrix([]); return; }
    setState('Memuat matrix jabatan...');
    const data = await post({type:'matrix', scope:'role', scope_type:'jabatan', ...role, kode_kantor:el('rbacOfficeSelect').value});
    renderMatrix(data.rows || []);
    setState('Siap disimpan', 'success');
  }

  async function loadUserMatrix() {
    const ids = selectedUserIds();
    if (!ids.length) { renderMatrix([]); setState('Pilih pegawai'); return; }
    setState('Memuat override pegawai...');
    const data = await post({type:'matrix', scope:'user', employee_ids:ids.slice(0, 1)});
    renderMatrix(data.rows || []);
    setState(ids.length > 1 ? 'Matrix memakai pegawai pertama, simpan ke semua pilihan' : 'Akses efektif siap disimpan', 'success');
  }

  async function loadMatrix() {
    try { if (state.mode === 'user') await loadUserMatrix(); else await loadScopeMatrix(); }
    catch (error) { setState(error.message, 'error'); renderMatrix([]); showToast(error.message, 'error'); }
  }

  function updateSaveLabel() {
    el('rbacSave').textContent = state.mode === 'user' ? 'Simpan override' : 'Simpan akses';
  }

  function applyMode() {
    const roleMode = state.mode === 'role';
    const divisionMode = state.mode === 'division';
    const userMode = state.mode === 'user';
    document.querySelectorAll('[data-rbac-mode]').forEach(button => { const active = button.dataset.rbacMode === state.mode; button.classList.toggle('is-active', active); button.setAttribute('aria-selected', active ? 'true' : 'false'); });
    document.querySelector('.rbac-role-fields').hidden = !roleMode;
    document.querySelector('.rbac-division-fields').hidden = !divisionMode;
    document.querySelector('.rbac-user-fields').hidden = !userMode;
    el('rbacUserPicker').hidden = !userMode;
    el('rbacRoleNote').hidden = !roleMode;
    el('rbacDivisionNote').hidden = !divisionMode;
    el('rbacUserNote').hidden = !userMode;
    el('rbacResetUsers').hidden = !userMode;
    updateSaveLabel();
    loadMatrix();
  }

  async function save() {
    const keys = featureKeys();
    const button = el('rbacSave');
    button.disabled = true;
    try {
      if (state.mode === 'role') {
        const role = selectedRole();
        await post({type:'save_role', scope_type:'jabatan', job_position:role.job_position, group_jabatan:role.group_jabatan, kode_kantor:el('rbacOfficeSelect').value, feature_keys:keys});
      } else if (state.mode === 'division') {
        const unit = el('rbacDivisionSelect').value;
        if (!unit) throw new Error('Pilih divisi terlebih dahulu.');
        await post({type:'save_role', scope_type:'divisi', unit_kerja:unit, kode_kantor:'*', feature_keys:keys});
      } else {
        const ids = selectedUserIds();
        if (!ids.length) throw new Error('Pilih minimal satu ID pegawai.');
        await post({type:'save_users', employee_ids:ids, feature_keys:keys});
      }
      await loadMatrix();
      const message = state.mode === 'user' ? 'Override pegawai berhasil disimpan.' : state.mode === 'role' ? 'Akses jabatan berhasil disimpan.' : 'Akses divisi berhasil disimpan.';
      setState(message, 'success');
      showToast(message, 'success');
      if (window.__USER?.rbac) window.__USER.rbac = null;
    } catch (error) { setState(error.message, 'error'); showToast(error.message, 'error'); }
    finally { button.disabled = false; }
  }

  async function resetUsers() {
    const ids = selectedUserIds();
    if (!ids.length) { setState('Pilih minimal satu ID pegawai.', 'error'); return; }
    const button = el('rbacResetUsers');
    button.disabled = true;
    try { await post({type:'reset_users', employee_ids:ids}); await loadUserMatrix(); setState('Override dihapus, kembali ke jabatan dan divisi', 'success'); showToast('Override pegawai dihapus.', 'success'); }
    catch (error) { setState(error.message, 'error'); showToast(error.message, 'error'); }
    finally { button.disabled = false; }
  }

  async function init() {
    try {
      const data = await post({type:'bootstrap'});
      state.menus = data.menus || [];
      state.roles = data.roles || [];
      state.divisions = data.divisions || [];
      state.users = data.users || [];
      renderRoles(); renderDivisions(); renderUsers(); renderMatrix([]); await loadMatrix();
    } catch (error) { setState(error.message, 'error'); showToast(error.message, 'error'); el('rbacFeatureBody').innerHTML = `<tr><td colspan="3" class="rbac-empty">${escapeHtml(error.message)}</td></tr>`; el('rbacSave').disabled = true; }
  }

  document.querySelectorAll('[data-rbac-mode]').forEach(button => button.addEventListener('click', () => { state.mode = button.dataset.rbacMode; applyMode(); }));
  const roleSearch = el('rbacRoleSearch');
  roleSearch.addEventListener('focus', () => { roleSearch.select(); renderRoleOptions('', true); });
  roleSearch.addEventListener('input', () => renderRoleOptions(roleSearch.value, true));
  roleSearch.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
      const selected = selectedRole();
      roleSearch.value = selected.job_position ? roleLabel(selected) : '';
      renderRoleOptions('', false);
    }
    if (event.key === 'Enter') {
      const first = el('rbacRoleOptions').querySelector('[data-role-value]');
      if (first) { event.preventDefault(); chooseRole(first.dataset.roleValue); }
    }
  });
  el('rbacRoleOptions').addEventListener('mousedown', event => {
    const option = event.target.closest('[data-role-value]');
    if (!option) return;
    event.preventDefault();
    chooseRole(option.dataset.roleValue);
  });
  document.addEventListener('click', event => {
    if (!el('rbacRoleCombo').contains(event.target)) renderRoleOptions('', false);
  });
  el('rbacOfficeSelect').addEventListener('change', loadScopeMatrix);
  el('rbacDivisionSelect').addEventListener('change', loadScopeMatrix);
  el('rbacUserSearch').addEventListener('input', renderUsers);
  el('rbacUserList').addEventListener('change', event => { const input = event.target.closest('input[data-user-id]'); if (!input) return; const id = input.dataset.userId; if (input.checked) state.selectedUsers.add(id); else state.selectedUsers.delete(id); updateSelectionInfo(); loadUserMatrix(); });
  el('rbacToggleUsers').addEventListener('click', () => { const query = String(el('rbacUserSearch').value || '').trim().toLowerCase(); state.users.filter(user => !query || [user.employee_id,user.full_name,user.job_position,user.branch_name].some(value => String(value || '').toLowerCase().includes(query))).forEach(user => state.selectedUsers.add(String(user.employee_id))); renderUsers(); loadUserMatrix(); });
  el('rbacFeatureBody').addEventListener('change', event => {
    const moduleToggle = event.target.closest('input[data-module-toggle]');
    if (moduleToggle) {
      const moduleKey = moduleToggle.dataset.moduleKey;
      el('rbacFeatureBody').querySelectorAll('input[data-feature-key]').forEach(input => {
        if (input.dataset.moduleKey === moduleKey && input.dataset.matrixVisible !== '0') input.checked = moduleToggle.checked;
      });
    }
    syncModuleToggles();
  });
  el('rbacSave').addEventListener('click', save);
  el('rbacResetUsers').addEventListener('click', resetUsers);
  init();
})();
</script>
