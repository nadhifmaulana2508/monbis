<style>
  /*
   * Style Recovery NPL tidak boleh mengubah shell/navbar aplikasi.
   * Jangan override `.hidden` atau `body` secara global dari page fragment.
   */
  #recoveryPage,
  #modalDebiturRecovery,
  #modalPeringatan,
  #modalInfoRecovery {
      --primary:#2563eb;
      --bg:#f8fafc;
      --text:#334155;
      font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
      color:var(--text);
  }

  #recoveryPage *,
  #modalDebiturRecovery *,
  #modalPeringatan *,
  #modalInfoRecovery * { box-sizing:border-box; }

  /* === PAGE: dibuat rapat agar area kerja maksimal === */
  #recoveryPage {
      width:100%;
      max-width:none !important;
      padding:6px 8px 8px !important;
      height:calc(100vh - 60px);
      height:calc(100dvh - 60px);
      overflow:hidden;
  }
  #recoveryHeader {
      margin-bottom:6px !important;
      padding:8px 10px !important;
      border-radius:10px !important;
      gap:8px !important;
  }
  #recoveryHeaderTop { min-width:0; }

  /* === INPUT & CONTROLS === */
  .filter-box { min-width:0; }
  .inp {
      width:100%;
      height:34px;
      min-width:0;
      border:1px solid #cbd5e1;
      border-radius:7px;
      padding:0 9px;
      background:#fff;
      color:#334155;
      font-size:12px;
      outline:none;
      transition:border-color .15s,box-shadow .15s;
  }
  .inp:focus {
      border-color:var(--primary);
      box-shadow:0 0 0 3px rgba(37,99,235,.11);
  }
  .inp:disabled {
      background:#f8fafc;
      color:#64748b;
      cursor:not-allowed;
  }
  select.inp {
      appearance:none;
      background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat:no-repeat;
      background-position:right .55rem center;
      background-size:1em;
      padding-right:1.75rem;
  }
  .lbl {
      display:block;
      margin-bottom:2px;
      color:#475569;
      font-size:9px;
      font-weight:850;
      line-height:1.1;
      letter-spacing:.045em;
      text-transform:uppercase;
      white-space:nowrap;
  }
  input[type="date"] { position:relative; cursor:pointer; }
  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator {
      position:absolute;
      inset:0;
      width:100%;
      height:100%;
      opacity:0;
      cursor:pointer;
  }

  .btn-icon {
      width:36px;
      height:34px;
      flex:0 0 auto;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:0;
      border-radius:7px;
      background:var(--primary);
      color:#fff;
      cursor:pointer;
      transition:transform .15s,background .15s,box-shadow .15s;
  }
  .btn-icon:hover { background:#1d4ed8; transform:translateY(-1px); box-shadow:0 4px 8px rgba(15,23,42,.12); }
  .mobile-filter-toggle { display:none; }
  .rec-info-btn {
      width:20px;
      height:20px;
      flex:0 0 20px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid #bfdbfe;
      border-radius:999px;
      background:#eff6ff;
      color:#2563eb;
      font-size:11px;
      font-weight:900;
      cursor:pointer;
  }

  /* === TABLE WIDTH SYSTEM === */
  #recScroller {
      --recCode:56px;
      --recName:176px;
      --recNoa:58px;
      --recNom:126px;
      --recRatio:92px;
      --recNet:146px;
      --rec_headH:64px;

      position:relative;
      flex:1 1 auto;
      min-height:0;
      width:100%;
      height:100%;
      overflow:auto;
      border:1px solid #dbe3ee;
      border-radius:8px;
      background:#fff;
      -webkit-overflow-scrolling:touch;
      overscroll-behavior:contain;
      scrollbar-gutter:stable;
  }
  #recScroller::-webkit-scrollbar { width:7px; height:7px; }
  #recScroller::-webkit-scrollbar-track { background:#f8fafc; }
  #recScroller::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }

  #tabelRecovery {
      width:calc(
          var(--recCode) + var(--recName) +
          (var(--recNoa) * 5) + (var(--recNom) * 5) +
          var(--recRatio) + var(--recNet)
      );
      min-width:100%;
      border-collapse:separate;
      border-spacing:0;
      table-layout:fixed;
      font-size:11px;
      color:#334155;
  }
  #tabelRecovery col.rec-col-code { width:var(--recCode); }
  #tabelRecovery col.rec-col-name { width:var(--recName); }
  #tabelRecovery col.rec-col-noa { width:var(--recNoa); }
  #tabelRecovery col.rec-col-nom { width:var(--recNom); }
  #tabelRecovery col.rec-col-ratio { width:var(--recRatio); }
  #tabelRecovery col.rec-col-net { width:var(--recNet); }

  #tabelRecovery th,
  #tabelRecovery td {
      height:38px;
      padding:6px 8px;
      vertical-align:middle;
      white-space:nowrap;
      border-bottom:1px solid #edf2f7;
  }
  #tabelRecovery tbody tr:hover td { background:#f8fafc; }

  /* === HEADER === */
  #tabelRecovery thead th {
      position:sticky;
      z-index:60;
      padding:4px 6px;
      background:#f1f5f9;
      color:#1e3a8a;
      border-bottom:1px solid #cbd5e1;
      box-shadow:inset 0 1px 0 #cbd5e1,inset 0 -1px 0 #cbd5e1;
      font-size:9px;
      font-weight:850;
      line-height:1.08;
      letter-spacing:.02em;
      text-transform:uppercase;
      white-space:normal;
  }
  #tabelRecovery .rec-head-1 th { top:0; height:35px; }
  #tabelRecovery .rec-head-2 th { top:35px; height:29px; font-size:8px; }
  #tabelRecovery thead th[rowspan="2"] { top:0; height:64px; vertical-align:middle; }
  #tabelRecovery .rec-head-2 th { background:#f8fafc; color:#475569; }
  .rec-group-head { text-align:center !important; border-right:1px solid #cbd5e1; }
  .rec-sub-head { text-align:center !important; border-right:1px solid #e2e8f0; }
  .head-lunas { color:#1e40af !important; background:#eff6ff !important; }
  .head-backflow { color:#b45309 !important; background:#fffbeb !important; }
  .head-angsuran { color:#047857 !important; background:#ecfdf5 !important; }
  .head-flow { color:#be123c !important; background:#fff1f2 !important; }
  .head-total { color:#0e7490 !important; background:#ecfeff !important; }
  .head-net { color:#4338ca !important; background:#eef2ff !important; }
  .head-ratio { color:#854d0e !important; background:#fefce8 !important; }
  .head-net { line-height:1.12 !important; overflow-wrap:anywhere; }
  .rec-head-short { display:none; }

  /* === STICKY COLUMNS === */
  .col-kode {
      position:sticky;
      left:0;
      z-index:45;
      width:var(--recCode);
      min-width:var(--recCode);
      max-width:var(--recCode);
      background:#fff;
      border-right:1px solid #e2e8f0;
      text-align:center;
  }
  .col-nama {
      position:sticky;
      left:var(--recCode);
      z-index:44;
      width:var(--recName);
      min-width:var(--recName);
      max-width:var(--recName);
      overflow:hidden;
      background:#fff;
      border-right:1px solid #dbe3ee;
      box-shadow:4px 0 8px -7px rgba(15,23,42,.8);
      text-align:left;
  }
  #tabelRecovery thead th.col-kode { z-index:75; background:#e0f2fe; }
  #tabelRecovery thead th.col-nama { z-index:74; background:#e0f2fe; }
  #tabelRecovery tbody tr:hover .col-kode,
  #tabelRecovery tbody tr:hover .col-nama { background:#f8fafc; }
  .col-nama > div { overflow:hidden; text-overflow:ellipsis; }

  .rec-noa-cell {
      width:var(--recNoa);
      min-width:var(--recNoa);
      max-width:var(--recNoa);
      text-align:center;
      font-variant-numeric:tabular-nums;
  }
  .rec-nom-cell {
      width:var(--recNom);
      min-width:var(--recNom);
      max-width:var(--recNom);
      text-align:right;
      font-variant-numeric:tabular-nums;
  }
  .rec-ratio-cell {
      width:var(--recRatio);
      min-width:var(--recRatio);
      max-width:var(--recRatio);
      text-align:right;
      font-variant-numeric:tabular-nums;
  }
  .rec-net-cell {
      width:var(--recNet);
      min-width:var(--recNet);
      max-width:var(--recNet);
      text-align:right;
      font-variant-numeric:tabular-nums;
  }
  .rec-cell-link {
      display:block;
      width:100%;
      padding:3px 4px;
      overflow:hidden;
      border-radius:5px;
      text-decoration:none !important;
      text-overflow:ellipsis;
  }
  .rec-cell-readonly {
      display:block;
      width:100%;
      padding:3px 4px;
      overflow:hidden;
      border-radius:5px;
      color:#64748b !important;
      cursor:not-allowed;
      opacity:.78;
      text-overflow:ellipsis;
  }
  .rec-cell-empty { color:#94a3b8; font-weight:750; }
  .rec-pos { color:#dc2626 !important; font-weight:850; }
  .rec-neg { color:#059669 !important; font-weight:850; }

  .rec-value {
      display:block;
      width:100%;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
  }
  .rec-noa-value { text-align:center; font-size:10px; font-weight:800; }
  .rec-nom-value { text-align:right; font-size:10.5px; font-weight:800; }

  /* === TOTAL STICKY === */
  .sticky-total td {
      position:sticky;
      top:var(--rec_headH);
      z-index:55;
      height:40px;
      background:#eff6ff;
      color:#1e40af;
      border-bottom:2px solid #bfdbfe;
      box-shadow:0 4px 7px -5px rgba(15,23,42,.45);
      font-weight:800;
  }
  .sticky-total td.col-kode { z-index:67; background:#eff6ff; }
  .sticky-total td.col-nama { z-index:66; background:#eff6ff; }

  /* === DETAIL MODAL: modern, rapat, dan user friendly === */
  #modalDebiturRecovery {
      padding:10px;
      background:rgba(15,23,42,.68);
      backdrop-filter:blur(7px);
  }
  #modalCardRecovery {
      width:min(1480px,calc(100vw - 20px));
      height:min(92dvh,900px);
      max-height:92dvh;
      border:1px solid rgba(226,232,240,.9);
      border-radius:16px;
      background:#fff;
      box-shadow:0 30px 80px rgba(15,23,42,.32);
  }
  #modalRecoveryHeader {
      display:grid;
      grid-template-columns:minmax(220px,1fr) auto 38px;
      align-items:center;
      gap:10px;
      padding:10px 12px;
      border-bottom:1px solid #e2e8f0;
      background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);
      flex:none;
  }
  .modal-recovery-title-wrap {
      display:flex;
      align-items:center;
      min-width:0;
      gap:10px;
  }
  .modal-recovery-icon {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:38px;
      height:38px;
      flex:0 0 38px;
      border-radius:10px;
      background:#eff6ff;
      color:#2563eb;
      border:1px solid #bfdbfe;
  }
  .modal-recovery-title-text { min-width:0; }
  #modalTitleRecovery {
      display:flex;
      align-items:center;
      min-width:0;
      gap:7px;
      color:#0f172a;
      font-size:17px;
      font-weight:900;
      line-height:1.2;
  }
  #modalSubtitleRecovery {
      margin-top:3px;
      color:#64748b;
      font-size:10px;
      font-weight:650;
      line-height:1.25;
  }
  .modal-code-badge {
      display:inline-flex;
      align-items:center;
      height:22px;
      padding:0 7px;
      border:1px solid #c7d2fe;
      border-radius:6px;
      background:#eef2ff;
      color:#4338ca;
      font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
      font-size:10px;
      font-weight:850;
      white-space:nowrap;
  }
  #modalCountRecovery {
      display:inline-flex;
      align-items:center;
      height:20px;
      padding:0 7px;
      border-radius:999px;
      background:#e2e8f0;
      color:#475569;
      font-size:9px;
      font-weight:850;
      white-space:nowrap;
  }
  #modalRecoveryToolbar {
      display:flex;
      align-items:center;
      justify-content:flex-end;
      gap:7px;
      min-width:0;
  }
  .modal-search-wrap {
      position:relative;
      width:230px;
      min-width:170px;
  }
  .modal-search-wrap svg {
      position:absolute;
      left:9px;
      top:50%;
      width:15px;
      height:15px;
      color:#94a3b8;
      pointer-events:none;
      transform:translateY(-50%);
  }
  #recovery_detail_search {
      width:100%;
      height:34px;
      padding:0 9px 0 30px;
      border:1px solid #cbd5e1;
      border-radius:8px;
      background:#fff;
      color:#334155;
      font-size:11px;
      font-weight:650;
      outline:none;
  }
  #recovery_detail_search:focus {
      border-color:#2563eb;
      box-shadow:0 0 0 3px rgba(37,99,235,.1);
  }
  #recovery_jt_status {
      width:180px;
      min-width:180px !important;
      height:34px;
      font-size:11px;
  }
  #btnCloseRecovery {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:34px;
      height:34px;
      border:1px solid #fecaca;
      border-radius:8px;
      background:#fff1f2;
      color:#e11d48;
      transition:.15s ease;
  }
  #btnCloseRecovery:hover {
      background:#ffe4e6;
      transform:translateY(-1px);
  }

  #recoveryDetailSummary {
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:7px;
      padding:8px 10px;
      border-bottom:1px solid #e2e8f0;
      background:#f8fafc;
      flex:none;
  }
  .detail-stat-card {
      min-width:0;
      padding:7px 9px;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.035);
  }
  .detail-stat-label {
      overflow:hidden;
      color:#64748b;
      font-size:8px;
      font-weight:900;
      letter-spacing:.045em;
      text-overflow:ellipsis;
      text-transform:uppercase;
      white-space:nowrap;
  }
  .detail-stat-value {
      margin-top:3px;
      overflow:hidden;
      color:#0f172a;
      font-size:14px;
      font-weight:900;
      line-height:1.1;
      text-overflow:ellipsis;
      white-space:nowrap;
      font-variant-numeric:tabular-nums;
  }
  .detail-stat-value.blue { color:#1d4ed8; }
  .detail-stat-value.emerald { color:#047857; }
  .detail-stat-value.orange { color:#c2410c; }

  #modalTableWrapRecovery {
      --detailRek:132px;
      --detailNama:205px;
      position:relative;
      flex:1;
      min-height:0;
      overflow:auto;
      background:#fff;
      scrollbar-gutter:stable;
      -webkit-overflow-scrolling:touch;
  }
  #modalTableWrapRecovery::-webkit-scrollbar { width:7px; height:7px; }
  #modalTableWrapRecovery::-webkit-scrollbar-track { background:#f8fafc; }
  #modalTableWrapRecovery::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  #modalBodyRecovery {
      width:100%;
      min-width:100%;
      padding:0;
  }
  .modal-table {
      width:max-content;
      min-width:1280px;
      border-collapse:separate;
      border-spacing:0;
      table-layout:fixed;
      color:#334155;
      font-size:10.5px;
  }
  .modal-table th {
      position:sticky;
      top:0;
      z-index:25;
      height:38px;
      padding:6px 8px;
      border-bottom:1px solid #cbd5e1;
      border-right:1px solid #e2e8f0;
      background:#f1f5f9;
      color:#475569;
      font-size:9px;
      font-weight:850;
      letter-spacing:.025em;
      text-align:left;
      text-transform:uppercase;
      white-space:nowrap;
  }
  .modal-table td {
      height:38px;
      padding:6px 8px;
      border-bottom:1px solid #edf2f7;
      border-right:1px solid #f1f5f9;
      background:#fff;
      color:#334155;
      white-space:nowrap;
      font-variant-numeric:tabular-nums;
  }
  .modal-table tbody tr:nth-child(even) td { background:#fbfdff; }
  .modal-table tbody tr:hover td { background:#eff6ff; }
  .modal-table .modal-freeze-rek {
      position:sticky;
      left:0;
      z-index:16;
      width:var(--detailRek);
      min-width:var(--detailRek);
      max-width:var(--detailRek);
      background:#fff;
      border-right:1px solid #cbd5e1;
  }
  .modal-table .modal-freeze-name {
      position:sticky;
      left:var(--detailRek);
      z-index:15;
      width:var(--detailNama);
      min-width:var(--detailNama);
      max-width:var(--detailNama);
      overflow:hidden;
      background:#fff;
      border-right:1px solid #cbd5e1;
      box-shadow:5px 0 9px -8px rgba(15,23,42,.9);
      text-overflow:ellipsis;
  }
  .modal-table th.modal-freeze-rek,
  .modal-table th.modal-freeze-name {
      z-index:40;
      background:#eaf1f8;
  }
  .modal-table tbody tr:nth-child(even) td.modal-freeze-rek,
  .modal-table tbody tr:nth-child(even) td.modal-freeze-name { background:#fbfdff; }
  .modal-table tbody tr:hover td.modal-freeze-rek,
  .modal-table tbody tr:hover td.modal-freeze-name { background:#eff6ff; }

  .detail-type-badge,
  .detail-status-badge,
  .detail-kolek-badge {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      max-width:100%;
      min-height:21px;
      padding:2px 7px;
      border-radius:999px;
      font-size:8.5px;
      font-weight:850;
      line-height:1.1;
      white-space:nowrap;
  }
  .detail-type-badge { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
  .detail-type-badge.orange { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
  .detail-type-badge.green { background:#ecfdf5; color:#047857; border-color:#a7f3d0; }
  .detail-status-badge.ok { background:#ecfdf5; color:#047857; }
  .detail-status-badge.warn { background:#fff7ed; color:#c2410c; }
  .detail-status-badge.neutral { background:#f1f5f9; color:#475569; }
  .detail-kolek-badge.old { min-width:29px; background:#fff1f2; color:#be123c; }
  .detail-kolek-badge.new { min-width:29px; background:#ecfdf5; color:#047857; }
  .detail-money-main { color:#1d4ed8; font-weight:850; }
  .detail-money-sub { color:#475569; font-weight:700; }
  .detail-empty-state {
      display:flex;
      min-height:260px;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      padding:28px;
      color:#94a3b8;
      text-align:center;
  }
  .detail-empty-state svg { width:38px; height:38px; margin-bottom:8px; }
  .detail-loading-state {
      display:flex;
      min-height:280px;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      color:#64748b;
      font-size:12px;
      font-weight:750;
  }
  /* === INFO MODAL: KONSISTEN DENGAN MENU LAIN === */
  #modalInfoRecovery {
      padding:14px;
      background:rgba(15,23,42,.66);
      backdrop-filter:blur(7px);
  }
  #modalInfoCardRecovery {
      display:flex;
      flex-direction:column;
      width:min(840px,calc(100vw - 28px));
      max-height:min(90dvh,790px);
      overflow:hidden;
      border:1px solid rgba(226,232,240,.96);
      border-radius:16px;
      background:#fff;
      box-shadow:0 28px 80px rgba(15,23,42,.34);
  }
  .rec-info-header {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:13px 15px;
      border-bottom:1px solid #e2e8f0;
      background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);
      flex:none;
  }
  .rec-info-heading {
      display:flex;
      align-items:center;
      gap:10px;
      min-width:0;
  }
  .rec-info-heading-icon {
      width:38px;
      height:38px;
      flex:0 0 38px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid #bfdbfe;
      border-radius:10px;
      background:#eff6ff;
      color:#2563eb;
  }
  .rec-info-heading-icon svg { width:19px; height:19px; }
  .rec-info-title {
      color:#0f172a;
      font-size:16px;
      font-weight:900;
      line-height:1.15;
  }
  .rec-info-subtitle {
      margin-top:3px;
      color:#64748b;
      font-size:10px;
      font-weight:650;
      line-height:1.3;
  }
  .rec-info-close {
      width:34px;
      height:34px;
      flex:0 0 34px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#fff;
      color:#64748b;
      cursor:pointer;
      transition:.16s ease;
  }
  .rec-info-close:hover { background:#fff1f2; border-color:#fecdd3; color:#e11d48; }
  #modalInfoBodyRecovery {
      overflow:auto;
      padding:14px 15px 16px;
      color:#475569;
      overscroll-behavior:contain;
  }
  #modalInfoBodyRecovery::-webkit-scrollbar { width:6px; }
  #modalInfoBodyRecovery::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  .rec-info-current {
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:7px;
      margin-bottom:10px;
  }
  .rec-info-stat {
      min-width:0;
      padding:9px 10px;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.035);
  }
  .rec-info-stat-label {
      color:#64748b;
      font-size:7.5px;
      font-weight:900;
      letter-spacing:.04em;
      text-transform:uppercase;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
  }
  .rec-info-stat-value {
      margin-top:3px;
      color:#0f172a;
      font-size:13px;
      font-weight:900;
      line-height:1.05;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      font-variant-numeric:tabular-nums;
  }
  .rec-info-stat-value.blue { color:#1d4ed8; }
  .rec-info-stat-value.green { color:#047857; }
  .rec-info-stat-value.orange { color:#c2410c; }
  .rec-info-stat-value.red { color:#be123c; }
  .rec-info-intro {
      padding:10px 12px;
      border:1px solid #bfdbfe;
      border-radius:10px;
      background:#eff6ff;
      color:#1e3a8a;
      font-size:10.5px;
      font-weight:750;
      line-height:1.45;
  }
  .rec-info-warning {
      display:flex;
      align-items:flex-start;
      gap:9px;
      margin-top:9px;
      padding:10px 11px;
      border:1px solid #fcd34d;
      border-radius:10px;
      background:#fffbeb;
      color:#92400e;
  }
  .rec-info-warning svg { width:17px; height:17px; flex:0 0 17px; margin-top:1px; }
  .rec-info-warning-title { font-size:10.5px; font-weight:900; line-height:1.25; }
  .rec-info-warning-text { margin-top:3px; font-size:9.5px; font-weight:650; line-height:1.42; }
  .rec-info-grid {
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:9px;
      margin-top:10px;
  }
  .rec-info-card {
      min-width:0;
      padding:10px 11px;
      border:1px solid #e2e8f0;
      border-left-width:4px;
      border-radius:10px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.035);
  }
  .rec-info-card.blue { border-left-color:#3b82f6; background:#f8fbff; }
  .rec-info-card.orange { border-left-color:#f59e0b; background:#fffdf7; }
  .rec-info-card.green { border-left-color:#10b981; background:#f7fffb; }
  .rec-info-card.red { border-left-color:#f43f5e; background:#fff9fa; }
  .rec-info-card.cyan { border-left-color:#06b6d4; background:#f7feff; }
  .rec-info-card.violet { border-left-color:#6366f1; background:#fafaff; }
  .rec-info-card.full { grid-column:1 / -1; }
  .rec-info-card-title {
      display:flex;
      align-items:center;
      gap:7px;
      color:#0f172a;
      font-size:10.5px;
      font-weight:900;
      line-height:1.2;
  }
  .rec-info-chip {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:24px;
      height:19px;
      padding:0 6px;
      border-radius:999px;
      background:#e2e8f0;
      color:#475569;
      font-size:7.5px;
      font-weight:900;
      white-space:nowrap;
  }
  .rec-info-card-text {
      margin-top:6px;
      color:#475569;
      font-size:9.5px;
      font-weight:650;
      line-height:1.45;
  }
  .rec-info-card-text b { color:#0f172a; font-weight:900; }
  .rec-info-action-title {
      margin-top:11px;
      color:#0f172a;
      font-size:10.5px;
      font-weight:900;
  }
  .rec-action-grid {
      display:grid;
      grid-template-columns:repeat(3,minmax(0,1fr));
      gap:7px;
      margin-top:7px;
  }
  .rec-action-card {
      min-width:0;
      padding:9px 10px;
      border:1px solid #dbe3ee;
      border-radius:9px;
      background:#f8fafc;
  }
  .rec-action-number {
      width:21px;
      height:21px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius:999px;
      background:#2563eb;
      color:#fff;
      font-size:8px;
      font-weight:900;
  }
  .rec-action-name { margin-top:5px; color:#0f172a; font-size:10px; font-weight:900; }
  .rec-action-text { margin-top:3px; color:#64748b; font-size:8.8px; font-weight:650; line-height:1.38; }
  .rec-info-toplist {
      margin-top:9px;
      padding:9px 10px;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#f8fafc;
  }
  .rec-info-toplist-title { color:#334155; font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; }
  .rec-info-toplist-items { display:grid; gap:5px; margin-top:6px; }
  .rec-info-toprow { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:8px; align-items:center; font-size:9px; }
  .rec-info-toprow span:first-child { overflow:hidden; color:#475569; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
  .rec-info-toprow b { color:#be123c; font-weight:900; white-space:nowrap; }

  .backflow-monitor-warning {
      grid-column:1 / -1;
      display:flex;
      align-items:flex-start;
      gap:8px;
      padding:8px 9px;
      border:1px solid #fcd34d;
      border-radius:9px;
      background:#fffbeb;
      color:#92400e;
  }
  .backflow-monitor-warning svg { width:15px; height:15px; flex:0 0 15px; margin-top:1px; }
  .backflow-monitor-warning strong { display:block; color:#92400e; font-size:8.5px; font-weight:900; }
  .backflow-monitor-warning span { display:block; margin-top:2px; font-size:7.5px; font-weight:700; line-height:1.35; }

  @media (max-width:767px) {
      #modalInfoRecovery { align-items:flex-end !important; padding:0 !important; }
      #modalInfoCardRecovery {
          width:100% !important;
          max-height:92dvh !important;
          border-radius:16px 16px 0 0 !important;
          border-left:0;
          border-right:0;
          border-bottom:0;
          box-shadow:0 -18px 48px rgba(15,23,42,.30);
      }
      .rec-info-header { padding:10px 11px; }
      .rec-info-heading { gap:8px; }
      .rec-info-heading-icon { width:32px; height:32px; flex-basis:32px; border-radius:9px; }
      .rec-info-heading-icon svg { width:16px; height:16px; }
      .rec-info-title { font-size:13px; }
      .rec-info-subtitle { font-size:8.5px; }
      .rec-info-close { width:31px; height:31px; flex-basis:31px; }
      #modalInfoBodyRecovery { padding:10px 11px 14px; }
      .rec-info-current { grid-template-columns:repeat(2,minmax(0,1fr)); gap:5px; }
      .rec-info-stat { padding:7px 8px; }
      .rec-info-stat-label { font-size:6.5px; }
      .rec-info-stat-value { font-size:10.5px; }
      .rec-info-intro { padding:8px 9px; font-size:9px; }
      .rec-info-warning { padding:8px 9px; }
      .rec-info-warning-title { font-size:9.5px; }
      .rec-info-warning-text { font-size:8.5px; }
      .rec-info-grid { grid-template-columns:1fr; gap:7px; margin-top:8px; }
      .rec-info-card,.rec-info-card.full { grid-column:auto; padding:8px 9px; }
      .rec-info-card-title { font-size:9.5px; }
      .rec-info-card-text { margin-top:4px; font-size:8.5px; line-height:1.4; }
      .rec-action-grid { grid-template-columns:1fr; gap:5px; }
      .rec-action-card { padding:8px 9px; }
      .rec-action-name { font-size:9.5px; }
      .rec-action-text { font-size:8px; }
      .rec-info-toplist { padding:8px 9px; }
      .rec-info-toprow { font-size:8.5px; }
  }

  /* === LARGE DESKTOP === */
  @media (min-width:1440px) {
      #recoveryPage { padding:4px 6px 6px !important; }
      #recoveryHeader { padding:8px 12px !important; }
      #recScroller {
          --recCode:60px;
          --recName:180px;
          --recNoa:54px;
          --recNom:116px;
          --recRatio:86px;
          --recNet:136px;
      }
      #tabelRecovery { font-size:11px; }
  }

  /* === LAPTOP === */
  @media (min-width:1024px) and (max-width:1439px) {
      #recoveryPage { padding:5px 7px 7px !important; }
      #recScroller {
          --recCode:52px;
          --recName:164px;
          --recNoa:54px;
          --recNom:112px;
          --recRatio:82px;
          --recNet:124px;
      }
      #tabelRecovery thead th { font-size:9px; }
      #tabelRecovery th,#tabelRecovery td { padding-left:6px; padding-right:6px; }
  }

  /* === TABLET === */
  @media (min-width:768px) and (max-width:1023px) {
      #recoveryPage {
          padding:6px !important;
          height:calc(100dvh - 64px);
      }
      #recoveryHeader {
          flex-direction:column !important;
          align-items:stretch !important;
      }
      #filterWrapperRecovery {
          width:100%;
          padding-top:7px !important;
          margin-top:0 !important;
          border-top:1px solid #e2e8f0 !important;
      }
      #formFilterRecovery {
          width:100%;
          display:grid !important;
          grid-template-columns:130px 130px minmax(220px,1fr) 38px;
          gap:7px !important;
      }
      #formFilterRecovery .filter-box { width:auto !important; min-width:0 !important; }
      #formFilterRecovery .btn-icon { margin:0 !important; align-self:end; }
      #recScroller {
          --recCode:48px;
          --recName:150px;
          --recNoa:50px;
          --recNom:104px;
          --recRatio:78px;
          --recNet:112px;
      }
      #tabelRecovery { font-size:10px; }
      #tabelRecovery thead th { font-size:8.5px; }
      #tabelRecovery th,#tabelRecovery td { padding:5px 5px; }
  }

  /* === MOBILE === */
  @media (max-width:767px) {
      #recoveryPage {
          height:calc(100vh - 54px) !important;
          height:calc(100dvh - 54px) !important;
          min-height:0 !important;
          padding:4px !important;
          overflow:hidden !important;
      }
      #recoveryHeader {
          margin-bottom:4px !important;
          padding:7px 8px !important;
          border-radius:8px !important;
          align-items:stretch !important;
      }
      #recoveryHeaderTop { width:100%; }
      #recoveryHeaderTop h1 {
          min-width:0;
          gap:6px !important;
          font-size:14px !important;
      }
      #recoveryHeaderTop h1 > span:nth-child(2) {
          overflow:hidden;
          text-overflow:ellipsis;
      }
      .mobile-filter-toggle {
          display:inline-flex;
          align-items:center;
          justify-content:center;
          gap:5px;
          height:30px;
          padding:0 9px;
          border:1px solid #cbd5e1;
          border-radius:7px;
          background:#fff;
          color:#334155;
          box-shadow:0 1px 2px rgba(15,23,42,.05);
          font-size:10px;
          font-weight:800;
      }
      #filterWrapperRecovery {
          width:100%;
          margin-top:0 !important;
          padding-top:7px !important;
          border-top:1px solid #e2e8f0 !important;
      }
      #filterWrapperRecovery:not(.is-open) { display:none !important; }
      #formFilterRecovery {
          display:grid !important;
          grid-template-columns:minmax(0,1fr) minmax(0,1fr) 36px;
          width:100% !important;
          gap:5px !important;
          align-items:end;
      }
      #formFilterRecovery .filter-box { width:auto !important; min-width:0 !important; }
      #formFilterRecovery .filter-box:nth-child(3) { grid-column:1 / 3; }
      #formFilterRecovery .btn-icon {
          grid-column:3;
          grid-row:1 / 3;
          align-self:end;
          width:36px !important;
          height:34px !important;
          margin:0 !important;
      }
      .lbl { font-size:7.5px; }
      .inp { height:32px; padding:0 6px; font-size:10px; border-radius:6px; }
      #closing_date_recovery,#harian_date_recovery { text-align:left; }

      #recScroller {
          --recCode:0px;
          --recName:100px;
          --recNoa:40px;
          --recNom:78px;
          --recRatio:62px;
          --recNet:80px;
          border-radius:7px;
          scrollbar-gutter:auto;
      }
      #recScroller::-webkit-scrollbar { width:4px; height:4px; }

      #recScroller { overscroll-behavior-x:contain; }
      #tabelRecovery th:last-child,
      #tabelRecovery td:last-child { border-right:0; }
      #tabelRecovery {
          width:calc(var(--recName) + (var(--recNoa) * 5) + (var(--recNom) * 5) + var(--recRatio) + var(--recNet));
          min-width:calc(var(--recName) + (var(--recNoa) * 5) + (var(--recNom) * 5) + var(--recRatio) + var(--recNet));
          font-size:8.2px;
      }
      #tabelRecovery col.rec-col-code { display:none; }
      #tabelRecovery .col-kode { display:none !important; }
      #tabelRecovery th,#tabelRecovery td {
          height:38px;
          padding:4px 4px;
      }
      #tabelRecovery thead th {
          padding:2px 3px;
          font-size:6.8px;
          line-height:1.05;
          letter-spacing:0;
      }
      #tabelRecovery .rec-head-1 th { height:29px; }
      #tabelRecovery .rec-head-2 th { top:29px; height:23px; font-size:6.3px; }
      #tabelRecovery thead th[rowspan="2"] { height:52px; }
      .rec-head-full { display:none; }
      .rec-head-short { display:inline; }
      .col-nama {
          left:0 !important;
          z-index:50 !important;
          padding-left:6px !important;
          padding-right:5px !important;
          white-space:normal !important;
          line-height:1.12;
      }
      #tabelRecovery thead th.col-nama { z-index:80 !important; }
      .sticky-total td.col-nama { z-index:70 !important; }
      .col-nama > div {
          display:-webkit-box;
          overflow:hidden;
          white-space:normal;
          -webkit-line-clamp:2;
          -webkit-box-orient:vertical;
          overflow-wrap:anywhere;
          font-size:8.5px;
          line-height:1.12;
      }
      .rec-noa-cell,.rec-nom-cell,.rec-ratio-cell,.rec-net-cell {
          padding:2px 3px !important;
      }
      .rec-cell-link { padding:2px 1px; }
      .rec-noa-value { font-size:7.2px; line-height:1; }
      .rec-nom-value { font-size:8.2px; line-height:1.05; }
      .rec-ratio-cell span { min-width:0 !important; padding:3px 4px !important; font-size:7.5px !important; }
      .rec-cell-empty {
          display:flex;
          min-height:30px;
          align-items:center;
          justify-content:flex-end;
      }
      .sticky-total td { height:38px; }

      #modalDebiturRecovery {
          align-items:flex-end !important;
          padding:0 !important;
      }
      #modalCardRecovery {
          width:100% !important;
          height:96dvh !important;
          max-height:96dvh !important;
          border-radius:16px 16px 0 0 !important;
          border-bottom:0;
      }
      #modalRecoveryHeader {
          grid-template-columns:minmax(0,1fr) 34px;
          gap:7px;
          padding:9px 10px 8px;
      }
      .modal-recovery-icon {
          width:34px;
          height:34px;
          flex-basis:34px;
          border-radius:9px;
      }
      #modalTitleRecovery { font-size:14px; gap:5px; }
      #modalSubtitleRecovery { margin-top:2px; font-size:9px; }
      .modal-code-badge { height:19px; padding:0 5px; font-size:8px; }
      #modalCountRecovery { height:18px; padding:0 5px; font-size:8px; }
      #modalRecoveryToolbar {
          grid-column:1 / -1;
          display:grid;
          grid-template-columns:minmax(0,1fr) minmax(130px,42%);
          gap:6px;
      }
      .modal-search-wrap { width:100%; min-width:0; }
      #recovery_detail_search,
      #recovery_jt_status { height:32px; font-size:10px; }
      #recovery_jt_status { width:100%; min-width:0 !important; }
      #btnCloseRecovery { width:32px; height:32px; }
      #recoveryDetailSummary {
          grid-template-columns:repeat(4,minmax(105px,1fr));
          gap:6px;
          padding:6px 8px;
          overflow-x:auto;
          scrollbar-width:none;
      }
      #recoveryDetailSummary::-webkit-scrollbar { display:none; }
      .detail-stat-card { padding:6px 7px; border-radius:8px; }
      .detail-stat-label { font-size:7px; }
      .detail-stat-value { font-size:11px; }
      #modalTableWrapRecovery {
          --detailRek:108px;
          --detailNama:145px;
      }
      .modal-table { min-width:990px; font-size:9px; }
      .modal-table th { height:34px; padding:5px 6px; font-size:8px; }
      .modal-table td { height:35px; padding:5px 6px; font-size:9px; }
      .modal-table .detail-col-jenis { display:none; }
      .modal-table .modal-freeze-name > div {
          max-width:100%;
          overflow:hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
      }
      .detail-type-badge,
      .detail-status-badge,
      .detail-kolek-badge { min-height:19px; padding:2px 5px; font-size:7.5px; }

      #modalInfoRecovery {
          align-items:flex-end !important;
          padding:0 !important;
      }
      #modalInfoCardRecovery {
          width:100%;
          max-height:92dvh;
          border-radius:16px 16px 0 0 !important;
      }
      #modalInfoBodyRecovery {
          padding:10px 11px 14px !important;
      }
  }

  @media (max-width:374px) {
      #recScroller {
          --recName:92px;
          --recNoa:38px;
          --recNom:72px;
          --recRatio:58px;
          --recNet:74px;
      }
      #recoveryHeaderTop h1 { font-size:13px !important; }
      .mobile-filter-toggle { padding:0 7px; }
      .rec-nom-value { font-size:7.6px; }
      .rec-noa-value { font-size:6.8px; }
      .col-nama > div { font-size:8px; }
  }


  /* === DETAIL MODAL RESPONSIVE V3 === */
  @media (max-width: 767px) {
      #modalDebiturRecovery {
          align-items:flex-end !important;
          padding:0 !important;
          background:rgba(15,23,42,.72);
      }
      #modalCardRecovery {
          width:100% !important;
          height:94dvh !important;
          max-height:94dvh !important;
          border-radius:16px 16px 0 0 !important;
          border-left:0;
          border-right:0;
          border-bottom:0;
          box-shadow:0 -18px 45px rgba(15,23,42,.28);
      }
      #modalRecoveryHeader {
          display:grid !important;
          grid-template-columns:minmax(0,1fr) 34px !important;
          grid-template-areas:
              "title close"
              "toolbar toolbar" !important;
          gap:7px 8px !important;
          padding:8px 9px !important;
          background:#fff;
      }
      .modal-recovery-title-wrap {
          grid-area:title;
          gap:7px;
          min-width:0;
      }
      .modal-recovery-icon {
          width:30px !important;
          height:30px !important;
          flex:0 0 30px !important;
          border-radius:8px !important;
      }
      .modal-recovery-icon svg { width:16px; height:16px; }
      #modalTitleRecovery {
          gap:4px !important;
          font-size:13px !important;
          line-height:1.1;
          flex-wrap:wrap;
      }
      #modalSubtitleRecovery {
          margin-top:2px !important;
          font-size:8.5px !important;
          line-height:1.15;
      }
      .modal-code-badge {
          height:18px !important;
          padding:0 5px !important;
          font-size:7.5px !important;
      }
      #modalCountRecovery {
          height:18px !important;
          padding:0 5px !important;
          font-size:7.5px !important;
      }
      #btnCloseRecovery {
          grid-area:close;
          align-self:start;
          justify-self:end;
          width:30px !important;
          height:30px !important;
          border-radius:8px !important;
      }
      #modalRecoveryToolbar {
          grid-area:toolbar;
          display:grid !important;
          grid-template-columns:minmax(0,1fr) !important;
          gap:6px !important;
          width:100%;
      }
      #modalRecoveryToolbar.has-status-filter {
          grid-template-columns:minmax(0,1fr) 118px !important;
      }
      .modal-search-wrap {
          width:100% !important;
          min-width:0 !important;
      }
      #recovery_detail_search,
      #recovery_jt_status {
          width:100% !important;
          height:31px !important;
          min-width:0 !important;
          border-radius:7px !important;
          font-size:9px !important;
      }
      #recovery_detail_search { padding-left:28px !important; }
      .modal-search-wrap svg { left:8px; width:13px; height:13px; }

      #recoveryDetailSummary {
          display:grid !important;
          grid-template-columns:repeat(2,minmax(0,1fr)) !important;
          gap:5px !important;
          padding:6px 8px !important;
          overflow:visible !important;
          background:#f8fafc;
      }
      #recoveryDetailSummary.hidden { display:none !important; }
      .detail-stat-card {
          min-width:0 !important;
          padding:6px 7px !important;
          border-radius:8px !important;
      }
      .detail-stat-label {
          font-size:6.5px !important;
          letter-spacing:.025em !important;
      }
      .detail-stat-value {
          margin-top:2px !important;
          font-size:10px !important;
          line-height:1.05 !important;
      }

      #modalTableWrapRecovery {
          --detailRek:0px;
          --detailNama:0px;
          overflow-y:auto !important;
          overflow-x:hidden !important;
          background:#f8fafc;
      }
      #modalBodyRecovery { min-width:0 !important; }
      .detail-loading-state,
      .detail-empty-state { min-height:190px; padding:24px 12px; }

      .detail-mobile-list {
          display:grid;
          gap:7px;
          padding:7px 8px 22px;
      }
      .detail-mobile-card {
          overflow:hidden;
          border:1px solid #e2e8f0;
          border-radius:10px;
          background:#fff;
          box-shadow:0 1px 2px rgba(15,23,42,.04);
      }
      .detail-mobile-card-head {
          display:flex;
          align-items:flex-start;
          justify-content:space-between;
          gap:7px;
          padding:7px 8px 6px;
          border-bottom:1px solid #f1f5f9;
          background:linear-gradient(180deg,#fff,#fbfdff);
      }
      .detail-mobile-identity { min-width:0; }
      .detail-mobile-rek {
          overflow:hidden;
          color:#475569;
          font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
          font-size:8.5px;
          font-weight:850;
          line-height:1.15;
          text-overflow:ellipsis;
          white-space:nowrap;
      }
      .detail-mobile-name {
          margin-top:2px;
          overflow:hidden;
          color:#0f172a;
          font-size:10px;
          font-weight:900;
          line-height:1.15;
          text-overflow:ellipsis;
          white-space:nowrap;
      }
      .detail-mobile-metrics {
          display:grid;
          grid-template-columns:repeat(2,minmax(0,1fr));
          gap:1px;
          background:#e2e8f0;
      }
      .detail-mobile-metric {
          min-width:0;
          padding:6px 8px;
          background:#fff;
      }
      .detail-mobile-label {
          color:#64748b;
          font-size:6.5px;
          font-weight:900;
          letter-spacing:.03em;
          text-transform:uppercase;
      }
      .detail-mobile-value {
          margin-top:2px;
          overflow:hidden;
          color:#0f172a;
          font-size:10px;
          font-weight:900;
          line-height:1.05;
          text-overflow:ellipsis;
          white-space:nowrap;
          font-variant-numeric:tabular-nums;
      }
      .detail-mobile-value.blue { color:#1d4ed8; }
      .detail-mobile-value.orange { color:#c2410c; }
      .detail-mobile-meta {
          display:flex;
          flex-wrap:wrap;
          gap:4px 7px;
          padding:6px 8px 7px;
          color:#64748b;
          font-size:7.5px;
          font-weight:750;
          line-height:1.2;
      }
      .detail-mobile-meta span {
          display:inline-flex;
          align-items:center;
          gap:3px;
          min-width:0;
      }
      .detail-mobile-meta b { color:#334155; font-weight:900; }
      .detail-mobile-card .detail-status-badge,
      .detail-mobile-card .detail-type-badge,
      .detail-mobile-card .detail-kolek-badge {
          min-height:18px !important;
          padding:2px 5px !important;
          font-size:7px !important;
      }
      .modal-table { display:none !important; }
  }

  @media (min-width: 768px) and (max-width: 1100px) {
      #modalCardRecovery {
          width:calc(100vw - 24px);
          height:90dvh;
      }
      #modalRecoveryHeader {
          grid-template-columns:minmax(190px,1fr) minmax(330px,auto) 34px;
      }
      .modal-search-wrap { width:190px; min-width:150px; }
      #recovery_jt_status { width:150px; min-width:150px !important; }
      #recoveryDetailSummary { grid-template-columns:repeat(4,minmax(125px,1fr)); }
      #modalTableWrapRecovery { --detailRek:118px; --detailNama:175px; }
      .modal-table { min-width:1150px; }
  }


  /* === DESKTOP / WEB: seluruh kolom muat tanpa scroll horizontal === */
  @media (min-width:1024px) {
      #recScroller {
          --recCode:4.2%;
          --recName:13.8%;
          --recNoa:4%;
          --recNom:8.5%;
          --recRatio:7.5%;
          --recNet:11.5%;
          overflow-x:hidden !important;
          scrollbar-gutter:auto;
      }

      #tabelRecovery {
          width:100% !important;
          min-width:0 !important;
          max-width:100% !important;
          table-layout:fixed !important;
      }

      #tabelRecovery th,
      #tabelRecovery td {
          min-width:0 !important;
          padding-left:4px !important;
          padding-right:4px !important;
          overflow:hidden;
          text-overflow:ellipsis;
      }

      #tabelRecovery thead th {
          padding-left:3px !important;
          padding-right:3px !important;
          font-size:8px !important;
          letter-spacing:0 !important;
      }

      #tabelRecovery .rec-head-2 th {
          font-size:7.5px !important;
      }

      .col-kode {
          font-size:9px !important;
      }

      .col-nama {
          padding-left:6px !important;
          padding-right:5px !important;
      }

      .col-nama > div {
          display:block;
          width:100%;
          overflow:hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
          font-size:10px;
      }

      .rec-noa-cell,
      .rec-nom-cell,
      .rec-ratio-cell,
      .rec-net-cell {
          min-width:0 !important;
          padding-left:3px !important;
          padding-right:3px !important;
      }

      .rec-value,
      .rec-cell-link {
          display:block;
          width:100%;
          min-width:0;
          overflow:hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
      }

      .rec-noa-value {
          font-size:9px !important;
          letter-spacing:-.02em;
      }

      .rec-nom-value {
          font-size:9.2px !important;
          letter-spacing:-.045em;
      }

      .rec-ratio-cell > span {
          width:100%;
          min-width:0 !important;
          padding-left:3px !important;
          padding-right:3px !important;
          justify-content:center !important;
          font-size:9px !important;
          letter-spacing:-.02em;
      }

      .rec-net-cell {
          font-size:9.2px !important;
          letter-spacing:-.045em;
      }

      .sticky-total td {
          font-size:9.2px !important;
      }
  }

</style>


<div id="recoveryPage" class="w-full flex flex-col font-sans bg-slate-50 overflow-hidden">
  
  <div id="recoveryHeader" class="relative z-20 flex-none w-full bg-white border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between shrink-0">
    <div id="recoveryHeaderTop" class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
      <h1 class="text-base md:text-xl font-extrabold flex items-center gap-2 text-slate-800 whitespace-nowrap">
        <span class="bg-blue-600 text-white p-1 rounded text-sm md:text-base shadow-sm">💰</span> 
        <span>Recovery NPL</span>
        <button type="button" id="btnInfoRecovery" class="rec-info-btn" title="Panduan kamus kolom">i</button>
      </h1>
      <button type="button" id="btnToggleRecoveryFilter" class="mobile-filter-toggle" aria-expanded="false">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M3 6h18M7 12h10M10 18h4" stroke-linecap="round"/></svg>
        Filter
      </button>
    </div>

    <div id="filterWrapperRecovery" class="w-full xl:w-auto flex-1 min-w-0 flex justify-end shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
    <form id="formFilterRecovery" class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-2.5 w-full xl:w-auto" onsubmit="event.preventDefault();">
      <div class="filter-box flex flex-col shrink-0 w-[calc(50%-4px)] xl:w-[120px]">
          <label class="lbl">Closing (M-1)</label>
          <input type="date" id="closing_date_recovery" class="inp" required>
      </div>
      
      <div class="filter-box flex flex-col shrink-0 w-[calc(50%-4px)] xl:w-[120px]">
          <label class="lbl">Harian (Actual)</label>
          <input type="date" id="harian_date_recovery" class="inp" required>
      </div>
      
      <div class="filter-box flex flex-col flex-1 min-w-[180px] xl:w-[260px]">
          <label class="lbl">Area/Cabang</label>
          <select id="opt_kantor_recovery" class="inp font-bold text-slate-700 truncate">
            <option value="ALL">Konsolidasi</option>
          </select>
      </div>

      <button type="button" onclick="exportRecoveryExcel()" class="btn-icon w-[32px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0" title="Export Rekap Excel">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
      </button>
    </form>
    </div>
  </div>

  <div class="flex-1 min-h-0 relative flex flex-col">
    <div id="loadingRecovery" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold backdrop-blur-sm rounded-lg">
       <div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2"></div>
       <span>Memuat Data...</span>
    </div>

    <div id="recScroller" class="table-wrapper">
      <table id="tabelRecovery">
        <colgroup>
          <col class="rec-col-code">
          <col class="rec-col-name">
          <col class="rec-col-noa"><col class="rec-col-nom">
          <col class="rec-col-noa"><col class="rec-col-nom">
          <col class="rec-col-noa"><col class="rec-col-nom">
          <col class="rec-col-noa"><col class="rec-col-nom">
          <col class="rec-col-noa"><col class="rec-col-nom">
          <col class="rec-col-ratio">
          <col class="rec-col-net">
        </colgroup>
        <thead id="theadRec">
          <tr class="rec-head-1">
            <th class="col-kode" rowspan="2">Kode</th>
            <th class="col-nama text-left" id="thNamaRec" rowspan="2">Area</th>
            <th class="rec-group-head head-lunas" colspan="2">Lunas NPL</th>
            <th class="rec-group-head head-backflow" colspan="2">Backflow</th>
            <th class="rec-group-head head-angsuran" colspan="2">Angsuran NPL</th>
            <th class="rec-group-head head-total" colspan="2">Total Recovery</th>
            <th class="rec-group-head head-flow" colspan="2">Flow NPL</th>
            <th class="rec-ratio-cell head-ratio" rowspan="2">% Flow PAR</th>
            <th class="rec-net-cell head-net" rowspan="2">OSC NPL</th>
          </tr>
          <tr class="rec-head-2">
            <th class="rec-sub-head head-lunas">NOA</th><th class="rec-sub-head head-lunas">Baki Debet</th>
            <th class="rec-sub-head head-backflow">NOA</th><th class="rec-sub-head head-backflow">Baki Debet</th>
            <th class="rec-sub-head head-angsuran">NOA</th><th class="rec-sub-head head-angsuran">Baki Debet</th>
            <th class="rec-sub-head head-total" id="sortTotalNoa">NOA ↕</th><th class="rec-sub-head head-total" id="sortTotalBaki">Baki Debet ↕</th>
            <th class="rec-sub-head head-flow">NOA</th><th class="rec-sub-head head-flow">Baki Debet</th>
          </tr>
        </thead>
        <tbody id="recoveryTotalRow"></tbody>
        <tbody id="recoveryBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDebiturRecovery" class="fixed inset-0 hidden items-center justify-center z-[9999]">
  <div id="modalCardRecovery" class="flex flex-col overflow-hidden animate-scale-up">
    <div id="modalRecoveryHeader">
      <div class="modal-recovery-title-wrap">
        <span class="modal-recovery-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <path d="M14 2v6h6"></path>
            <path d="M8 13h8M8 17h6"></path>
          </svg>
        </span>
        <div class="modal-recovery-title-text">
          <div id="modalTitleRecovery">Detail Debitur</div>
          <div id="modalSubtitleRecovery">Daftar rekening</div>
        </div>
      </div>

      <div id="modalRecoveryToolbar">
        <label class="modal-search-wrap" aria-label="Cari debitur">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
          <input type="search" id="recovery_detail_search" placeholder="Cari nama / no rekening..." autocomplete="off">
        </label>
        <select id="recovery_jt_status" class="inp hidden">
          <option value="all">Semua Backflow</option>
          <option value="sudah">Sudah Angsuran</option>
          <option value="belum">Potensi Flow</option>
        </select>
      </div>

      <button id="btnCloseRecovery" type="button" aria-label="Tutup modal" title="Tutup">
        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
      </button>
    </div>

    <div id="recoveryDetailSummary" class="hidden"></div>

    <div id="modalTableWrapRecovery">
      <div id="modalBodyRecovery"></div>
    </div>
  </div>
</div>

<div id="modalPeringatan" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-center justify-center z-[9999] px-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-scale-up">
    <div class="bg-red-50 p-4 border-b border-red-100 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl shrink-0">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
      </div>
      <h3 class="font-bold text-red-800 text-lg">Akses Ditolak</h3>
    </div>
    <div class="p-6 text-center text-slate-600 text-sm">
      <p>Anda login sebagai <span class="font-bold text-blue-600 px-1 bg-blue-50 rounded" id="warnUserLvl">Cabang</span>.</p>
      <p class="mt-2">Rekap Korwil boleh dipantau, tetapi detail debitur hanya dapat dibuka untuk <b>cabang login sendiri</b>. Detail <span class="font-bold text-red-600 px-1 bg-red-50 rounded" id="warnTargetLvl">Cabang</span> tidak dapat dibuka.</p>
    </div>
    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
      <button onclick="closeModalPeringatan()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold transition">Mengerti</button>
    </div>
  </div>
</div>

<div id="modalInfoRecovery" class="fixed inset-0 hidden items-center justify-center z-[10000]" role="dialog" aria-modal="true" aria-labelledby="titleInfoRecovery">
  <div id="modalInfoCardRecovery" class="animate-scale-up">
    <div class="rec-info-header">
      <div class="rec-info-heading">
        <span class="rec-info-heading-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
          </svg>
        </span>
        <div>
          <div id="titleInfoRecovery" class="rec-info-title">Panduan & Fokus Recovery NPL</div>
          <div class="rec-info-subtitle">Ringkasan posisi dan prioritas tindak lanjut agar recovery tetap terjaga dan NPL tidak kembali memburuk.</div>
        </div>
      </div>
      <button type="button" id="btnCloseInfoRecovery" class="rec-info-close" title="Tutup" aria-label="Tutup panduan">
        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
      </button>
    </div>

    <div id="modalInfoBodyRecovery">
      <div id="recInfoCurrentSummary" class="rec-info-current"></div>

      <div id="recInfoHeadline" class="rec-info-intro">
        Recovery perlu dijaga sampai posisi akhir bulan. Perhatikan Flow NPL, percepat recovery, dan jangan berhenti memantau rekening yang baru kembali lancar.
      </div>

      <div class="rec-info-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"></path></svg>
        <div>
          <div class="rec-info-warning-title">Backflow belum berarti selesai.</div>
          <div class="rec-info-warning-text">Rekening backflow yang <b>belum melewati tanggal jatuh tempo angsuran berikutnya</b> tetap wajib dipantau. Pastikan kewajiban berikutnya dibayar tepat waktu agar rekening tidak kembali masuk NPL.</div>
        </div>
      </div>

      <div class="rec-info-grid">
        <section class="rec-info-card blue">
          <div class="rec-info-card-title"><span class="rec-info-chip">LUNAS</span> Lunas NPL</div>
          <div class="rec-info-card-text">Rekening NPL yang sudah selesai/lunas. Pastikan penyelesaian sudah tercermin pada posisi harian dan tidak ada kewajiban tersisa.</div>
        </section>
        <section class="rec-info-card orange">
          <div class="rec-info-card-title"><span class="rec-info-chip">BF</span> Backflow</div>
          <div class="rec-info-card-text">Rekening yang membaik dari NPL ke kolektibilitas lebih baik. <b>Tetap monitor sampai angsuran berikutnya terealisasi</b>, terutama yang jatuh temponya masih di depan.</div>
        </section>
        <section class="rec-info-card green">
          <div class="rec-info-card-title"><span class="rec-info-chip">ANG</span> Angsuran NPL</div>
          <div class="rec-info-card-text">Penurunan baki debet NPL karena pembayaran angsuran. Dorong pembayaran lanjutan agar outstanding NPL terus turun.</div>
        </section>
        <section class="rec-info-card red">
          <div class="rec-info-card-title"><span class="rec-info-chip">FLOW</span> Flow NPL</div>
          <div class="rec-info-card-text">Rekening yang saat closing belum NPL lalu menjadi KL/D/M pada posisi harian. Nilai ini perlu ditekan karena langsung menambah pemburukan.</div>
        </section>
        <section class="rec-info-card cyan">
          <div class="rec-info-card-title"><span class="rec-info-chip">REC</span> Total Recovery</div>
          <div class="rec-info-card-text"><b>Lunas NPL + Backflow + Angsuran NPL.</b> Gunakan untuk melihat seberapa besar perbaikan yang sudah dicapai pada periode berjalan.</div>
        </section>
        <section class="rec-info-card violet">
          <div class="rec-info-card-title"><span class="rec-info-chip">NET</span> Perbaikan / Pemburukan</div>
          <div class="rec-info-card-text"><b>Flow NPL - Total Recovery.</b> Nilai negatif = perbaikan. Nilai positif = pemburukan dan perlu tindak lanjut lebih cepat.</div>
        </section>
      </div>

      <div class="rec-info-action-title">Urutan tindak lanjut yang disarankan</div>
      <div class="rec-action-grid">
        <div class="rec-action-card">
          <span class="rec-action-number">1</span>
          <div class="rec-action-name">Potensi NPL</div>
          <div class="rec-action-text">Cegah rekening berisiko sebelum benar-benar masuk NPL. Prioritaskan debitur yang mendekati batas kolektibilitas.</div>
        </div>
        <div class="rec-action-card">
          <span class="rec-action-number">2</span>
          <div class="rec-action-name">Flow PAR</div>
          <div class="rec-action-text">Buka Flow PAR untuk melihat rekening penyebab flow dan segera lakukan pembayaran/tindak lanjut sebelum posisi memburuk.</div>
        </div>
        <div class="rec-action-card">
          <span class="rec-action-number">3</span>
          <div class="rec-action-name">25 NPL Terbesar</div>
          <div class="rec-action-text">Fokus penyelesaian NPL terbesar melalui strategi yang sesuai, termasuk litigasi, lelang, SKK Kejaksaan, atau cessie sesuai ketentuan dan kesiapan dokumen.</div>
        </div>
      </div>

      <div id="recInfoTopList" class="rec-info-toplist hidden">
        <div class="rec-info-toplist-title">Cabang yang perlu perhatian lebih dulu</div>
        <div id="recInfoTopListItems" class="rec-info-toplist-items"></div>
      </div>
    </div>
  </div>
</div>

<script>
  // --- CONFIG ---
  const API_NPL  = './api/npl/'; 
  const API_DATE = './api/date/';

  const nfID = new Intl.NumberFormat('id-ID');
  const fmt  = n => nfID.format(Number(n||0));
  const num  = v => Number(v||0);
  const kodeNum = v => Number(String(v??'').replace(/\D/g,'')||0);
  const formatDate = (s) => { if(!s) return '-'; const d=new Date(s); return isNaN(d)?'-': `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`; };

  function recoveryDateOnly(value) {
      if (!value) return null;
      const raw = String(value).trim();
      const d = /^\d{4}-\d{2}-\d{2}$/.test(raw) ? new Date(`${raw}T00:00:00`) : new Date(raw);
      if (Number.isNaN(d.getTime())) return null;
      d.setHours(0,0,0,0);
      return d;
  }

  function isBackflowStillBeforeDue(item) {
      const due = recoveryDateOnly(item?.tgl_jatuh_tempo);
      const position = recoveryDateOnly(document.getElementById('harian_date_recovery')?.value) || recoveryDateOnly(new Date());
      return !!(due && position && due.getTime() > position.getTime());
  }

  function renderRecoveryInfoInsight() {
      const target = document.getElementById('recInfoCurrentSummary');
      const headline = document.getElementById('recInfoHeadline');
      const topWrap = document.getElementById('recInfoTopList');
      const topItems = document.getElementById('recInfoTopListItems');
      if (!target || !headline) return;

      const gt = recoveryGtRaw || {};
      const totalRecovery = num(gt.total_recovery || (num(gt.baki_debet_lunas) + num(gt.baki_debet_backflow) + num(gt.baki_debet_angsuran_npl)));
      const flowNpl = num(gt.baki_debet_flow_npl);
      const net = num(gt.net_flow_recovery);
      const backflowNoa = num(gt.noa_backflow);

      target.innerHTML = `
        <div class="rec-info-stat"><div class="rec-info-stat-label">Total Recovery</div><div class="rec-info-stat-value green">Rp ${fmt(totalRecovery)}</div></div>
        <div class="rec-info-stat"><div class="rec-info-stat-label">Flow NPL</div><div class="rec-info-stat-value red">Rp ${fmt(flowNpl)}</div></div>
        <div class="rec-info-stat"><div class="rec-info-stat-label">Backflow</div><div class="rec-info-stat-value orange">${fmt(backflowNoa)} NOA</div></div>
        <div class="rec-info-stat"><div class="rec-info-stat-label">Kondisi Bersih</div><div class="rec-info-stat-value ${net > 0 ? 'red' : 'green'}">${net > 0 ? '+' : ''}Rp ${fmt(net)}</div></div>
      `;

      if (net > 0) {
          headline.innerHTML = `<b>Posisi masih memburuk Rp ${fmt(net)}.</b> Flow NPL masih lebih besar daripada recovery. Cabang dengan pemburukan terbesar perlu segera ditindaklanjuti agar NPL akhir bulan tidak semakin naik.`;
      } else if (net < 0) {
          headline.innerHTML = `<b>Posisi membaik Rp ${fmt(Math.abs(net))}.</b> Recovery saat ini lebih besar daripada Flow NPL. Tetap jaga rekening backflow dan cegah flow baru agar perbaikan bertahan sampai akhir bulan.`;
      } else {
          headline.innerHTML = `<b>Recovery dan Flow NPL saat ini seimbang.</b> Fokus mencegah flow baru dan menjaga rekening backflow supaya posisi akhir bulan bergerak ke arah perbaikan.`;
      }

      const attention = [...(recoveryDataRaw || [])]
          .filter(row => num(row.net_flow_recovery) > 0)
          .sort((a,b) => num(b.net_flow_recovery) - num(a.net_flow_recovery))
          .slice(0,3);

      if (topWrap && topItems) {
          if (attention.length) {
              topItems.innerHTML = attention.map(row => {
                  const nama = recoveryEscape(row.nama_kantor || row.nama_unit || row.kode_cabang || row.kode_unit || '-');
                  return `<div class="rec-info-toprow"><span>${nama}</span><b>+Rp ${fmt(row.net_flow_recovery)}</b></div>`;
              }).join('');
              topWrap.classList.remove('hidden');
          } else {
              topItems.innerHTML = '';
              topWrap.classList.add('hidden');
          }
      }
  }

  // --- STATE PENTING UNTUK EXPORT & SORTING ---
  let recoveryDataRaw = [];
  let recoveryGtRaw = null;
  let sortState = { column: null, direction: 1 };
  let abortCtrl;
  let recoveryScopeMode = 'konsolidasi';

  // --- FUNGSI AMBIL USER REALTIME (ANTI BUG NULL) ---
  function getAppUser() {
      let uObj = null;
      if(typeof window.getUser === 'function') uObj = window.getUser();
      if(!uObj && localStorage.getItem('app_user')) {
          try { uObj = JSON.parse(localStorage.getItem('app_user')); } catch(e){}
      }
      return (uObj && uObj.kode) ? String(uObj.kode).padStart(3, '0') : '000';
  }

  function updateRecStickyHeader() {
      const thead = document.getElementById('theadRec');
      const scroller = document.getElementById('recScroller');
      if(thead && scroller) {
          scroller.style.setProperty('--rec_headH', (thead.offsetHeight - 1) + 'px');
      }
  }
  window.addEventListener('resize', () => {
      clearTimeout(window.__recoveryResizeTimer);
      window.__recoveryResizeTimer = setTimeout(updateRecStickyHeader, 100);
  });

  function renderRecoveryHeader() {
      const thead = document.getElementById('theadRec');
      if (!thead) return;

      thead.innerHTML = `
        <tr class="rec-head-1">
          <th class="col-kode" rowspan="2">Kode</th>
          <th class="col-nama text-left" id="thNamaRec" rowspan="2">Area</th>
          <th class="rec-group-head head-lunas" colspan="2"><span class="rec-head-full">Lunas NPL</span><span class="rec-head-short">Lunas</span></th>
          <th class="rec-group-head head-backflow" colspan="2">Backflow</th>
          <th class="rec-group-head head-angsuran" colspan="2"><span class="rec-head-full">Angsuran NPL</span><span class="rec-head-short">Angsuran</span></th>
          <th class="rec-group-head head-total" colspan="2"><span class="rec-head-full">Total Recovery</span><span class="rec-head-short">Recovery</span></th>
          <th class="rec-group-head head-flow" colspan="2"><span class="rec-head-full">Flow NPL</span><span class="rec-head-short">Flow</span></th>
          <th class="rec-ratio-cell head-ratio" rowspan="2"><span class="rec-head-full">% Flow PAR</span><span class="rec-head-short">% Flow</span></th>
          <th class="rec-net-cell head-net" rowspan="2"><span class="rec-head-full">Perbaikan (-) / Pemburukan (+)</span><span class="rec-head-short">Baik (-) / Buruk (+)</span></th>
        </tr>
        <tr class="rec-head-2">
          <th class="rec-sub-head head-lunas">NOA</th><th class="rec-sub-head head-lunas">Baki Debet</th>
          <th class="rec-sub-head head-backflow">NOA</th><th class="rec-sub-head head-backflow">Baki Debet</th>
          <th class="rec-sub-head head-angsuran">NOA</th><th class="rec-sub-head head-angsuran">Baki Debet</th>
          <th class="rec-sub-head head-total cursor-pointer hover:bg-cyan-100" id="sortTotalNoa" title="Urutkan total NOA">NOA ↕</th>
          <th class="rec-sub-head head-total cursor-pointer hover:bg-cyan-100" id="sortTotalBaki" title="Urutkan total baki debet">Baki Debet ↕</th>
          <th class="rec-sub-head head-flow">NOA</th><th class="rec-sub-head head-flow">Baki Debet</th>
        </tr>
      `;

      document.getElementById('sortTotalNoa')?.addEventListener('click', () => doSort('total_noa'));
      document.getElementById('sortTotalBaki')?.addEventListener('click', () => doSort('total_baki'));
      setTimeout(updateRecStickyHeader, 20);
  }

  // --- HAK AKSES AREA / KORWIL ---
  // Mapping mengikuti pembagian korwil yang dipakai backend:
  // 001-007 Semarang, 008-014 Solo, 015-021 Banyumas, 022-028 Pekalongan.
  const RECOVERY_KORWIL_RANGES = [
      { key:'SEMARANG',   label:'Korwil Semarang',   min:1,  max:7  },
      { key:'SOLO',       label:'Korwil Solo',       min:8,  max:14 },
      { key:'BANYUMAS',   label:'Korwil Banyumas',   min:15, max:21 },
      { key:'PEKALONGAN', label:'Korwil Pekalongan', min:22, max:28 }
  ];

  function getRecoveryKorwilByCabang(kodeCabang) {
      const n = Number(String(kodeCabang || '').replace(/\D/g,''));
      if (!Number.isFinite(n) || n <= 0) return null;
      return RECOVERY_KORWIL_RANGES.find(item => n >= item.min && n <= item.max) || null;
  }

  function isRecoveryKorwilAllowedForUser(korwilKey, userKode = getAppUser()) {
      if (userKode === '000') return true;
      const own = getRecoveryKorwilByCabang(userKode);
      return !!(own && own.key === String(korwilKey || '').toUpperCase());
  }

  function recoveryDetailTargetCabang(kode) {
      const clean = String(kode || '').replace(/\D/g,'');
      if (!clean) return '';
      return clean.length > 3 ? clean.substring(0,3) : clean.padStart(3,'0');
  }

  function canOpenRecoveryDetail(kode) {
      const myKode = getAppUser();
      if (myKode === '000') return true;
      return recoveryDetailTargetCabang(kode) === myKode;
  }

  async function populateKantorOptionsRecovery(userKode) {
      const opt = document.getElementById('opt_kantor_recovery');
      if (!opt) return;

      let list = [];
      try {
          const res = await fetch('./api/kode/', {
              method:'POST',
              headers:{'Content-Type':'application/json'},
              body:JSON.stringify({type:'kode_kantor'})
          });
          const json = await res.json();
          list = Array.isArray(json.data) ? json.data : [];
      } catch(e) {
          console.warn('Gagal memuat master kantor Recovery NPL', e);
      }

      // Login cabang: hanya diberikan 2 scope yang relevan:
      // 1) Cabang sendiri, 2) Korwil tempat cabang tersebut berada.
      if (userKode && userKode !== '000') {
          const ownKode = String(userKode).padStart(3,'0');
          const ownOffice = list.find(x => String(x.kode_kantor || '').padStart(3,'0') === ownKode);
          const ownName = ownOffice?.nama_kantor || ownOffice?.nama || 'Cabang Login';
          const ownKorwil = getRecoveryKorwilByCabang(ownKode);

          let html = `<option value="CAB-${ownKode}">${ownKode} - ${recoveryEscape(ownName)}</option>`;
          if (ownKorwil) {
              html += `<option value="KOR-${ownKorwil.key}">${ownKorwil.label}</option>`;
              opt.dataset.userKorwil = ownKorwil.key;
          } else {
              opt.dataset.userKorwil = '';
          }

          opt.innerHTML = html;
          opt.value = `CAB-${ownKode}`;
          // Jangan disable: user cabang boleh berpindah antara Cabang sendiri dan Korwilnya.
          opt.disabled = !ownKorwil;
          recoveryScopeMode = 'cabang';
          return;
      }

      let html = `
        <option value="ALL">Konsolidasi</option>
        <option value="KOR-SEMARANG">Korwil Semarang</option>
        <option value="KOR-SOLO">Korwil Solo</option>
        <option value="KOR-BANYUMAS">Korwil Banyumas</option>
        <option value="KOR-PEKALONGAN">Korwil Pekalongan</option>
      `;
      list.filter(x => String(x.kode_kantor || '') !== '000')
          .sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
          .forEach(it => {
              const kode = String(it.kode_kantor).padStart(3,'0');
              html += `<option value="CAB-${kode}">${kode} - ${recoveryEscape(it.nama_kantor || it.nama || `Cabang ${kode}`)}</option>`;
          });

      opt.innerHTML = html;
      opt.disabled = false;
      opt.dataset.userKorwil = '';
  }

  // --- INIT ---
  window.addEventListener('DOMContentLoaded', async () => {
    renderRecoveryHeader();
    document.getElementById('btnInfoRecovery')?.addEventListener('click', () => {
        const modal = document.getElementById('modalInfoRecovery');
        renderRecoveryInfoInsight();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    });
    document.getElementById('btnCloseInfoRecovery')?.addEventListener('click', () => {
        const modal = document.getElementById('modalInfoRecovery');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (!document.getElementById('modalDebiturRecovery')?.classList.contains('flex')) document.body.style.overflow = '';
    });
    document.getElementById('modalInfoRecovery')?.addEventListener('click', e => {
        if (e.target.id === 'modalInfoRecovery') {
            e.currentTarget.classList.add('hidden');
            e.currentTarget.classList.remove('flex');
            if (!document.getElementById('modalDebiturRecovery')?.classList.contains('flex')) document.body.style.overflow = '';
        }
    });
    document.getElementById('btnToggleRecoveryFilter')?.addEventListener('click', () => {
        const wrapper = document.getElementById('filterWrapperRecovery');
        const btn = document.getElementById('btnToggleRecoveryFilter');
        const isOpen = wrapper.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    const userKodeInit = getAppUser();
    await populateKantorOptionsRecovery(userKodeInit);
    // Load Tanggal Default
    try {
        const r = await fetch(API_DATE); 
        const j = await r.json(); 
        const d = j.data;
        if(d) {
            document.getElementById('closing_date_recovery').value = d.last_closing;
            document.getElementById('harian_date_recovery').value  = d.last_created;
        }
    } catch(e) {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('closing_date_recovery').value = today;
        document.getElementById('harian_date_recovery').value = today;
    }
    
    ['closing_date_recovery', 'harian_date_recovery', 'opt_kantor_recovery'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => {
            sortState = { column:null, direction:1 };
            fetchRecoveryData();
        });
    });

    // Fetch Data
    fetchRecoveryData();
  });

  // --- FILTER SUBMIT ---
  document.getElementById('formFilterRecovery').addEventListener('submit', e => {
    e.preventDefault();
    sortState = { column:null, direction:1 }; 
    fetchRecoveryData();
  });

  // --- FETCH DATA UTAMA ---
  async function fetchRecoveryData(){
    const loading = document.getElementById('loadingRecovery');
    loading.classList.remove('hidden');
    
    if(abortCtrl) abortCtrl.abort();
    abortCtrl = new AbortController();

    const tbody = document.getElementById('recoveryBody');
    const ttotal = document.getElementById('recoveryTotalRow');
    
    const closing_date = document.getElementById('closing_date_recovery').value;
    const harian_date  = document.getElementById('harian_date_recovery').value;
    
    const myKode = getAppUser();
    const optVal = document.getElementById('opt_kantor_recovery')?.value || (myKode === '000' ? 'ALL' : `CAB-${myKode}`);
    const payload = { type:'Recovery NPL', closing_date, harian_date };
    let kantor = '';
    let korwil = '';
    recoveryScopeMode = 'konsolidasi';

    if (myKode !== '000') {
        const ownKorwil = getRecoveryKorwilByCabang(myKode);
        const requestedKorwil = optVal.startsWith('KOR-') ? optVal.replace('KOR-', '').toUpperCase() : '';

        if (requestedKorwil && ownKorwil && requestedKorwil === ownKorwil.key) {
            korwil = ownKorwil.key;
            payload.korwil = korwil;
            recoveryScopeMode = 'korwil';
        } else {
            // Scope default dan fallback keamanan selalu cabang login sendiri.
            kantor = myKode;
            payload.kode_kantor = myKode;
            recoveryScopeMode = 'cabang';

            if (optVal !== `CAB-${myKode}` && document.getElementById('opt_kantor_recovery')) {
                document.getElementById('opt_kantor_recovery').value = `CAB-${myKode}`;
            }
        }
    } else if (optVal.startsWith('CAB-')) {
        kantor = optVal.replace('CAB-', '');
        payload.kode_kantor = kantor;
        recoveryScopeMode = 'cabang';
    } else if (optVal.startsWith('KOR-')) {
        korwil = optVal.replace('KOR-', '');
        payload.korwil = korwil;
        recoveryScopeMode = 'korwil';
    }

    document.getElementById('thNamaRec').innerText = (recoveryScopeMode === 'cabang') ? "NAMA KANKAS" : "AREA";

    tbody.innerHTML = ''; 
    ttotal.innerHTML = ``;

    try {
      const res = await fetch(API_NPL, {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify(payload),
        signal: abortCtrl.signal
      });
      const json = await res.json();
      
      let data = [];
      let totalRow = null;

      if(json.data && json.grand_total) {
          data = json.data.data || json.data; 
          totalRow = json.grand_total;
      } else if (Array.isArray(json.data)) {
          data = json.data;
          totalRow = data.find(d => (d.nama_kantor||d.nama_unit||'').toUpperCase().includes('TOTAL')) || null;
          data = data.filter(d => !(d.nama_kantor||d.nama_unit||'').toUpperCase().includes('TOTAL'));
      }

      // Simpan Ke Variabel Global Agar Bisa Di-Export Excel Nanti
      recoveryGtRaw = totalRow;
      recoveryDataRaw = data;
      recoveryDataRaw.sort((a,b)=> kodeNum(a.kode_cabang || a.kode_unit) - kodeNum(b.kode_cabang || b.kode_unit));

      renderTotal(totalRow);
      renderRows(recoveryDataRaw);
      renderRecoveryInfoInsight();

    } catch(err) {
      if(err.name !== 'AbortError') {
          console.error(err);
          tbody.innerHTML = `<tr><td colspan="14" class="p-4 text-center text-red-500 font-bold">Gagal memuat data.</td></tr>`;
      }
    } finally {
      loading.classList.add('hidden');

      const scroller = document.getElementById('recScroller');
      if (scroller) scroller.scrollLeft = 0;

      if (window.innerWidth < 768) {
          const wrapper = document.getElementById('filterWrapperRecovery');
          const btn = document.getElementById('btnToggleRecoveryFilter');
          wrapper?.classList.remove('is-open');
          btn?.setAttribute('aria-expanded', 'false');
      }

      setTimeout(updateRecStickyHeader, 50);
    }
  }

  function renderTotal(tot) {
     const el = document.getElementById('recoveryTotalRow');
     if(!tot) { el.innerHTML = ''; return; }

     const tNoa = num(tot.total_noa_recovery || (num(tot.noa_lunas) + num(tot.noa_backflow) + num(tot.noa_angsuran_npl)));
     const tBak = num(tot.total_recovery || (num(tot.baki_debet_lunas) + num(tot.baki_debet_backflow) + num(tot.baki_debet_angsuran_npl)));
     const net = num(tot.net_flow_recovery);
     const detailKode = getTotalDetailKode();

     el.innerHTML = `
        <tr class="sticky-total">
            <td class="col-kode font-bold uppercase"></td>
            <td class="col-nama font-bold uppercase text-left"><div>TOTAL</div></td>
            <td class="rec-noa-cell border-r border-blue-200">${recMetricValueLink(tot.noa_lunas, 'lunas', detailKode, 'blue', 'noa')}</td>
            <td class="rec-nom-cell border-r border-blue-300">${recMetricValueLink(tot.baki_debet_lunas, 'lunas', detailKode, 'blue', 'nom')}</td>
            <td class="rec-noa-cell border-r border-blue-200">${recMetricValueLink(tot.noa_backflow, 'backflow', detailKode, 'orange', 'noa')}</td>
            <td class="rec-nom-cell border-r border-blue-300">${recMetricValueLink(tot.baki_debet_backflow, 'backflow', detailKode, 'orange', 'nom')}</td>
            <td class="rec-noa-cell border-r border-blue-200">${recMetricValueLink(tot.noa_angsuran_npl, 'angsuran', detailKode, 'green', 'noa')}</td>
            <td class="rec-nom-cell border-r border-blue-300">${recMetricValueLink(tot.baki_debet_angsuran_npl, 'angsuran', detailKode, 'green', 'nom')}</td>
            <td class="rec-noa-cell border-r border-blue-200">${recMetricValueLink(tNoa, 'total_recovery', detailKode, 'blue', 'noa')}</td>
            <td class="rec-nom-cell text-blue-800 border-r border-blue-300">${recMetricValueLink(tBak, 'total_recovery', detailKode, 'blue', 'nom')}</td>
            <td class="rec-noa-cell text-red-600 border-r border-blue-200">${metricValue(tot.noa_flow_npl, 'noa')}</td>
            <td class="rec-nom-cell text-red-600 border-r border-blue-300">${metricValue(tot.baki_debet_flow_npl, 'nom')}</td>
            <td class="rec-ratio-cell border-r border-blue-300">${flowParRatioCell(tot)}</td>
            <td class="rec-net-cell ${net > 0 ? 'rec-pos' : (net < 0 ? 'rec-neg' : 'text-slate-500')}">${net > 0 ? '+' : ''}${fmt(net)}</td>
        </tr>
     `;
  }

  function metricValue(value, kind = 'nom') {
      const number = num(value);
      const valueText = number !== 0 ? fmt(number) : '-';
      const valueClass = kind === 'noa' ? 'rec-noa-value' : 'rec-nom-value';
      return `<span class="rec-value ${valueClass}">${valueText}</span>`;
  }

  function flowParRatioValue(row) {
      const closingNpl = num(row?.npl_closing);
      if (closingNpl <= 0) return 0;
      return (num(row?.baki_debet_flow_npl) / closingNpl) * 100;
  }

  function flowParRatioClass(value) {
      if (value <= 0) return 'bg-emerald-50 text-emerald-700 border-emerald-200';
      if (value > 3) return 'bg-red-50 text-red-700 border-red-200';
      return 'bg-orange-50 text-orange-700 border-orange-200';
  }

  function flowParRatioCell(row) {
      const val = flowParRatioValue(row);
      return `<span class="inline-flex items-center justify-end min-w-[70px] rounded-md border px-2 py-1 text-[11px] font-extrabold ${flowParRatioClass(val)}">${val.toFixed(2)}%</span>`;
  }

  function getTotalDetailKode() {
      const myKode = getAppUser();
      const optVal = document.getElementById('opt_kantor_recovery')?.value || 'ALL';

      // Saat login cabang sedang melihat Korwil, TOTAL adalah total seluruh korwil.
      // Jangan jadikan total tersebut pintu masuk ke detail lintas cabang.
      if (myKode !== '000') {
          if (optVal.startsWith('KOR-')) return 'LOCKED';
          return myKode;
      }

      if (optVal.startsWith('CAB-')) return optVal.replace('CAB-', '');
      return '000';
  }

  function recMetricValueLink(value, type, kode, color = 'blue', kind = 'nom') {
      if (num(value) <= 0) return `<span class="rec-cell-empty">${metricValue(0, kind)}</span>`;
      const cls = color === 'orange' ? 'text-orange-600' : (color === 'green' ? 'text-emerald-600' : 'text-blue-600');

      // Login cabang boleh melihat rekap Korwil, tetapi detail debitur hanya milik cabangnya.
      if (kode === 'LOCKED' || !canOpenRecoveryDetail(kode)) {
          return `<span class="rec-cell-readonly ${cls} font-bold" title="Detail hanya dapat dibuka untuk cabang login">${metricValue(value, kind)}</span>`;
      }

      return `<a href="#" class="rec-cell-link ${cls} font-bold hover:bg-slate-100" data-act="view" data-type="${type}" data-kode="${kode}">${metricValue(value, kind)}</a>`;
  }

  function renderRows(rows) {
     const tbody = document.getElementById('recoveryBody');
     if(rows.length === 0) {
         tbody.innerHTML = `<tr><td colspan="14" class="p-8 text-center text-slate-400">Tidak ada data recovery.</td></tr>`;
         return;
     }

     tbody.innerHTML = rows.map(r => {
        const tNoa = num(r.total_noa_recovery || (num(r.noa_lunas) + num(r.noa_backflow) + num(r.noa_angsuran_npl)));
        const tBak = num(r.total_recovery || (num(r.baki_debet_lunas) + num(r.baki_debet_backflow) + num(r.baki_debet_angsuran_npl)));
        const net = num(r.net_flow_recovery);
        const rawKode = r.kode_cabang || r.kode_unit || '';
        const kode = String(rawKode).padStart(recoveryScopeMode === 'cabang' ? 6 : 3,'0');
        const nama = r.nama_kantor || r.nama_unit || '-';

        return `
            <tr class="border-b transition hover:bg-blue-50">
                <td class="col-kode font-mono font-bold text-slate-500 text-xs">${kode}</td>
                <td class="col-nama font-semibold text-slate-700 text-xs"><div class="truncate" title="${nama}">${nama}</div></td>

                <td class="rec-noa-cell border-r border-slate-100">${recMetricValueLink(r.noa_lunas, 'lunas', kode, 'blue', 'noa')}</td>
                <td class="rec-nom-cell border-r border-slate-200">${recMetricValueLink(r.baki_debet_lunas, 'lunas', kode, 'blue', 'nom')}</td>
                <td class="rec-noa-cell border-r border-slate-100">${recMetricValueLink(r.noa_backflow, 'backflow', kode, 'orange', 'noa')}</td>
                <td class="rec-nom-cell border-r border-slate-200">${recMetricValueLink(r.baki_debet_backflow, 'backflow', kode, 'orange', 'nom')}</td>
                <td class="rec-noa-cell border-r border-slate-100">${recMetricValueLink(r.noa_angsuran_npl, 'angsuran', kode, 'green', 'noa')}</td>
                <td class="rec-nom-cell border-r border-slate-200">${recMetricValueLink(r.baki_debet_angsuran_npl, 'angsuran', kode, 'green', 'nom')}</td>
                <td class="rec-noa-cell bg-blue-50/30 border-r border-slate-100">${recMetricValueLink(tNoa, 'total_recovery', kode, 'blue', 'noa')}</td>
                <td class="rec-nom-cell bg-blue-50/30 border-r border-slate-200">${recMetricValueLink(tBak, 'total_recovery', kode, 'blue', 'nom')}</td>
                <td class="rec-noa-cell text-red-600 border-r border-slate-100">${metricValue(r.noa_flow_npl, 'noa')}</td>
                <td class="rec-nom-cell text-red-600 border-r border-slate-200">${metricValue(r.baki_debet_flow_npl, 'nom')}</td>
                <td class="rec-ratio-cell border-r border-slate-100">${flowParRatioCell(r)}</td>
                <td class="rec-net-cell ${net > 0 ? 'rec-pos' : (net < 0 ? 'rec-neg' : 'text-slate-500')}">${net > 0 ? '+' : ''}${fmt(net)}</td>
            </tr>
        `;
     }).join('');

     tbody.innerHTML += `<tr style="height:50px;"><td colspan="14" class="border-none bg-transparent"></td></tr>`;
  }

  // --- SORTING ---
  const doSort = (colKey) => {
    const isSame = sortState.column === colKey;
    sortState = { column: colKey, direction: isSame ? -sortState.direction : -1 };

    const sorted = [...recoveryDataRaw].sort((a,b) => {
        const valA = colKey === 'total_noa'
            ? num(a.total_noa_recovery || (num(a.noa_lunas) + num(a.noa_backflow) + num(a.noa_angsuran_npl)))
            : num(a.total_recovery || (num(a.baki_debet_lunas) + num(a.baki_debet_backflow) + num(a.baki_debet_angsuran_npl)));
        const valB = colKey === 'total_noa'
            ? num(b.total_noa_recovery || (num(b.noa_lunas) + num(b.noa_backflow) + num(b.noa_angsuran_npl)))
            : num(b.total_recovery || (num(b.baki_debet_lunas) + num(b.baki_debet_backflow) + num(b.baki_debet_angsuran_npl)));

        if (valA === valB) {
            return kodeNum(a.kode_cabang || a.kode_unit) - kodeNum(b.kode_cabang || b.kode_unit);
        }
        return (valA - valB) * sortState.direction;
    });

    const noaHead = document.getElementById('sortTotalNoa');
    const bakiHead = document.getElementById('sortTotalBaki');
    if (noaHead) noaHead.textContent = `NOA ${colKey === 'total_noa' ? (sortState.direction > 0 ? '↑' : '↓') : '↕'}`;
    if (bakiHead) bakiHead.textContent = `BAKI DEBET ${colKey === 'total_baki' ? (sortState.direction > 0 ? '↑' : '↓') : '↕'}`;
    renderRows(sorted);
  };

  // --- EXPORT EXCEL (HTML STRING FIX) ---
  function exportRecoveryExcel() {
      const rows = recoveryDataRaw || [];
      const gt = recoveryGtRaw || null;
      
      if(rows.length === 0) { 
          alert("Tidak ada data untuk diexport!"); 
          return; 
      }

      // Gunakan String Table HTML agar formatnya sempurna dibaca Excel
      let table = `<table border="1">
          <thead>
              <tr>
                  <th style="background-color:#d9ead3;">KODE</th>
                  <th style="background-color:#d9ead3;">NAMA KANTOR</th>
                  <th style="background-color:#d9ead3;">NOA LUNAS</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET LUNAS</th>
                  <th style="background-color:#d9ead3;">NOA BACKFLOW</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET BACKFLOW</th>
                  <th style="background-color:#d9ead3;">NOA ANGSURAN NPL</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET ANGSURAN NPL</th>
                  <th style="background-color:#d9ead3;">TOTAL NOA RECOVERY</th>
                  <th style="background-color:#d9ead3;">TOTAL BAKI DEBET RECOVERY</th>
                  <th style="background-color:#d9ead3;">NOA FLOW NPL</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET FLOW NPL</th>
                  <th style="background-color:#d9ead3;">% FLOW PAR / NPL M-1</th>
                  <th style="background-color:#d9ead3;">PERBAIKAN (-) / PEMBURUKAN (+)</th>
              </tr>
          </thead>
          <tbody>`;
      
      if(gt) {
          const tNoa = num(gt.total_noa_recovery || (num(gt.noa_lunas) + num(gt.noa_backflow) + num(gt.noa_angsuran_npl)));
          const tBak = num(gt.total_recovery || (num(gt.baki_debet_lunas) + num(gt.baki_debet_backflow) + num(gt.baki_debet_angsuran_npl)));
          table += `<tr>
              <td style="font-weight:bold;"></td>
              <td style="font-weight:bold;">GRAND TOTAL</td>
              <td style="font-weight:bold;">${gt.noa_lunas}</td>
              <td style="font-weight:bold;">${gt.baki_debet_lunas}</td>
              <td style="font-weight:bold;">${gt.noa_backflow}</td>
              <td style="font-weight:bold;">${gt.baki_debet_backflow}</td>
              <td style="font-weight:bold;">${gt.noa_angsuran_npl}</td>
              <td style="font-weight:bold;">${gt.baki_debet_angsuran_npl}</td>
              <td style="font-weight:bold;">${tNoa}</td>
              <td style="font-weight:bold;">${tBak}</td>
              <td style="font-weight:bold;">${gt.noa_flow_npl}</td>
              <td style="font-weight:bold;">${gt.baki_debet_flow_npl}</td>
              <td style="font-weight:bold;">${flowParRatioValue(gt).toFixed(2)}%</td>
              <td style="font-weight:bold;">${gt.net_flow_recovery}</td>
          </tr>`;
      }

      rows.forEach(r => {
          const tNoa = num(r.total_noa_recovery || (num(r.noa_lunas) + num(r.noa_backflow) + num(r.noa_angsuran_npl)));
          const tBak = num(r.total_recovery || (num(r.baki_debet_lunas) + num(r.baki_debet_backflow) + num(r.baki_debet_angsuran_npl)));
          const kode = r.kode_cabang || r.kode_unit || '-';
          const nama = r.nama_kantor || r.nama_unit || '-';
          
          table += `<tr>
              <td style="mso-number-format:'\\@'">${kode}</td>
              <td>${nama}</td>
              <td>${r.noa_lunas}</td>
              <td>${r.baki_debet_lunas}</td>
              <td>${r.noa_backflow}</td>
              <td>${r.baki_debet_backflow}</td>
              <td>${r.noa_angsuran_npl}</td>
              <td>${r.baki_debet_angsuran_npl}</td>
              <td>${tNoa}</td>
              <td>${tBak}</td>
              <td>${r.noa_flow_npl}</td>
              <td>${r.baki_debet_flow_npl}</td>
              <td>${flowParRatioValue(r).toFixed(2)}%</td>
              <td>${r.net_flow_recovery}</td>
          </tr>`;
      });
      
      table += `</tbody></table>`;

      const tgl = document.getElementById('harian_date_recovery').value;
      const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_Recovery_NPL_${tgl}.xls`;
      document.body.appendChild(a); 
      a.click(); 
      document.body.removeChild(a);
  }

  // --- MODAL DEBITUR & PERINGATAN (ACCESS DENIED) ---
  function closeModalPeringatan() {
      const modal = document.getElementById('modalPeringatan');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }

  document.getElementById('tabelRecovery').addEventListener('click', e => {
      const link = e.target.closest('a[data-act="view"]');
      if(!link) return;
      e.preventDefault();

      const targetKode = String(link.dataset.kode);
      const targetCabang = recoveryDetailTargetCabang(targetKode);
      const myKode = getAppUser();

      // PENGAMANAN AKSES: rekap Korwil boleh dilihat, detail debitur tetap cabang sendiri.
      if (myKode !== '000' && myKode !== targetCabang) {
          document.getElementById('warnUserLvl').innerText = `Cabang ${myKode}`;
          document.getElementById('warnTargetLvl').innerText = `Cabang ${targetCabang}`;
          const modalWarn = document.getElementById('modalPeringatan');
          modalWarn.classList.remove('hidden');
          modalWarn.classList.add('flex');
          return;
      }

      openModalDebitur(link.dataset.type, targetKode);
  });

  function buildRecoveryDetailPayload(type, kode, closing, harian) {
      const payload = { type, closing_date: closing, harian_date: harian };
      const cleanKode = String(kode || '').trim();
      const myKode = getAppUser();
      const optVal = document.getElementById('opt_kantor_recovery')?.value || 'ALL';

      // Defense-in-depth di FE: user cabang tidak pernah membentuk payload detail
      // untuk cabang lain, meskipun value DOM dimanipulasi lewat DevTools.
      if (myKode !== '000') {
          if (cleanKode.length > 3 && recoveryDetailTargetCabang(cleanKode) === myKode) {
              payload.kode_kantor = myKode;
              payload.kode_kankas = cleanKode;
          } else {
              payload.kode_kantor = myKode;
          }
          return payload;
      }

      if (cleanKode.length > 3) {
          payload.kode_kantor = cleanKode.substring(0, 3);
          payload.kode_kankas = cleanKode;
          return payload;
      }

      if (cleanKode && cleanKode !== '000' && cleanKode !== 'TOTAL' && cleanKode !== 'LOCKED') {
          payload.kode_kantor = cleanKode.padStart(3, '0');
          return payload;
      }

      if (optVal.startsWith('CAB-')) {
          payload.kode_kantor = optVal.replace('CAB-', '');
      } else if (optVal.startsWith('KOR-')) {
          payload.korwil = optVal.replace('KOR-', '');
      }

      return payload;
  }

  let recoveryDetailRows = [];
  let recoveryDetailLabel = '';

  function recoveryEscape(value) {
      return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
  }

  function recoveryTypeBadge(typeText, type) {
      const cls = type === 'backflow' ? 'orange' : (type === 'angsuran' ? 'green' : '');
      return `<span class="detail-type-badge ${cls}">${recoveryEscape(typeText || recoveryDetailLabel)}</span>`;
  }

  function recoveryStatusBadge(status) {
      const raw = String(status || '-');
      const lower = raw.toLowerCase();
      const cls = lower.includes('sudah') ? 'ok' : (lower.includes('potensi') || lower.includes('belum') ? 'warn' : 'neutral');
      return `<span class="detail-status-badge ${cls}">${recoveryEscape(raw)}</span>`;
  }

  function renderRecoveryDetailSummary(rows, totalRows, type = '') {
      const summary = document.getElementById('recoveryDetailSummary');
      if (!summary) return;

      const totalBaki = rows.reduce((sum, item) => sum + num(item.baki_debet), 0);
      const totalRecovery = rows.reduce((sum, item) => sum + num(item.recovery_nominal), 0);
      const totalPokok = rows.reduce((sum, item) => sum + num(item.angsuran_pokok), 0);
      const totalBunga = rows.reduce((sum, item) => sum + num(item.angsuran_bunga), 0);
      const monitoredBackflow = type === 'backflow' ? rows.filter(isBackflowStillBeforeDue) : [];
      const monitoredBaki = monitoredBackflow.reduce((sum, item) => sum + num(item.baki_debet), 0);

      summary.classList.remove('hidden');
      summary.innerHTML = `
          <div class="detail-stat-card">
              <div class="detail-stat-label">Jumlah Debitur</div>
              <div class="detail-stat-value">${fmt(rows.length)}${rows.length !== totalRows ? ` / ${fmt(totalRows)}` : ''}</div>
          </div>
          <div class="detail-stat-card">
              <div class="detail-stat-label">Total Baki Debet</div>
              <div class="detail-stat-value blue">Rp ${fmt(totalBaki)}</div>
          </div>
          <div class="detail-stat-card">
              <div class="detail-stat-label">Total Recovery</div>
              <div class="detail-stat-value emerald">Rp ${fmt(totalRecovery)}</div>
          </div>
          <div class="detail-stat-card">
              <div class="detail-stat-label">Pokok + Bunga</div>
              <div class="detail-stat-value orange">Rp ${fmt(totalPokok + totalBunga)}</div>
          </div>
          ${type === 'backflow' && monitoredBackflow.length ? `
            <div class="backflow-monitor-warning">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"></path></svg>
              <div>
                <strong>${fmt(monitoredBackflow.length)} debitur backflow masih dalam masa pantau • Rp ${fmt(monitoredBaki)}</strong>
                <span>Tanggal jatuh tempo angsuran berikutnya masih setelah posisi harian. Pastikan pembayaran berikutnya terealisasi tepat waktu agar rekening tidak kembali masuk NPL.</span>
              </div>
            </div>` : ''}
      `;
  }


  function isRecoveryDetailMobile() {
      return window.innerWidth < 768;
  }

  function renderRecoveryDetailMobileCards(rows, type) {
      return `
          <div class="detail-mobile-list">
              ${rows.map(item => {
                  const noRek = recoveryEscape(item.no_rekening || '-');
                  const nama = recoveryEscape(item.nama_nasabah || '-');
                  const kolek = recoveryEscape(item.kolek || '-');
                  const kolekUpdate = recoveryEscape(item.kolek_update || '-');
                  return `
                      <article class="detail-mobile-card">
                          <div class="detail-mobile-card-head">
                              <div class="detail-mobile-identity">
                                  <div class="detail-mobile-rek">${noRek}</div>
                                  <div class="detail-mobile-name" title="${nama}">${nama}</div>
                              </div>
                              ${recoveryStatusBadge(item.jt_status)}
                          </div>
                          <div class="detail-mobile-metrics">
                              <div class="detail-mobile-metric">
                                  <div class="detail-mobile-label">Recovery</div>
                                  <div class="detail-mobile-value blue">Rp ${fmt(item.recovery_nominal)}</div>
                              </div>
                              <div class="detail-mobile-metric">
                                  <div class="detail-mobile-label">Baki Debet</div>
                                  <div class="detail-mobile-value">Rp ${fmt(item.baki_debet)}</div>
                              </div>
                              <div class="detail-mobile-metric">
                                  <div class="detail-mobile-label">Pokok</div>
                                  <div class="detail-mobile-value">Rp ${fmt(item.angsuran_pokok)}</div>
                              </div>
                              <div class="detail-mobile-metric">
                                  <div class="detail-mobile-label">Bunga</div>
                                  <div class="detail-mobile-value orange">Rp ${fmt(item.angsuran_bunga)}</div>
                              </div>
                          </div>
                          <div class="detail-mobile-meta">
                              <span>${recoveryTypeBadge(item.jenis_recovery || recoveryDetailLabel, type)}</span>
                              <span>Kolek <span class="detail-kolek-badge old">${kolek}</span> → <span class="detail-kolek-badge new">${kolekUpdate}</span></span>
                              <span>Angsuran: <b>${formatDate(item.tgl_jatuh_tempo)}</b></span>
                              <span>Bayar: <b>${formatDate(item.tgl_trans)}</b></span>
                          </div>
                      </article>
                  `;
              }).join('')}
          </div>
      `;
  }

  function renderRecoveryDetailTable(type) {
      const body = document.getElementById('modalBodyRecovery');
      const countBadge = document.getElementById('modalCountRecovery');
      if (!body) return;

      const query = String(document.getElementById('recovery_detail_search')?.value || '').trim().toLowerCase();
      const rows = query
          ? recoveryDetailRows.filter(item => {
                const haystack = `${item.no_rekening || ''} ${item.nama_nasabah || ''} ${item.jenis_recovery || ''}`.toLowerCase();
                return haystack.includes(query);
            })
          : [...recoveryDetailRows];

      if (countBadge) countBadge.textContent = `${fmt(rows.length)} data`;
      renderRecoveryDetailSummary(rows, recoveryDetailRows.length, type);

      if (!rows.length) {
          body.innerHTML = `
              <div class="detail-empty-state">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                  <div class="font-bold text-slate-600">Data tidak ditemukan</div>
                  <div class="mt-1 text-[10px]">Coba ubah kata pencarian atau filter status.</div>
              </div>
          `;
          return;
      }

      if (isRecoveryDetailMobile()) {
          body.innerHTML = renderRecoveryDetailMobileCards(rows, type);
          return;
      }

      let tableHtml = `
        <table class="modal-table">
            <colgroup>
                <col style="width:105px">
                <col style="width:var(--detailRek)">
                <col style="width:var(--detailNama)">
                <col style="width:120px">
                <col style="width:120px">
                <col style="width:54px">
                <col style="width:54px">
                <col style="width:100px">
                <col style="width:115px">
                <col style="width:100px">
                <col style="width:105px">
                <col style="width:105px">
            </colgroup>
            <thead>
                <tr>
                    <th class="detail-col-jenis">Jenis</th>
                    <th class="modal-freeze-rek">No Rekening</th>
                    <th class="modal-freeze-name">Nama Nasabah</th>
                    <th class="text-right">Baki Debet</th>
                    <th class="text-right">Recovery</th>
                    <th class="text-center">Kol</th>
                    <th class="text-center">Upd</th>
                    <th class="text-center">Tgl Angsuran</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Tgl Bayar</th>
                    <th class="text-right">Pokok</th>
                    <th class="text-right">Bunga</th>
                </tr>
            </thead>
            <tbody>`;

      rows.forEach(item => {
          const noRek = recoveryEscape(item.no_rekening || '-');
          const nama = recoveryEscape(item.nama_nasabah || '-');
          tableHtml += `
            <tr>
                <td class="detail-col-jenis">${recoveryTypeBadge(item.jenis_recovery || recoveryDetailLabel, type)}</td>
                <td class="modal-freeze-rek font-mono font-bold text-slate-600">${noRek}</td>
                <td class="modal-freeze-name font-bold text-slate-800" title="${nama}"><div class="truncate">${nama}</div></td>
                <td class="text-right detail-money-sub">${fmt(item.baki_debet)}</td>
                <td class="text-right detail-money-main">${fmt(item.recovery_nominal)}</td>
                <td class="text-center"><span class="detail-kolek-badge old">${recoveryEscape(item.kolek || '-')}</span></td>
                <td class="text-center"><span class="detail-kolek-badge new">${recoveryEscape(item.kolek_update || '-')}</span></td>
                <td class="text-center text-slate-600">${formatDate(item.tgl_jatuh_tempo)}</td>
                <td class="text-center">${recoveryStatusBadge(item.jt_status)}</td>
                <td class="text-center text-slate-600">${formatDate(item.tgl_trans)}</td>
                <td class="text-right detail-money-sub">${fmt(item.angsuran_pokok)}</td>
                <td class="text-right detail-money-sub">${fmt(item.angsuran_bunga)}</td>
            </tr>`;
      });

      tableHtml += `</tbody></table>`;
      body.innerHTML = tableHtml;
  }

  async function openModalDebitur(type, kode){
      const modal = document.getElementById('modalDebiturRecovery');
      const title = document.getElementById('modalTitleRecovery');
      const sub   = document.getElementById('modalSubtitleRecovery');
      const body  = document.getElementById('modalBodyRecovery');
      const summary = document.getElementById('recoveryDetailSummary');
      const search = document.getElementById('recovery_detail_search');

      const closing = document.getElementById('closing_date_recovery').value;
      const harian  = document.getElementById('harian_date_recovery').value;

      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.style.overflow = 'hidden';

      const labelType = type === 'lunas'
          ? 'Lunas NPL'
          : (type === 'angsuran'
              ? 'Angsuran NPL'
              : (type === 'total_recovery' ? 'Total Recovery' : 'Backflow'));
      recoveryDetailLabel = labelType;
      recoveryDetailRows = [];

      title.innerHTML = `
          <span class="truncate">${recoveryEscape(labelType)}</span>
          <span class="modal-code-badge">${recoveryEscape(kode)}</span>
          <span id="modalCountRecovery">0 data</span>
      `;
      sub.textContent = `Posisi ${formatDate(closing)} dibanding ${formatDate(harian)}`;

      const jtSelect = document.getElementById('recovery_jt_status');
      const toolbar = document.getElementById('modalRecoveryToolbar');
      const showStatusFilter = type === 'backflow';
      if (jtSelect) {
          jtSelect.classList.toggle('hidden', !showStatusFilter);
          jtSelect.value = 'all';
      }
      toolbar?.classList.toggle('has-status-filter', showStatusFilter);
      if (search) {
          search.value = '';
          search.oninput = () => renderRecoveryDetailTable(type);
      }
      if (summary) summary.classList.add('hidden');
      body.innerHTML = `
          <div class="detail-loading-state">
              <div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-3"></div>
              <span>Mengambil detail debitur...</span>
          </div>
      `;

      const loadDetail = async () => {
          body.innerHTML = `
              <div class="detail-loading-state">
                  <div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-3"></div>
                  <span>Mengambil detail debitur...</span>
              </div>
          `;
          if (summary) summary.classList.add('hidden');

          const payload = buildRecoveryDetailPayload(type, kode, closing, harian);
          if (type === 'backflow') payload.jt_status = jtSelect?.value || 'all';

          try {
              const res = await fetch(API_NPL, {
                  method:'POST',
                  headers:{'Content-Type':'application/json'},
                  body:JSON.stringify(payload)
              });
              const json = await res.json();
              recoveryDetailRows = Array.isArray(json.data) ? json.data : [];
              renderRecoveryDetailTable(type);
              const wrap = document.getElementById('modalTableWrapRecovery');
              if (wrap) { wrap.scrollTop = 0; wrap.scrollLeft = 0; }
          } catch(e) {
              recoveryDetailRows = [];
              if (summary) summary.classList.add('hidden');
              body.innerHTML = `
                  <div class="detail-empty-state text-red-500">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5M12 17h.01"></path></svg>
                      <div class="font-bold">Gagal mengambil detail</div>
                      <div class="mt-1 text-[10px]">${recoveryEscape(e.message)}</div>
                  </div>
              `;
          }
      };

      if (jtSelect) jtSelect.onchange = loadDetail;
      await loadDetail();
  }

  let recoveryDetailResizeTimer = null;
  window.addEventListener('resize', () => {
      clearTimeout(recoveryDetailResizeTimer);
      recoveryDetailResizeTimer = setTimeout(() => {
          const modal = document.getElementById('modalDebiturRecovery');
          if (modal?.classList.contains('flex') && recoveryDetailRows.length) {
              const type = String(recoveryDetailLabel || '').toLowerCase().includes('backflow')
                  ? 'backflow'
                  : (String(recoveryDetailLabel || '').toLowerCase().includes('angsuran') ? 'angsuran' : (String(recoveryDetailLabel || '').toLowerCase().includes('total') ? 'total_recovery' : 'lunas'));
              renderRecoveryDetailTable(type);
          }
      }, 120);
  });

  const closeModal = () => {
      const modal = document.getElementById('modalDebiturRecovery');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.body.style.overflow = '';
      recoveryDetailRows = [];
      const search = document.getElementById('recovery_detail_search');
      if (search) search.value = '';
  };
  document.getElementById('btnCloseRecovery').onclick = closeModal;
  document.getElementById('modalDebiturRecovery').onclick = (e) => {
      if(e.target.id === 'modalDebiturRecovery') closeModal();
  };
  document.addEventListener('keydown', e => {
      if (e.key !== 'Escape') return;
      closeModal();
      closeModalPeringatan();
      const infoModal = document.getElementById('modalInfoRecovery');
      infoModal?.classList.add('hidden');
      infoModal?.classList.remove('flex');
      if (!document.getElementById('modalDebiturRecovery')?.classList.contains('flex')) document.body.style.overflow = '';
  });
</script>
