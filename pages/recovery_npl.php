<style>
  :root {
      --primary:#2563eb;
      --bg:#f8fafc;
      --text:#334155;
  }

  * { box-sizing:border-box; }
  body {
      margin:0;
      font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
      background:var(--bg);
      color:var(--text);
      overflow:hidden;
  }

  .hidden { display:none !important; }

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
      --recMetric:148px;
      --recNet:158px;
      --rec_headH:40px;

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
      width:calc(var(--recCode) + var(--recName) + (var(--recMetric) * 6) + var(--recNet));
      min-width:100%;
      border-collapse:separate;
      border-spacing:0;
      table-layout:fixed;
      font-size:11px;
      color:#334155;
  }
  #tabelRecovery col.rec-col-code { width:var(--recCode); }
  #tabelRecovery col.rec-col-name { width:var(--recName); }
  #tabelRecovery col.rec-col-metric { width:var(--recMetric); }
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
      top:0;
      z-index:60;
      height:40px;
      padding:5px 7px;
      background:#f1f5f9;
      color:#1e3a8a;
      border-bottom:1px solid #cbd5e1;
      box-shadow:inset 0 1px 0 #cbd5e1,inset 0 -1px 0 #cbd5e1;
      font-size:10px;
      font-weight:850;
      line-height:1.12;
      letter-spacing:.025em;
      text-transform:uppercase;
      white-space:normal;
  }
  .head-lunas { color:#1e40af !important; background:#eff6ff !important; }
  .head-backflow { color:#b45309 !important; background:#fffbeb !important; }
  .head-angsuran { color:#047857 !important; background:#ecfdf5 !important; }
  .head-flow { color:#be123c !important; background:#fff1f2 !important; }
  .head-total { color:#0e7490 !important; background:#ecfeff !important; }
  .head-net { color:#4338ca !important; background:#eef2ff !important; }
  .head-ratio { color:#854d0e !important; background:#fefce8 !important; }
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

  .rec-metric-cell {
      width:var(--recMetric);
      min-width:var(--recMetric);
      max-width:var(--recMetric);
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
  .rec-cell-empty { color:#94a3b8; font-weight:750; }
  .rec-pos { color:#dc2626 !important; font-weight:850; }
  .rec-neg { color:#059669 !important; font-weight:850; }

  .rec-desktop-value { display:inline; }
  .rec-mobile-noa,
  .rec-mobile-nom { display:none; }

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
  /* === INFO MODAL === */
  #modalInfoRecovery { padding:10px; }
  #modalInfoCardRecovery {
      display:flex;
      flex-direction:column;
      width:min(520px,calc(100vw - 20px));
      max-height:min(82dvh,680px);
  }
  #modalInfoBodyRecovery { overflow:auto; }

  /* === LARGE DESKTOP === */
  @media (min-width:1440px) {
      #recoveryPage { padding:4px 6px 6px !important; }
      #recoveryHeader { padding:8px 12px !important; }
      #recScroller {
          --recCode:60px;
          --recName:180px;
          --recMetric:150px;
          --recNet:160px;
      }
      #tabelRecovery { font-size:11px; }
  }

  /* === LAPTOP === */
  @media (min-width:1024px) and (max-width:1439px) {
      #recoveryPage { padding:5px 7px 7px !important; }
      #recScroller {
          --recCode:52px;
          --recName:164px;
          --recMetric:132px;
          --recNet:142px;
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
          --recMetric:118px;
          --recNet:128px;
      }
      #tabelRecovery { font-size:10px; }
      #tabelRecovery thead th { font-size:8.5px; }
      #tabelRecovery th,#tabelRecovery td { padding:5px 5px; }
  }

  /* === MOBILE === */
  @media (max-width:767px) {
      body { overflow:hidden; }
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
          --recName:102px;
          --recMetric:86px;
          --recNet:94px;
          border-radius:7px;
          scrollbar-gutter:auto;
      }
      #recScroller::-webkit-scrollbar { width:4px; height:4px; }
      #tabelRecovery {
          min-width:calc(var(--recName) + (var(--recMetric) * 6) + var(--recNet));
          font-size:8.5px;
      }
      #tabelRecovery col.rec-col-code { display:none; }
      #tabelRecovery .col-kode { display:none !important; }
      #tabelRecovery th,#tabelRecovery td {
          height:38px;
          padding:4px 4px;
      }
      #tabelRecovery thead th {
          height:36px;
          padding:3px 3px;
          font-size:7px;
          line-height:1.08;
          letter-spacing:0;
      }
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
      .rec-metric-cell,.rec-net-cell {
          padding:2px 3px !important;
      }
      .rec-cell-link { padding:2px 1px; overflow:visible; }
      .rec-desktop-value { display:none; }
      .rec-mobile-noa,
      .rec-mobile-nom { display:block; width:100%; text-align:right; white-space:nowrap; }
      .rec-mobile-noa {
          color:#64748b;
          font-family:Inter,system-ui,sans-serif;
          font-size:6.5px;
          font-weight:850;
          line-height:1;
      }
      .rec-mobile-nom {
          margin-top:2px;
          overflow:hidden;
          font-size:8.5px;
          font-weight:850;
          line-height:1.05;
          text-overflow:ellipsis;
      }
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
          max-height:82dvh;
          border-radius:14px 14px 0 0 !important;
      }
      #modalInfoBodyRecovery {
          padding:12px !important;
          font-size:12px !important;
          line-height:1.4;
      }
  }

  @media (max-width:374px) {
      #recScroller {
          --recName:92px;
          --recMetric:80px;
          --recNet:88px;
      }
      #recoveryHeaderTop h1 { font-size:13px !important; }
      .mobile-filter-toggle { padding:0 7px; }
      .rec-mobile-nom { font-size:8px; }
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
          <col class="rec-col-metric">
          <col class="rec-col-metric">
          <col class="rec-col-metric">
          <col class="rec-col-metric">
          <col class="rec-col-metric">
          <col class="rec-col-metric">
          <col class="rec-col-net">
        </colgroup>
        <thead id="theadRec">
          <tr>
            <th class="col-kode">Kode</th>
            <th class="col-nama text-left" id="thNamaRec">Area</th>
            <th class="rec-metric-cell head-flow">Flow NPL</th>
            <th class="rec-metric-cell head-lunas">Lunas NPL</th>
            <th class="rec-metric-cell head-backflow">Backflow</th>
            <th class="rec-metric-cell head-angsuran">Angsuran NPL</th>
            <th class="rec-metric-cell head-total">Total Recovery</th>
            <th class="rec-metric-cell head-ratio">% Flow PAR</th>
            <th class="rec-net-cell head-net">Flow - Recovery</th>
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
      <p class="mt-2">Anda tidak memiliki izin untuk melihat detail data nasabah milik <span class="font-bold text-red-600 px-1 bg-red-50 rounded" id="warnTargetLvl">Unit</span>.</p>
    </div>
    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
      <button onclick="closeModalPeringatan()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold transition">Mengerti</button>
    </div>
  </div>
</div>

<div id="modalInfoRecovery" class="fixed inset-0 hidden bg-slate-900/40 backdrop-blur-sm items-center justify-center z-[9999] px-4">
  <div id="modalInfoCardRecovery" class="bg-white rounded-xl shadow-2xl overflow-hidden animate-scale-up">
    <div class="p-3 md:p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
      <h3 class="font-bold text-slate-800">Panduan Kamus Kolom</h3>
      <button type="button" id="btnCloseInfoRecovery" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 font-bold">x</button>
    </div>
    <div id="modalInfoBodyRecovery" class="p-4 text-sm text-slate-600 space-y-3">
      <p><b>Flow NPL:</b> rekening yang tidak NPL di closing, lalu menjadi KL/D/M di harian.</p>
      <p><b>% Flow PAR:</b> OS Flow NPL dibagi baki debet NPL bulan lalu. Hijau untuk 0/negatif, kuning untuk sampai 7%, merah jika lebih dari 7%.</p>
      <p><b>Recovery:</b> Lunas NPL + Backflow + Angsuran NPL.</p>
      <p><b>Angsuran NPL:</b> selisih baki debet closing NPL dibanding harian jika baki debet harian lebih kecil.</p>
      <div class="p-3 rounded-lg border border-blue-200 bg-blue-50 text-blue-900 text-xs">
        <b>Formula:</b> Flow - Recovery. Positif berarti tekanan NPL naik, negatif berarti recovery lebih besar.
      </div>
      <p class="text-xs text-slate-500">Jika total recovery berbeda dengan migrasi kolek, biasanya ada kapitalisasi/restrukturisasi yang membuat baki debet naik.</p>
      <p class="text-xs text-slate-500">Catatan: % NPL bisa naik walaupun OSC NPL turun jika total portofolio/OS turun lebih besar.</p>
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
          <th class="col-kode">Kode</th>
          <th class="col-nama text-left" id="thNamaRec">Area</th>
          <th class="rec-metric-cell head-flow border-r border-slate-200">
              <span class="rec-head-full">Flow NPL</span><span class="rec-head-short">Flow</span>
          </th>
          <th class="rec-metric-cell head-lunas border-r border-slate-200">
              <span class="rec-head-full">Lunas NPL</span><span class="rec-head-short">Lunas</span>
          </th>
          <th class="rec-metric-cell head-backflow border-r border-slate-200">
              <span class="rec-head-full">Backflow</span><span class="rec-head-short">Backflow</span>
          </th>
          <th class="rec-metric-cell head-angsuran border-r border-slate-200">
              <span class="rec-head-full">Angsuran NPL</span><span class="rec-head-short">Angsuran</span>
          </th>
          <th class="rec-metric-cell head-total border-r border-slate-200 cursor-pointer hover:bg-cyan-100" id="sortTotalBaki" title="Urutkan total recovery">
              <span class="rec-head-full">Total Recovery</span><span class="rec-head-short">Recovery</span>
          </th>
          <th class="rec-metric-cell head-ratio border-r border-slate-200">
              <span class="rec-head-full">% Flow PAR</span><span class="rec-head-short">% Flow</span>
          </th>
          <th class="rec-net-cell head-net">
              <span class="rec-head-full">Flow - Recovery</span><span class="rec-head-short">Net Flow</span>
          </th>
        </tr>
      `;

      document.getElementById('sortTotalBaki')?.addEventListener('click', () => doSort('total_baki'));
      setTimeout(updateRecStickyHeader, 20);
  }

  async function populateKantorOptionsRecovery(userKode) {
      const opt = document.getElementById('opt_kantor_recovery');
      if (!opt) return;

      if (userKode && userKode !== '000') {
          opt.innerHTML = `<option value="CAB-${userKode}">${userKode} - Cabang Login</option>`;
          opt.value = `CAB-${userKode}`;
          opt.disabled = true;
          recoveryScopeMode = 'cabang';
          return;
      }

      try {
          const res = await fetch('./api/kode/', {
              method:'POST',
              headers:{'Content-Type':'application/json'},
              body:JSON.stringify({type:'kode_kantor'})
          });
          const json = await res.json();
          const list = Array.isArray(json.data) ? json.data : [];
          let html = `
            <option value="ALL">Konsolidasi</option>
            <option value="KOR-SEMARANG">Korwil Semarang</option>
            <option value="KOR-SOLO">Korwil Solo</option>
            <option value="KOR-BANYUMAS">Korwil Banyumas</option>
            <option value="KOR-PEKALONGAN">Korwil Pekalongan</option>
          `;
          list.filter(x => x.kode_kantor !== '000')
              .sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor))
              .forEach(it => {
                  const kode = String(it.kode_kantor).padStart(3,'0');
                  html += `<option value="CAB-${kode}">${kode} - ${it.nama_kantor}</option>`;
              });
          opt.innerHTML = html;
          opt.disabled = false;
      } catch(e) {
          opt.innerHTML = `<option value="ALL">Konsolidasi</option>`;
      }
  }

  // --- INIT ---
  window.addEventListener('DOMContentLoaded', async () => {
    renderRecoveryHeader();
    document.getElementById('btnInfoRecovery')?.addEventListener('click', () => {
        const modal = document.getElementById('modalInfoRecovery');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
    document.getElementById('btnCloseInfoRecovery')?.addEventListener('click', () => {
        const modal = document.getElementById('modalInfoRecovery');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
    document.getElementById('modalInfoRecovery')?.addEventListener('click', e => {
        if (e.target.id === 'modalInfoRecovery') {
            e.currentTarget.classList.add('hidden');
            e.currentTarget.classList.remove('flex');
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
        kantor = myKode;
        payload.kode_kantor = myKode;
        recoveryScopeMode = 'cabang';
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

    } catch(err) {
      if(err.name !== 'AbortError') {
          console.error(err);
          tbody.innerHTML = `<tr><td colspan="9" class="p-4 text-center text-red-500 font-bold">Gagal memuat data.</td></tr>`;
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
            <td class="rec-metric-cell text-red-600 font-bold border-r border-blue-300">${metricText(tot.baki_debet_flow_npl, tot.noa_flow_npl)}</td>
            <td class="rec-metric-cell border-r border-blue-300">${recMetricLink(tot.baki_debet_lunas, tot.noa_lunas, 'lunas', detailKode, 'blue')}</td>
            <td class="rec-metric-cell border-r border-blue-300">${recMetricLink(tot.baki_debet_backflow, tot.noa_backflow, 'backflow', detailKode, 'orange')}</td>
            <td class="rec-metric-cell border-r border-blue-300">${recMetricLink(tot.baki_debet_angsuran_npl, tot.noa_angsuran_npl, 'angsuran', detailKode, 'green')}</td>
            <td class="rec-metric-cell text-blue-800 border-r border-blue-300">${recMetricLink(tBak, tNoa, 'total_recovery', detailKode, 'blue')}</td>
            <td class="rec-metric-cell border-r border-blue-300">${flowParRatioCell(tot)}</td>
            <td class="rec-net-cell ${net > 0 ? 'rec-pos' : (net < 0 ? 'rec-neg' : 'text-slate-500')}">${net > 0 ? '+' : ''}${fmt(net)}</td>
        </tr>
     `;
  }

  function metricText(nominal, noa) {
      const nominalText = num(nominal) !== 0 ? fmt(nominal) : '-';
      const noaText = fmt(noa);
      return `
          <span class="rec-desktop-value">${nominalText} / (${noaText})</span>
          <span class="rec-mobile-noa">${noaText} NOA</span>
          <span class="rec-mobile-nom">${nominalText}</span>
      `;
  }

  function flowParRatioValue(row) {
      const closingNpl = num(row?.npl_closing);
      if (closingNpl <= 0) return 0;
      return (num(row?.baki_debet_flow_npl) / closingNpl) * 100;
  }

  function flowParRatioClass(value) {
      if (value <= 0) return 'bg-emerald-50 text-emerald-700 border-emerald-200';
      if (value > 7) return 'bg-red-50 text-red-700 border-red-200';
      return 'bg-yellow-50 text-yellow-700 border-yellow-200';
  }

  function flowParRatioCell(row) {
      const val = flowParRatioValue(row);
      return `<span class="inline-flex items-center justify-end min-w-[70px] rounded-md border px-2 py-1 text-[11px] font-extrabold ${flowParRatioClass(val)}">${val.toFixed(2)}%</span>`;
  }

  function getTotalDetailKode() {
      const myKode = getAppUser();
      const optVal = document.getElementById('opt_kantor_recovery')?.value || 'ALL';
      if (myKode !== '000') return myKode;
      if (optVal.startsWith('CAB-')) return optVal.replace('CAB-', '');
      return '000';
  }

  function recMetricLink(nominal, noa, type, kode, color = 'blue') {
      if (num(noa) <= 0 && num(nominal) <= 0) return `<span class="rec-cell-empty">${metricText(0, 0)}</span>`;
      const cls = color === 'orange' ? 'text-orange-600' : (color === 'green' ? 'text-emerald-600' : 'text-blue-600');
      return `<a href="#" class="rec-cell-link ${cls} font-bold hover:bg-slate-100" data-act="view" data-type="${type}" data-kode="${kode}">${metricText(nominal, noa)}</a>`;
  }

  function renderRows(rows) {
     const tbody = document.getElementById('recoveryBody');
     if(rows.length === 0) {
         tbody.innerHTML = `<tr><td colspan="9" class="p-8 text-center text-slate-400">Tidak ada data recovery.</td></tr>`;
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
                <td class="col-nama font-semibold text-slate-700 text-xs">
                    <div class="truncate" title="${nama}">${nama}</div>
                </td>
                
                <td class="rec-metric-cell text-red-600 font-bold border-r border-slate-100">${metricText(r.baki_debet_flow_npl, r.noa_flow_npl)}</td>
                <td class="rec-metric-cell border-r border-slate-100">${recMetricLink(r.baki_debet_lunas, r.noa_lunas, 'lunas', kode, 'blue')}</td>
                <td class="rec-metric-cell border-r border-slate-100">${recMetricLink(r.baki_debet_backflow, r.noa_backflow, 'backflow', kode, 'orange')}</td>
                <td class="rec-metric-cell border-r border-slate-100">${recMetricLink(r.baki_debet_angsuran_npl, r.noa_angsuran_npl, 'angsuran', kode, 'green')}</td>
                <td class="rec-metric-cell font-bold bg-blue-50/30 border-r border-slate-100">${recMetricLink(tBak, tNoa, 'total_recovery', kode, 'blue')}</td>
                <td class="rec-metric-cell border-r border-slate-100">${flowParRatioCell(r)}</td>
                <td class="rec-net-cell ${net > 0 ? 'rec-pos' : (net < 0 ? 'rec-neg' : 'text-slate-500')}">${net > 0 ? '+' : ''}${fmt(net)}</td>
            </tr>
        `;
     }).join('');

     // Spacer buat scroll mobile biar gak ketutup
     tbody.innerHTML += `<tr style="height: 60px;"><td colspan="9" class="border-none bg-transparent"></td></tr>`;
  }

  // --- SORTING ---
  const doSort = (colKey) => {
    sortState = { column: colKey, direction: sortState.column === colKey ? -sortState.direction : 1 };
    const sorted = [...recoveryDataRaw].sort((a,b) => {
        let valA, valB;
        if(colKey === 'total_noa') {
            valA = num(a.noa_lunas) + num(a.noa_backflow) + num(a.noa_angsuran_npl);
            valB = num(b.noa_lunas) + num(b.noa_backflow) + num(b.noa_angsuran_npl);
        } else {
            valA = num(a.baki_debet_lunas) + num(a.baki_debet_backflow) + num(a.baki_debet_angsuran_npl);
            valB = num(b.baki_debet_lunas) + num(b.baki_debet_backflow) + num(b.baki_debet_angsuran_npl);
        }
        return (valA - valB) * sortState.direction;
    });
    const sortTotalNoa = document.getElementById('sortTotalNoa');
    const sortTotalBaki = document.getElementById('sortTotalBaki');
    if (sortTotalNoa) sortTotalNoa.innerText = `NOA ${colKey==='total_noa' ? (sortState.direction>0?'ASC':'DESC') : ''}`;
    if (sortTotalBaki) sortTotalBaki.innerText = `Total Recovery ${colKey==='total_baki' ? (sortState.direction>0?'ASC':'DESC') : ''}`;
    renderRows(sorted);
  };
  document.getElementById('sortTotalNoa')?.addEventListener('click', () => doSort('total_noa'));
  document.getElementById('sortTotalBaki')?.addEventListener('click', () => doSort('total_baki'));

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
                  <th style="background-color:#d9ead3;">NOA FLOW NPL</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET FLOW NPL</th>
                  <th style="background-color:#d9ead3;">NOA LUNAS</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET LUNAS</th>
                  <th style="background-color:#d9ead3;">NOA BACKFLOW</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET BACKFLOW</th>
                  <th style="background-color:#d9ead3;">NOA ANGSURAN NPL</th>
                  <th style="background-color:#d9ead3;">BAKI DEBET ANGSURAN NPL</th>
                  <th style="background-color:#d9ead3;">TOTAL NOA</th>
                  <th style="background-color:#d9ead3;">TOTAL BAKI DEBET</th>
                  <th style="background-color:#d9ead3;">% FLOW PAR / NPL M-1</th>
                  <th style="background-color:#d9ead3;">FLOW - RECOVERY</th>
              </tr>
          </thead>
          <tbody>`;
      
      if(gt) {
          const tNoa = num(gt.total_noa_recovery || (num(gt.noa_lunas) + num(gt.noa_backflow) + num(gt.noa_angsuran_npl)));
          const tBak = num(gt.total_recovery || (num(gt.baki_debet_lunas) + num(gt.baki_debet_backflow) + num(gt.baki_debet_angsuran_npl)));
          table += `<tr>
              <td style="font-weight:bold;"></td>
              <td style="font-weight:bold;">GRAND TOTAL</td>
              <td style="font-weight:bold;">${gt.noa_flow_npl}</td>
              <td style="font-weight:bold;">${gt.baki_debet_flow_npl}</td>
              <td style="font-weight:bold;">${gt.noa_lunas}</td>
              <td style="font-weight:bold;">${gt.baki_debet_lunas}</td>
              <td style="font-weight:bold;">${gt.noa_backflow}</td>
              <td style="font-weight:bold;">${gt.baki_debet_backflow}</td>
              <td style="font-weight:bold;">${gt.noa_angsuran_npl}</td>
              <td style="font-weight:bold;">${gt.baki_debet_angsuran_npl}</td>
              <td style="font-weight:bold;">${tNoa}</td>
              <td style="font-weight:bold;">${tBak}</td>
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
              <td>${r.noa_flow_npl}</td>
              <td>${r.baki_debet_flow_npl}</td>
              <td>${r.noa_lunas}</td>
              <td>${r.baki_debet_lunas}</td>
              <td>${r.noa_backflow}</td>
              <td>${r.baki_debet_backflow}</td>
              <td>${r.noa_angsuran_npl}</td>
              <td>${r.baki_debet_angsuran_npl}</td>
              <td>${tNoa}</td>
              <td>${tBak}</td>
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
      const targetCabang = targetKode.length > 3 ? targetKode.substring(0, 3) : targetKode.padStart(3,'0');
      const myKode = getAppUser();

      // PENGAMANAN AKSES (HANYA BISA BUKA MILIKNYA SENDIRI ATAU PUSAT)
      if (myKode !== '000' && myKode !== targetCabang) {
          document.getElementById('warnUserLvl').innerText = `Unit ${myKode}`;
          document.getElementById('warnTargetLvl').innerText = `Unit ${targetCabang}`;
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

      if (cleanKode.length > 3) {
          payload.kode_kantor = cleanKode.substring(0, 3);
          payload.kode_kankas = cleanKode;
          return payload;
      }

      if (cleanKode && cleanKode !== '000' && cleanKode !== 'TOTAL') {
          payload.kode_kantor = cleanKode.padStart(3, '0');
          return payload;
      }

      if (myKode !== '000') {
          payload.kode_kantor = myKode;
      } else if (optVal.startsWith('CAB-')) {
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

  function renderRecoveryDetailSummary(rows, totalRows) {
      const summary = document.getElementById('recoveryDetailSummary');
      if (!summary) return;

      const totalBaki = rows.reduce((sum, item) => sum + num(item.baki_debet), 0);
      const totalRecovery = rows.reduce((sum, item) => sum + num(item.recovery_nominal), 0);
      const totalPokok = rows.reduce((sum, item) => sum + num(item.angsuran_pokok), 0);
      const totalBunga = rows.reduce((sum, item) => sum + num(item.angsuran_bunga), 0);

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
      renderRecoveryDetailSummary(rows, recoveryDetailRows.length);

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
  });
</script>
