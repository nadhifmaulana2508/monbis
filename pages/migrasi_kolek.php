<div id="migrasiKolekPage" class="mk-page">
  <section class="mk-report-head">
    <div class="mk-heading">
      <span class="mk-title-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 19V5"></path><path d="M4 19h16"></path><path d="M8 16v-5"></path><path d="M12 16V7"></path><path d="M16 16v-8"></path>
        </svg>
      </span>
      <div class="mk-title-copy">
        <h1>Migrasi Kolektibilitas</h1>
        <p>Cockpit kredit cabang: migrasi, Flow PAR, RR, potensi mingguan, dan proyeksi run off.</p>
      </div>
    </div>
    <div class="mk-head-actions">
      <span id="mkNominalBadge" class="mk-nominal-badge">SALDO BANK</span>
      <button type="button" id="mkViewSwap" class="mk-view-swap" onclick="toggleMkReportView()" title="Tampilkan Rekap Migrasi Kolektibilitas" aria-label="Tampilkan Rekap Migrasi Kolektibilitas" aria-pressed="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h11"></path><path d="m14 3 4 4-4 4"></path><path d="M17 17H6"></path><path d="m10 13-4 4 4 4"></path></svg>
      </button>
      <button type="button" id="mkExportBtn" class="mk-export-btn" onclick="exportMigrasiKolek()" title="Download Excel" aria-label="Download Excel">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 20h14"></path></svg>
      </button>
    </div>
  </section>

  <section id="mkSummary" class="mk-summary" hidden aria-label="Ringkasan migrasi">
    <div class="mk-summary-card"><span>M-1</span><strong id="mkM1Os">-</strong><small id="mkM1Noa">NOA: -</small></div>
    <div class="mk-summary-card"><span>ACTUAL</span><strong id="mkActOs">-</strong><small id="mkActNoa">NOA: -</small></div>
    <div class="mk-summary-card"><span>REALISASI BARU</span><strong id="mkRealisasiOs">-</strong><small id="mkRealisasiNoa">NOA: -</small></div>
    <div class="mk-summary-card"><span>RESTRUCK</span><strong id="mkRestruckOs">-</strong><small id="mkRestruckNoa">NOA: -</small></div>
    <div class="mk-summary-card"><span>ANGSURAN MURNI</span><strong id="mkAngsuranOs">-</strong><small id="mkAngsuranNoa">NOA: -</small></div>
    <div class="mk-summary-card"><span>PELUNASAN</span><strong id="mkPelunasanOs">-</strong><small id="mkPelunasanNoa">NOA: -</small></div>
    <div class="mk-summary-card mk-summary-growth"><span>GROWTH ACT - M-1</span><strong id="mkGrowth">-</strong><small id="mkNplDelta">Δ NPL: -</small></div>
  </section>

  <section id="mkCockpit" class="mk-cockpit" aria-label="Dashboard kredit cabang">
    <article class="mk-panel mk-panel-chart">
      <div class="mk-panel-head">
        <div><h2>OSC, NPL &amp; RR — Closing vs Actual</h2><p>Posisi outstanding, NPL, dan RR berdasarkan basis nominal terpilih.</p></div>
        <div class="mk-panel-head-badges"><span id="mkNplTrend" class="mk-panel-badge">-</span><span id="mkRrTrend" class="mk-panel-badge mk-panel-badge-rr">RR -</span></div>
      </div>
      <div id="mkOscChart" class="mk-chart mk-chart-osc" aria-label="Grafik OSC, NPL, RR, dan CKPN"></div>
      <div id="mkOscLegend" class="mk-compare-legend"><span><i class="mk-bar-prev-legend"></i>Closing</span><span><i class="mk-bar-current-legend"></i>Actual</span><span><i class="mk-bar-os"></i>OSC</span><span><i class="mk-bar-npl"></i>NPL</span><span><i class="mk-bar-rr"></i>RR (DPD 0 &amp; L)</span><span><i class="mk-bar-ckpn"></i>CKPN</span></div>
    </article>

    <article class="mk-panel mk-panel-chart">
      <div class="mk-panel-head">
        <div><h2>Realisasi, Run Off M-1 vs Actual, Flow PAR &amp; Recovery NPL</h2><p>Run off dipisah angsuran dan lunas agar perbandingan bulan lalu lebih mudah dibaca.</p></div>
        <span id="mkGrowthBadge" class="mk-panel-badge">-</span>
      </div>
      <div id="mkMovementChart" class="mk-chart mk-chart-movement" aria-label="Grafik arus kredit"></div>
      <div id="mkMovementLegend" class="mk-legend"></div>
    </article>

    <article class="mk-panel mk-panel-flow">
      <div class="mk-panel-head">
        <div><h2>Flow PAR — Penyebab Utama</h2><p>Klik kategori untuk membuka daftar rekening yang perlu ditindaklanjuti.</p></div>
        <button type="button" class="mk-text-action" onclick="mkOpenFlowPar()">Detail Flow PAR <span>↗</span></button>
      </div>
      <div id="mkFlowCauseList" class="mk-cause-list"><div class="mk-state">Menunggu data Flow PAR...</div></div>
      <div class="mk-flow-total"><span>Total Flow PAR</span><strong id="mkFlowTotal">-</strong><small id="mkFlowTotalNoa">NOA: -</small></div>
    </article>

    <article class="mk-panel mk-panel-recovery">
      <div class="mk-panel-head">
        <div><h2>Recovery NPL</h2><p>Komposisi pengurangan NPL: backflow, lunas, dan angsuran.</p></div>
        <button type="button" class="mk-text-action" onclick="mkOpenRecovery()">Detail Recovery <span>↗</span></button>
      </div>
      <div id="mkRecoveryCauseList" class="mk-cause-list"><div class="mk-state">Menunggu data Recovery NPL...</div></div>
      <div id="mkRecoveryPanelNote" class="mk-recovery-panel-note">NOA angsuran NPL tidak dijumlahkan karena rekening Backflow bisa termasuk di dalam detail angsuran.</div>
      <div class="mk-flow-total mk-recovery-total"><span>Total Recovery NPL</span><strong id="mkRecoveryPanelTotal">-</strong><small id="mkRecoveryPanelNoa">NOA: -</small></div>
    </article>

    <article class="mk-panel mk-panel-outlook">
      <div class="mk-panel-head">
        <div><h2>RR &amp; Proyeksi Run Off</h2><p>Fokus angsuran murni per bucket DPD untuk melihat run off yang sudah dan belum masuk.</p></div>
        <button type="button" class="mk-text-action" onclick="mkOpenRR()">Buka RR <span>→</span></button>
      </div>
      <div class="mk-outlook-grid">
        <div class="mk-outlook-item mk-outlook-primary"><span>RR actual</span><strong id="mkRrPct">-</strong><small id="mkRrDetail">Menunggu data RR</small></div>
        <div class="mk-outlook-item"><span>Basis RR M-1</span><strong id="mkRrTarget">-</strong><small id="mkRrTargetNoa">NOA: -</small></div>
        <div class="mk-outlook-item"><span>Lancar actual</span><strong id="mkRrPaid">-</strong><small id="mkRrPaidNoa">NOA: -</small></div>
        <div class="mk-outlook-item mk-outlook-warning"><span>Keluar basis RR</span><strong id="mkRrLate">-</strong><small id="mkRrLateNoa">NOA: -</small></div>
        <div class="mk-outlook-item mk-outlook-runoff"><span>Proyeksi run off angsuran</span><strong id="mkProjectedRunoff">-</strong><small id="mkProjectedRunoffNote">Angsuran murni</small></div>
      </div>
      <div class="mk-runoff-projection">
        <div class="mk-runoff-projection-head"><div><b>Proyeksi Angsuran per Bucket DPD</b><small>Tanpa pelunasan · hanya rekening yang belum bayar · % = pokok Okt / closing</small></div><small id="mkRunoffProjectionPeriod">-</small></div>
        <div class="mk-runoff-table-wrap">
          <div class="mk-runoff-table-head"><span>BUCKET</span><span>CLOSING</span><span>ANGSURAN SEP</span><span>ANGSURAN OKT</span><span>%</span></div>
          <div id="mkRunoffBucketTable"><div class="mk-state">Menunggu proyeksi angsuran...</div></div>
        </div>
      </div>
      <div class="mk-rr-unpaid-strip mk-runoff-gap-strip">
        <div><strong>Potensi run off pokok belum masuk</strong><small id="mkRunoffGapNote">Pokok tagihan Oktober dari rekening yang belum bayar</small></div>
        <div class="mk-rr-unpaid-value"><strong id="mkRunoffGap">-</strong><small id="mkRunoffGapNoa">NOA closing: -</small></div>
      </div>
    </article>

    <article class="mk-panel mk-panel-mob">
      <div class="mk-panel-head">
        <div><h2>FPD &amp; MOB 2–6</h2><p>Nominal cohort yang belum migrasi (DPD 0) dan sudah migrasi (DPD &gt; 0) berdasarkan tanggal realisasi.</p></div>
        <span id="mkMobBasis" class="mk-panel-badge">SALDO BANK</span>
      </div>
      <div id="mkMobSummary" class="mk-mob-summary"><div class="mk-state">Menunggu data FPD / MOB...</div></div>
    </article>

    <article class="mk-panel mk-panel-npl-breakdown">
      <div class="mk-panel-head mk-npl-breakdown-head">
        <div><h2 id="mkNplBreakdownTitle">NPL by Tahun</h2><p>Komposisi nominal NPL berdasarkan kelompok yang dipilih.</p></div>
        <div class="mk-npl-head-actions">
          <span id="mkNplBreakdownBasis" class="mk-panel-badge">SALDO BANK</span>
          <label class="mk-npl-dimension-label">
            <span class="sr-only">Kelompok NPL</span>
            <select id="mkNplDimension" onchange="loadMkNplBreakdown(getMigrasiKolekFilter())" aria-label="Kelompok NPL">
              <option value="TAHUN" selected>By Tahun</option>
              <option value="PRODUK">By Produk</option>
              <option value="ANGSURAN">By Jangka Waktu</option>
              <option value="PLAFOND">By Plafond</option>
            </select>
          </label>
        </div>
      </div>
      <div id="mkNplBreakdownBody" class="mk-npl-breakdown-body"><div class="mk-state">Menunggu data NPL...</div></div>
    </article>

    <article class="mk-panel mk-panel-potential">
      <div class="mk-panel-head">
        <div><h2>Potensi NPL</h2><p>Kandidat NPL dari risiko DPD, jatuh tempo, dan flow kolektibilitas.</p></div>
        <span id="mkPotentialBasis" class="mk-panel-badge">SALDO BANK</span>
      </div>
      <div class="mk-potential-layout">
        <div class="mk-potential-donut-col">
          <button type="button" id="mkPotentialDonut" class="mk-flow-donut mk-potential-donut" onclick="mkOpenPotentialNpl('ALL')" title="Buka seluruh detail Potensi NPL" aria-label="Buka seluruh detail Potensi NPL">
          <span class="mk-flow-donut-center"><strong id="mkPotentialDonutValue">-</strong><small id="mkPotentialDonutMeta">Potensi NPL · NOA: -</small></span>
          </button>
          <div id="mkPotentialLegend" class="mk-potential-legend"><div class="mk-state">Menunggu data Potensi NPL...</div></div>
        </div>
        <div class="mk-potential-priority">
          <div class="mk-potential-priority-head"><b>Prioritas Penagihan Mingguan</b><small>Potensi penambahan nominal NPL berdasarkan jatuh tempo.</small></div>
          <div id="mkPotentialWeekList" class="mk-potential-week-list"><div class="mk-state">Menunggu pembagian mingguan...</div></div>
        </div>
      </div>
      <div class="mk-flow-total"><span>Total Potensi NPL</span><strong id="mkPotentialTotal">-</strong><small id="mkPotentialTotalNoa">NOA: -</small></div>
    </article>

    <article class="mk-panel mk-panel-projection">
      <div class="mk-panel-head">
        <div><h2>Proyeksi Pembayaran Kredit</h2><p>Kolek L dipisah sudah/belum bayar; DP, KL, D, dan M ditampilkan jika konsisten membayar 3 bulan berturut-turut.</p></div>
        <span id="mkProjectionBasis" class="mk-panel-badge">SALDO BANK</span>
      </div>
      <div class="mk-projection-grid">
        <section class="mk-projection-section">
          <div class="mk-projection-section-head"><h3>Kolek L — DPD 0</h3><small>Bayar bulan berjalan dibanding baseline bulan lalu</small></div>
          <div id="mkLPaymentStats" class="mk-payment-stats"><div class="mk-state">Menunggu data pembayaran...</div></div>
          <div class="mk-projection-subhead"><span>Belum bayar — histori pembayaran bulan lalu</span><small id="mkLProjectionPeriod">-</small></div>
          <div id="mkLUnpaidList" class="mk-projection-detail-action"><button type="button" onclick="mkOpenProjectionDetail('l_unpaid')">Lihat detail Kolek L belum bayar <span>↗</span></button></div>
        </section>
        <section class="mk-projection-section">
          <div class="mk-projection-section-head"><h3>DP / KL / D / M</h3><small>Debitur yang membayar positif 3 bulan berturut-turut</small></div>
          <div id="mkConsistentSummary" class="mk-consistent-table"><div class="mk-state">Menunggu histori pembayaran...</div></div>
          <div class="mk-projection-subhead"><span>Nominal pembayaran per bulan</span><small id="mkConsistentPeriod">-</small></div>
          <div id="mkConsistentList" class="mk-projection-detail-action"><button type="button" onclick="mkOpenProjectionDetail('consistent')">Lihat debitur konsisten 3 bulan <span>↗</span></button></div>
        </section>
      </div>
    </article>
  </section>

  <div id="mkRecoveryModal" class="mk-modal" hidden>
    <div class="mk-modal-backdrop" data-mk-close-recovery></div>
    <section class="mk-modal-card" role="dialog" aria-modal="true" aria-labelledby="mkRecoveryModalTitle">
      <header class="mk-modal-head">
        <div class="mk-modal-head-main">
        <div class="mk-modal-title-wrap">
          <span class="mk-modal-kicker">DETAIL RECOVERY NPL</span>
          <h2 id="mkRecoveryModalTitle">Recovery NPL</h2>
          <p id="mkRecoveryModalMeta">Recovery sesuai filter aktif.</p>
        </div>
        <div id="mkRecoveryModalSummary" class="mk-modal-summary mk-modal-summary-inline"></div>
        <button type="button" id="mkRecoveryClose" class="mk-modal-close" aria-label="Tutup detail Recovery NPL">×</button>
        </div>
      <div class="mk-modal-head-tools">
        <label class="mk-modal-search"><span aria-hidden="true">⌕</span><input id="mkRecoverySearch" type="search" placeholder="Cari rekening / nama nasabah..." autocomplete="off"></label>
        <span id="mkRecoveryModalBasis" class="mk-panel-badge">SALDO BANK</span>
      </div>
      </header>
      <div id="mkRecoveryModalBody" class="mk-modal-table-wrap"><div class="mk-state">Memuat detail Recovery NPL...</div></div>
    </section>
  </div>

  <div id="mkDetailModal" class="mk-modal" hidden>
    <div class="mk-modal-backdrop" data-mk-close-detail></div>
    <section class="mk-modal-card" role="dialog" aria-modal="true" aria-labelledby="mkDetailModalTitle">
      <header class="mk-modal-head">
        <div class="mk-modal-head-main">
        <div class="mk-modal-title-wrap">
          <span id="mkDetailModalKicker" class="mk-modal-kicker">DETAIL KREDIT</span>
          <h2 id="mkDetailModalTitle">Detail Kredit</h2>
          <p id="mkDetailModalMeta">Data sesuai filter aktif.</p>
        </div>
        <div id="mkDetailModalSummary" class="mk-modal-summary mk-modal-summary-inline"></div>
        <button type="button" id="mkDetailClose" class="mk-modal-close" aria-label="Tutup detail kredit">×</button>
        </div>
      <div class="mk-modal-head-tools">
        <label class="mk-modal-search"><span aria-hidden="true">⌕</span><input id="mkDetailSearch" type="search" placeholder="Cari rekening / nama nasabah..." autocomplete="off"></label>
        <span id="mkDetailModalBasis" class="mk-panel-badge">SALDO BANK</span>
      </div>
      </header>
      <div id="mkDetailModalBody" class="mk-modal-table-wrap"><div class="mk-state">Memuat detail...</div></div>
    </section>
  </div>

  <section id="mkTableCard" class="mk-table-card" hidden>
    <div class="mk-table-head">
      <div>
        <h2>Rekap Migrasi Kolektibilitas</h2>
        <p id="mkTableSubtitle">Nominal dalam saldo bank asli</p>
      </div>
      <div class="mk-table-actions">
        <button type="button" id="mkSummaryToggle" class="mk-summary-toggle" onclick="toggleMkSummary()" title="Buka ringkasan" aria-label="Buka ringkasan" aria-expanded="false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
        </button>
        <span id="mkLoading" class="mk-loading hidden"><i></i> Memuat data...</span>
      </div>
    </div>

    <div class="mk-table-scroll">
      <table id="mkMigrationTable" class="mk-table">
        <colgroup>
          <col class="mk-col-label"><col class="mk-col-m1">
          <col class="mk-col-actual"><col class="mk-col-actual"><col class="mk-col-actual"><col class="mk-col-actual"><col class="mk-col-actual">
          <col class="mk-col-realisasi"><col class="mk-col-run"><col class="mk-col-run"><col class="mk-col-run">
        </colgroup>
        <thead>
          <tr>
            <th rowspan="2">KOLEK M-1</th>
            <th rowspan="2">M-1</th>
            <th colspan="5">ACTUAL / MIGRASI</th>
            <th rowspan="2">REALISASI /<br>RESTRUCK</th>
          <th colspan="3">RUN OFF</th>
          </tr>
          <tr>
            <th>L</th><th>DP</th><th>KL</th><th>D</th><th>M</th>
            <th>ANGSURAN</th><th>LUNAS</th><th>TOTAL<br>RUN OFF</th>
          </tr>
        </thead>
        <tbody id="mkMigrationBody">
          <tr><td colspan="11" class="mk-empty-row">Pilih filter untuk memuat data.</td></tr>
        </tbody>
      </table>
    </div>

    <div class="mk-table-note">
      <span><b>Angsuran:</b> selisih positif antara nominal closing dan actual.</span>
      <span><b>Restruck:</b> selisih positif saat nominal actual lebih besar dari closing.</span>
    </div>
  </section>
</div>

<style>
  #migrasiKolekPage {
    --mk-surface:#fff;
    --mk-head:#eef3f8;
    --mk-border:#dbe4ef;
    --mk-text:#172033;
    --mk-muted:#64748b;
    max-width:1680px; margin:0 auto; padding:18px 20px 28px;
    color:var(--mk-text); font-family:Roboto, Arial, system-ui, sans-serif;
  }
  #migrasiKolekPage * { box-sizing:border-box; }
  .mk-report-head, .mk-table-card { background:var(--mk-surface); border:1px solid var(--mk-border); border-radius:16px; box-shadow:0 8px 24px rgba(15,23,42,.05); }
  .mk-report-head { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:15px 18px; margin-bottom:12px; }
  .mk-heading, .mk-head-actions { display:flex; align-items:center; min-width:0; }
  .mk-heading { gap:12px; }
  .mk-title-icon { display:grid; place-items:center; flex:0 0 40px; width:40px; height:40px; border-radius:11px; color:#fff; background:#2563eb; box-shadow:0 6px 14px rgba(37,99,235,.18); }
  .mk-title-icon svg { width:21px; height:21px; }
  .mk-title-copy { min-width:0; }
  .mk-title-copy h1 { margin:0; color:var(--mk-text); font-size:20px; line-height:1.2; font-weight:800; letter-spacing:-.02em; }
  .mk-title-copy p { margin:4px 0 0; color:var(--mk-muted); font-size:11px; line-height:1.35; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .mk-head-actions { gap:8px; flex:0 0 auto; }
  .mk-nominal-badge { padding:6px 9px; border:1px solid #bfdbfe; border-radius:8px; color:#1d4ed8; background:#eff6ff; font-size:9px; font-weight:800; letter-spacing:.05em; white-space:nowrap; }
  .mk-summary-toggle { display:grid; place-items:center; width:38px; height:38px; padding:0; border:1px solid #bfdbfe; border-radius:10px; color:#2563eb; background:#eff6ff; cursor:pointer; transition:background .15s ease, color .15s ease, transform .15s ease; }
  .mk-summary-toggle:hover { background:#dbeafe; }
  .mk-summary-toggle svg { width:17px; height:17px; transition:transform .18s ease; }
  .mk-summary-toggle.is-open svg { transform:rotate(180deg); }
  .mk-view-swap { display:grid; place-items:center; width:38px; height:38px; padding:0; border:1px solid #bfdbfe; border-radius:10px; color:#2563eb; background:#eff6ff; cursor:pointer; transition:background .15s ease, color .15s ease, transform .15s ease; }
  .mk-view-swap:hover { background:#dbeafe; transform:translateY(-1px); }
  .mk-view-swap svg { width:17px; height:17px; }
  .mk-export-btn { display:grid; place-items:center; width:38px; height:38px; border:0; border-radius:10px; color:#fff; background:#059669; cursor:pointer; box-shadow:0 5px 12px rgba(5,150,105,.18); transition:background .15s ease, transform .15s ease; }
  .mk-export-btn:hover { background:#047857; transform:translateY(-1px); }
  .mk-export-btn svg { width:17px; height:17px; }
  .mk-summary { display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); gap:10px; margin-bottom:12px; }
  .mk-summary-card { min-width:0; padding:11px 12px; border:1px solid var(--mk-border); border-radius:12px; background:var(--mk-surface); box-shadow:0 4px 14px rgba(15,23,42,.035); }
  .mk-summary-card span { display:block; color:var(--mk-muted); font-size:9px; font-weight:800; letter-spacing:.06em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .mk-summary-card strong { display:block; margin-top:6px; color:var(--mk-text); font-size:15px; line-height:1.1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .mk-summary-card small { display:block; margin-top:4px; color:var(--mk-muted); font-size:9px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .mk-summary[hidden] { display:none; }
  .mk-summary-growth strong { color:#2563eb; }
  .mk-cockpit { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin-bottom:12px; }
  .mk-panel { min-width:0; overflow:hidden; background:var(--mk-surface); border:1px solid var(--mk-border); border-radius:16px; box-shadow:0 6px 18px rgba(15,23,42,.045); }
  .mk-panel-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; min-width:0; padding:14px 16px 10px; }
  .mk-panel-head > div { min-width:0; }
  .mk-panel-head h2 { margin:0; color:var(--mk-text); font-size:13px; line-height:1.25; font-weight:800; }
  .mk-panel-head p { margin:4px 0 0; color:var(--mk-muted); font-size:9px; line-height:1.35; }
  .mk-panel-badge { flex:0 0 auto; padding:7px 9px; border:1px solid #bfdbfe; border-radius:8px; color:#1d4ed8; background:#eff6ff; font-size:10px; font-weight:800; white-space:nowrap; }
  .mk-panel-head-badges { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:6px; flex:0 0 auto; }
  .mk-panel-badge-rr { border-color:#bbf7d0; color:#047857; background:#f0fdf4; }
  .mk-chart { min-height:194px; padding:4px 16px 0; }
  .mk-compare-chart { display:flex; align-items:flex-end; justify-content:center; gap:16px; height:190px; padding:8px 8px 26px; border-bottom:1px solid #e5eaf0; }
  .mk-compare-group { display:flex; align-items:flex-end; justify-content:center; gap:6px; width:112px; flex:0 1 112px; height:100%; position:relative; }
  .mk-compare-group > label { position:absolute; bottom:-23px; color:var(--mk-muted); font-size:9px; font-weight:700; }
  .mk-bar { position:relative; width:27px; min-height:3px; border-radius:9px 9px 3px 3px; transition:height .3s ease; }
  .mk-bar > span { position:absolute; bottom:calc(100% + 5px); left:50%; transform:translateX(-50%); color:var(--mk-text); font-size:8px; font-weight:700; white-space:nowrap; }
  .mk-compare-group .mk-bar-prev > span { left:auto; right:calc(100% + 4px); transform:none; text-align:right; }
  .mk-compare-group .mk-bar:not(.mk-bar-prev) > span { left:calc(100% + 4px); transform:none; text-align:left; }
  .mk-bar-prev { opacity:.4; }
  .mk-bar[data-tip], .mk-movement-bar[data-tip] { cursor:help; }
  .mk-bar[data-tip]::after, .mk-movement-bar[data-tip]::after { position:absolute; z-index:20; left:50%; bottom:calc(100% + 22px); width:max-content; max-width:150px; padding:5px 7px; border:1px solid #cbd5e1; border-radius:6px; color:#f8fafc; background:#172033; box-shadow:0 5px 14px rgba(15,23,42,.18); content:attr(data-tip); font-size:8px; font-weight:600; line-height:1.25; text-align:center; white-space:normal; opacity:0; pointer-events:none; transform:translateX(-50%) translateY(3px); visibility:hidden; transition:opacity .12s ease, transform .12s ease; }
  .mk-bar[data-tip]:hover::after, .mk-bar[data-tip]:focus-visible::after, .mk-bar[data-tip].is-tip-open::after, .mk-movement-bar[data-tip]:hover::after, .mk-movement-bar[data-tip]:focus-visible::after, .mk-movement-bar[data-tip].is-tip-open::after { opacity:1; transform:translateX(-50%) translateY(0); visibility:visible; }
  .mk-bar-os { background:#60a5fa; } .mk-bar-npl { background:#059669; } .mk-bar-rr { background:#f59e0b; } .mk-bar-ckpn { background:#2563eb; }
  .mk-bar-npl > span { color:#047857; }
  .mk-bar-rr > span { color:#b45309; }
  .mk-bar-ckpn > span { color:#1d4ed8; }
  .mk-compare-legend { display:flex; justify-content:center; gap:12px; padding:4px 16px 0; color:var(--mk-muted); font-size:8px; }
  .mk-compare-legend span { display:inline-flex; align-items:center; gap:4px; }
  .mk-compare-legend i { width:8px; height:8px; border-radius:2px; }
  .mk-bar-prev-legend { opacity:.4; background:#2563eb; }
  .mk-bar-current-legend { background:#2563eb; }
  .mk-movement-chart { display:flex; align-items:flex-end; justify-content:space-around; gap:10px; height:190px; padding:8px 10px 28px; border-bottom:1px solid #e5eaf0; }
  .mk-movement-group { display:flex; align-items:flex-end; justify-content:center; width:23%; height:100%; position:relative; }
  .mk-movement-group > label { position:absolute; bottom:-24px; width:100%; overflow:hidden; color:var(--mk-muted); font-size:8px; font-weight:700; text-align:center; text-overflow:ellipsis; white-space:nowrap; }
  .mk-movement-group > label span, .mk-movement-group > label small { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .mk-movement-group > label small { margin-top:2px; color:var(--mk-muted); font-size:7px; font-weight:500; }
  .mk-movement-pair { display:flex; align-items:flex-end; justify-content:center; gap:4px; width:100%; height:100%; }
  .mk-movement-prev { opacity:.38; }
  .mk-movement-bar { position:relative; display:block; width:38px; min-height:3px; padding:0; border:0; border-radius:9px 9px 3px 3px; cursor:default; }
  button.mk-movement-bar { appearance:none; cursor:pointer; }
  .mk-movement-bar > span { position:absolute; bottom:calc(100% + 5px); left:50%; transform:translateX(-50%); color:var(--mk-text); font-size:8px; font-weight:700; white-space:nowrap; }
  .mk-movement-bar:focus-visible { outline:2px solid #2563eb; outline-offset:3px; }
  .mk-movement-realisasi { background:#6366f1; } .mk-movement-runoff { background:#fb923c; } .mk-movement-angsuran { background:#3b82f6; } .mk-movement-lunas { background:#10b981; } .mk-movement-flow { background:#e879b5; } .mk-movement-reduce { background:#10b981; }
  .mk-movement-prev-legend { opacity:.38; border:1px solid #64748b; }
  .mk-panel-foot { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:9px 16px 13px; color:var(--mk-muted); font-size:10px; }
  .mk-panel-foot strong { color:var(--mk-text); font-size:12px; }
  .mk-legend { display:flex; flex-wrap:wrap; gap:10px; padding:8px 16px 13px; color:var(--mk-muted); font-size:9px; }
  .mk-legend span { display:inline-flex; align-items:center; gap:4px; }
  .mk-legend-button { display:inline-flex; align-items:center; gap:4px; padding:0; border:0; color:inherit; background:transparent; font:inherit; cursor:pointer; }
  .mk-legend-button:hover { color:#2563eb; }
  .mk-legend i { width:8px; height:8px; border-radius:2px; }
  .mk-text-action { flex:0 0 auto; padding:3px 0; border:0; color:#2563eb; background:transparent; font-size:9px; font-weight:800; cursor:pointer; white-space:nowrap; }
  .mk-text-action:hover { color:#1d4ed8; text-decoration:underline; }
  .mk-panel-flow, .mk-panel-recovery, .mk-panel-outlook, .mk-panel-potential { min-height:220px; }
  .mk-cause-list, .mk-week-list { display:grid; gap:6px; padding:2px 16px 11px; }
  .mk-cause-row, .mk-week-row { display:flex; align-items:center; justify-content:space-between; gap:10px; min-width:0; padding:9px 10px; border:1px solid #e5eaf0; border-radius:10px; color:var(--mk-text); background:var(--mk-surface); text-align:left; cursor:pointer; transition:border-color .15s ease, background .15s ease, transform .15s ease; }
  .mk-cause-row:hover, .mk-week-row:hover { border-color:#93c5fd; background:#f8fbff; transform:translateY(-1px); }
  .mk-cause-left, .mk-week-left { display:flex; flex-direction:column; min-width:0; }
  .mk-cause-left b, .mk-week-left b { overflow:hidden; color:var(--mk-text); font-size:10px; line-height:1.2; text-overflow:ellipsis; white-space:nowrap; }
  .mk-cause-left small, .mk-week-left small { margin-top:3px; color:var(--mk-muted); font-size:8px; }
  .mk-cause-value, .mk-week-value { flex:0 0 auto; max-width:55%; overflow:hidden; color:var(--mk-text); font-size:10px; font-weight:800; text-align:right; text-overflow:ellipsis; white-space:nowrap; }
  .mk-cause-value small, .mk-week-value small { display:block; max-width:100%; margin-top:3px; overflow:hidden; color:var(--mk-muted); font-size:8px; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
  .mk-cause-row.is-danger { border-left:3px solid #ef4444; } .mk-cause-row.is-warning { border-left:3px solid #f59e0b; } .mk-cause-row.is-neutral { border-left:3px solid #94a3b8; }
  .mk-flow-visual { display:grid; grid-template-columns:170px minmax(0,1fr); align-items:center; gap:14px; padding:3px 16px 11px; }
  .mk-potential-layout { display:grid; grid-template-columns:170px minmax(0,1fr); align-items:start; gap:14px; padding:3px 16px 11px; }
  .mk-potential-donut-col { min-width:0; }
  .mk-potential-priority { min-width:0; }
  .mk-potential-priority-head { display:flex; flex-direction:column; gap:3px; min-width:0; padding:2px 0 6px; }
  .mk-potential-priority-head b { overflow:hidden; color:var(--mk-text); font-size:10px; line-height:1.2; text-overflow:ellipsis; white-space:nowrap; }
  .mk-potential-priority-head small { color:var(--mk-muted); font-size:8px; line-height:1.25; }
  .mk-potential-week-list { display:grid; gap:5px; }
  .mk-potential-week-row { display:flex; align-items:center; justify-content:space-between; gap:8px; min-width:0; padding:7px 8px; border:1px solid #e5eaf0; border-left:3px solid #60a5fa; border-radius:8px; color:var(--mk-text); background:var(--mk-surface); text-align:left; cursor:pointer; transition:border-color .15s ease, background .15s ease, transform .15s ease; }
  .mk-potential-week-row:hover { border-color:#93c5fd; background:#f8fbff; transform:translateY(-1px); }
  .mk-potential-week-row.is-overdue { border-left-color:#ef4444; }
  .mk-potential-week-copy { display:flex; flex-direction:column; min-width:0; }
  .mk-potential-week-copy b { display:flex; align-items:baseline; gap:4px; overflow:hidden; color:var(--mk-text); font-size:9px; line-height:1.2; text-overflow:ellipsis; white-space:nowrap; }
  .mk-potential-week-copy b small { color:var(--mk-muted); font-size:7px; font-weight:600; }
  .mk-potential-week-copy > small { margin-top:3px; overflow:hidden; color:var(--mk-muted); font-size:7px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-potential-week-value { flex:0 0 auto; max-width:52%; overflow:hidden; color:var(--mk-text); font-size:10px; font-weight:800; text-align:right; text-overflow:ellipsis; white-space:nowrap; }
  .mk-potential-week-value small { display:block; max-width:100%; margin-top:2px; overflow:hidden; color:var(--mk-muted); font-size:7px; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
  .mk-potential-legend { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:3px 5px; margin-top:8px; }
  .mk-potential-legend-item { display:flex; align-items:center; gap:4px; min-width:0; padding:2px 0; border:0; color:var(--mk-muted); background:transparent; text-align:left; cursor:pointer; }
  .mk-potential-legend-item:hover { color:#2563eb; }
  .mk-potential-legend-item i { width:6px; height:18px; flex:0 0 6px; border-radius:3px; background:var(--mk-flow-color); }
  .mk-potential-legend-item span { display:flex; min-width:0; flex-direction:column; overflow:hidden; font-size:7px; line-height:1.15; text-overflow:ellipsis; white-space:nowrap; }
  .mk-potential-legend-item span small { margin-top:2px; color:var(--mk-muted); font-size:6px; }
  .mk-potential-legend-item b { margin-left:auto; color:var(--mk-text); font-size:7px; white-space:nowrap; }
  .mk-flow-donut { position:relative; display:grid; place-items:center; width:150px; height:150px; margin:auto; border-radius:50%; background:var(--mk-flow-gradient,#e2e8f0); box-shadow:inset 0 0 0 1px rgba(148,163,184,.16); }
  .mk-flow-donut::after { position:absolute; inset:28px; content:""; border-radius:50%; background:var(--mk-surface); box-shadow:0 2px 8px rgba(15,23,42,.07); }
  .mk-flow-donut-center { position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; max-width:92px; text-align:center; }
  .mk-flow-donut-center strong { color:var(--mk-text); font-size:14px; line-height:1.1; }
  .mk-flow-donut-center small { margin-top:4px; color:var(--mk-muted); font-size:8px; }
  .mk-potential-donut { padding:0; border:0; color:inherit; font:inherit; cursor:pointer; }
  .mk-potential-donut:focus-visible { outline:2px solid #2563eb; outline-offset:4px; }
  .mk-flow-legend { display:grid; gap:6px; min-width:0; }
  .mk-flow-legend-row { display:flex; align-items:center; justify-content:space-between; gap:10px; min-width:0; padding:7px 8px; border:1px solid #e5eaf0; border-left:3px solid var(--mk-flow-color); border-radius:8px; color:var(--mk-text); background:var(--mk-surface); text-align:left; cursor:pointer; transition:border-color .15s ease, background .15s ease; }
  .mk-flow-legend-row:hover { border-color:#93c5fd; background:#f8fbff; }
  .mk-flow-legend-copy { display:flex; flex-direction:column; min-width:0; }
  .mk-flow-legend-copy b { overflow:hidden; font-size:9px; line-height:1.2; text-overflow:ellipsis; white-space:nowrap; }
  .mk-flow-legend-copy small { margin-top:3px; color:var(--mk-muted); font-size:8px; }
  .mk-flow-legend-value { flex:0 0 auto; color:var(--mk-text); font-size:10px; font-weight:800; text-align:right; }
  .mk-flow-legend-value small { display:block; margin-top:2px; color:var(--mk-muted); font-size:8px; font-weight:500; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-flow-legend-row { border-color:#334155; background:#142033; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-flow-legend-row:hover { background:#1a3150; }
  .mk-week-row.is-overdue { border-left:3px solid #ef4444; } .mk-week-row.is-upcoming { border-left:3px solid #60a5fa; }
  .mk-flow-total { display:flex; align-items:center; justify-content:space-between; gap:9px; margin:0 16px 14px; padding:9px 10px; border-radius:10px; background:#f8fafc; color:var(--mk-muted); font-size:9px; }
  .mk-flow-total strong { color:#dc2626; font-size:12px; } .mk-flow-total small { font-size:8px; }
  .mk-recovery-total strong { color:#059669; }
  .mk-recovery-panel-note { margin:0 16px 8px; padding:7px 9px; border:1px solid #dbeafe; border-radius:8px; color:#64748b; background:#f8fbff; font-size:8px; line-height:1.35; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-recovery-panel-note { border-color:#3656a3; color:#94a3b8; background:#172554; }
  .mk-state { padding:18px 8px; color:var(--mk-muted); font-size:10px; text-align:center; }
  .mk-outlook-grid { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:7px; padding:5px 16px 15px; }
  .mk-outlook-item { min-width:0; padding:9px 8px; border:1px solid #e5eaf0; border-radius:10px; background:#fbfdff; }
  .mk-outlook-item span { display:block; overflow:hidden; color:var(--mk-muted); font-size:8px; font-weight:800; text-overflow:ellipsis; text-transform:uppercase; white-space:nowrap; }
  .mk-outlook-item strong { display:block; margin-top:6px; overflow:hidden; color:var(--mk-text); font-size:12px; line-height:1.1; text-overflow:ellipsis; white-space:nowrap; }
  .mk-outlook-item small { display:block; margin-top:4px; overflow:hidden; color:var(--mk-muted); font-size:8px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-outlook-primary strong { color:#2563eb; } .mk-outlook-warning strong { color:#dc2626; } .mk-outlook-runoff strong { color:#059669; }
  .mk-runoff-projection { min-width:0; padding:0 16px 10px; }
  .mk-runoff-projection-head { display:flex; align-items:flex-end; justify-content:space-between; gap:8px; min-width:0; padding:2px 0 6px; }
  .mk-runoff-projection-head > div { display:flex; min-width:0; flex-direction:column; gap:3px; }
  .mk-runoff-projection-head b { overflow:hidden; color:var(--mk-text); font-size:10px; line-height:1.2; text-overflow:ellipsis; white-space:nowrap; }
  .mk-runoff-projection-head small { color:var(--mk-muted); font-size:8px; line-height:1.25; }
  .mk-runoff-projection-head > small { flex:0 0 auto; white-space:nowrap; }
  .mk-runoff-table-wrap { min-width:0; overflow:hidden; border:1px solid #e5eaf0; border-radius:9px; }
  .mk-runoff-table-head, .mk-runoff-row { display:grid; grid-template-columns:minmax(74px,1.15fr) repeat(3,minmax(62px,.9fr)) minmax(58px,.75fr); align-items:center; gap:6px; min-width:0; }
  .mk-runoff-table-head { padding:6px 8px; color:var(--mk-muted); background:#f8fafc; font-size:7px; font-weight:900; letter-spacing:.03em; }
  .mk-runoff-table-head span:not(:first-child) { text-align:right; }
  .mk-runoff-row { padding:6px 8px; border-top:1px solid #edf1f5; background:var(--mk-surface); }
  .mk-runoff-row:hover { background:#f8fbff; }
  .mk-runoff-cell { min-width:0; overflow:hidden; color:var(--mk-text); font-size:9px; text-align:right; text-overflow:ellipsis; white-space:nowrap; }
  .mk-runoff-cell:first-child { text-align:left; }
  .mk-runoff-cell strong { display:block; overflow:hidden; font-size:9px; font-weight:800; text-overflow:ellipsis; white-space:nowrap; }
  .mk-runoff-cell small { display:block; margin-top:2px; overflow:hidden; color:var(--mk-muted); font-size:6px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-runoff-cell.is-pct strong { color:#059669; }
  .mk-runoff-track { display:block; width:100%; height:4px; margin-top:4px; overflow:hidden; border-radius:99px; background:#e8eef5; }
  .mk-runoff-track i { display:block; height:100%; min-width:2px; border-radius:inherit; background:#10b981; }
  .mk-runoff-gap-strip { border-color:#dbeafe; border-left-color:#2563eb; background:#f8fbff; }
  .mk-runoff-gap-strip .mk-rr-unpaid-value strong { color:#2563eb; }
  .mk-rr-unpaid-strip { display:flex; align-items:center; gap:12px; margin:0 16px 15px; padding:9px 10px; border:1px solid #fee2e2; border-left:3px solid #ef4444; border-radius:10px; background:#fffafa; }
  .mk-rr-unpaid-strip.mk-runoff-gap-strip { border-color:#dbeafe; border-left-color:#2563eb; background:#f8fbff; }
  .mk-rr-unpaid-strip > div:first-child { min-width:0; flex:1 1 auto; }
  .mk-rr-unpaid-strip strong { display:block; color:var(--mk-text); font-size:10px; line-height:1.2; }
  .mk-rr-unpaid-strip small { display:block; margin-top:3px; overflow:hidden; color:var(--mk-muted); font-size:8px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-rr-unpaid-value { flex:0 0 auto; min-width:95px; text-align:right; }
  .mk-rr-unpaid-value strong { color:#dc2626; font-size:12px; }
  .mk-inline-action { flex:0 0 auto; padding:5px 0; border:0; color:#2563eb; background:transparent; font-size:8px; font-weight:800; cursor:pointer; white-space:nowrap; }
  .mk-inline-action:hover { text-decoration:underline; }
  .mk-panel-mob { grid-column:auto; }
  .mk-panel-chart { order:1; }
  .mk-panel-flow, .mk-panel-recovery { order:2; }
  .mk-panel-mob, .mk-panel-npl-breakdown, .mk-panel-potential { order:3; }
  .mk-panel-outlook { order:4; }
  .mk-mob-summary { display:grid; gap:6px; padding:3px 16px 15px; }
  .mk-mob-row { display:grid; grid-template-columns:58px minmax(0,1fr) 54px; gap:7px; align-items:center; min-width:0; padding:8px 9px; border:1px solid #e5eaf0; border-radius:9px; background:#fbfdff; }
  .mk-mob-row > div { min-width:0; }
  .mk-mob-label strong { display:block; overflow:hidden; color:var(--mk-text); font-size:10px; line-height:1.2; text-overflow:ellipsis; white-space:nowrap; }
  .mk-mob-label small { display:block; margin-top:3px; overflow:hidden; color:var(--mk-muted); font-size:7px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-mob-bars { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; min-width:0; }
  .mk-mob-bar-card { display:grid; grid-template-columns:auto minmax(0,1fr); grid-template-rows:auto auto auto; column-gap:6px; align-items:center; min-width:0; padding:5px 6px; border:1px solid #e5eaf0; border-radius:7px; color:var(--mk-text); background:var(--mk-surface); text-align:left; cursor:pointer; }
  .mk-mob-bar-card:hover { border-color:#93c5fd; background:#f8fbff; }
  .mk-mob-bar-card:disabled { cursor:default; opacity:.65; }
  .mk-mob-bar-label { grid-column:1 / -1; color:var(--mk-muted); font-size:7px; font-weight:800; text-transform:uppercase; }
  .mk-mob-track { display:block; grid-column:1 / -1; width:100%; height:5px; margin:4px 0 3px; overflow:hidden; border-radius:99px; background:#edf2f7; }
  .mk-mob-track i { display:block; height:100%; min-width:2px; border-radius:inherit; background:#94a3b8; }
  .mk-mob-bar-card.is-migrated .mk-mob-track i { background:#059669; }
  .mk-mob-bar-card strong { display:block; overflow:hidden; color:var(--mk-text); font-size:9px; line-height:1.1; text-overflow:ellipsis; white-space:nowrap; }
  .mk-mob-bar-card.is-migrated strong { color:#047857; }
  .mk-mob-bar-card small { display:block; overflow:hidden; color:var(--mk-muted); font-size:7px; text-align:right; text-overflow:ellipsis; white-space:nowrap; }
  .mk-mob-bar-card.is-disabled strong, .mk-mob-bar-card.is-disabled small { color:var(--mk-muted); }
  .mk-mob-rate { min-width:0; padding-left:6px; border-left:1px solid #e8eef5; text-align:right; }
  .mk-mob-rate strong { display:block; color:#b45309; font-size:10px; white-space:nowrap; }
  .mk-mob-rate small { display:block; margin-top:3px; color:var(--mk-muted); font-size:7px; }
  .mk-mob-legend { display:flex; align-items:center; flex-wrap:wrap; gap:8px; padding:0 1px 4px; color:var(--mk-muted); font-size:8px; }
  .mk-mob-legend span { display:inline-flex; align-items:center; gap:4px; }
  .mk-mob-legend i { width:7px; height:7px; border-radius:2px; background:#94a3b8; }
  .mk-mob-legend i.is-migrated { background:#059669; }
  .mk-mob-legend small { margin-left:auto; font-size:7px; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-rr-unpaid-strip { border-color:#7f1d1d; background:#2a151b; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-mob-row { border-color:#334155; background:#142033; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-mob-bar-card { border-color:#334155; background:#111827; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-mob-bar-card:hover { background:#1a3150; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-mob-rate { border-color:#263449; }
  .mk-npl-breakdown-head { align-items:center; }
  .mk-npl-head-actions { display:flex; align-items:center; justify-content:flex-end; gap:6px; flex:0 0 auto; }
  .mk-npl-head-actions .mk-panel-badge { padding:6px 7px; font-size:8px; }
  .mk-npl-dimension-label { flex:0 0 auto; }
  .mk-npl-dimension-label > .sr-only { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
  .mk-npl-dimension-label select { min-width:132px; padding:7px 25px 7px 9px; border:1px solid #bfdbfe; border-radius:8px; color:#1d4ed8; background:#eff6ff; outline:0; font:inherit; font-size:9px; font-weight:800; cursor:pointer; }
  .mk-npl-dimension-label select:focus { border-color:#60a5fa; box-shadow:0 0 0 3px rgba(96,165,250,.15); }
  .mk-npl-breakdown-body { min-height:177px; padding:2px 16px 15px; }
  .mk-npl-breakdown-layout { display:grid; grid-template-columns:172px minmax(0,1fr); gap:13px; align-items:center; min-width:0; }
  .mk-npl-semi-wrap { position:relative; display:block; width:172px; height:172px; overflow:visible; margin:0 auto; padding:0; border:0; border-radius:50%; background:transparent; cursor:pointer; }
  .mk-npl-semi-wrap:hover::before { filter:brightness(.96); }
  .mk-npl-semi-wrap::before { content:""; position:absolute; inset:0; width:172px; height:172px; border-radius:50%; background:conic-gradient(from 270deg, var(--mk-npl-gradient, #cbd5e1 0 100%)); }
  .mk-npl-semi-wrap::after { content:""; position:absolute; top:31px; left:31px; width:110px; height:110px; border-radius:50%; background:var(--mk-surface); }
  .mk-npl-semi-center { position:absolute; z-index:1; top:56px; left:0; width:100%; text-align:center; }
  .mk-npl-semi-center strong { display:block; color:var(--mk-text); font-size:14px; line-height:1.1; }
  .mk-npl-semi-center small { display:block; margin-top:4px; color:var(--mk-muted); font-size:8px; }
  .mk-npl-breakdown-total { margin-top:2px; color:var(--mk-muted); font-size:8px; text-align:center; }
  .mk-npl-breakdown-total b { color:#dc2626; font-weight:800; }
  .mk-npl-group-list { display:grid; gap:5px; max-height:none; overflow:visible; padding-right:2px; }
  .mk-npl-group { display:grid; grid-template-columns:7px minmax(0,1fr) auto; gap:7px; align-items:center; width:100%; min-width:0; padding:6px 7px; border:1px solid #e5eaf0; border-radius:8px; color:inherit; background:#fbfdff; font:inherit; text-align:left; cursor:pointer; }
  .mk-npl-group:hover { border-color:#93c5fd; background:#f8fbff; }
  .mk-npl-group i { width:7px; height:24px; border-radius:99px; background:var(--mk-npl-color,#94a3b8); }
  .mk-npl-group-copy { min-width:0; }
  .mk-npl-group-copy strong { display:block; overflow:hidden; color:var(--mk-text); font-size:9px; font-weight:700; text-overflow:ellipsis; white-space:nowrap; }
  .mk-npl-group-copy small { display:block; margin-top:3px; overflow:hidden; color:var(--mk-muted); font-size:7px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-npl-group-value { min-width:72px; color:#dc2626; font-size:10px; font-weight:800; text-align:right; white-space:nowrap; }
  .mk-npl-group-value small { display:block; margin-top:2px; color:var(--mk-muted); font-size:7px; font-weight:500; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-npl-dimension-label select { border-color:#3656a3; color:#bfdbfe; background:#172554; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-npl-group { border-color:#334155; background:#142033; }
  .mk-panel-projection { grid-column:1 / -1; order:5; }
  .mk-projection-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; padding:4px 16px 16px; }
  .mk-projection-section { min-width:0; padding:11px; border:1px solid #e5eaf0; border-radius:12px; background:#fbfdff; }
  .mk-projection-section-head { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
  .mk-projection-section-head h3 { margin:0; color:var(--mk-text); font-size:11px; line-height:1.25; }
  .mk-projection-section-head small { color:var(--mk-muted); font-size:8px; text-align:right; }
  .mk-payment-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:7px; margin-top:9px; }
  .mk-payment-stat { min-width:0; padding:8px; border:1px solid #e5eaf0; border-radius:9px; background:var(--mk-surface); }
  button.mk-payment-stat { width:100%; border:1px solid #e5eaf0; color:inherit; text-align:left; cursor:pointer; font:inherit; }
  button.mk-payment-stat:hover { border-color:#93c5fd; background:#f8fbff; }
  .mk-payment-stat span { display:block; overflow:hidden; color:var(--mk-muted); font-size:8px; font-weight:800; text-overflow:ellipsis; white-space:nowrap; }
  .mk-payment-stat strong { display:block; margin-top:5px; overflow:hidden; color:var(--mk-text); font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-payment-stat small { display:block; margin-top:3px; overflow:hidden; color:var(--mk-muted); font-size:8px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-payment-stat.is-paid strong { color:#059669; } .mk-payment-stat.is-unpaid strong { color:#dc2626; } .mk-payment-stat.is-projection strong { color:#2563eb; }
  .mk-projection-subhead { display:flex; align-items:center; justify-content:space-between; gap:8px; margin:11px 0 6px; color:var(--mk-text); font-size:9px; font-weight:800; }
  .mk-projection-subhead small { color:var(--mk-muted); font-size:8px; font-weight:500; white-space:nowrap; }
  .mk-projection-list { max-height:222px; overflow:auto; scrollbar-width:thin; scrollbar-color:#94a3b8 transparent; }
  .mk-projection-list::-webkit-scrollbar { width:4px; height:4px; } .mk-projection-list::-webkit-scrollbar-thumb { border-radius:999px; background:#94a3b8; }
  .mk-projection-row { display:grid; grid-template-columns:minmax(0,1.2fr) minmax(90px,.75fr) minmax(90px,.7fr); gap:8px; align-items:center; padding:7px 8px; border-top:1px solid #e8eef5; color:var(--mk-text); font-size:9px; }
  .mk-projection-row:first-child { border-top:0; }
  .mk-projection-row.is-head { color:var(--mk-muted); font-size:8px; font-weight:800; text-transform:uppercase; }
  .mk-projection-row.is-head span:last-child { text-align:right; }
  .mk-projection-row strong { display:block; overflow:hidden; font-size:9px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-projection-row small { display:block; margin-top:2px; overflow:hidden; color:var(--mk-muted); font-size:8px; text-overflow:ellipsis; white-space:nowrap; }
  .mk-projection-row .num { text-align:right; white-space:nowrap; } .mk-projection-row .num strong { color:#2563eb; }
  .mk-projection-detail-action { margin-top:10px; }
  .mk-projection-detail-action button { width:100%; padding:8px 10px; border:1px dashed #bfdbfe; border-radius:8px; color:#2563eb; background:#f8fbff; font-size:9px; font-weight:800; text-align:center; cursor:pointer; }
  .mk-projection-detail-action button:hover { border-style:solid; background:#eff6ff; }
  .mk-consistent-table { margin-top:9px; overflow:auto; }
  .mk-consistent-table table { width:100%; min-width:420px; border-collapse:collapse; color:var(--mk-text); font-size:8px; }
  .mk-consistent-table th { padding:6px 7px; border-bottom:1px solid var(--mk-border); color:var(--mk-muted); font-size:8px; text-align:right; white-space:nowrap; }
  .mk-consistent-table th:first-child, .mk-consistent-table td:first-child { text-align:left; }
  .mk-consistent-table td { padding:7px; border-bottom:1px solid #e8eef5; text-align:right; white-space:nowrap; }
  .mk-consistent-table td strong { color:#059669; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-projection-section,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-payment-stat { border-color:#334155; background:#142033; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage button.mk-payment-stat:hover,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-projection-detail-action button { border-color:#3656a3; background:#172554; color:#bfdbfe; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-projection-row,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-consistent-table td { border-color:#263449; }
  .mk-table-card { overflow:hidden; }
  .mk-table-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:15px 17px; border-bottom:1px solid #e8eef5; }
  .mk-table-actions { display:flex; align-items:center; justify-content:flex-end; gap:9px; flex:0 0 auto; }
  .mk-table-head h2 { margin:0; color:var(--mk-text); font-size:14px; font-weight:800; }
  .mk-table-head p { margin:4px 0 0; color:var(--mk-muted); font-size:10px; }
  .mk-loading { align-items:center; gap:6px; color:#2563eb; font-size:10px; font-weight:700; }
  .mk-loading:not(.hidden) { display:inline-flex; }
  .mk-loading i { width:12px; height:12px; border:2px solid #bfdbfe; border-top-color:#2563eb; border-radius:999px; animation:mk-spin .8s linear infinite; }
  @keyframes mk-spin { to { transform:rotate(360deg); } }
  .mk-table-scroll { width:100%; overflow-x:auto; scrollbar-width:thin; scrollbar-color:#94a3b8 transparent; }
  .mk-table-scroll::-webkit-scrollbar { width:5px; height:5px; }
  .mk-table-scroll::-webkit-scrollbar-thumb { background:#94a3b8; border-radius:999px; }
  .mk-table { width:100%; min-width:0; table-layout:fixed; border-collapse:separate; border-spacing:0; color:var(--mk-text); }
  .mk-table th { height:40px; padding:7px 8px; border-right:1px solid var(--mk-border); border-bottom:1px solid var(--mk-border); background:var(--mk-head); color:#475569; font-size:9px; font-weight:800; line-height:1.2; letter-spacing:.025em; text-align:center; white-space:nowrap; }
  .mk-table th:last-child { border-right:0; }
  .mk-table td { height:64px; padding:8px 9px; border-right:1px solid #e8eef5; border-bottom:1px solid #e8eef5; background:var(--mk-surface); vertical-align:middle; text-align:right; }
  .mk-table td:first-child { text-align:left; }
  .mk-table td:last-child { border-right:0; }
  .mk-table tbody tr:nth-child(even) td { background:#fbfdff; }
  .mk-table tbody tr:hover td { background:#f1f7ff; }
  .mk-table tbody tr.mk-total-row td { background:#edf4ff; border-bottom-color:#c7daf7; font-weight:700; }
  .mk-table tbody tr.mk-special-row td { background:#f8fafc; }
  .mk-col-label { width:11.5%; } .mk-col-m1 { width:8.5%; } .mk-col-actual { width:8.3%; } .mk-col-realisasi { width:8.5%; } .mk-col-run { width:10%; }
  .mk-row-label { color:var(--mk-text); font-size:11px; font-weight:700; white-space:nowrap; }
  .mk-row-label small { display:block; margin-top:4px; color:var(--mk-muted); font-size:8px; font-weight:500; }
  .mk-metric strong { display:block; color:var(--mk-text); font-size:11px; font-weight:500; line-height:1.15; white-space:nowrap; }
  .mk-metric small { display:block; margin-top:4px; color:var(--mk-muted); font-size:8px; line-height:1.2; white-space:nowrap; }
  .mk-metric small b { color:#2563eb; font-weight:500; }
  .mk-metric.is-empty { color:#94a3b8; text-align:center; }
  .mk-metric.is-empty strong { color:#94a3b8; font-size:12px; font-weight:500; }
  .mk-realisasi strong { color:#2563eb; } .mk-restruktur strong { color:#b45309; } .mk-angsuran strong { color:#059669; } .mk-pelunasan strong { color:#475569; } .mk-runoff strong { color:#2563eb; }
  .mk-empty-row { height:170px !important; color:var(--mk-muted); text-align:center !important; font-size:11px; }
  .mk-table-note { display:flex; flex-wrap:wrap; gap:14px; padding:11px 17px; color:var(--mk-muted); font-size:10px; line-height:1.35; }
  .mk-table-note b { color:var(--mk-text); }
  .mk-modal[hidden] { display:none; }
  .mk-modal { position:fixed; inset:0; z-index:1200; display:grid; place-items:center; padding:14px; font-family:Roboto, Arial, system-ui, sans-serif; }
  .mk-modal-backdrop { position:absolute; inset:0; background:rgba(15,23,42,.58); backdrop-filter:blur(2px); }
  .mk-modal-card { position:relative; z-index:1; display:flex; flex-direction:column; width:min(1120px,100%); max-height:min(88dvh,780px); overflow:hidden; border:1px solid var(--mk-border); border-radius:18px; background:var(--mk-surface); box-shadow:0 24px 70px rgba(15,23,42,.28); }
  .mk-modal-head { display:block; padding:17px 18px 12px; border-bottom:1px solid var(--mk-border); }
  .mk-modal-head-main { display:grid; grid-template-columns:minmax(220px,1fr) auto auto; align-items:start; gap:12px; }
  .mk-modal-head-tools { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:10px; padding-top:9px; border-top:1px solid #eef2f7; }
  .mk-modal-head-tools .mk-panel-badge { padding:4px 7px; font-size:8px; }
  .mk-modal-title-wrap { min-width:0; }
  .mk-modal-kicker { display:block; color:#2563eb; font-size:9px; font-weight:800; letter-spacing:.08em; }
  .mk-modal-head h2 { margin:4px 0 0; color:var(--mk-text); font-size:18px; line-height:1.2; }
  .mk-modal-head p { margin:4px 0 0; color:var(--mk-muted); font-size:10px; }
  .mk-modal-close { display:grid; place-items:center; flex:0 0 auto; width:32px; height:32px; border:1px solid var(--mk-border); border-radius:9px; color:var(--mk-muted); background:transparent; font-size:20px; line-height:1; cursor:pointer; }
  .mk-modal-close:hover { border-color:#93c5fd; color:#2563eb; background:#eff6ff; }
  .mk-modal-toolbar { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 18px; }
  .mk-modal-search { display:flex; align-items:center; gap:7px; width:min(360px,100%); min-height:34px; padding:0 10px; border:1px solid var(--mk-border); border-radius:9px; color:#94a3b8; background:var(--mk-surface); }
  .mk-modal-search input { width:100%; min-width:0; border:0; outline:0; color:var(--mk-text); background:transparent; font:inherit; font-size:11px; }
  .mk-modal-summary { display:flex; flex-wrap:wrap; gap:7px; padding:0 18px 11px; }
  .mk-modal-summary-inline { align-self:center; justify-content:flex-end; max-width:620px; padding:0; }
  .mk-modal-stat { min-width:120px; padding:7px 9px; border:1px solid var(--mk-border); border-radius:9px; background:#f8fafc; }
  .mk-modal-stat span { display:block; color:var(--mk-muted); font-size:8px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; }
  .mk-modal-stat strong { display:block; margin-top:3px; color:var(--mk-text); font-size:12px; }
  .mk-modal-table-wrap { min-height:160px; overflow:auto; border-top:1px solid var(--mk-border); scrollbar-width:thin; scrollbar-color:#94a3b8 transparent; }
  .mk-modal-table-wrap::-webkit-scrollbar { width:5px; height:5px; }
  .mk-modal-table-wrap::-webkit-scrollbar-thumb { border-radius:999px; background:#94a3b8; }
  .mk-recovery-table { width:100%; min-width:900px; border-collapse:separate; border-spacing:0; color:var(--mk-text); }
  .mk-recovery-table th { position:sticky; top:0; z-index:1; padding:9px 10px; border-right:1px solid var(--mk-border); border-bottom:1px solid var(--mk-border); background:var(--mk-head); color:#475569; font-size:8px; font-weight:800; text-align:left; white-space:nowrap; }
  .mk-recovery-table th.num, .mk-recovery-table td.num { text-align:right; }
  .mk-recovery-table td { max-width:220px; padding:9px 10px; border-right:1px solid #e8eef5; border-bottom:1px solid #e8eef5; background:var(--mk-surface); font-size:9px; vertical-align:middle; }
  .mk-recovery-table tbody tr:nth-child(even) td { background:#fbfdff; }
  .mk-recovery-table tbody tr:hover td { background:#f1f7ff; }
  .mk-recovery-name { display:block; overflow:hidden; font-weight:700; text-overflow:ellipsis; white-space:nowrap; }
  .mk-recovery-sub { display:block; margin-top:3px; color:var(--mk-muted); font-size:8px; }
  .mk-recovery-type { display:inline-flex; padding:4px 6px; border:1px solid #bfdbfe; border-radius:6px; color:#1d4ed8; background:#eff6ff; font-size:8px; font-weight:800; white-space:nowrap; }
  .mk-recovery-amount { color:#059669; font-weight:800; white-space:nowrap; }
  .mk-modal-empty { padding:34px 12px; color:var(--mk-muted); font-size:11px; text-align:center; }
  .mk-migration-metric { display:block; width:100%; min-width:0; padding:0; border:0; color:inherit; background:transparent; font:inherit; text-align:inherit; cursor:pointer; }
  .mk-migration-metric:hover .mk-metric strong { color:#2563eb; text-decoration:underline; }
  .mk-migration-metric:focus-visible { outline:2px solid #2563eb; outline-offset:-2px; border-radius:4px; }
  body.mk-modal-open { overflow:hidden; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage { --mk-surface:#111827; --mk-head:#1c2a3d; --mk-border:#334155; --mk-text:#e5e7eb; --mk-muted:#94a3b8; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-table td { border-color:#263449; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-table tbody tr:nth-child(even) td { background:#142033; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-table tbody tr:hover td { background:#1a3150; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-table tbody tr.mk-total-row td { background:#172b4b; border-color:#334d78; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-table tbody tr.mk-special-row td { background:#172236; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-nominal-badge { background:#172554; border-color:#3656a3; color:#bfdbfe; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-summary-toggle { background:#172554; border-color:#3656a3; color:#bfdbfe; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-summary-toggle:hover { background:#1e3a8a; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-view-swap { background:#172554; border-color:#3656a3; color:#bfdbfe; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-view-swap:hover { background:#1e3a8a; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-panel-badge { background:#172554; border-color:#3656a3; color:#bfdbfe; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-panel-badge-rr { background:#052e1b; border-color:#166534; color:#86efac; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-cause-row,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-week-row,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-outlook-item { border-color:#334155; background:#142033; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-cause-row:hover,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-week-row:hover { background:#1a3150; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-flow-total { background:#172236; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-compare-chart,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-movement-chart { border-color:#334155; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-modal-stat,
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-recovery-table tbody tr:nth-child(even) td { background:#142033; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-recovery-table td { border-color:#263449; }
  :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-recovery-type { border-color:#3656a3; color:#bfdbfe; background:#172554; }
  @media (max-width:1100px) { #migrasiKolekPage { padding:13px 12px 22px; } .mk-summary { grid-template-columns:repeat(4,minmax(0,1fr)); } }
  @media (max-width:1100px) { .mk-outlook-grid { grid-template-columns:repeat(3,minmax(0,1fr)); } }
  @media (max-width:700px) {
    #migrasiKolekPage { padding:10px 8px 18px; }
    .mk-report-head { align-items:flex-start; padding:12px; border-radius:13px; }
    .mk-title-icon { width:34px; height:34px; flex-basis:34px; border-radius:9px; } .mk-title-icon svg { width:18px; height:18px; }
    .mk-title-copy h1 { font-size:15px; } .mk-title-copy p { max-width:210px; font-size:9px; } .mk-nominal-badge { display:none; } .mk-summary-toggle, .mk-view-swap, .mk-export-btn { width:34px; height:34px; }
    .mk-summary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:7px; } .mk-summary-card { padding:9px 10px; } .mk-summary-card strong { font-size:12px; }
    .mk-cockpit { grid-template-columns:1fr; gap:9px; }
    .mk-panel { border-radius:13px; }
    .mk-panel-head { padding:11px 12px 8px; }
    .mk-panel-head h2 { font-size:11px; } .mk-panel-head p { font-size:8px; }
    .mk-panel-head-badges { gap:4px; } .mk-panel-badge { padding:6px 7px; font-size:8px; }
    .mk-chart { min-height:165px; padding:3px 10px 0; }
    .mk-compare-chart, .mk-movement-chart { height:162px; padding-left:2px; padding-right:2px; }
    .mk-compare-chart { gap:4px; padding-left:0; padding-right:0; }
    .mk-compare-group { width:25%; flex:1 1 0; gap:3px; }
    .mk-bar { width:19px; } .mk-movement-bar { width:29px; }
    .mk-bar > span, .mk-movement-bar > span { font-size:6px; }
    .mk-compare-group > label, .mk-movement-group > label { font-size:8px; }
    .mk-panel-foot, .mk-legend, .mk-compare-legend { padding-left:12px; padding-right:12px; }
    .mk-cause-list, .mk-week-list { padding-left:12px; padding-right:12px; }
    .mk-flow-visual { grid-template-columns:1fr; gap:10px; padding-left:12px; padding-right:12px; }
    .mk-potential-layout { grid-template-columns:1fr; gap:10px; padding-left:12px; padding-right:12px; }
    .mk-potential-priority-head b { font-size:9px; }
    .mk-potential-week-row { padding:7px; }
    .mk-flow-donut { width:132px; height:132px; }
    .mk-flow-donut::after { inset:25px; }
    .mk-flow-donut-center strong { font-size:12px; }
    .mk-cause-left b, .mk-week-left b { font-size:9px; } .mk-cause-value, .mk-week-value { font-size:9px; }
    .mk-flow-total { margin-left:12px; margin-right:12px; }
    .mk-outlook-grid { grid-template-columns:repeat(2,minmax(0,1fr)); padding-left:12px; padding-right:12px; }
    .mk-runoff-projection { padding-left:12px; padding-right:12px; }
    .mk-runoff-table-head, .mk-runoff-row { grid-template-columns:minmax(62px,1.05fr) repeat(3,minmax(55px,.9fr)) minmax(50px,.75fr); gap:4px; padding-left:6px; padding-right:6px; }
    .mk-runoff-table-head { font-size:6px; }
    .mk-runoff-cell, .mk-runoff-cell strong { font-size:8px; }
    .mk-runoff-cell small { font-size:5.5px; }
    .mk-rr-unpaid-strip { align-items:flex-start; flex-wrap:wrap; gap:7px; margin-left:12px; margin-right:12px; }
    .mk-rr-unpaid-strip > div:first-child { flex-basis:calc(100% - 1px); }
    .mk-rr-unpaid-value { margin-left:auto; text-align:right; }
    .mk-mob-summary { overflow:visible; padding-left:12px; padding-right:12px; }
    .mk-mob-head { display:none; }
    .mk-mob-row { grid-template-columns:52px minmax(0,1fr) 48px; gap:6px; min-width:0; padding:8px 8px; }
    .mk-mob-label { grid-column:auto; padding-bottom:0; border-bottom:0; }
    .mk-mob-bars { grid-template-columns:1fr; gap:4px; }
    .mk-mob-bar-card { padding:5px; }
    .mk-mob-rate { padding-left:5px; }
    .mk-mob-legend small { flex-basis:100%; margin-left:0; }
    .mk-npl-breakdown-head { align-items:flex-start; }
    .mk-npl-head-actions { gap:4px; }
    .mk-npl-head-actions .mk-panel-badge { padding:5px 6px; font-size:7px; }
    .mk-npl-dimension-label select { min-width:112px; padding:6px 21px 6px 7px; font-size:8px; }
    .mk-npl-breakdown-body { padding-left:12px; padding-right:12px; }
    .mk-npl-breakdown-layout { grid-template-columns:128px minmax(0,1fr); gap:8px; }
    .mk-npl-semi-wrap { width:128px; height:128px; }
    .mk-npl-semi-wrap::before { width:128px; height:128px; }
    .mk-npl-semi-wrap::after { top:23px; left:23px; width:82px; height:82px; }
    .mk-npl-semi-center { top:43px; }
    .mk-npl-semi-center strong { font-size:11px; }
    .mk-npl-semi-center small { margin-top:3px; font-size:7px; }
    .mk-npl-breakdown-total { font-size:7px; }
    .mk-npl-group-list { max-height:none; overflow:visible; }
    .mk-npl-group { grid-template-columns:5px minmax(0,1fr) auto; gap:5px; padding:5px; }
    .mk-npl-group i { width:5px; height:20px; }
    .mk-npl-group-copy strong { font-size:8px; }
    .mk-npl-group-copy small { font-size:6px; }
    .mk-npl-group-value { min-width:58px; font-size:8px; }
    .mk-npl-group-value small { font-size:6px; }
    .mk-mob-cell::before { display:block; margin-bottom:2px; color:var(--mk-muted); font-size:7px; font-weight:800; text-transform:uppercase; }
    .mk-mob-cell:nth-child(2)::before { content:'Belum migrasi · DPD 0'; }
    .mk-mob-cell:nth-child(3)::before { content:'Sudah migrasi · DPD > 0'; }
    .mk-mob-cell:nth-child(4)::before { content:'Total nominal'; }
    .mk-mob-cell:nth-child(5)::before { content:'Persentase migrasi'; }
    .mk-mob-cell.is-rate { text-align:left; }
    :root[data-monbis-theme="dark"] #migrasiKolekPage .mk-mob-label { border-color:#263449; }
    .mk-projection-grid { grid-template-columns:1fr; gap:9px; padding-left:12px; padding-right:12px; }
    .mk-projection-section { padding:9px; }
    .mk-projection-section-head { flex-direction:column; gap:3px; } .mk-projection-section-head small { text-align:left; }
    .mk-payment-stats { gap:5px; } .mk-payment-stat { padding:7px; } .mk-payment-stat strong { font-size:10px; }
    .mk-table-head { align-items:flex-start; padding:12px; } .mk-table-head h2 { font-size:12px; } .mk-table-head p { font-size:9px; } .mk-table-note { padding:10px 12px; font-size:9px; }
    .mk-table-actions { gap:6px; } .mk-table-actions .mk-loading { font-size:8px; } .mk-table-actions .mk-loading i { width:10px; height:10px; }
    .mk-table { min-width:920px; }
    .mk-modal { padding:8px; }
    .mk-modal-card { max-height:calc(100dvh - 16px); border-radius:14px; }
    .mk-modal-head { padding:13px 12px 10px; } .mk-modal-head h2 { font-size:15px; } .mk-modal-head p { font-size:9px; }
    .mk-modal-head-main { grid-template-columns:minmax(0,1fr) auto; gap:9px; }
    .mk-modal-head-main .mk-modal-title-wrap { grid-column:1; grid-row:1; }
    .mk-modal-head-main .mk-modal-summary-inline { grid-column:1 / -1; grid-row:2; justify-content:flex-start; max-width:none; padding:0; }
    .mk-modal-head-main .mk-modal-close { grid-column:2; grid-row:1; }
    .mk-modal-head-tools { margin-top:8px; padding-top:8px; }
    .mk-modal-head-tools .mk-modal-search { width:100%; min-height:32px; }
    .mk-modal-head-tools .mk-panel-badge { flex:0 0 auto; padding:4px 6px; font-size:7px; }
    .mk-modal-summary-inline .mk-modal-stat { min-width:105px; padding:6px 8px; }
    .mk-modal-toolbar { align-items:stretch; flex-direction:column; padding:9px 12px; }
    .mk-modal-search { width:100%; } .mk-modal-toolbar .mk-panel-badge { align-self:flex-start; }
    .mk-modal-summary { padding:0 12px 9px; }
    .mk-modal-stat { flex:1 1 105px; min-width:0; } .mk-modal-stat strong { font-size:11px; }
  }
</style>

<script>
  const mkNf = new Intl.NumberFormat('id-ID');
  const mkNum = value => Number(value || 0);
  const mkFmt = value => mkNf.format(Math.round(mkNum(value)));
  const mkPct = value => `${mkNum(value).toFixed(2)}%`;
  const mkApi = (url, options={}) => window.apiFetch ? window.apiFetch(url, options) : fetch(url, options);
  let mkAbort = null;
  let mkLastData = null;
  let mkCkpnData = {};
  let mkLastRecovery = null;
  const mkEl = id => document.getElementById(id);
  const mkEscape = value => String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
  const mkIsEmpty = value => !value || (mkNum(value.os) === 0 && mkNum(value.noa) === 0);

  function toggleMkSummary() {
    const summary = mkEl('mkSummary');
    const button = mkEl('mkSummaryToggle');
    if (!summary || !button) return;
    const willOpen = summary.hidden;
    summary.hidden = !willOpen;
    button.classList.toggle('is-open', willOpen);
    button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    button.setAttribute('aria-label', willOpen ? 'Tutup ringkasan' : 'Buka ringkasan');
    button.title = willOpen ? 'Tutup ringkasan' : 'Buka ringkasan';
  }

  let mkReportView = 'cockpit';
  function toggleMkReportView() {
    const cockpit = mkEl('mkCockpit');
    const table = mkEl('mkTableCard');
    const button = mkEl('mkViewSwap');
    if (!cockpit || !table) return;
    const showTable = mkReportView !== 'table';
    cockpit.hidden = showTable;
    table.hidden = !showTable;
    mkReportView = showTable ? 'table' : 'cockpit';
    if (!showTable) {
      const summary = mkEl('mkSummary');
      const summaryButton = mkEl('mkSummaryToggle');
      if (summary) summary.hidden = true;
      if (summaryButton) {
        summaryButton.classList.remove('is-open');
        summaryButton.setAttribute('aria-expanded', 'false');
        summaryButton.setAttribute('aria-label', 'Buka ringkasan');
        summaryButton.title = 'Buka ringkasan';
      }
    }
    if (button) {
      const nextLabel = showTable ? 'Tampilkan Dashboard Kredit' : 'Tampilkan Rekap Migrasi Kolektibilitas';
      button.title = nextLabel;
      button.setAttribute('aria-label', nextLabel);
      button.setAttribute('aria-pressed', showTable ? 'true' : 'false');
    }
  }

  function mkMetric(value, extraClass='', showPct=true) {
    if (mkIsEmpty(value)) return '<div class="mk-metric is-empty"><strong>-</strong></div>';
    const pct = showPct && value.pct !== undefined ? ` <b>• ${mkPct(value.pct)}</b>` : '';
    return `<div class="mk-metric ${extraClass}"><strong>${mkFmt(value.os)}</strong><small>NOA: ${mkFmt(value.noa)}${pct}</small></div>`;
  }
  function mkCombined(a, b) { return {os:mkNum(a?.os)+mkNum(b?.os), noa:mkNum(a?.noa)+mkNum(b?.noa)}; }
  function mkRowLabel(label, note='') { return `<div class="mk-row-label">${mkEscape(label)}${note ? `<small>${mkEscape(note)}</small>` : ''}</div>`; }

  function renderMigrasiSummary(totals) {
    const set=(id,value)=>{const el=mkEl(id);if(el)el.textContent=value;};
    const actualNoa=['L','DP','KL','D','M'].reduce((sum,key)=>sum+mkNum(totals.actual?.[key]?.noa),0);
    const realisasi=totals.realisasi_baru||totals.realisasi||{}; const restruck=totals.restruck||{}; const angsuran=totals.angsuran||{}; const pelunasan=totals.pelunasan||{};
    set('mkM1Os',mkFmt(totals.m1_os)); set('mkM1Noa',`NOA: ${mkFmt(totals.m1_noa)}`);
    set('mkActOs',mkFmt(totals.actual_total)); set('mkActNoa',`NOA: ${mkFmt(actualNoa)}`);
    set('mkRealisasiOs',mkFmt(realisasi.os)); set('mkRealisasiNoa',`NOA: ${mkFmt(realisasi.noa)}`);
    set('mkRestruckOs',mkFmt(restruck.os)); set('mkRestruckNoa',`NOA: ${mkFmt(restruck.noa)}`);
    set('mkAngsuranOs',mkFmt(angsuran.os)); set('mkAngsuranNoa',`NOA: ${mkFmt(angsuran.noa)}`);
    set('mkPelunasanOs',mkFmt(pelunasan.os)); set('mkPelunasanNoa',`NOA: ${mkFmt(pelunasan.noa)}`);
    const growth=mkNum(totals.growth); const growthEl=mkEl('mkGrowth');
    if(growthEl){growthEl.textContent=`${growth>=0?'+':'-'}${mkFmt(Math.abs(growth))}`;growthEl.style.color=growth>=0?'#dc2626':'#059669';}
    set('mkNplDelta',`Δ NPL: ${totals.npl_delta_pct>=0?'+':''}${mkPct(totals.npl_delta_pct)}`);
    renderMkCharts(totals);
    renderMkOutlookFromMigration(totals);
    setTimeout(() => loadMkCockpit(getMigrasiKolekFilter()), 0);
  }

  function mkFmtShort(value) {
    const n = mkNum(value), abs = Math.abs(n), sign = n < 0 ? '-' : '';
    if (abs >= 1e12) return `${sign}${(abs / 1e12).toFixed(2)} T`;
    if (abs >= 1e9) return `${sign}${(abs / 1e9).toFixed(2)} M`;
    if (abs >= 1e6) return `${sign}${(abs / 1e6).toFixed(2)} Jt`;
    if (abs >= 1e3) return `${sign}${(abs / 1e3).toFixed(1)} Rb`;
    return mkFmt(n);
  }
  function renderMkCkpnSummary(data = {}) {
    mkCkpnData = data || {};
    const closing = mkNum(data.ckpn_closing);
    const actual = mkNum(data.ckpn_actual);
    if ((data.ckpn_closing !== undefined || data.ckpn_actual !== undefined) && mkLastData?.totals) renderMkCharts(mkLastData.totals, mkLastRecovery);
  }
  function mkBarHeight(value, max) { return `${Math.max(3, Math.min(100, max > 0 ? (mkNum(value) / max) * 100 : 3))}%`; }
  function mkCompareTip(label, period, value, baseline, ratioBase=0, ratioLabel=label) {
    const amount = mkNum(value);
    const ratio = mkNum(ratioBase) > 0 ? ` • ${ratioLabel} ${mkPct(amount / mkNum(ratioBase) * 100)}` : '';
    if (period === 'Closing') return `${label} Closing: ${mkFmt(amount)}${ratio} • Baseline`;
    const base = mkNum(baseline), delta = amount - base;
    if (base === 0) return `${label} Actual: ${mkFmt(amount)}${ratio} • Tidak ada baseline Closing`;
    const direction = delta > 0 ? 'Naik' : delta < 0 ? 'Turun' : 'Tetap';
    const percentage = Math.abs(delta / base * 100);
    return `${label} Actual: ${mkFmt(amount)}${ratio} • ${direction} ${percentage.toFixed(2)}% (${delta > 0 ? '+' : delta < 0 ? '-' : ''}${mkFmt(Math.abs(delta))}) vs Closing`;
  }
  function mkBarValue(value, extraClass, max, tooltip='') {
    const tip = tooltip ? ` data-tip="${mkEscape(tooltip)}" tabindex="0" role="img" aria-label="${mkEscape(tooltip)}"` : '';
    return `<div class="${extraClass}" style="height:${mkBarHeight(value, max)}"${tip}><span>${mkEscape(mkFmtShort(value))}</span></div>`;
  }
  function mkInteractiveBarValue(value, extraClass, max, action, title) { return `<button type="button" class="${extraClass}" style="height:${mkBarHeight(value, max)}" onclick="${action}" title="${mkEscape(title)}" aria-label="${mkEscape(title)}"><span>${mkEscape(mkFmtShort(value))}</span></button>`; }

  function renderMkCharts(totals = {}, recovery = null) {
    const osc = mkEl('mkOscChart'), movementChart = mkEl('mkMovementChart');
    if (!osc || !movementChart) return;
    const m1Os = mkNum(totals.m1_os), actualOs = mkNum(totals.actual_total), m1Npl = mkNum(totals.npl_prev), actualNpl = mkNum(totals.npl_now), m1Rr = mkNum(totals.rr_prev), actualRr = mkNum(totals.rr_actual);
    const ckpnClosing = mkNum(mkCkpnData.ckpn_closing), ckpnActual = mkNum(mkCkpnData.ckpn_actual);
    const compareMax = Math.max(m1Os, actualOs, m1Npl, actualNpl, m1Rr, actualRr, ckpnClosing, ckpnActual, 1);
    osc.innerHTML = `<div class="mk-compare-chart">
      <div class="mk-compare-group">${mkBarValue(m1Os,'mk-bar mk-bar-os mk-bar-prev',compareMax,mkCompareTip('OSC','Closing',m1Os,m1Os))}${mkBarValue(actualOs,'mk-bar mk-bar-os',compareMax,mkCompareTip('OSC','Actual',actualOs,m1Os))}<label>OSC</label></div>
      <div class="mk-compare-group">${mkBarValue(m1Npl,'mk-bar mk-bar-npl mk-bar-prev',compareMax,mkCompareTip('NPL','Closing',m1Npl,m1Npl,m1Os,'Rasio NPL'))}${mkBarValue(actualNpl,'mk-bar mk-bar-npl',compareMax,mkCompareTip('NPL','Actual',actualNpl,m1Npl,actualOs,'Rasio NPL'))}<label>NPL</label></div>
      <div class="mk-compare-group">${mkBarValue(m1Rr,'mk-bar mk-bar-rr mk-bar-prev',compareMax,mkCompareTip('RR','Closing',m1Rr,m1Rr,m1Os,'Rasio RR'))}${mkBarValue(actualRr,'mk-bar mk-bar-rr',compareMax,mkCompareTip('RR','Actual',actualRr,m1Rr,actualOs,'Rasio RR'))}<label>RR</label></div>
      <div class="mk-compare-group">${mkBarValue(ckpnClosing,'mk-bar mk-bar-ckpn mk-bar-prev',compareMax,mkCompareTip('CKPN','Closing',ckpnClosing,ckpnClosing))}${mkBarValue(ckpnActual,'mk-bar mk-bar-ckpn',compareMax,mkCompareTip('CKPN','Actual',ckpnActual,ckpnClosing))}<label>CKPN</label></div>
    </div>`;
    const trend = mkEl('mkNplTrend'); if (trend) { trend.textContent = `NPL ${mkPct(totals.npl_now_pct)}`; trend.style.color = mkNum(totals.npl_delta_pct) > 0 ? '#dc2626' : '#059669'; }
    const rrTrend = mkEl('mkRrTrend'); if (rrTrend) { rrTrend.textContent = `RR ${mkPct(totals.rr_actual_pct)}`; rrTrend.style.color = mkNum(totals.rr_delta_pct) >= 0 ? '#047857' : '#dc2626'; }

    const realisasi = mkNum(totals.realisasi_bulan_ini || totals.realisasi?.os), flow = mkNum(totals.flow_par);
    const recoveryHasSummary = recovery && recovery.total_recovery !== undefined;
    const recoveryBackflow = recoveryHasSummary ? mkNum(recovery.baki_debet_backflow) : 0;
    const recoveryAngsuran = recoveryHasSummary ? mkNum(recovery.baki_debet_angsuran_npl) : mkNum(totals.angsuran?.os);
    const recoveryNpl = recoveryHasSummary ? recoveryBackflow + mkNum(recovery.baki_debet_lunas) + recoveryAngsuran : Math.abs(mkNum(totals.backflow_total) + mkNum(totals.angsuran?.os) + mkNum(totals.pelunasan?.os));
    const movement = [
      {label:'Realisasi', current:realisasi, previous:null, color:'mk-movement-realisasi'},
      {label:'Angsuran', current:mkNum(totals.angsuran?.os), previous:mkNum(totals.run_off_prev_angsuran), color:'mk-movement-angsuran', compare:true},
      {label:'Lunas', current:mkNum(totals.pelunasan?.os), previous:mkNum(totals.run_off_prev_pelunasan), color:'mk-movement-lunas', compare:true},
      {label:'Flow PAR', current:flow, previous:null, color:'mk-movement-flow'},
      {label:'Recovery NPL', current:recoveryNpl, previous:null, color:'mk-movement-reduce', recovery:true}
    ];
    const movementMax = Math.max(...movement.flatMap(item => item.compare ? [item.current, item.previous] : [item.current]), 1);
    movementChart.innerHTML = `<div class="mk-movement-chart">${movement.map(item => {
      const currentBar = item.recovery
        ? mkInteractiveBarValue(item.current,`mk-movement-bar ${item.color}`,movementMax,'mkOpenRecovery()','Buka detail Recovery NPL')
        : mkBarValue(item.current,`mk-movement-bar ${item.color}`,movementMax,`${item.label} Actual: ${mkFmt(item.current)}`);
      const bars = item.compare ? `<div class="mk-movement-pair">${mkBarValue(item.previous,`mk-movement-bar ${item.color} mk-movement-prev`,movementMax,`${item.label} M-1: ${mkFmt(item.previous)}`)}${currentBar}</div>` : currentBar;
      return `<div class="mk-movement-group">${bars}<label><span>${item.label}</span>${item.compare ? `<small>M-1 ${mkFmtShort(item.previous)}</small>` : ''}</label></div>`;
    }).join('')}</div>`;
    const legend = mkEl('mkMovementLegend');
    if (legend) legend.innerHTML = `<span><i class="mk-movement-prev-legend"></i>M-1</span><span><i class="mk-movement-realisasi"></i>Actual</span>${movement.map(item => item.recovery ? `<button type="button" class="mk-legend-button" onclick="mkOpenRecovery()" title="Buka detail Recovery NPL"><i class="${item.color}"></i>${item.label}</button>` : `<span><i class="${item.color}"></i>${item.label}</span>`).join('')}`;
    const growthBadge = mkEl('mkGrowthBadge'); if (growthBadge) { const growth = mkNum(totals.growth); growthBadge.textContent = `Growth ${growth >= 0 ? '+' : '-'}${mkFmtShort(Math.abs(growth))}`; growthBadge.style.color = growth >= 0 ? '#dc2626' : '#059669'; }
  }

  function renderMkOutlookFromMigration(totals = {}) {
    const runoff = mkNum(totals.angsuran?.os);
    const el = mkEl('mkProjectedRunoff'); if (el) el.textContent = mkFmtShort(runoff);
    const note = mkEl('mkProjectedRunoffNote'); if (note) note.textContent = 'Angsuran murni, tanpa pelunasan';
  }

  function renderMkRecoveryDashboard(recovery = {}) {
    const list = mkEl('mkRecoveryCauseList');
    const totalEl = mkEl('mkRecoveryPanelTotal');
    const totalNoaEl = mkEl('mkRecoveryPanelNoa');
    const noteEl = mkEl('mkRecoveryPanelNote');
    const backflowNominal = mkNum(recovery.baki_debet_backflow);
    const grossAngsuranNominal = mkNum(recovery.baki_debet_angsuran_npl);
    const categories = [
      {label:'Backflow', nominal:backflowNominal, noa:mkNum(recovery.noa_backflow), color:'#2563eb', type:'backflow'},
      {label:'Lunas NPL', nominal:mkNum(recovery.baki_debet_lunas), noa:mkNum(recovery.noa_lunas), color:'#10b981', type:'lunas'},
      {label:'Angsuran NPL', nominal:grossAngsuranNominal, noa:null, detailNominal:grossAngsuranNominal, color:'#64748b', type:'angsuran'}
    ];
    const total = categories.reduce((sum, item) => sum + item.nominal, 0);
    const totalNoa = mkNum(recovery.noa_lunas) + mkNum(recovery.noa_backflow);
    if (totalEl) totalEl.textContent = mkFmtShort(total);
    if (totalNoaEl) totalNoaEl.textContent = `NOA: ${mkFmt(totalNoa)}`;
    if (noteEl) noteEl.textContent = `Nominal Angsuran NPL tetap ditampilkan sebesar ${mkFmtShort(grossAngsuranNominal)}. NOA angsuran tidak dijumlahkan agar tidak double count dengan Backflow; detail tetap menampilkan seluruh rekening angsuran.`;
    if (!list) return;
    if (recovery.total_recovery === undefined && total <= 0) {
      list.innerHTML = '<div class="mk-state">Menunggu data Recovery NPL...</div>';
      return;
    }
    let cursor = 0;
    const segments = categories.map(item => {
      const share = total > 0 ? item.nominal / total * 100 : 0;
      const start = cursor; cursor += share;
      return `${item.color} ${start}% ${cursor}%`;
    });
    list.innerHTML = `<div class="mk-flow-visual">
      <div class="mk-flow-donut" style="--mk-flow-gradient:conic-gradient(${segments.join(',')})">
        <div class="mk-flow-donut-center"><strong>${mkFmtShort(total)}</strong><small>Recovery NPL<br>NOA: ${mkFmt(totalNoa)}</small></div>
      </div>
      <div class="mk-flow-legend">${categories.map(item => {
        const share = total > 0 ? item.nominal / total * 100 : 0;
        const detailNoa = item.noa === null ? 'NOA tidak dihitung' : `${mkFmt(item.noa)} rekening`;
        const detailNominal = item.detailNominal === undefined ? item.nominal : item.detailNominal;
        return `<button type="button" class="mk-flow-legend-row" style="--mk-flow-color:${item.color}" onclick="mkOpenRecovery('${item.type}')"><span class="mk-flow-legend-copy"><b>${item.label}</b><small>${detailNoa} · ${share.toFixed(1)}%</small></span><span class="mk-flow-legend-value">${mkFmtShort(item.nominal)}<small>${mkFmt(detailNominal)} detail</small></span></button>`;
      }).join('')}</div>
    </div>`;
  }

  function renderMkFlowDashboard(flow = {}) {
    const causeList = mkEl('mkFlowCauseList'), weekList = mkEl('mkWorkWeekList');
    const categories = Array.isArray(flow.categories) ? flow.categories : [];
    if (causeList) {
      if (!categories.length) {
        causeList.innerHTML = '<div class="mk-state">Belum ada Flow PAR pada filter ini.</div>';
      } else {
        const colors = ['#ef4444', '#f59e0b', '#dc2626', '#94a3b8'];
        const total = categories.reduce((sum, item) => sum + mkNum(item.nominal), 0);
        let cursor = 0;
        const segments = categories.map((item, index) => {
          const share = total > 0 ? (mkNum(item.nominal) / total) * 100 : 0;
          const start = cursor;
          cursor += share;
          return `${colors[index % colors.length]} ${start}% ${cursor}%`;
        });
        causeList.innerHTML = `<div class="mk-flow-visual">
          <div class="mk-flow-donut" style="--mk-flow-gradient:conic-gradient(${segments.join(',')})">
            <div class="mk-flow-donut-center"><strong>${mkFmtShort(total)}</strong><small>Flow PAR<br>NOA: ${mkFmt(flow.total_noa)}</small></div>
          </div>
          <div class="mk-flow-legend">${categories.map((item, index) => {
            const share = total > 0 ? (mkNum(item.nominal) / total) * 100 : 0;
            return `<button type="button" class="mk-flow-legend-row" style="--mk-flow-color:${colors[index % colors.length]}" onclick="mkOpenFlowPar('${mkEscape(item.key || '')}')"><span class="mk-flow-legend-copy"><b>${mkEscape(item.label)}</b><small>${mkFmt(item.noa)} rekening · ${share.toFixed(1)}%</small></span><span class="mk-flow-legend-value">${mkFmtShort(item.nominal)}<small>${mkFmt(item.nominal)}</small></span></button>`;
          }).join('')}</div>
        </div>`;
      }
    }
    const totalEl = mkEl('mkFlowTotal'); if (totalEl) totalEl.textContent = mkFmtShort(flow.total_nominal);
    const totalNoa = mkEl('mkFlowTotalNoa'); if (totalNoa) totalNoa.textContent = `NOA: ${mkFmt(flow.total_noa)}`;
    const weeks = Array.isArray(flow.weeks) ? flow.weeks : [];
    renderMkPotentialWeekList(weeks);
    if (weekList) {
      weekList.innerHTML = weeks.map(item => {
        const overdue = mkNum(item.overdue_noa) > 0;
        return `<button type="button" class="mk-week-row ${overdue ? 'is-overdue' : 'is-upcoming'}" onclick="mkOpenPotensi(${Number(item.week) || 0})"><span class="mk-week-left"><b>${mkEscape(item.label)} <small>${mkEscape(item.range)}</small></b><small>${overdue ? `Sudah lewat JT: ${mkFmt(item.overdue_noa)} rekening` : 'Belum masuk JT — siapkan follow up'}</small></span><span class="mk-week-value">${mkFmtShort(item.total_tunggakan)}<small>Target bayar • T.P ${mkFmtShort(item.tunggakan_pokok)} • T.B ${mkFmtShort(item.tunggakan_bunga)} • OS ${mkFmtShort(item.nominal)}</small></span></button>`;
      }).join('') || '<div class="mk-state">Belum ada potensi jatuh tempo.</div>';
    }
  }

  function renderMkPotentialWeekList(weeks = []) {
    const list = mkEl('mkPotentialWeekList');
    if (!list) return;
    const rows = Array.isArray(weeks) ? weeks : [];
    if (!rows.length) {
      list.innerHTML = '<div class="mk-state">Belum ada prioritas jatuh tempo.</div>';
      return;
    }
    list.innerHTML = rows.slice(0, 4).map(item => {
      const overdue = mkNum(item.overdue_noa) > 0;
      const nominal = mkNum(item.nominal);
      const tunggakan = mkNum(item.total_tunggakan);
      const overdueText = overdue ? `Lewat JT: ${mkFmt(item.overdue_noa)} rekening` : 'Siapkan follow up sebelum jatuh tempo';
      return `<button type="button" class="mk-potential-week-row ${overdue ? 'is-overdue' : ''}" onclick="mkOpenPotensi(${Number(item.week) || 0})" title="Buka detail potensi NPL ${mkEscape(item.label || '')}">
        <span class="mk-potential-week-copy"><b>${mkEscape(item.label || '-')} <small>${mkEscape(item.range || '')}</small></b><small>${overdueText}</small></span>
        <span class="mk-potential-week-value">${mkFmtShort(nominal)}<small>Tunggakan ${mkFmtShort(tunggakan)}</small></span>
      </button>`;
    }).join('');
  }

  function renderMkPotentialDashboard(payload = {}) {
    const gt = payload.grand_total || {};
    const basis = getMigrasiKolekFilter().nominal_field === 'baki_debet' ? 'BAKI DEBET' : 'SALDO BANK';
    const basisEl = mkEl('mkPotentialBasis'); if (basisEl) basisEl.textContent = basis;
    const donut = mkEl('mkPotentialDonut');
    const valueEl = mkEl('mkPotentialDonutValue');
    const metaEl = mkEl('mkPotentialDonutMeta');
    const totalEl = mkEl('mkPotentialTotal');
    const totalNoaEl = mkEl('mkPotentialTotalNoa');
    const legend = mkEl('mkPotentialLegend');
    const categories = [
      {label:'Masih Potensi', status:'MASIH POTENSI', nominal:mkNum(gt.baki_potensi), noa:mkNum(gt.noa_potensi), color:'#f97316'},
      {label:'Jatuh Tempo', status:'JATUH TEMPO', nominal:mkNum(gt.baki_jt), noa:mkNum(gt.noa_jt), color:'#f59e0b'},
      {label:'Flow Kolek', status:'FLOW KOLEK', nominal:mkNum(gt.baki_flow), noa:mkNum(gt.noa_flow), color:'#ef4444'},
      {label:'Aman / Lunas', status:'AMAN', nominal:mkNum(gt.baki_aman), noa:mkNum(gt.noa_aman), color:'#94a3b8'}
    ];
    const total = mkNum(gt.total_baki) || categories.reduce((sum, item) => sum + item.nominal, 0);
    const totalNoa = mkNum(gt.total_noa) || categories.reduce((sum, item) => sum + item.noa, 0);
    if (valueEl) valueEl.textContent = mkFmtShort(total);
    if (metaEl) metaEl.innerHTML = `Potensi NPL<br>NOA: ${mkFmt(totalNoa)}`;
    if (totalEl) totalEl.textContent = mkFmtShort(total);
    if (totalNoaEl) totalNoaEl.textContent = `NOA: ${mkFmt(totalNoa)}`;
    if (!legend) return;
    if (total <= 0) {
      if (donut) donut.style.setProperty('--mk-flow-gradient', '#e2e8f0');
      legend.innerHTML = '<div class="mk-state">Belum ada Potensi NPL pada filter ini.</div>';
      return;
    }
    let cursor = 0;
    const segments = categories.map(item => {
      const share = item.nominal / total * 100;
      const start = cursor; cursor += share;
      return `${item.color} ${start.toFixed(2)}% ${cursor.toFixed(2)}%`;
    });
    if (donut) donut.style.setProperty('--mk-flow-gradient', `conic-gradient(${segments.join(',')})`);
    legend.innerHTML = categories.map(item => {
      const share = total > 0 ? item.nominal / total * 100 : 0;
      return `<button type="button" class="mk-potential-legend-item" style="--mk-flow-color:${item.color}" onclick="mkOpenPotentialNpl('${mkEscape(item.status)}')"><i aria-hidden="true"></i><span>${item.label}<small>${mkFmt(item.noa)} rekening Â· ${share.toFixed(1)}%</small></span><b>${mkFmtShort(item.nominal)}</b></button>`;
    }).join('');
  }

  function renderMkRR(rr = {}) {
    const target = mkNum(rr.target_os || rr.m1_all_os), paid = mkNum(rr.total_bayar || rr.cur_lancar_os), late = mkNum(rr.lancar_lewat_os || rr.migrasi_os), pct = rr.persen !== undefined ? rr.persen : rr.cur_pct;
    const set=(id,value)=>{const el=mkEl(id);if(el)el.textContent=value;};
    set('mkRrPct', pct === undefined ? '-' : mkPct(pct));
    set('mkRrDetail', `${rr.angsuran !== undefined ? `Bayar ${mkFmtShort(rr.angsuran)}` : `Actual ${mkFmtShort(rr.cur_lancar_os)}`} • M-1 ${mkPct(rr.m1_pct)}`);
    set('mkRrTarget', mkFmtShort(target)); set('mkRrTargetNoa', `NOA: ${mkFmt(rr.target_noa || rr.m1_all_noa)}`);
    set('mkRrPaid', mkFmtShort(paid)); set('mkRrPaidNoa', `NOA: ${mkFmt(rr.angsuran_sesuai_noa + rr.angsuran_lewat_noa || rr.cur_all_noa - rr.migrasi_noa)}`);
    set('mkRrLate', mkFmtShort(late)); set('mkRrLateNoa', `NOA: ${mkFmt(rr.lancar_lewat_noa || rr.migrasi_noa)}`);
  }

  function renderMkMobDashboard(payload = {}) {
    const summary = mkEl('mkMobSummary');
    const basisEl = mkEl('mkMobBasis');
    const rows = Array.isArray(payload.data) ? payload.data : (Array.isArray(payload) ? payload : []);
    const basis = String(payload.filter_aktif?.hitung_berdasarkan || payload.hitung_berdasarkan || '').toLowerCase() === 'baki_debet' ? 'BAKI DEBET' : 'SALDO BANK';
    if (basisEl) basisEl.textContent = basis;
    if (!summary) return;
    if (!rows.length) {
      summary.innerHTML = '<div class="mk-state">Belum ada data FPD / MOB 2–6 pada filter ini.</div>';
      return;
    }
    const byMob = new Map(rows.map(row => [Number(row.mob || 0), row]));
    const migratedKeys = ['1 - 7', '8 - 14', '15 - 21', '22 - 30', '31 - 60', '61 - 90', '> 90'];
    const getBucket = (row, key) => row && row.buckets && row.buckets[key] ? row.buckets[key] : {};
    const getMobSummary = mob => {
      const row = byMob.get(mob) || {};
      const notMigrated = getBucket(row, '0');
      const migrated = migratedKeys.reduce((result, key) => {
        const bucket = getBucket(row, key);
        result.os += mkNum(bucket.os);
        result.noa += mkNum(bucket.noa);
        return result;
      }, {os:0, noa:0});
      const total = mkNum(notMigrated.os) + migrated.os;
      return { row, notMigrated: {os:mkNum(notMigrated.os), noa:mkNum(notMigrated.noa)}, migrated, total, totalNoa: mkNum(notMigrated.noa) + migrated.noa, rate: total > 0 ? migrated.os / total * 100 : 0 };
    };
    const jsString = value => String(value ?? '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    const renderBar = (state, item, total, row, mob) => {
      const isMigrated = state === 'migrated';
      const label = isMigrated ? 'DPD > 0 (Pemburukan Bucket)' : 'DPD 0';
      const share = mkNum(total) > 0 ? Math.min(100, mkNum(item.os) / mkNum(total) * 100) : 0;
      const hasData = !!row.group_id;
      const action = hasData ? ` onclick="mkOpenMobDetail('${jsString(row.group_name)}','${state}','${label}',${mob})"` : ' disabled';
      return `<button type="button" class="mk-mob-bar-card ${isMigrated ? 'is-migrated' : 'is-not-migrated'}${hasData ? '' : ' is-disabled'}"${action} title="${hasData ? `Buka detail ${label} ${row.group_name}` : 'Belum ada data'}">
        <span class="mk-mob-bar-label">${label}</span>
        <span class="mk-mob-track"><i style="width:${share.toFixed(2)}%"></i></span>
        <strong>${mkFmtShort(item.os)}</strong>
        <small>NOA: ${mkFmt(item.noa)}</small>
      </button>`;
    };
    const body = [1,2,3,4,5,6].map(mob => {
      const item = getMobSummary(mob);
      const label = mob === 1 ? 'FPD' : `MOB ${mob}`;
      const month = item.row.group_name ? mkMonthLabel(item.row.group_name) : '-';
      return `<div class="mk-mob-row">
        <div class="mk-mob-label"><strong>${label}</strong><small>${month} · Total NOA: ${mkFmt(item.totalNoa)}</small></div>
        <div class="mk-mob-bars">${renderBar('not_migrated', item.notMigrated, item.total, item.row, mob)}${renderBar('migrated', item.migrated, item.total, item.row, mob)}</div>
        <div class="mk-mob-rate"><strong>${mkPct(item.rate)}</strong><small>Migrasi</small></div>
      </div>`;
    }).join('');
    summary.innerHTML = `<div class="mk-mob-legend"><span><i class="is-not-migrated"></i>DPD 0</span><span><i class="is-migrated"></i>DPD &gt; 0 (Pemburukan Bucket)</span><small>Bar dapat diklik untuk melihat detail angsuran.</small></div>${body}`;
  }

  const mkNplDimensionLabels = { TAHUN:'NPL by Tahun', PRODUK:'NPL by Produk', ANGSURAN:'NPL by Jangka Waktu', PLAFOND:'NPL by Plafond' };
  const mkNplColors = ['#2563eb','#059669','#f59e0b','#94a3b8','#7c3aed','#0891b2','#64748b','#d97706','#475569','#0f766e'];
  let mkNplBreakdownRequest = 0;

  function renderMkNplBreakdown(payload = {}) {
    const body = mkEl('mkNplBreakdownBody');
    const basisEl = mkEl('mkNplBreakdownBasis');
    const titleEl = mkEl('mkNplBreakdownTitle');
    const dimension = String(payload.dimension || mkEl('mkNplDimension')?.value || 'TAHUN').toUpperCase();
    const basis = String(payload.nominal_field || payload.hitung_berdasarkan || '').toLowerCase() === 'baki_debet' ? 'BAKI DEBET' : 'SALDO BANK';
    if (basisEl) basisEl.textContent = basis;
    if (titleEl) titleEl.textContent = mkNplDimensionLabels[dimension] || 'NPL by Tahun';
    if (!body) return;
    if (payload.loading) {
      body.innerHTML = '<div class="mk-state">Memuat rekap NPL...</div>';
      return;
    }
    const rows = Array.isArray(payload.data) ? payload.data : [];
    const grand = payload.grand_total || {};
    const totalNpl = mkNum(grand.npl_os);
    const totalPortfolio = mkNum(grand.total_os);
    const groups = rows.filter(row => mkNum(row.npl_os) > 0);
    if (!groups.length || totalNpl <= 0) {
      body.innerHTML = '<div class="mk-state">Belum ada nominal NPL pada filter ini.</div>';
      return;
    }
    let cursor = 0;
    const gradient = groups.map((row, index) => {
      const start = cursor;
      cursor += mkNum(row.npl_os) / totalNpl * 100;
      return `${mkNplColors[index % mkNplColors.length]} ${start.toFixed(2)}% ${cursor.toFixed(2)}%`;
    }).join(', ');
    const centerPct = totalPortfolio > 0 ? totalNpl / totalPortfolio * 100 : 0;
    const nplDetailArgs = (key = '', label = '') => [dimension, key, label].map(value => encodeURIComponent(String(value ?? ''))).join("','");
    const list = groups.map((row, index) => {
      const share = totalNpl > 0 ? mkNum(row.npl_os) / totalNpl * 100 : 0;
      const nplPct = row.npl_pct !== undefined ? mkNum(row.npl_pct) : 0;
      return `<button type="button" class="mk-npl-group" style="--mk-npl-color:${mkNplColors[index % mkNplColors.length]}" onclick="mkOpenNplBreakdownDetail('${nplDetailArgs(row.group_key, row.group_label)}')" title="Buka detail ${mkEscape(row.group_label || '-')}">
        <i aria-hidden="true"></i>
        <div class="mk-npl-group-copy"><strong>${mkEscape(row.group_label || '-')}</strong><small>NOA NPL: ${mkFmt(row.npl_noa)} Â· ${mkPct(nplPct)} dari kelompok</small></div>
        <div class="mk-npl-group-value">${mkFmtShort(row.npl_os)}<small>${mkPct(share)} dari NPL</small></div>
      </button>`;
    }).join('');
    body.innerHTML = `<div class="mk-npl-breakdown-layout">
      <div>
        <button type="button" class="mk-npl-semi-wrap" style="--mk-npl-gradient:${gradient}" onclick="mkOpenNplBreakdownDetail('${nplDetailArgs()}')" title="Buka seluruh detail NPL" aria-label="Buka seluruh detail NPL">
          <div class="mk-npl-semi-center"><strong>${mkFmtShort(totalNpl)}</strong><small>NPL · ${mkPct(centerPct)}</small></div>
        </button>
        <div class="mk-npl-breakdown-total">Total NPL <b>${mkFmt(totalNpl)}</b></div>
      </div>
      <div class="mk-npl-group-list">${list}</div>
    </div>`;
  }

  async function loadMkNplBreakdown(payload = {}) {
    const requestId = ++mkNplBreakdownRequest;
    const dimension = String(mkEl('mkNplDimension')?.value || 'TAHUN').toUpperCase();
    renderMkNplBreakdown({ loading:true, dimension, nominal_field:payload.nominal_field });
    const requestPayload = {...mkScopedPayload(payload, 'npl_breakdown'), dimension};
    try {
      const response = await mkApi('./api/kredit/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(requestPayload)});
      const result = await response.json();
      if (requestId === mkNplBreakdownRequest && result.status === 200) renderMkNplBreakdown(result.data || {});
      else if (requestId === mkNplBreakdownRequest) renderMkNplBreakdown({dimension, nominal_field:payload.nominal_field, data:[], grand_total:{}});
    } catch (_) {
      if (requestId === mkNplBreakdownRequest) renderMkNplBreakdown({dimension, nominal_field:payload.nominal_field, data:[], grand_total:{}});
    }
  }

  function renderMkRRDaily(payload = {}) {
    const gt = payload.grand_total || {};
    if (!Object.keys(gt).length) return;
    renderMkRR(gt);
    const pct = mkEl('mkRrPct'); if (pct && gt.persen !== undefined) pct.textContent = mkPct(gt.persen);
  }

  function mkMonthLabel(value) {
    if (!value) return '-';
    const text = String(value).slice(0, 7);
    const [year, month] = text.split('-');
    return year && month ? `${month}/${year}` : text;
  }

  function renderMkRunoffProjection(payload = {}) {
    const data = payload.data || payload;
    const rows = Array.isArray(data.runoff_buckets) ? data.runoff_buckets : [];
    const total = data.runoff_total || {};
    const period = data.runoff_period || {};
    const table = mkEl('mkRunoffBucketTable');
    const periodEl = mkEl('mkRunoffProjectionPeriod');
    const gapEl = mkEl('mkRunoffGap');
    const gapNoteEl = mkEl('mkRunoffGapNote');
    const gapNoaEl = mkEl('mkRunoffGapNoa');
    const projectedEl = mkEl('mkProjectedRunoff');
    const projectedNoteEl = mkEl('mkProjectedRunoffNote');
    const sepLabel = mkMonthLabel(period.angsuran_sep_start);
    const oktLabel = mkMonthLabel(period.angsuran_okt_start);
    if (periodEl) periodEl.textContent = `Closing ${mkMonthLabel(period.closing)} · ${sepLabel} & ${oktLabel} s.d. ${String(period.angsuran_okt_end || '').slice(8, 10) || '-'}`;
    if (gapEl) gapEl.textContent = mkFmtShort(total.belum_masuk);
    if (gapNoteEl) gapNoteEl.textContent = `Tagihan Okt P+B ${mkFmtShort(total.angsuran_okt)} · Bunga ${mkFmtShort(total.bunga_okt)}`;
    if (gapNoaEl) gapNoaEl.textContent = `NOA belum bayar Okt: ${mkFmt(total.belum_bayar_okt_noa)}`;
    if (projectedEl) projectedEl.textContent = mkFmtShort(total.pokok_okt);
    if (projectedNoteEl) projectedNoteEl.textContent = `Pokok Okt · tagihan P+B ${mkFmtShort(total.angsuran_okt)}`;
    if (!table) return;
    if (!rows.length) {
      table.innerHTML = '<div class="mk-state">Belum ada data proyeksi angsuran.</div>';
      return;
    }
    table.innerHTML = rows.map(row => {
      const pct = Math.max(0, Math.min(100, mkNum(row.persen)));
      return `<div class="mk-runoff-row">
        <div class="mk-runoff-cell"><strong>${mkEscape(row.label || '-')}</strong><small>NOA: ${mkFmt(row.noa)}</small></div>
        <div class="mk-runoff-cell"><strong>${mkFmtShort(row.closing)}</strong><small>${mkFmt(row.closing)}</small></div>
        <div class="mk-runoff-cell"><strong>${mkFmtShort(row.angsuran_sep)}</strong><small>P ${mkFmtShort(row.pokok_sep)} · B ${mkFmtShort(row.bunga_sep)}</small></div>
        <div class="mk-runoff-cell"><strong>${mkFmtShort(row.angsuran_okt)}</strong><small>P ${mkFmtShort(row.pokok_okt)} · B ${mkFmtShort(row.bunga_okt)}</small></div>
        <div class="mk-runoff-cell is-pct"><strong>${mkPct(row.persen)}</strong><span class="mk-runoff-track"><i style="width:${pct.toFixed(2)}%"></i></span><small>Pokok ${mkFmtShort(row.pokok_okt)}</small></div>
      </div>`;
    }).join('');
  }

  function renderMkPaymentProjection(payload = {}) {
    const data = payload.data || payload;
    const params = payload.params || {};
    renderMkRunoffProjection(data);
    const lancar = data.lancar || {};
    const basis = params.nominal_field === 'baki_debet' ? 'BAKI DEBET' : 'SALDO BANK';
    const basisEl = mkEl('mkProjectionBasis'); if (basisEl) basisEl.textContent = basis;
    const rrUnpaid = mkEl('mkRrUnpaid'); if (rrUnpaid) rrUnpaid.textContent = mkFmtShort(lancar.belum_bayar_os);
    const rrUnpaidNoa = mkEl('mkRrUnpaidNoa'); if (rrUnpaidNoa) rrUnpaidNoa.textContent = `NOA: ${mkFmt(lancar.belum_bayar_noa)}`;
    const periodEl = mkEl('mkLProjectionPeriod'); if (periodEl) periodEl.textContent = `Baseline ${mkMonthLabel(params.bulan_lalu)}`;
    const consistentPeriod = mkEl('mkConsistentPeriod'); if (consistentPeriod) consistentPeriod.textContent = `${mkMonthLabel(params.bulan_lalu_ke_3)} • ${mkMonthLabel(params.bulan_lalu_ke_2)} • ${mkMonthLabel(params.bulan_lalu)}`;
    const stats = mkEl('mkLPaymentStats');
    if (stats) {
      stats.innerHTML = [
        ['Sudah bayar', mkFmt(lancar.sudah_bayar_noa), `${mkFmtShort(lancar.sudah_bayar_nominal)} pembayaran bulan ini`, 'is-paid', "mkOpenProjectionDetail('l_paid')"],
        ['Belum bayar', mkFmt(lancar.belum_bayar_noa), `OS ${mkFmtShort(lancar.belum_bayar_os)}`, 'is-unpaid', "mkOpenProjectionDetail('l_unpaid')"],
        ['Proyeksi bulan lalu', mkFmtShort(lancar.proyeksi_nominal), `${mkFmt(lancar.proyeksi_noa)} rekening punya histori`, 'is-projection', "mkOpenProjectionDetail('l_unpaid')"]
      ].map(item => `<button type="button" class="mk-payment-stat ${item[3]}" onclick="${item[4]}"><span>${item[0]}</span><strong>${item[1]}</strong><small>${item[2]}</small></button>`).join('');
    }

    const unpaidRows = Array.isArray(data.belum_bayar_rows) ? data.belum_bayar_rows : [];
    const unpaidList = mkEl('mkLUnpaidList');
    if (unpaidList) {
      unpaidList.innerHTML = unpaidRows.length ? `<div class="mk-projection-row is-head"><span>Debitur belum bayar</span><span class="num">Bayar bulan lalu / tanggal</span><span class="num">OS actual</span></div>${unpaidRows.map(row => `<div class="mk-projection-row">
        <div><strong>${mkEscape(row.nama_nasabah || '-')}</strong><small>${mkEscape(row.no_rekening || '-')} • Cab. ${mkEscape(row.kode_cabang || '-')}</small></div>
        <div class="num"><strong>${mkFmtShort(row.bulan_lalu_nominal)}</strong><small>${mkRecoveryDate(row.tgl_bayar_bulan_lalu)}</small></div>
        <div class="num"><strong>${mkFmtShort(row.nominal_actual)}</strong><small>OS actual</small></div>
      </div>`).join('')}` : '<div class="mk-state">Tidak ada data Kolek L yang belum bayar.</div>';
    }

    if (unpaidList) unpaidList.innerHTML = `<button type="button" onclick="mkOpenProjectionDetail('l_unpaid')">Lihat detail Kolek L belum bayar <span>↗</span></button>`;

    const consistent = data.konsisten_3_bulan || {};
    const consistentSummary = mkEl('mkConsistentSummary');
    if (consistentSummary) {
      const buckets = ['DP','KL','D','M'];
      consistentSummary.innerHTML = `<table><thead><tr><th>KOLEK</th><th>NOA</th><th>OS ACTUAL</th><th>${mkMonthLabel(params.bulan_lalu_ke_3)}</th><th>${mkMonthLabel(params.bulan_lalu_ke_2)}</th><th>${mkMonthLabel(params.bulan_lalu)}</th></tr></thead><tbody>${buckets.map(bucket => {
        const item = consistent[bucket] || {};
        return `<tr><td><strong>${bucket}</strong></td><td>${mkFmt(item.noa)}</td><td>${mkFmtShort(item.os)}</td><td>${mkFmtShort(item.bulan_3)}</td><td>${mkFmtShort(item.bulan_2)}</td><td>${mkFmtShort(item.bulan_1)}</td></tr>`;
      }).join('')}</tbody></table>`;
    }
    const consistentRows = Array.isArray(data.konsisten_3_bulan_rows) ? data.konsisten_3_bulan_rows : [];
    const consistentList = mkEl('mkConsistentList');
    if (consistentList) {
      consistentList.innerHTML = consistentRows.length ? `<div class="mk-projection-row is-head"><span>Debitur konsisten</span><span class="num">B-3 / B-2 / B-1</span><span class="num">OS actual</span></div>${consistentRows.slice(0, 8).map(row => `<div class="mk-projection-row">
        <div><strong>${mkEscape(row.nama_nasabah || '-')}</strong><small>${mkEscape(row.no_rekening || '-')} • ${mkEscape(row.kolektibilitas || '-')}</small></div>
        <div class="num"><strong>${mkFmtShort(row.bulan_3)} / ${mkFmtShort(row.bulan_2)} / ${mkFmtShort(row.bulan_1)}</strong><small>${mkRecoveryDate(row.tgl_bayar_bulan_3)} • ${mkRecoveryDate(row.tgl_bayar_bulan_2)} • ${mkRecoveryDate(row.tgl_bayar_bulan_1)}</small></div>
        <div class="num"><strong>${mkFmtShort(row.nominal_actual)}</strong><small>OS actual</small></div>
      </div>`).join('')}` : '<div class="mk-state">Belum ada debitur DP/KL/D/M dengan pembayaran 3 bulan berturut-turut.</div>';
    }
    if (consistentList) consistentList.innerHTML = `<button type="button" onclick="mkOpenProjectionDetail('consistent')">Lihat debitur konsisten 3 bulan <span>↗</span></button>`;
  }

  function mkScopedPayload(payload, type) {
    const scoped = { type, closing_date: payload.closing_date, harian_date: payload.harian_date, hitung_berdasarkan: payload.nominal_field };
    if (payload.korwil) scoped.korwil = payload.korwil;
    if (payload.kode_kantor) scoped.kode_kantor = payload.kode_kantor;
    return scoped;
  }

  let mkCockpitRequest = 0;
  async function loadMkCockpit(payload) {
    const requestId = ++mkCockpitRequest;
    mkLastRecovery = null;
    mkCkpnData = {};
    renderMkFlowDashboard({categories:[], weeks:[]});
    renderMkPotentialDashboard({grand_total:{}});
    renderMkRecoveryDashboard({});
    renderMkCkpnSummary({});
    renderMkPaymentProjection({});
    renderMkMobDashboard({});
    renderMkNplBreakdown({loading:true, dimension:mkEl('mkNplDimension')?.value || 'TAHUN', nominal_field:payload.nominal_field});
    loadMkNplBreakdown(payload);
    const flowRequest = mkApi('./api/flow_par/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(mkScopedPayload(payload, 'Flow Par Dashboard'))}).then(response => response.json());
    const potentialRequest = mkApi('./api/npl/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(mkScopedPayload(payload, 'Potensi NPL'))}).then(response => response.json());
    const rrRequest = mkApi('./api/rr/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(mkScopedPayload(payload, 'rr'))}).then(response => response.json());
    const recoveryRequest = mkApi('./api/npl/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(mkScopedPayload(payload, 'Recovery NPL'))}).then(response => response.json());
    const paymentRequest = mkApi('./api/kolek/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(mkScopedPayload(payload, 'migrasi payment projection'))}).then(response => response.json());
    const ckpnRequest = mkApi('./api/kolek/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(mkScopedPayload(payload, 'migrasi ckpn summary'))}).then(response => response.json());
    const mobPayload = {...mkScopedPayload(payload, 'mob_vintage'), rekap_by:'bulan', status_jatuh_tempo:'ALL'};
    const mobRequest = mkApi('./api/kredit/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(mobPayload)}).then(response => response.json());
    try { const result = await flowRequest; if (requestId === mkCockpitRequest && result.status === 200) renderMkFlowDashboard(result.data?.data || {}); } catch (_) {}
    try { const result = await potentialRequest; if (requestId === mkCockpitRequest && result.status === 200) renderMkPotentialDashboard(result.data || {}); } catch (_) {}
    try { const result = await rrRequest; if (requestId === mkCockpitRequest && result.status === 200) renderMkRR(result.data?.grand_total || {}); } catch (_) {}
    try {
      const result = await recoveryRequest;
      if (requestId === mkCockpitRequest && result.status === 200) {
        const rows = Array.isArray(result.data) ? result.data : [];
        const total = rows.find(row => String(row.kode_cabang || '').toUpperCase() === 'TOTAL') || rows[rows.length - 1] || {};
        mkLastRecovery = total;
        renderMkCharts(mkLastData?.totals || {}, total);
        renderMkRecoveryDashboard(total);
      }
    } catch (_) {}
    try { const result = await paymentRequest; if (requestId === mkCockpitRequest && result.status === 200) renderMkPaymentProjection(result.data || {}); } catch (_) {}
    try { const result = await ckpnRequest; if (requestId === mkCockpitRequest && result.status === 200) renderMkCkpnSummary(result.data?.data || {}); } catch (_) {}
    try { const result = await mobRequest; if (requestId === mkCockpitRequest && result.status === 200) renderMkMobDashboard(result.data || {}); } catch (_) {}
  }

  let mkRecoveryRows = [];
  let mkRecoveryAbort = null;
  let mkRecoveryRequest = 0;

  function mkRecoveryDate(value) {
    if (!value) return '-';
    const text = String(value);
    return mkEscape(text.length >= 10 ? text.slice(0, 10) : text);
  }

  function mkRecoveryLabel(value) {
    const text = String(value || '').toLowerCase();
    if (text.includes('lunas')) return 'Lunas NPL';
    if (text.includes('backflow')) return 'Backflow';
    if (text.includes('angsuran')) return 'Angsuran NPL';
    return value || '-';
  }

  function renderMkRecoveryRows() {
    const body = mkEl('mkRecoveryModalBody');
    const search = String(mkEl('mkRecoverySearch')?.value || '').trim().toLowerCase();
    if (!body) return;
    const rows = mkRecoveryRows.filter(row => {
      if (!search) return true;
      return [row.no_rekening, row.nama_nasabah, row.kode_cabang, row.jenis_recovery, row.kolek, row.kolek_update]
        .some(value => String(value || '').toLowerCase().includes(search));
    });
    if (!rows.length) {
      body.innerHTML = '<div class="mk-modal-empty">Tidak ada detail Recovery NPL sesuai pencarian.</div>';
      return;
    }
    body.innerHTML = `<table class="mk-recovery-table">
      <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>JENIS RECOVERY</th><th>KOLEK M-1 → ACTUAL</th><th class="num">NOMINAL RECOVERY</th><th>TGL JATUH TEMPO</th><th>TGL TRANSAKSI</th><th class="num">T.POKOK</th><th class="num">T.BUNGA</th></tr></thead>
      <tbody>${rows.map(row => `<tr>
        <td>${mkEscape(row.no_rekening || '-')}<small class="mk-recovery-sub">Cab. ${mkEscape(row.kode_cabang || '-')}</small></td>
        <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}" >${mkEscape(row.nama_nasabah || '-')}</span></td>
        <td><span class="mk-recovery-type">${mkEscape(mkRecoveryLabel(row.jenis_recovery))}</span></td>
        <td>${mkEscape(row.kolek || '-')} → ${mkEscape(row.kolek_update || '-')}</td>
        <td class="num"><span class="mk-recovery-amount">${mkFmt(row.recovery_nominal)}</span><small class="mk-recovery-sub">OS actual: ${mkFmt(row.baki_debet)}</small></td>
        <td>${mkRecoveryDate(row.tgl_jatuh_tempo)}</td>
        <td>${mkRecoveryDate(row.tgl_trans)}</td>
        <td class="num">${mkFmt(row.angsuran_pokok)}</td>
        <td class="num">${mkFmt(row.angsuran_bunga)}</td>
      </tr>`).join('')}</tbody>
    </table>`;
  }

  function renderMkRecoverySummary() {
    const summary = mkEl('mkRecoveryModalSummary');
    if (!summary) return;
    const total = mkRecoveryRows.reduce((sum, row) => sum + mkNum(row.recovery_nominal), 0);
    const counts = mkRecoveryRows.reduce((acc, row) => {
      const key = mkRecoveryLabel(row.jenis_recovery);
      acc[key] = (acc[key] || 0) + 1;
      return acc;
    }, {});
    summary.innerHTML = [
      ['Debitur recovery', mkFmt(mkRecoveryRows.length)],
      ['Total recovery', mkFmtShort(total)],
      ['Lunas', mkFmt(counts['Lunas NPL'] || 0)],
      ['Backflow / angsuran', mkFmt((counts['Backflow'] || 0) + (counts['Angsuran NPL'] || 0))]
    ].map(item => `<div class="mk-modal-stat"><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('');
  }

  async function mkOpenRecovery(type = 'total_recovery') {
    const modal = mkEl('mkRecoveryModal');
    if (!modal) return;
    modal.hidden = false;
    document.body.classList.add('mk-modal-open');
    const search = mkEl('mkRecoverySearch');
    if (search) search.value = '';
    const body = mkEl('mkRecoveryModalBody');
    const summary = mkEl('mkRecoveryModalSummary');
    if (body) body.innerHTML = '<div class="mk-state">Memuat detail Recovery NPL...</div>';
    if (summary) summary.innerHTML = '';
    const payload = getMigrasiKolekFilter();
    payload.type = type;
    const recoveryTitles = {total_recovery:'Recovery NPL', backflow:'Backflow', lunas:'Lunas NPL', angsuran:'Angsuran NPL'};
    const title = mkEl('mkRecoveryModalTitle');
    if (title) title.textContent = recoveryTitles[type] || 'Recovery NPL';
    const basis = payload.nominal_field === 'baki_debet' ? 'BAKI DEBET' : 'SALDO BANK';
    const badge = mkEl('mkRecoveryModalBasis');
    if (badge) badge.textContent = basis;
    const meta = mkEl('mkRecoveryModalMeta');
    if (meta) meta.textContent = `${payload.closing_date || '-'} → ${payload.harian_date || '-'} • ${payload.korwil || payload.kode_kantor || 'Konsolidasi'}`;
    const requestId = ++mkRecoveryRequest;
    if (mkRecoveryAbort) mkRecoveryAbort.abort();
    mkRecoveryAbort = new AbortController();
    try {
      const response = await mkApi('./api/npl/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload), signal:mkRecoveryAbort.signal});
      const result = await response.json();
      if (requestId !== mkRecoveryRequest || modal.hidden) return;
      if (result.status !== 200) throw new Error(result.message || 'Detail Recovery NPL tidak tersedia.');
      mkRecoveryRows = Array.isArray(result.data) ? result.data : [];
      renderMkRecoverySummary();
      renderMkRecoveryRows();
    } catch (error) {
      if (error.name === 'AbortError') return;
      if (body) body.innerHTML = `<div class="mk-modal-empty">${mkEscape(error.message || 'Gagal memuat detail Recovery NPL.')}</div>`;
    }
  }

  function mkCloseRecovery() {
    const modal = mkEl('mkRecoveryModal');
    if (mkRecoveryAbort) mkRecoveryAbort.abort();
    mkRecoveryRequest++;
    if (modal) modal.hidden = true;
    document.body.classList.remove('mk-modal-open');
  }

  let mkDetailMode = '';
  let mkDetailRows = [];
  let mkDetailProjectionType = '';
  let mkDetailProjectionParams = {};
  let mkDetailMigrationSource = '';
  let mkDetailMigrationTarget = '';
  let mkDetailMobState = '';
  let mkDetailMobLabel = '';
  let mkDetailNplDimension = '';
  let mkDetailNplKey = '';
  let mkDetailNplLabel = '';
  let mkDetailAbort = null;
  let mkDetailRequest = 0;

  function mkDetailScopeText(payload) {
    return `${payload.closing_date || '-'} → ${payload.harian_date || '-'} • ${payload.korwil || payload.kode_kantor || 'Konsolidasi'}`;
  }

  function renderMkDetailSummary() {
    const summary = mkEl('mkDetailModalSummary');
    if (!summary) return;
    if (mkDetailMode === 'migration') {
      const closing = mkDetailRows.reduce((sum, row) => sum + mkNum(row.nominal_closing), 0);
      const actual = mkDetailRows.reduce((sum, row) => sum + mkNum(row.nominal_actual), 0);
      const delta = mkDetailRows.reduce((sum, row) => sum + mkNum(row.selisih), 0);
      const label = `${mkDetailMigrationSource || '-'} → ${mkDetailMigrationTarget || '-'}`;
      summary.innerHTML = [
        [label, mkFmt(mkDetailRows.length)],
        ['Nominal M-1', mkFmtShort(closing)],
        ['Nominal actual', mkFmtShort(actual)],
        ['Selisih', `${delta >= 0 ? '+' : '-'}${mkFmtShort(Math.abs(delta))}`]
      ].map(item => `<div class="mk-modal-stat"><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('');
      return;
    }
    if (mkDetailMode === 'projection') {
      const nominal = mkDetailRows.reduce((sum, row) => sum + mkNum(row.nominal_actual), 0);
      const nominalLabel = `${mkEl('mkDetailModalBasis')?.textContent || 'SALDO BANK'} actual`;
      let items;
      if (mkDetailProjectionType === 'l_paid') {
        const paid = mkDetailRows.reduce((sum, row) => sum + mkNum(row.bayar_current), 0);
        items = [['Sudah bayar', mkFmt(mkDetailRows.length)], [nominalLabel, mkFmtShort(nominal)], ['Bayar bulan ini', mkFmtShort(paid)]];
      } else if (mkDetailProjectionType === 'l_unpaid') {
        const projection = mkDetailRows.reduce((sum, row) => sum + mkNum(row.bulan_lalu_nominal), 0);
        items = [['Belum bayar', mkFmt(mkDetailRows.length)], [nominalLabel, mkFmtShort(nominal)], ['Baseline bulan lalu', mkFmtShort(projection)]];
      } else {
        const history = mkDetailRows.reduce((sum, row) => sum + mkNum(row.bulan_1) + mkNum(row.bulan_2) + mkNum(row.bulan_3), 0);
        items = [['Konsisten 3 bulan', mkFmt(mkDetailRows.length)], [nominalLabel, mkFmtShort(nominal)], ['Total histori bayar', mkFmtShort(history)]];
      }
      summary.innerHTML = items.map(item => `<div class="mk-modal-stat"><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('');
      return;
    }
    if (mkDetailMode === 'mob') {
      const angsuran = mkDetailRows.reduce((sum, row) => sum + mkNum(row.transaksi), 0);
      const totung = mkDetailRows.reduce((sum, row) => sum + mkNum(row.totung), 0);
      const lateTotal = mkDetailRows.reduce((sum, row) => sum + mkNum(row.hari_menunggak), 0);
      const lateAverage = mkDetailRows.length ? lateTotal / mkDetailRows.length : 0;
      const stateLabel = mkDetailMobLabel || (mkDetailMobState === 'migrated' ? 'DPD > 0 (Pemburukan Bucket)' : 'DPD 0');
      const items = [[stateLabel, mkFmt(mkDetailRows.length)], ['Nominal angsuran', mkFmtShort(angsuran)], ['Total tunggakan', mkFmtShort(totung)], ['Rata-rata hari menunggak', `${lateAverage.toFixed(1)} hari`]];
      summary.innerHTML = items.map(item => `<div class="mk-modal-stat"><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('');
      return;
    }
    if (mkDetailMode === 'npl') {
      const nominal = mkDetailRows.reduce((sum, row) => sum + mkNum(row.nominal_npl), 0);
      const label = mkDetailNplLabel || 'Semua NPL';
      const nominalLabel = mkEl('mkDetailModalBasis')?.textContent || 'SALDO BANK';
      const items = [[label, mkFmt(mkDetailRows.length)], [`${nominalLabel} NPL`, mkFmtShort(nominal)], ['Status', 'KL / D / M']];
      summary.innerHTML = items.map(item => `<div class="mk-modal-stat"><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('');
      return;
    }
    const nominalKey = mkDetailMode === 'flow' ? 'baki_debet' : 'baki_debet_harian';
    const tunggakan = mkDetailRows.reduce((sum, row) => sum + mkNum(row.total_tunggakan), 0);
    const nominal = mkDetailRows.reduce((sum, row) => sum + mkNum(row[nominalKey]), 0);
    const items = mkDetailMode === 'flow'
      ? [['Rekening Flow PAR', mkFmt(mkDetailRows.length)], ['Nominal actual', mkFmtShort(nominal)], ['Total tunggakan', mkFmtShort(tunggakan)]]
      : [['Kandidat potensi', mkFmt(mkDetailRows.length)], ['Nominal actual', mkFmtShort(nominal)], ['Total tunggakan', mkFmtShort(tunggakan)]];
    summary.innerHTML = items.map(item => `<div class="mk-modal-stat"><span>${item[0]}</span><strong>${item[1]}</strong></div>`).join('');
  }

  function renderMkDetailRows() {
    const body = mkEl('mkDetailModalBody');
    const query = String(mkEl('mkDetailSearch')?.value || '').trim().toLowerCase();
    if (!body) return;
    const rows = mkDetailRows.filter(row => {
      if (!query) return true;
      return [row.no_rekening, row.nama_nasabah, row.nama_kantor, row.nama_kankas, row.nama_ao, row.nama_produk, row.kode_cabang, row.status_potensi, row.kolek_closing, row.kolek_harian, row.kolektibilitas, row.kolektibilitas_m1, row.kolektibilitas_actual]
        .some(value => String(value || '').toLowerCase().includes(query));
    });
    if (!rows.length) {
      body.innerHTML = '<div class="mk-modal-empty">Tidak ada detail sesuai pencarian.</div>';
      return;
    }
    if (mkDetailMode === 'npl') {
      const basis = mkEl('mkDetailModalBasis')?.textContent || 'SALDO BANK';
      body.innerHTML = `<table class="mk-recovery-table">
        <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>PRODUK</th><th>KOLEK</th><th class="num">JML PINJAMAN</th><th class="num">JANGKA WAKTU</th><th class="num">${mkEscape(basis)} NPL</th><th class="num">HARI MENUNGGAK</th><th class="num">TOTUNG</th><th>TGL REALISASI</th></tr></thead>
        <tbody>${rows.map(row => `<tr>
          <td>${mkEscape(row.no_rekening || '-')}<small class="mk-recovery-sub">Cab. ${mkEscape(row.kode_cabang || '-')}</small></td>
          <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span></td>
          <td>${mkEscape(row.nama_produk || '-')}</td>
          <td><span class="mk-recovery-type">${mkEscape(row.kolektibilitas || '-')}</span></td>
          <td class="num">${mkFmt(row.jml_pinjaman)}</td>
          <td class="num">${mkNum(row.jml_angsuran) > 0 ? `${mkFmt(row.jml_angsuran)} bln` : '-'}</td>
          <td class="num"><span class="mk-recovery-amount">${mkFmt(row.nominal_npl)}</span></td>
          <td class="num">${mkFmt(row.hari_menunggak)}</td>
          <td class="num"><strong>${mkFmt(row.total_tunggakan)}</strong></td>
          <td>${mkRecoveryDate(row.tgl_realisasi)}</td>
        </tr>`).join('')}</tbody>
      </table>`;
      return;
    }
    if (mkDetailMode === 'flow') {
      body.innerHTML = `<table class="mk-recovery-table">
        <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>CABANG / KANKAS</th><th>KOLEK M-1 → ACTUAL</th><th class="num">NOMINAL ACTUAL</th><th class="num">T.POKOK</th><th class="num">T.BUNGA</th><th class="num">TOTUNG</th><th>DPD P / B</th><th>TGL JATUH TEMPO</th></tr></thead>
        <tbody>${rows.map(row => `<tr>
          <td>${mkEscape(row.no_rekening || '-')}</td>
          <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span></td>
          <td>${mkEscape(row.nama_kantor || row.kode_cabang || '-')}<small class="mk-recovery-sub">${mkEscape(row.nama_kankas || '-')}</small></td>
          <td>${mkEscape(row.kolek_closing || '-')} → ${mkEscape(row.kolek_harian || '-')}</td>
          <td class="num"><span class="mk-recovery-amount">${mkFmt(row.baki_debet)}</span></td>
          <td class="num">${mkFmt(row.tunggakan_pokok)}</td>
          <td class="num">${mkFmt(row.tunggakan_bunga)}</td>
          <td class="num"><strong>${mkFmt(row.total_tunggakan)}</strong></td>
          <td>${mkFmt(row.hari_menunggak_pokok)} / ${mkFmt(row.hari_menunggak_bunga)}</td>
          <td>${mkRecoveryDate(row.tgl_jatuh_tempo)}</td>
        </tr>`).join('')}</tbody>
      </table>`;
      return;
    }
    if (mkDetailMode === 'migration') {
      body.innerHTML = `<table class="mk-recovery-table">
        <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>CABANG</th><th>KOLEK M-1</th><th>KOLEK ACTUAL</th><th class="num">NOMINAL M-1</th><th class="num">NOMINAL ACTUAL</th><th class="num">SELISIH</th><th>DPD ACTUAL</th><th>TGL JATUH TEMPO</th></tr></thead>
        <tbody>${rows.map(row => `<tr>
          <td>${mkEscape(row.no_rekening || '-')}</td>
          <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span></td>
          <td>${mkEscape(row.kode_cabang || '-')}</td>
          <td><span class="mk-recovery-type">${mkEscape(row.kolektibilitas_m1 || '-')}</span></td>
          <td><span class="mk-recovery-type">${mkEscape(row.kolektibilitas_actual || '-')}</span></td>
          <td class="num">${mkFmt(row.nominal_closing)}</td>
          <td class="num"><span class="mk-recovery-amount">${mkFmt(row.nominal_actual)}</span></td>
          <td class="num">${mkFmt(row.selisih)}</td>
          <td>${mkFmt(row.hari_menunggak_actual)}</td>
          <td>${mkRecoveryDate(row.tgl_jatuh_tempo)}</td>
        </tr>`).join('')}</tbody>
      </table>`;
      return;
    }
    if (mkDetailMode === 'mob') {
      const basis = mkEl('mkDetailModalBasis')?.textContent || 'SALDO BANK';
      body.innerHTML = `<table class="mk-recovery-table">
        <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>KOLEKTIBILITAS</th><th class="num">JML PINJAMAN</th><th class="num">${mkEscape(basis)} ACTUAL</th><th class="num">ANGSURAN / TGL TRANSAKSI</th><th>TGL REALISASI / JT</th><th class="num">HARI MENUNGGAK</th><th class="num">T.POKOK</th><th class="num">T.BUNGA</th><th class="num">TOTUNG</th></tr></thead>
        <tbody>${rows.map(row => `<tr>
          <td>${mkEscape(row.no_rekening || '-')}</td>
          <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span><small class="mk-recovery-sub">${mkEscape(row.kode_cabang || '-')}</small></td>
          <td><span class="mk-recovery-type">${mkEscape(row.kolektibilitas || '-')}</span></td>
          <td class="num"><span class="mk-recovery-amount">${mkFmt(row.plafond)}</span></td>
          <td class="num"><span class="mk-recovery-amount">${mkFmt(row.os)}</span></td>
          <td class="num"><span class="mk-recovery-amount">${mkFmt(row.transaksi)}</span><small class="mk-recovery-sub">${mkRecoveryDate(row.tgl_trans)}</small></td>
          <td><span>${mkRecoveryDate(row.tgl_realisasi)}</span><small class="mk-recovery-sub">JT: ${mkRecoveryDate(row.tgl_jatuh_tempo)}</small></td>
          <td class="num">${mkFmt(row.hari_menunggak)}</td>
          <td class="num">${mkFmt(row.total_pokok)}</td>
          <td class="num">${mkFmt(row.total_bunga)}</td>
          <td class="num"><strong>${mkFmt(row.totung)}</strong></td>
        </tr>`).join('')}</tbody>
      </table>`;
      return;
    }
    if (mkDetailMode === 'projection') {
      if (mkDetailProjectionType === 'l_paid') {
        body.innerHTML = `<table class="mk-recovery-table">
          <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>CABANG</th><th class="num">${mkEscape((mkEl('mkDetailModalBasis')?.textContent || 'SALDO BANK') + ' ACTUAL')}</th><th class="num">BAYAR BULAN INI</th><th>TGL BAYAR</th><th>TGL JATUH TEMPO</th></tr></thead>
          <tbody>${rows.map(row => `<tr>
            <td>${mkEscape(row.no_rekening || '-')}</td>
            <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span></td>
            <td>${mkEscape(row.kode_cabang || '-')}</td>
            <td class="num"><span class="mk-recovery-amount">${mkFmt(row.nominal_actual)}</span></td>
            <td class="num">${mkFmt(row.bayar_current)}</td>
            <td>${mkRecoveryDate(row.tgl_bayar_current)}</td>
            <td>${mkRecoveryDate(row.tgl_jatuh_tempo)}</td>
          </tr>`).join('')}</tbody>
        </table>`;
        return;
      }
      if (mkDetailProjectionType === 'l_unpaid') {
        body.innerHTML = `<table class="mk-recovery-table">
          <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>CABANG</th><th class="num">${mkEscape((mkEl('mkDetailModalBasis')?.textContent || 'SALDO BANK') + ' ACTUAL')}</th><th class="num">BAYAR BULAN LALU</th><th>TGL BAYAR LALU</th><th>TGL JATUH TEMPO</th></tr></thead>
          <tbody>${rows.map(row => `<tr>
            <td>${mkEscape(row.no_rekening || '-')}</td>
            <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span></td>
            <td>${mkEscape(row.kode_cabang || '-')}</td>
            <td class="num"><span class="mk-recovery-amount">${mkFmt(row.nominal_actual)}</span></td>
            <td class="num">${mkFmt(row.bulan_lalu_nominal)}</td>
            <td>${mkRecoveryDate(row.tgl_bayar_bulan_lalu)}</td>
            <td>${mkRecoveryDate(row.tgl_jatuh_tempo)}</td>
          </tr>`).join('')}</tbody>
        </table>`;
        return;
      }
      const paymentCell = (amount, date) => `<span class="mk-recovery-amount">${mkFmt(amount)}</span><small class="mk-recovery-sub">${mkRecoveryDate(date)}</small>`;
      body.innerHTML = `<table class="mk-recovery-table">
        <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>KOLEK</th><th class="num">${mkEscape((mkEl('mkDetailModalBasis')?.textContent || 'SALDO BANK') + ' ACTUAL')}</th><th class="num">${mkEscape(mkMonthLabel(mkDetailProjectionParams.bulan_lalu_ke_3))}</th><th class="num">${mkEscape(mkMonthLabel(mkDetailProjectionParams.bulan_lalu_ke_2))}</th><th class="num">${mkEscape(mkMonthLabel(mkDetailProjectionParams.bulan_lalu))}</th></tr></thead>
        <tbody>${rows.map(row => `<tr>
          <td>${mkEscape(row.no_rekening || '-')}</td>
          <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span></td>
          <td><span class="mk-recovery-type">${mkEscape(row.kolektibilitas || '-')}</span></td>
          <td class="num"><span class="mk-recovery-amount">${mkFmt(row.nominal_actual)}</span></td>
          <td class="num">${paymentCell(row.bulan_3, row.tgl_bayar_bulan_3)}</td>
          <td class="num">${paymentCell(row.bulan_2, row.tgl_bayar_bulan_2)}</td>
          <td class="num">${paymentCell(row.bulan_1, row.tgl_bayar_bulan_1)}</td>
        </tr>`).join('')}</tbody>
      </table>`;
      return;
    }
    body.innerHTML = `<table class="mk-recovery-table">
      <thead><tr><th>REKENING</th><th>NAMA NASABAH</th><th>CABANG / AO</th><th>STATUS</th><th class="num">NOMINAL ACTUAL</th><th class="num">T.POKOK</th><th class="num">T.BUNGA</th><th class="num">TOTUNG</th><th>TGL JATUH TEMPO</th><th>TGL TRANSAKSI</th></tr></thead>
      <tbody>${rows.map(row => `<tr>
        <td>${mkEscape(row.no_rekening || '-')}</td>
        <td><span class="mk-recovery-name" title="${mkEscape(row.nama_nasabah || '-')}">${mkEscape(row.nama_nasabah || '-')}</span><small class="mk-recovery-sub">${mkEscape(row.alamat || '-')}</small></td>
        <td>${mkEscape(row.nama_kantor || row.kode_cabang || '-')}<small class="mk-recovery-sub">${mkEscape(row.nama_ao || '-')}</small></td>
        <td><span class="mk-recovery-type">${mkEscape(row.status_potensi || '-')}</span></td>
        <td class="num"><span class="mk-recovery-amount">${mkFmt(row.baki_debet_harian)}</span></td>
        <td class="num">${mkFmt(row.tunggakan_pokok)}</td>
        <td class="num">${mkFmt(row.tunggakan_bunga)}</td>
        <td class="num"><strong>${mkFmt(row.total_tunggakan)}</strong></td>
        <td>${mkRecoveryDate(row.jt_harian)}</td>
        <td>${mkRecoveryDate(row.tgl_trans_terakhir)}</td>
      </tr>`).join('')}</tbody>
    </table>`;
  }

  async function mkOpenDetail(mode, options = {}) {
    const modal = mkEl('mkDetailModal');
    if (!modal) return;
    mkDetailMode = mode;
    modal.hidden = false;
    document.body.classList.add('mk-modal-open');
    if (mkDetailAbort) mkDetailAbort.abort();
    mkDetailAbort = new AbortController();
    const search = mkEl('mkDetailSearch');
    if (search) search.value = '';
    const body = mkEl('mkDetailModalBody');
    const summary = mkEl('mkDetailModalSummary');
    if (body) body.innerHTML = '<div class="mk-state">Memuat detail...</div>';
    if (summary) summary.innerHTML = '';

    const payload = getMigrasiKolekFilter();
    const basis = payload.nominal_field === 'baki_debet' ? 'BAKI DEBET' : 'SALDO BANK';
    const badge = mkEl('mkDetailModalBasis');
    if (badge) badge.textContent = basis;
    const meta = mkEl('mkDetailModalMeta');
    if (meta) meta.textContent = mkDetailScopeText(payload);
    const kicker = mkEl('mkDetailModalKicker');
    const title = mkEl('mkDetailModalTitle');
    if (mode === 'flow') {
      payload.type = 'Flow Par Dashboard Detail';
      if (options.classification) payload.klasifikasi_flow = options.classification;
      if (kicker) kicker.textContent = 'DETAIL FLOW PAR';
      if (title) title.textContent = options.label ? `Flow PAR — ${options.label}` : 'Flow PAR';
    } else if (mode === 'potensi') {
      payload.type = 'Potensi NPL Dashboard Detail';
      if (options.weekNo) payload.week_no = options.weekNo;
      if (options.status && options.status !== 'ALL') payload.status_potensi = options.status;
      if (kicker) kicker.textContent = 'DETAIL POTENSI NPL';
      if (title) {
        title.textContent = options.weekNo
          ? `Potensi NPL — Minggu ${options.weekNo}`
          : (options.status && options.status !== 'ALL' ? `Potensi NPL — ${options.status}` : 'Potensi NPL');
      }
    } else if (mode === 'migration') {
      payload.type = 'migrasi kolek';
      payload.detail = 'migration';
      payload.detail_source = options.source || '';
      payload.detail_target = options.target || '';
      mkDetailMigrationSource = payload.detail_source;
      mkDetailMigrationTarget = payload.detail_target;
      if (kicker) kicker.textContent = 'DETAIL MIGRASI KOLEKTIBILITAS';
      if (title) title.textContent = `${mkDetailMigrationSource || '-'} -> ${mkDetailMigrationTarget || '-'}`;
    } else if (mode === 'mob') {
      payload.type = 'detail_mob_debitur';
      payload.bulan_realisasi = options.month || '';
      payload.bucket_label = 'ALL';
      payload.migration_state = options.state || '';
      mkDetailMobState = options.state || '';
      mkDetailMobLabel = options.label || '';
      if (kicker) kicker.textContent = 'DETAIL FPD / MOB';
      if (title) title.textContent = `${options.label || 'Detail migrasi'} - ${options.month || '-'}`;
      if (title) title.textContent = `${options.label || 'Detail migrasi'} - ${options.month || '-'}`;
      if (title) title.textContent = `${mkDetailMigrationSource || '-'} → ${mkDetailMigrationTarget || '-'}`;
    }
    if (mode === 'mob' && title) title.textContent = `${mkDetailMobLabel || 'Detail migrasi'} - ${options.month || '-'}`;
    if (mode === 'npl') {
      payload.type = 'npl_breakdown_detail';
      payload.dimension = options.dimension || 'TAHUN';
      payload.group_key = options.groupKey || '';
      mkDetailNplDimension = payload.dimension;
      mkDetailNplKey = payload.group_key;
      mkDetailNplLabel = options.groupLabel || '';
      if (kicker) kicker.textContent = 'DETAIL NPL';
      if (title) title.textContent = mkDetailNplLabel ? `NPL - ${mkDetailNplLabel}` : 'Seluruh NPL';
    }
    if (mode === 'projection') {
      payload.type = 'migrasi payment projection';
      payload.detail = options.detail || 'l_unpaid';
      mkDetailProjectionType = payload.detail;
      if (kicker) kicker.textContent = 'DETAIL PROYEKSI PEMBAYARAN';
      const projectionTitles = {
        l_paid: 'Kolek L - sudah bayar',
        l_unpaid: 'Kolek L - belum bayar',
        consistent: 'DP / KL / D / M - konsisten 3 bulan'
      };
      if (title) title.textContent = projectionTitles[mkDetailProjectionType] || 'Proyeksi pembayaran';
    }
    const requestId = ++mkDetailRequest;
    try {
      const endpoint = mode === 'flow' ? './api/flow_par/' : (mode === 'potensi' ? './api/npl/' : ((mode === 'mob' || mode === 'npl') ? './api/kredit/' : './api/kolek/'));
      const response = await mkApi(endpoint, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload), signal:mkDetailAbort.signal});
      const result = await response.json();
      if (requestId !== mkDetailRequest || modal.hidden) return;
      if (result.status !== 200) throw new Error(result.message || 'Detail tidak tersedia.');
      if (mode === 'projection' || mode === 'migration') {
        if (mode === 'projection') mkDetailProjectionParams = result.data?.params || {};
        mkDetailRows = Array.isArray(result.data?.data?.detail_rows) ? result.data.data.detail_rows : [];
      } else if (mode === 'mob' || mode === 'npl') {
        mkDetailRows = Array.isArray(result.data?.data) ? result.data.data : [];
      } else {
        mkDetailRows = Array.isArray(result.data) ? result.data : [];
      }
      renderMkDetailSummary();
      renderMkDetailRows();
    } catch (error) {
      if (error.name === 'AbortError') return;
      if (body) body.innerHTML = `<div class="mk-modal-empty">${mkEscape(error.message || 'Gagal memuat detail.')}</div>`;
    }
  }

  function mkCloseDetail() {
    if (mkDetailAbort) mkDetailAbort.abort();
    mkDetailRequest++;
    const modal = mkEl('mkDetailModal');
    if (modal) modal.hidden = true;
    document.body.classList.remove('mk-modal-open');
  }

  function mkOpenFlowPar(classification = '') {
    const labels = {pokok:'Tunggakan pokok >90 hari', bunga:'Tunggakan bunga >90 hari', pokok_bunga:'Tunggakan pokok & bunga >90 hari', lainnya:'Lainnya / JT'};
    mkOpenDetail('flow', {classification, label:labels[classification] || ''});
  }
  function mkOpenPotensi(weekNo = 0) { mkOpenDetail('potensi', {weekNo:Number(weekNo) || 0}); }
  function mkOpenPotentialNpl(status = 'ALL') { mkOpenDetail('potensi', {status}); }
  function mkOpenProjectionDetail(detail = 'l_unpaid') { mkOpenDetail('projection', {detail}); }
  function mkOpenMigrationDetail(source = '', target = '') { mkOpenDetail('migration', {source, target}); }
  function mkOpenMobDetail(month = '', state = '', label = '', mob = 0) { mkOpenDetail('mob', {month, state, label, mob}); }
  function mkOpenNplBreakdownDetail(dimension = 'TAHUN', groupKey = '', groupLabel = '') {
    mkOpenDetail('npl', {dimension:decodeURIComponent(dimension || 'TAHUN'), groupKey:decodeURIComponent(groupKey || ''), groupLabel:decodeURIComponent(groupLabel || '')});
  }
  function mkOpenRR() { window.location.href = './rekap_rr'; }

  function mkMigrationMetric(value, source, target) {
    if (mkIsEmpty(value)) return mkMetric(value, '', true);
    const safeSource = mkEscape(source || '');
    const safeTarget = mkEscape(target || '');
    return `<button type="button" class="mk-migration-metric" onclick="mkOpenMigrationDetail('${safeSource}','${safeTarget}')" title="Buka detail ${safeSource} ke ${safeTarget}" aria-label="Buka detail ${safeSource} ke ${safeTarget}">${mkMetric(value)}</button>`;
  }

  function renderMigrasiTable(data) {
    const body=mkEl('mkMigrationBody'); if(!body)return;
    const totals=data?.totals||{}; const rows=Array.isArray(data?.rows)?data.rows:[];
    if(!rows.length){body.innerHTML='<tr><td colspan="11" class="mk-empty-row">Tidak ada data migrasi.</td></tr>';return;}
    const actualKeys=['L','DP','KL','D','M']; const actualTotal=actualKeys.map(key=>totals.actual?.[key]||{noa:0,os:0,pct:0});
    const realisasi=totals.realisasi_baru||totals.realisasi||{noa:0,os:0}; const restruck=totals.restruck||{noa:0,os:0}; const combined=mkCombined(realisasi,restruck);
    const totalAngsuran=totals.angsuran||{noa:0,os:0}; const totalPelunasan=totals.pelunasan||{noa:0,os:0};
    const totalRunOff={noa:mkNum(totalAngsuran.noa)+mkNum(totalPelunasan.noa),os:mkNum(totals.run_off_total||mkNum(totalAngsuran.os)+mkNum(totalPelunasan.os))};
    let html=`<tr class="mk-total-row"><td>${mkRowLabel('TOTAL')}</td><td>${mkMetric({noa:totals.m1_noa,os:totals.m1_os},'',false)}</td>${actualTotal.map(v=>`<td>${mkMetric(v)}</td>`).join('')}<td>${mkMetric(combined,'mk-realisasi',false)}</td><td>${mkMetric(totalAngsuran,'mk-angsuran',false)}</td><td>${mkMetric(totalPelunasan,'mk-pelunasan',false)}</td><td>${mkMetric(totalRunOff,'mk-runoff',false)}</td></tr>`;
    const empty=()=>mkMetric({noa:0,os:0},'',false);
    html+=`<tr class="mk-special-row"><td>${mkRowLabel('REALISASI BARU','rekening tidak ada di closing')}</td><td>${empty()}</td>${actualKeys.map(()=>`<td>${empty()}</td>`).join('')}<td>${mkMetric(realisasi,'mk-realisasi',false)}</td><td>${empty()}</td><td>${empty()}</td><td>${empty()}</td></tr>`;
    html+=`<tr class="mk-special-row"><td>${mkRowLabel('RESTRUCK / KAPITALISASI','actual > closing')}</td><td>${empty()}</td>${actualKeys.map(()=>`<td>${empty()}</td>`).join('')}<td>${mkMetric(restruck,'mk-restruktur',false)}</td><td>${empty()}</td><td>${empty()}</td><td>${empty()}</td></tr>`;
    rows.forEach(row=>{const a=row.actual||{};const rr=row.realisasi_restruck||{noa:0,os:0};const angsuran=row.angsuran||{noa:0,os:0};const pelunasan=row.pelunasan||{noa:0,os:0};const runOff={noa:mkNum(angsuran.noa)+mkNum(pelunasan.noa),os:mkNum(row.run_off||mkNum(angsuran.os)+mkNum(pelunasan.os))};html+=`<tr><td>${mkRowLabel(row.kol)}</td><td>${mkMetric(row.m1,'',false)}</td>${actualKeys.map(key=>`<td>${mkMigrationMetric(a[key]||{noa:0,os:0},row.kol,key)}</td>`).join('')}<td>${mkMetric(rr,'mk-restruktur',false)}</td><td>${mkMetric(angsuran,'mk-angsuran',false)}</td><td>${mkMigrationMetric(pelunasan,row.kol,'LUNAS')}</td><td>${mkMetric(runOff,'mk-runoff',false)}</td></tr>`;});
    body.innerHTML=html;
  }

  function getMigrasiKolekFilter(){
    const area=mkEl('migrasiKolekKantor')?.value||''; const nominal=mkEl('migrasiKolekNominal')?.value==='baki_debet'?'baki_debet':'saldo_bank'; const payload={type:'migrasi kolek',closing_date:mkEl('migrasiKolekClosing')?.value||'',harian_date:mkEl('migrasiKolekHarian')?.value||'',nominal_field:nominal,hitung_berdasarkan:nominal};
    if(area.indexOf('KORWIL:')===0)payload.korwil=area.substring(7); else if(area&&area!=='000')payload.kode_kantor=area.replace('CABANG:',''); return payload;
  }
  async function fetchMigrasiKolekFromNavbar(){
    const payload=getMigrasiKolekFilter(); if(!payload.closing_date||!payload.harian_date)return;
    const loading=mkEl('mkLoading'); loading?.classList.remove('hidden'); if(mkAbort)mkAbort.abort(); mkAbort=new AbortController();
    try{const response=await mkApi('./api/kolek/',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload),signal:mkAbort.signal});const result=await response.json();if(result.status!==200||!result.data?.data)throw new Error(result.message||'Data migrasi tidak tersedia');mkLastData=result.data.data;renderMigrasiSummary(mkLastData.totals||{});renderMigrasiTable(mkLastData);const label=payload.nominal_field==='baki_debet'?'BAKI DEBET':'SALDO BANK';if(mkEl('mkNominalBadge'))mkEl('mkNominalBadge').textContent=label;if(mkEl('mkTableSubtitle'))mkEl('mkTableSubtitle').textContent=`Nominal dalam ${label.toLowerCase()} asli • Angsuran murni dipisahkan dari restruck`;}catch(error){if(error.name!=='AbortError')mkEl('mkMigrationBody').innerHTML=`<tr><td colspan="11" class="mk-empty-row">${mkEscape(error.message||'Gagal memuat data migrasi.')}</td></tr>`;}finally{loading?.classList.add('hidden');}
  }
  window.fetchMigrasiKolekFromNavbar=fetchMigrasiKolekFromNavbar;

  async function populateMigrasiKolekArea(){
    const select=mkEl('migrasiKolekKantor');if(!select)return;const user=(window.getUser&&window.getUser())||null;const userCode=user?.kode?String(user.kode).padStart(3,'0'):'000';
    if(userCode!=='000'){select.innerHTML=`<option value="${mkEscape(userCode)}">${mkEscape(userCode)} - Cabang</option>`;select.value=userCode;select.disabled=true;return;}
    try{const response=await mkApi('./api/kode/',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'kode_kantor'})});const result=await response.json();const list=Array.isArray(result.data)?result.data:[];let html='<option value="">Konsolidasi</option>';['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].forEach(k=>{html+=`<option value="KORWIL:${k}">Korwil ${k[0]+k.slice(1).toLowerCase()}</option>`;});list.filter(x=>x.kode_kantor&&x.kode_kantor!=='000').sort((a,b)=>String(a.kode_kantor).localeCompare(String(b.kode_kantor))).forEach(x=>{const code=String(x.kode_kantor).padStart(3,'0');html+=`<option value="${code}">${code} - ${mkEscape(x.nama_kantor||x.nama_cabang||'')}</option>`;});select.innerHTML=html;}catch(_){select.innerHTML='<option value="">Konsolidasi</option>';}
  }
  function bindMigrasiKolekNavbarFilter(){
    const panel=mkEl('migrasiKolekNavbarFilterPanel'),toggle=mkEl('migrasiKolekNavbarFilterToggle'),close=mkEl('migrasiKolekNavbarFilterClose');if(!panel||!toggle||toggle.dataset.bound==='1')return;const setOpen=open=>{panel.classList.toggle('hidden',!open);panel.classList.toggle('flex',open);toggle.setAttribute('aria-expanded',open?'true':'false');};toggle.addEventListener('click',e=>{e.stopPropagation();setOpen(panel.classList.contains('hidden'));});close?.addEventListener('click',()=>setOpen(false));document.addEventListener('click',e=>{if(!panel.contains(e.target)&&!toggle.contains(e.target))setOpen(false);});toggle.dataset.bound='1';
  }
  function bindMkRecoveryModal(){
    const modal=mkEl('mkRecoveryModal'),close=mkEl('mkRecoveryClose'),search=mkEl('mkRecoverySearch');
    if(!modal||modal.dataset.bound==='1')return;
    close?.addEventListener('click',mkCloseRecovery);
    modal.querySelector('[data-mk-close-recovery]')?.addEventListener('click',mkCloseRecovery);
    search?.addEventListener('input',renderMkRecoveryRows);
    document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!modal.hidden)mkCloseRecovery();});
    modal.dataset.bound='1';
  }
  function bindMkDetailModal(){
    const modal=mkEl('mkDetailModal'),close=mkEl('mkDetailClose'),search=mkEl('mkDetailSearch');
    if(!modal||modal.dataset.bound==='1')return;
    close?.addEventListener('click',mkCloseDetail);
    modal.querySelector('[data-mk-close-detail]')?.addEventListener('click',mkCloseDetail);
    search?.addEventListener('input',renderMkDetailRows);
    document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!modal.hidden)mkCloseDetail();});
    modal.dataset.bound='1';
  }
  function exportMigrasiKolek(){
    if(!mkLastData?.rows?.length)return;const t=mkLastData.totals||{};const lines=[['Keterangan','Nominal','NOA','Persentase']];const add=(label,value,pct='')=>lines.push([label,Math.round(mkNum(value?.os)),mkNum(value?.noa),pct]);add('M-1',{os:t.m1_os,noa:t.m1_noa});add('RR M-1',{os:t.rr_prev,noa:t.rr_prev_noa},mkPct(t.rr_prev_pct));add('RR Actual',{os:t.rr_actual,noa:t.rr_actual_noa},mkPct(t.rr_actual_pct));['L','DP','KL','D','M'].forEach(k=>add(`Actual ${k}`,t.actual?.[k],mkPct(t.actual?.[k]?.pct)));add('Realisasi Baru',t.realisasi_baru||t.realisasi);add('Restruck / Kapitalisasi',t.restruck);add('Angsuran Murni',t.angsuran);add('Pelunasan',t.pelunasan);add('Total Run Off',{os:t.run_off_total,noa:mkNum(t.angsuran?.noa)+mkNum(t.pelunasan?.noa)});const csv='\ufeff'+lines.map(row=>row.map(cell=>`"${String(cell??'').replace(/"/g,'""')}"`).join('\t')).join('\n');const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([csv],{type:'text/tab-separated-values;charset=utf-8'}));a.download='migrasi-kolektibilitas.tsv';a.click();URL.revokeObjectURL(a.href);
  }
  window.exportMigrasiKolek=exportMigrasiKolek;

  function bindMkChartTooltips(){
    if(document.documentElement.dataset.mkChartTooltips==='1')return;
    document.addEventListener('click',event=>{
      const bar=event.target.closest('#mkOscChart .mk-bar[data-tip], #mkMovementChart .mk-movement-bar[data-tip]');
      const opened=bar?.classList.contains('is-tip-open');
      document.querySelectorAll('#mkOscChart .is-tip-open, #mkMovementChart .is-tip-open').forEach(item=>item.classList.remove('is-tip-open'));
      if(bar&&!opened)bar.classList.add('is-tip-open');
    });
    document.addEventListener('keydown',event=>{if(event.key==='Escape')document.querySelectorAll('#mkOscChart .is-tip-open, #mkMovementChart .is-tip-open').forEach(item=>item.classList.remove('is-tip-open'));});
    document.documentElement.dataset.mkChartTooltips='1';
  }

  window.addEventListener('DOMContentLoaded',async()=>{bindMigrasiKolekNavbarFilter();bindMkRecoveryModal();bindMkDetailModal();bindMkChartTooltips();try{const dateRes=await mkApi('./api/date/');const dateJson=await dateRes.json();const dates=dateJson.data||{};if(mkEl('migrasiKolekClosing'))mkEl('migrasiKolekClosing').value=dates.last_closing||'';if(mkEl('migrasiKolekHarian'))mkEl('migrasiKolekHarian').value=dates.last_created||'';}catch(_){}await populateMigrasiKolekArea();fetchMigrasiKolekFromNavbar();});
</script>
