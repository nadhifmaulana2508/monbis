<?php $branchlessMode = $_GET['id'] ?? ''; ?>
<!-- Load Library ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  :root { --primary: #059669; --bg: #f9fafb; --text: #334155; }
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
  .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th { background-color: #f8fafc; color: #1e293b; font-weight: 800; padding: 10px 8px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 11px; }
  td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-weight: 700; color: #334155; }
  tr:hover td { background-color: #ecfdf5; }
  .card-shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
  .local-loader { position: absolute; inset: 0; background: rgba(255,255,255,0.7); z-index: 50; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px); border-radius: inherit; }
  .local-loader.hidden { display: none; }
  .apexcharts-tooltip { z-index: 99999 !important; background: transparent !important; border: none !important; box-shadow: none !important; }
  .trend-note { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 9999px; background: #ecfdf5; color: #047857; font-size: 10px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
  .trend-note-dot { width: 7px; height: 7px; border-radius: 9999px; background: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,.14); }
  .admin-card { border: 1px solid #e5e7eb; border-radius: 16px; background: #fff; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); }
  .admin-card pre { white-space: pre-wrap; word-break: break-word; }
  .admin-kbd { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 11px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2px 8px; color: #0f172a; }
  @media (max-width: 767px) {
    th { padding: 8px 6px; font-size: 10px; }
    td { padding: 8px 6px; font-size: 11px; }
  }
</style>

<div class="max-w-[1600px] mx-auto px-2 md:px-4 py-4 md:py-6 min-h-screen font-sans space-y-3 md:space-y-4">
  <!-- HEADER & FILTER -->
  <div class="flex flex-col md:flex-row justify-between md:items-end gap-3">
    <div class="flex justify-between items-center w-full md:w-auto">
    <div class="flex items-center gap-2">
        <div>
          <h1 class="text-xl md:text-2xl font-extrabold text-gray-800 tracking-tight flex items-center gap-2">
              <span class="bg-emerald-600 text-white p-1.5 rounded-lg text-sm shadow-sm">
                  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
              </span>
              Branchless Banking
          </h1>
          <p class="text-[10px] md:text-xs text-gray-500 mt-0.5 font-medium" id="lbl_periode_aktif">Menunggu data sinkronisasi...</p>
        </div>
        <button type="button" onclick="openNarrative()" class="w-7 h-7 flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 hover:text-emerald-600 hover:border-emerald-300 transition-colors shadow-sm" title="Narasi Otomatis">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        </button>
      </div>
      <button type="button" id="btnFilterToggle" onclick="toggleFilter()" class="md:hidden flex items-center gap-1.5 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 active:scale-95 transition-transform">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        <span id="filterToggleLabel">Filter</span>
      </button>
    </div>
    <form id="formFilterGlobal" class="hidden md:flex flex-col md:flex-row items-end gap-2.5 md:gap-3 bg-white p-2.5 md:p-3 rounded-xl shadow-sm border border-gray-200 w-full md:w-auto">
      <div class="flex w-full md:w-auto gap-2 shrink-0">
        <div class="flex flex-col flex-1 min-w-0 md:w-[130px]">
          <label class="text-[9px] md:text-[10px] font-bold text-gray-500 uppercase tracking-wider">Closing M-1</label>
          <input type="date" id="closing_date" class="border-b-2 border-transparent hover:border-gray-300 px-1 py-1 text-[10px] md:text-sm outline-none focus:border-emerald-500 transition-colors font-semibold cursor-pointer w-full bg-transparent" required>
        </div>
        <div class="flex flex-col flex-1 min-w-0 md:w-[130px]">
          <label class="text-[9px] md:text-[10px] font-bold text-gray-500 uppercase tracking-wider">Harian/Actual</label>
          <input type="date" id="harian_date" class="border-b-2 border-transparent hover:border-gray-300 px-1 py-1 text-[10px] md:text-sm outline-none focus:border-emerald-500 transition-colors font-semibold cursor-pointer w-full bg-transparent" required>
        </div>
      </div>
      <div class="flex w-full md:w-auto items-end gap-2 shrink-0 mt-0.5 md:mt-0">
        <div class="flex flex-col flex-1 min-w-0 md:w-[180px]">
          <label class="text-[9px] md:text-[10px] font-bold text-gray-500 uppercase tracking-wider">Area/Cabang</label>
          <select id="opt_area" class="border-b-2 border-transparent hover:border-gray-300 px-1 py-1 text-[10px] md:text-sm outline-none focus:border-emerald-500 bg-transparent transition-colors font-bold text-emerald-700 cursor-pointer w-full truncate">
              <option value="KONSOLIDASI" class="font-bold">Konsolidasi</option>
              <optgroup label="Berdasarkan Korwil" class="text-gray-400">
                  <option value="KORWIL_SEMARANG" class="text-gray-700">Korwil Semarang</option>
                  <option value="KORWIL_SOLO" class="text-gray-700">Korwil Solo</option>
                  <option value="KORWIL_BANYUMAS" class="text-gray-700">Korwil Banyumas</option>
                  <option value="KORWIL_PEKALONGAN" class="text-gray-700">Korwil Pekalongan</option>
              </optgroup>
              <optgroup label="Berdasarkan Cabang" id="opt_cabang_list" class="text-gray-400"></optgroup>
          </select>
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white w-[34px] md:w-auto h-[32px] md:h-[36px] md:px-5 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-md active:scale-95 shrink-0 mb-[1px]">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="md:hidden"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <span class="hidden md:inline">Tampilkan</span>
        </button>
      </div>
    </form>
  </div>

  <!-- TAB NAVIGATION -->
  <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg w-fit">
    <button onclick="switchView('rekap')" id="tab-rekap" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-emerald-700" title="Rekap Cabang">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
      <span class="hidden md:inline">Rekap</span>
    </button>
    <button onclick="switchView('chart')" id="tab-chart" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700" title="Chart">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
      <span class="hidden md:inline">Chart</span>
    </button>
    <button onclick="switchView('device')" id="tab-device" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700" title="Device">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
      <span class="hidden md:inline">Device</span>
    </button>
  </div>

  <!-- SECTION: REKAP (Breakdown Table) -->
  <div id="section-rekap" class="space-y-3 md:space-y-4">
    <div class="bg-white rounded-xl md:rounded-2xl border border-gray-100 flex flex-col overflow-hidden card-shadow relative min-h-[200px]">
      <div id="loadTable" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div class="px-3 md:px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h2 class="text-[13px] md:text-base font-black text-gray-800">Perbandingan Cabang Branchless - MoM & YoY</h2>
      </div>
      <div class="overflow-x-auto custom-scrollbar max-h-[600px]">
        <table class="w-full text-left" style="min-width: 800px;">
          <thead class="sticky top-0 z-10">
            <tr>
              <th class="w-[40px] pl-4 text-center">NO</th>
              <th class="w-[200px]">NAMA CABANG</th>
              <th class="text-right">NOM THN LALU</th>
              <th class="text-right">NOM THN INI</th>
              <th class="text-center w-[80px]">GAP% YOY</th>
              <th class="text-right">NOM BLN LALU</th>
              <th class="text-right">NOM BLN INI</th>
              <th class="text-center w-[80px] pr-4">GAP% MOM</th>
            </tr>
          </thead>
          <tbody id="bodyUnified" class="divide-y divide-gray-50"></tbody>
        </table>
      </div>
    </div>
  </div><!-- end section-rekap -->

  <!-- SECTION: CHART (Trend + Distribution) -->
  <div id="section-chart" class="hidden space-y-3 md:space-y-4">

  <!-- TREND CHART + DISTRIBUTION (side by side) -->
  <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 md:gap-4">
    <!-- Trend Chart (7 cols) -->
    <div class="xl:col-span-7 bg-white rounded-xl md:rounded-2xl border border-gray-100 p-3 md:p-4 card-shadow flex flex-col relative" style="min-height:380px;">
      <div id="loadTrend" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div class="flex justify-between items-center mb-2 border-b border-gray-100 pb-2">
          <h2 class="font-bold text-gray-800 text-[13px] md:text-base">Tren Transaksi Branchless</h2>
          <div class="flex items-center gap-2">
              <div id="trendNote" class="trend-note hidden sm:inline-flex">
                  <span class="trend-note-dot"></span>
                  Label 6 Bulan Aktif
              </div>
              <select id="trendPeriode" class="border border-gray-200 rounded-md px-2 py-1 text-[10px] md:text-xs font-semibold text-gray-600 outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer bg-white shadow-sm" onchange="fetchTrend()">
                  <option value="bulanan">6 Bulan Terakhir</option>
                  <option value="7_hari">7 Hari Terakhir</option>
                  <option value="30_hari">30 Hari Terakhir</option>
                  <option value="tahunan">Tahunan</option>
              </select>
          </div>
      </div>
      <div id="chartTrend" class="w-full flex-1 mt-1"></div>
    </div>
    <!-- Distribution (5 cols) -->
    <div class="xl:col-span-5 bg-white rounded-xl md:rounded-2xl border border-gray-100 p-3 md:p-4 card-shadow flex flex-col relative" style="min-height:380px;">
      <div id="loadDist" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <h2 class="font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2 text-[13px] md:text-base">Top 5 Distribusi</h2>
      <div class="flex-1 flex flex-col gap-3">
          <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 flex flex-col gap-3" id="listTop5"></div>
          <div class="w-full flex items-center justify-center shrink-0">
              <div id="chartDonut" class="w-full max-w-[220px]"></div>
          </div>
      </div>
    </div>
  </div>

  </div><!-- end section-chart -->

  <div id="section-device" class="hidden space-y-3 md:space-y-4">
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
      <div class="bg-white rounded-xl md:rounded-2xl border border-gray-100 p-3 md:p-4 card-shadow">
        <div class="text-[10px] md:text-[11px] uppercase tracking-wide text-slate-500 font-bold">Nominal Bulan Ini</div>
        <div id="branchlessNominal" class="mt-2 text-lg md:text-2xl font-black text-emerald-700">Rp 0</div>
      </div>
      <div class="bg-white rounded-xl md:rounded-2xl border border-gray-100 p-3 md:p-4 card-shadow">
        <div class="text-[10px] md:text-[11px] uppercase tracking-wide text-slate-500 font-bold">Transaksi</div>
        <div id="branchlessTrx" class="mt-2 text-lg md:text-2xl font-black text-slate-800">0</div>
      </div>
      <div class="bg-white rounded-xl md:rounded-2xl border border-gray-100 p-3 md:p-4 card-shadow">
        <div class="text-[10px] md:text-[11px] uppercase tracking-wide text-slate-500 font-bold">Device Aktif</div>
        <div id="branchlessDevice" class="mt-2 text-lg md:text-2xl font-black text-slate-800">0</div>
      </div>
      <div class="bg-white rounded-xl md:rounded-2xl border border-gray-100 p-3 md:p-4 card-shadow">
        <div class="text-[10px] md:text-[11px] uppercase tracking-wide text-slate-500 font-bold">User Aktif</div>
        <div id="branchlessUser" class="mt-2 text-lg md:text-2xl font-black text-slate-800">0</div>
      </div>
    </div>

    <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg w-fit">
      <button onclick="switchDeviceView('by-device')" id="tab-device-by-device" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-emerald-700" title="Rekap By Device">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
        <span>By Device</span>
      </button>
      <button onclick="switchDeviceView('by-user')" id="tab-device-by-user" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700" title="Rekap By User">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
        <span>By User</span>
      </button>
    </div>

    <div id="device-view-by-device" class="bg-white rounded-xl md:rounded-2xl border border-gray-100 flex flex-col overflow-hidden card-shadow relative min-h-[220px]">
        <div id="loadBranchlessRekap" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
        <div class="px-3 md:px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
          <div>
            <h2 class="text-[13px] md:text-base font-black text-gray-800">Rekap Device Branchless</h2>
            <p class="mt-0.5 text-[10px] md:text-xs text-slate-500 font-medium">Khusus device branchless bulan berjalan, diurutkan dari nominal terbesar dan dikelompokkan per kantor + user + device.</p>
          </div>
          <div class="text-right">
            <div class="text-[10px] md:text-xs font-bold text-slate-400 uppercase">NoA</div>
            <div id="branchlessNoa" class="text-sm md:text-lg font-black text-slate-800">0</div>
          </div>
        </div>
        <div class="overflow-x-auto custom-scrollbar max-h-[560px]">
          <table class="w-full text-left" style="min-width: 1000px;">
            <thead class="sticky top-0 z-10">
              <tr>
                <th class="w-[44px] pl-4 text-center">NO</th>
                <th>KODE KANTOR</th>
                <th>NAMA KANTOR</th>
                <th>USER ID</th>
                <th>NAMA USER</th>
                <th>DEVICE</th>
                <th class="text-right">TRX</th>
                <th class="text-right">NOA</th>
                <th class="text-right">NOMINAL</th>
                <th class="text-right">ADM</th>
                <th class="pr-4">LAST TRX</th>
              </tr>
            </thead>
            <tbody id="bodyBranchlessRekap" class="divide-y divide-gray-50"></tbody>
          </table>
        </div>
        <div class="flex flex-col gap-2 border-t border-gray-100 bg-white px-3 md:px-4 py-3 md:flex-row md:items-center md:justify-between">
          <div id="branchlessPaginationInfo" class="text-[11px] md:text-xs text-slate-500 font-semibold">Belum ada data.</div>
          <div class="flex items-center gap-2">
            <button type="button" id="btnBranchlessPrev" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] md:text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Sebelumnya</button>
            <button type="button" id="btnBranchlessNext" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] md:text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Berikutnya</button>
          </div>
        </div>
    </div>

    <div id="device-view-by-user" class="hidden bg-white rounded-xl md:rounded-2xl border border-gray-100 p-3 md:p-4 card-shadow relative min-h-[220px]">
      <div id="loadBranchlessTopUser" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div class="pb-3 border-b border-gray-100">
        <h2 class="text-[13px] md:text-base font-black text-gray-800">Rekap User Branchless</h2>
        <p class="mt-0.5 text-[10px] md:text-xs text-slate-500 font-medium">Ringkasan performa per AO/User, supaya terlihat satu user membawa berapa device dan total transaksi branchless-nya.</p>
      </div>
      <div class="mt-3 overflow-x-auto custom-scrollbar max-h-[560px]">
        <table class="w-full text-left" style="min-width: 700px;">
          <thead class="sticky top-0 z-10">
            <tr>
              <th class="w-[40px]">NO</th>
              <th>KODE KANTOR</th>
              <th>NAMA KANTOR</th>
              <th>USER ID</th>
              <th>NAMA USER</th>
              <th class="text-right">DEVICE</th>
              <th class="text-right">TRX</th>
              <th class="text-right">NOA</th>
              <th class="text-right">NOMINAL</th>
              <th class="text-right">ADM</th>
            </tr>
          </thead>
          <tbody id="branchlessUserSummary"></tbody>
        </table>
      </div>
    </div>
  </div>

  <div id="section-admin" class="hidden space-y-3 md:space-y-4">
    <div id="adminDenied" class="hidden admin-card p-4 md:p-5">
      <div class="flex items-start gap-3">
        <div class="mt-0.5 rounded-xl bg-red-50 p-2 text-red-600">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
        </div>
        <div>
          <h2 class="text-sm md:text-base font-black text-slate-800">Akses Admin Branchless Khusus Pusat</h2>
          <p class="mt-1 text-xs md:text-sm text-slate-600">Halaman <span class="admin-kbd">/branchless/admin</span> hanya ditampilkan untuk user pusat. Rekap dan chart branchless tetap bisa dipakai seperti biasa.</p>
        </div>
      </div>
    </div>

    <div id="adminContent" class="hidden space-y-3 md:space-y-4">
      <div class="admin-card p-4 md:p-5">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-sm md:text-base font-black text-slate-800">Admin Branchless</h2>
            <p class="mt-1 text-xs md:text-sm text-slate-600">Panel internal untuk template import, perubahan pemakaian device, nonaktif AO, dan penugasan sementara yang bisa otomatis kembali ke user sebelumnya saat masa berlaku habis.</p>
          </div>
          <a href="branchless" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Kembali ke Rekap</a>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-3 gap-3 md:gap-4">
        <div class="admin-card p-4 md:p-5 xl:col-span-2">
          <h3 class="text-sm md:text-base font-black text-slate-800">Tabel yang Perlu Diisi Dulu</h3>
          <div class="mt-3 space-y-3 text-xs md:text-sm text-slate-600">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-3">
              <div class="font-extrabold text-emerald-800">1. `branchless_device_master`</div>
              <p class="mt-1">Isi 1 baris per device logis. Ini identitas utamanya, jadi walaupun raw device berubah karena reset, ID master-nya tetap.</p>
            </div>
            <div class="rounded-xl border border-sky-100 bg-sky-50/70 p-3">
              <div class="font-extrabold text-sky-800">2. `branchless_device_identifier_history`</div>
              <p class="mt-1">Isi mapping raw `device` dari tabel `va` ke device master. Untuk kondisi awal, cukup masukkan device yang aktif hari ini saja.</p>
            </div>
            <div class="rounded-xl border border-violet-100 bg-violet-50/70 p-3">
              <div class="font-extrabold text-violet-800">3. `branchless_device_ao_history`</div>
              <p class="mt-1">Isi AO/User yang pegang device per tanggal berlaku. Kalau ada pengganti sementara, tinggal tambah baris baru dengan tipe penugasan sementara.</p>
            </div>
          </div>
        </div>

        <div class="admin-card p-4 md:p-5">
          <h3 class="text-sm md:text-base font-black text-slate-800">Aturan Awal Input</h3>
          <ul class="mt-3 space-y-2 text-xs md:text-sm text-slate-600">
            <li>Mulai dari kondisi hari ini, tidak perlu backfill histori lama.</li>
            <li><span class="font-bold text-slate-800">`effective_start`</span> isi tanggal mulai berlaku.</li>
            <li><span class="font-bold text-slate-800">`effective_end`</span> biarkan <span class="admin-kbd">NULL</span> kalau masih aktif.</li>
            <li>Kalau AO diganti sementara, cukup buat record baru dengan tanggal mulai dan tanggal selesai. Saat tanggal selesai lewat, query aktif otomatis kembali ke record sebelumnya.</li>
            <li>Kalau device reset, tambah identifier baru tanpa mengubah transaksi lama.</li>
          </ul>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-3 md:gap-4">
        <div class="admin-card p-4 md:p-5">
          <h3 class="text-sm md:text-base font-black text-slate-800">Template Import</h3>
          <p class="mt-1 text-xs md:text-sm text-slate-600">Template bisa dibuka langsung di Excel. Import belum diikat ke database produksi kalau tabel history belum dibuat, tapi formatnya sudah siap.</p>
          <div class="mt-4 flex flex-wrap gap-2">
            <button type="button" onclick="downloadBranchlessTemplate('device')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Download Template Device</button>
            <button type="button" onclick="downloadBranchlessTemplate('temporary')" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 hover:bg-sky-100">Download Template Temporary</button>
          </div>
          <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
            <label class="block text-[11px] md:text-xs font-bold uppercase tracking-wide text-slate-500">Preview File Import</label>
            <input type="file" id="branchlessImportFile" accept=".csv,.xlsx,.xls" class="mt-2 block w-full text-xs text-slate-600">
            <p class="mt-2 text-[11px] md:text-xs text-slate-500">Saat ini file akan dipreview dulu di browser. Setelah tabel history siap di database, endpoint import tinggal kita sambungkan ke panel ini.</p>
          </div>
          <div id="branchlessImportPreview" class="mt-3 text-xs text-slate-500"></div>
        </div>

        <div class="admin-card p-4 md:p-5">
          <h3 class="text-sm md:text-base font-black text-slate-800">Rule Penugasan Sementara</h3>
          <div class="mt-3 space-y-3 text-xs md:text-sm text-slate-600">
            <div class="rounded-xl border border-amber-100 bg-amber-50/80 p-3">
              <div class="font-extrabold text-amber-800">Contoh 2 Hari</div>
              <p class="mt-1">AO A bawa device utama. Lalu AO B pegang sementara dari <span class="admin-kbd">2026-07-11</span> sampai <span class="admin-kbd">2026-07-12</span>. Query aktif cukup baca record yang tanggalnya menutup hari berjalan. Setelah lewat 2 hari, record AO B tidak aktif lagi dan otomatis kembali ke AO A.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
              <div class="font-extrabold text-slate-800">Status yang Disarankan</div>
              <ul class="mt-2 space-y-1">
                <li><span class="admin-kbd">PRIMARY</span> untuk pemegang utama device.</li>
                <li><span class="admin-kbd">TEMPORARY</span> untuk pengganti sementara sesuai range tanggal.</li>
                <li><span class="admin-kbd">REPLACEMENT</span> untuk pergantian permanen.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="admin-card p-4 md:p-5">
        <h3 class="text-sm md:text-base font-black text-slate-800">SQL Create Table</h3>
        <p class="mt-1 text-xs md:text-sm text-slate-600">Ini versi yang sudah disesuaikan untuk kebutuhan pergantian AO sementara dan riwayat device reset.</p>
        <pre class="mt-3 overflow-x-auto rounded-xl bg-slate-950 p-4 text-[11px] leading-5 text-emerald-100"><code>CREATE TABLE branchless_device_master (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    kode_kantor CHAR(3) NOT NULL,
    kode_kankas VARCHAR(20) DEFAULT NULL,
    logical_device_code VARCHAR(50) NOT NULL,
    device_label VARCHAR(100) DEFAULT NULL,
    status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    notes VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_branchless_device_master (kode_kantor, logical_device_code),
    KEY idx_branchless_device_master_kantor (kode_kantor),
    KEY idx_branchless_device_master_kankas (kode_kankas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>

        <pre class="mt-3 overflow-x-auto rounded-xl bg-slate-950 p-4 text-[11px] leading-5 text-sky-100"><code>CREATE TABLE branchless_device_identifier_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    branchless_device_id BIGINT UNSIGNED NOT NULL,
    raw_device_id VARCHAR(100) NOT NULL,
    old_raw_device_id VARCHAR(100) DEFAULT NULL,
    effective_start DATETIME NOT NULL,
    effective_end DATETIME DEFAULT NULL,
    change_reason VARCHAR(100) DEFAULT NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bdi_device (branchless_device_id),
    KEY idx_bdi_raw_device (raw_device_id),
    KEY idx_bdi_effective (effective_start, effective_end),
    CONSTRAINT fk_bdi_master
        FOREIGN KEY (branchless_device_id) REFERENCES branchless_device_master(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>

        <pre class="mt-3 overflow-x-auto rounded-xl bg-slate-950 p-4 text-[11px] leading-5 text-violet-100"><code>CREATE TABLE branchless_device_ao_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    branchless_device_id BIGINT UNSIGNED NOT NULL,
    source_user_id VARCHAR(50) DEFAULT NULL,
    employee_id VARCHAR(50) DEFAULT NULL,
    username VARCHAR(100) DEFAULT NULL,
    ao_name_snapshot VARCHAR(150) NOT NULL,
    assignment_type ENUM('PRIMARY','TEMPORARY','REPLACEMENT') NOT NULL DEFAULT 'PRIMARY',
    role_label VARCHAR(100) DEFAULT NULL,
    effective_start DATETIME NOT NULL,
    effective_end DATETIME DEFAULT NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 1,
    notes VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bah_device (branchless_device_id),
    KEY idx_bah_user (source_user_id, employee_id),
    KEY idx_bah_effective (effective_start, effective_end),
    CONSTRAINT fk_bah_master
        FOREIGN KEY (branchless_device_id) REFERENCES branchless_device_master(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-3 md:gap-4">
        <div class="admin-card p-4 md:p-5">
          <h3 class="text-sm md:text-base font-black text-slate-800">Contoh Insert Awal</h3>
          <pre class="mt-3 overflow-x-auto rounded-xl bg-slate-950 p-4 text-[11px] leading-5 text-amber-100"><code>-- 1. master device logis
INSERT INTO branchless_device_master
    (kode_kantor, kode_kankas, logical_device_code, device_label, status)
VALUES
    ('007', 'MPOS', 'BLS-007-001', 'Branchless 007 - Device 1', 'ACTIVE');

-- 2. raw device aktif per hari ini
INSERT INTO branchless_device_identifier_history
    (branchless_device_id, raw_device_id, effective_start, change_reason, is_current)
VALUES
    (1, '48AD2C2EEC73A9D', '2026-06-24 00:00:00', 'INITIAL MAPPING', 1);

-- 3. AO aktif per hari ini
INSERT INTO branchless_device_ao_history
    (branchless_device_id, source_user_id, employee_id, username, ao_name_snapshot, assignment_type, effective_start, is_current)
VALUES
    (1, '795', '795', 'AGUSHAR', 'AGUS HARTONO', 'PRIMARY', '2026-06-24 00:00:00', 1);</code></pre>
        </div>

        <div class="admin-card p-4 md:p-5">
          <h3 class="text-sm md:text-base font-black text-slate-800">Kalau Ada Pergantian</h3>
          <pre class="mt-3 overflow-x-auto rounded-xl bg-slate-950 p-4 text-[11px] leading-5 text-rose-100"><code>-- Tutup AO lama
UPDATE branchless_device_ao_history
SET effective_end = '2026-07-10 23:59:59',
    is_current = 0
WHERE branchless_device_id = 1
  AND is_current = 1;

-- AO pengganti sementara
INSERT INTO branchless_device_ao_history
    (branchless_device_id, source_user_id, employee_id, username, ao_name_snapshot,
     assignment_type, effective_start, is_current, notes)
VALUES
    (1, '809', '809', 'FAJAR007', 'FAJAR JOKO SUTARNA',
     'TEMPORARY', '2026-07-11 00:00:00', 1, 'Pengganti sementara');

-- Jika raw device reset/ganti
UPDATE branchless_device_identifier_history
SET effective_end = '2026-07-11 23:59:59',
    is_current = 0
WHERE branchless_device_id = 1
  AND is_current = 1;

INSERT INTO branchless_device_identifier_history
    (branchless_device_id, raw_device_id, old_raw_device_id, effective_start, change_reason, is_current)
VALUES
    (1, 'D6C11675067D2B0', '48AD2C2EEC73A9D', '2026-07-12 00:00:00', 'DEVICE RESET', 1);</code></pre>
        </div>
      </div>
    </div>
  </div>

  <div id="branchlessDetailModal" class="fixed inset-0 z-[9998] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeBranchlessDetail()"></div>
    <div class="relative w-full max-w-6xl rounded-2xl bg-white shadow-2xl overflow-hidden">
      <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
        <div>
          <h3 class="text-sm md:text-base font-black text-slate-800">Detail Transaksi Device Branchless</h3>
          <p id="branchlessDetailTitle" class="mt-0.5 text-[11px] md:text-xs text-slate-500 font-medium">Menunggu device dipilih...</p>
        </div>
        <button type="button" onclick="closeBranchlessDetail()" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">Tutup</button>
      </div>
      <div class="relative">
        <div id="loadBranchlessDetail" class="local-loader hidden rounded-none"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
        <div class="overflow-x-auto custom-scrollbar max-h-[60vh]">
          <table class="w-full text-left" style="min-width: 980px;">
            <thead class="sticky top-0 z-10">
              <tr>
                <th class="pl-4">TANGGAL</th>
                <th>JAM</th>
                <th>NO REKENING</th>
                <th>KODE TRX</th>
                <th class="text-right">JUMLAH</th>
                <th class="text-right">ADM</th>
                <th>NO BUKTI</th>
                <th>USER ID</th>
                <th>KANTOR</th>
                <th class="pr-4">KANKAS</th>
              </tr>
            </thead>
            <tbody id="bodyBranchlessDetail"></tbody>
          </table>
        </div>
      </div>
      <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3">
        <div id="branchlessDetailPaginationInfo" class="text-[11px] md:text-xs text-slate-500 font-semibold">Belum ada data.</div>
        <div class="flex items-center gap-2">
          <button type="button" id="btnDetailPrev" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] md:text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Sebelumnya</button>
          <button type="button" id="btnDetailNext" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] md:text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Berikutnya</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Narrative Modal -->
  <div id="modalNarrative" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeNarrative()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-y-auto p-6">
      <button onclick="closeNarrative()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        Narasi Otomatis
      </h3>
      <div id="narrativeContent" class="text-sm text-gray-700 space-y-2"></div>
    </div>
  </div>
</div>

<script>
  const API_URL = './api/transaksi/';
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';
  const CHANNEL = 'BRANCHLESS';
  const BRANCHLESS_MODE = <?= json_encode($branchlessMode) ?>;

  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n||0));
  const fmtCompact = (value, digits = 1) => {
      const num = Number(value || 0);
      const abs = Math.abs(num);
      if (abs >= 1000000000000) return (num / 1000000000000).toFixed(digits) + ' T';
      if (abs >= 1000000000) return (num / 1000000000).toFixed(digits) + ' M';
      if (abs >= 1000000) return (num / 1000000).toFixed(digits) + ' Jt';
      if (abs >= 1000) return (num / 1000).toFixed(digits) + ' Rb';
      return nf.format(num);
  };
  const esc = (s) => String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

  let chartTrendObj = null;
  let chartDonutObj = null;
  let isBranchlessPusat = false;
  let branchlessPage = 1;
  let branchlessLimit = 15;
  let branchlessDetailPage = 1;
  let branchlessCurrentDevice = '';

  const showLoad = (id) => document.getElementById(id)?.classList.remove('hidden');
  const hideLoad = (id) => document.getElementById(id)?.classList.add('hidden');

  function toggleFilter() {
      const form = document.getElementById('formFilterGlobal');
      const label = document.getElementById('filterToggleLabel');
      if (form.classList.contains('hidden')) {
          form.classList.remove('hidden');
          form.classList.add('flex');
          label.textContent = 'Tutup';
      } else {
          form.classList.add('hidden');
          form.classList.remove('flex');
          label.textContent = 'Filter';
      }
  }

  function switchView(view) {
      document.getElementById('section-rekap').classList.add('hidden');
      document.getElementById('section-chart').classList.add('hidden');
      document.getElementById('section-device').classList.add('hidden');
      document.getElementById('section-admin').classList.add('hidden');
      document.getElementById('section-' + view).classList.remove('hidden');
      ['rekap','chart','device'].forEach(t => {
          const btn = document.getElementById('tab-' + t);
          if (!btn || btn.classList.contains('hidden')) return;
          if (t === view) {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-emerald-700';
          } else {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700';
          }
      });
      if (view === 'chart') {
          setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 100);
      }
  }

  function switchDeviceView(view) {
      document.getElementById('device-view-by-device').classList.add('hidden');
      document.getElementById('device-view-by-user').classList.add('hidden');
      document.getElementById('device-view-' + view).classList.remove('hidden');
      ['by-device', 'by-user'].forEach((key) => {
          const btn = document.getElementById('tab-device-' + key);
          if (!btn) return;
          if (key === view) {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-emerald-700';
          } else {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700';
          }
      });
  }

  function isPusatUser(user) {
      const kode = String(user?.kode ?? '').padStart(3, '0');
      return kode === '000' || kode === '099';
  }

  function initBranchlessAdmin(user) {
      isBranchlessPusat = isPusatUser(user || {});
      const denied = document.getElementById('adminDenied');
      const content = document.getElementById('adminContent');
      if (isBranchlessPusat) {
          denied?.classList.add('hidden');
          content?.classList.remove('hidden');
      } else {
          denied?.classList.remove('hidden');
          content?.classList.add('hidden');
      }

      if (BRANCHLESS_MODE === 'admin') {
          switchView('admin');
          if (!isBranchlessPusat) {
              const url = new URL(window.location.href);
              if (url.pathname.endsWith('/branchless/admin')) {
                  window.history.replaceState({}, '', url.pathname.replace(/\/admin$/, ''));
              }
          }
      }
  }

  function openNarrative() {
      document.getElementById('narrativeContent').innerHTML = generateNarrative();
      document.getElementById('modalNarrative').classList.remove('hidden');
  }
  function closeNarrative() {
      document.getElementById('modalNarrative').classList.add('hidden');
  }
  function downloadBranchlessTemplate(type) {
      const rows = type === 'temporary'
          ? [
              ['kode_kantor','device_id','employee_id_lama','ao_lama','employee_id_baru','ao_baru','assignment_type','effective_start','effective_end','notes'],
              ['007','48AD2C2EEC73A9D','795','AGUS HARTONO','809','FAJAR JOKO SUTARNA','TEMPORARY','2026-07-11 00:00:00','2026-07-12 23:59:59','Pengganti 2 hari']
            ]
          : [
              ['kode_kantor','logical_device_code','device_label','raw_device_id','employee_id','username','ao_name_snapshot','effective_start','status','notes'],
              ['007','BLS-007-001','Branchless 007 - Device 1','48AD2C2EEC73A9D','795','AGUSHAR','AGUS HARTONO','2026-06-24 00:00:00','ACTIVE','Initial mapping']
            ];
      const csv = rows.map(row => row.map(col => `"${String(col).replace(/"/g, '""')}"`).join(',')).join('\n');
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = type === 'temporary' ? 'template_branchless_temporary.csv' : 'template_branchless_device.csv';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
  }
  function escNarrative(str) {
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
  }
  function generateNarrative() {
      let html = '';
      const tbody = document.getElementById('bodyUnified');
      const rows = tbody ? tbody.querySelectorAll('tr') : [];
      if (rows.length > 1) {
          const gtRow = rows[0];
          const gtCells = gtRow.querySelectorAll('td');
          const totalNomBlnIni = gtCells[6] ? gtCells[6].textContent.trim() : '-';
          const totalGrowthMom = gtCells[7] ? gtCells[7].textContent.trim() : '-';
          const totalNomThnIni = gtCells[3] ? gtCells[3].textContent.trim() : '-';
          const totalGrowthYoy = gtCells[4] ? gtCells[4].textContent.trim() : '-';
          html += '<p class="font-bold text-gray-800">Ringkasan Branchless Banking:</p>';
          html += '<ul class="list-disc list-inside space-y-1">';
          html += '<li>Total nominal bulan ini: <strong>' + escNarrative(totalNomBlnIni) + '</strong> (Growth MoM: ' + escNarrative(totalGrowthMom) + ')</li>';
          html += '<li>Total nominal tahun ini: <strong>' + escNarrative(totalNomThnIni) + '</strong> (Growth YoY: ' + escNarrative(totalGrowthYoy) + ')</li>';
          if (rows.length > 2) {
              const topRow = rows[1].querySelectorAll('td');
              const topNama = topRow[1] ? topRow[1].textContent.trim() : '-';
              const topNom = topRow[6] ? topRow[6].textContent.trim() : '-';
              html += '<li>Top performer: <strong>' + escNarrative(topNama) + '</strong> dengan nominal ' + escNarrative(topNom) + '</li>';
          }
          html += '</ul>';
      } else {
          html += '<p class="text-gray-400">Data belum tersedia. Silakan muat data terlebih dahulu.</p>';
      }
      return html;
  }

  async function getLastHarianData() {
      try { const r = await fetch(API_DATE); const j = await r.json(); return j.data || null; }
      catch { return null; }
  }

  window.addEventListener('DOMContentLoaded', async () => {
    const user = (window.getUser && window.getUser()) || null;
    initBranchlessAdmin(user);
    let uKode = user?.kode ? String(user.kode).padStart(3,'0') : '000';
    if(uKode === '099') uKode = '000';

    if (uKode === '000') {
        await loadCabangList();
        document.getElementById('opt_area').value = 'KONSOLIDASI';
    } else {
        document.getElementById('opt_area').innerHTML = '<option value="' + uKode + '">CABANG ' + uKode + '</option>';
        document.getElementById('opt_area').disabled = true;
    }

    const dateData = await getLastHarianData();
    const today = new Date();

    document.getElementById('harian_date').value = today.toISOString().split('T')[0];
    const prevMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
    document.getElementById('closing_date').value = prevMonthEnd.getFullYear() + '-' + String(prevMonthEnd.getMonth()+1).padStart(2,'0') + '-' + String(prevMonthEnd.getDate()).padStart(2,'0');

    if (dateData) {
        if (dateData.last_created) document.getElementById('harian_date').value = dateData.last_created;
        if (dateData.last_closing) document.getElementById('closing_date').value = dateData.last_closing;
    }

    document.getElementById('branchlessImportFile')?.addEventListener('change', async (event) => {
        const file = event.target.files?.[0];
        const preview = document.getElementById('branchlessImportPreview');
        if (!preview) return;
        if (!file) {
            preview.innerHTML = '';
            return;
        }
        preview.innerHTML = '<div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">File dipilih: <span class="font-bold text-slate-700">' + esc(file.name) + '</span></div>';
        if (!/\.csv$/i.test(file.name)) {
            preview.innerHTML += '<div class="mt-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-amber-700">Preview langsung saat ini khusus file CSV. File Excel tetap bisa dipakai sebagai format acuan.</div>';
            return;
        }
        const text = await file.text();
        const lines = text.split(/\r?\n/).filter(Boolean).slice(0, 6);
        if (!lines.length) return;
        const tableRows = lines.map(line => line.split(',').map(col => esc(col.replace(/^"|"$/g, '').replace(/""/g, '"'))));
        preview.innerHTML += '<div class="mt-2 overflow-x-auto rounded-xl border border-slate-200"><table class="w-full text-left text-[11px]"><tbody>'
            + tableRows.map((cols, rowIdx) => '<tr class="' + (rowIdx === 0 ? 'bg-slate-100 font-bold' : '') + '">' + cols.map(col => '<td class="border-b border-slate-100 px-2 py-1.5">' + col + '</td>').join('') + '</tr>').join('')
            + '</tbody></table></div>';
    });

    initCharts();
    runFullSync();
  });

  async function loadCabangList() {
    try {
        const res = await fetch(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
        const json = await res.json();
        let html = '';
        (json.data || []).filter(x => x.kode_kantor && x.kode_kantor !== '000').sort((a,b) => a.kode_kantor.localeCompare(b.kode_kantor)).forEach(it => {
            html += '<option value="' + String(it.kode_kantor).padStart(3,'0') + '" class="text-slate-700">' + String(it.kode_kantor).padStart(3,'0') + ' - ' + it.nama_kantor + '</option>';
        });
        document.getElementById('opt_cabang_list').innerHTML = html;
    } catch(e){}
  }

  function parseAreaValue() {
      const val = document.getElementById('opt_area').value;
      let kode_kantor = ''; let korwil = '';
      if (val.startsWith('KORWIL_')) { korwil = val.replace('KORWIL_', ''); }
      else if (val !== 'KONSOLIDASI') { kode_kantor = val; }
      return { kode_kantor, korwil };
  }

  document.getElementById('formFilterGlobal').addEventListener('submit', e => {
      e.preventDefault();
      branchlessPage = 1;
      branchlessDetailPage = 1;
      runFullSync();
  });

  document.getElementById('btnBranchlessPrev')?.addEventListener('click', () => {
      if (branchlessPage > 1) {
          branchlessPage--;
          fetchBranchlessRekap();
      }
  });
  document.getElementById('btnBranchlessNext')?.addEventListener('click', () => {
      branchlessPage++;
      fetchBranchlessRekap();
  });
  document.getElementById('btnDetailPrev')?.addEventListener('click', () => {
      if (branchlessDetailPage > 1) {
          branchlessDetailPage--;
          fetchBranchlessDetail();
      }
  });
  document.getElementById('btnDetailNext')?.addEventListener('click', () => {
      branchlessDetailPage++;
      fetchBranchlessDetail();
  });

  async function runFullSync() {
      fetchBranchlessRekap();
      fetchTrend();
      fetchDistribusi();
      fetchUnifiedBreakdown();
  }

  function renderUserSummary(items) {
      const el = document.getElementById('branchlessUserSummary');
      if (!el) return;
      if (!items || items.length === 0) {
          el.innerHTML = '<tr><td colspan="10" class="py-6 text-center text-slate-400">Belum ada data branchless pada range ini.</td></tr>';
          return;
      }
      el.innerHTML = items.map((item, index) => {
          return '<tr>'
              + '<td class="text-slate-400">' + (index + 1) + '</td>'
              + '<td class="font-bold text-slate-700">' + esc(item.kode_kantor || '-') + '</td>'
              + '<td class="font-bold text-slate-700">' + esc(item.nama_kantor || '-') + '</td>'
              + '<td class="font-bold text-slate-700">' + esc(item.user_id || '-') + '</td>'
              + '<td class="font-bold text-slate-700">' + esc(item.ao_name || ('User ' + (item.user_id || '-'))) + '</td>'
              + '<td class="text-right">' + fmt(item.total_device || 0) + '</td>'
              + '<td class="text-right">' + fmt(item.total_trx || 0) + '</td>'
              + '<td class="text-right">' + fmt(item.total_noa || 0) + '</td>'
              + '<td class="text-right font-black text-emerald-700">' + fmt(item.total_nominal || 0) + '</td>'
              + '<td class="text-right">' + fmt(item.total_adm || 0) + '</td>'
              + '</tr>';
      }).join('');
  }

  async function fetchBranchlessRekap() {
      showLoad('loadBranchlessRekap');
      showLoad('loadBranchlessTopUser');
      const area = parseAreaValue();
      const payload = {
          type: 'rekap_branchless_bulanan',
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date').value,
          kode_kantor: area.kode_kantor,
          korwil: area.korwil,
          page: branchlessPage,
          limit: branchlessLimit
      };
      try {
          const res = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const json = await res.json();
          const tbody = document.getElementById('bodyBranchlessRekap');
          const info = document.getElementById('branchlessPaginationInfo');
          if (json.status !== 200 || !json.data) {
              tbody.innerHTML = '<tr><td colspan="11" class="py-6 text-center text-slate-400">Data branchless belum tersedia.</td></tr>';
              renderUserSummary([]);
              info.textContent = 'Belum ada data.';
              document.getElementById('btnBranchlessPrev').disabled = true;
              document.getElementById('btnBranchlessNext').disabled = true;
              return;
          }

          const gt = json.data.grand_total || {};
          document.getElementById('branchlessNominal').textContent = 'Rp ' + fmtCompact(gt.total_nominal || 0, 2);
          document.getElementById('branchlessTrx').textContent = fmt(gt.total_trx || 0);
          document.getElementById('branchlessDevice').textContent = fmt(gt.total_device || 0);
          document.getElementById('branchlessUser').textContent = fmt(gt.total_user || 0);
          document.getElementById('branchlessNoa').textContent = fmt(gt.total_noa || 0);

          const rows = json.data.data || [];
          if (rows.length === 0) {
              tbody.innerHTML = '<tr><td colspan="11" class="py-6 text-center text-slate-400">Data branchless belum tersedia.</td></tr>';
              document.getElementById('btnBranchlessPrev').disabled = true;
              document.getElementById('btnBranchlessNext').disabled = true;
          } else {
              const startNo = ((json.data.pagination?.current_page || 1) - 1) * (json.data.pagination?.per_page || branchlessLimit);
              tbody.innerHTML = rows.map((row, idx) => {
                  const rowNo = startNo + idx + 1;
                  const deviceBtn = '<button type="button" class="text-left font-black text-emerald-700 hover:text-emerald-900 hover:underline" onclick="openBranchlessDetail(' + "'" + String(row.device_id || '').replace(/'/g, "\\'") + "'" + ',' + "'" + String(row.ao_name || '').replace(/'/g, "\\'") + "'" + ')">' + esc(row.device_id || '-') + '</button>';
                  return '<tr>'
                      + '<td class="pl-4 text-center text-slate-400">' + rowNo + '</td>'
                      + '<td class="font-black text-slate-800">' + esc(row.kode_kantor || '-') + '</td>'
                      + '<td><div class="font-bold text-slate-700">' + esc(row.nama_kantor || '-') + '</div></td>'
                      + '<td class="font-bold text-slate-700">' + esc(row.user_id || '-') + '</td>'
                      + '<td class="font-bold text-slate-700">' + esc(row.ao_name || '-') + '</td>'
                      + '<td>' + deviceBtn + '</td>'
                      + '<td class="text-right">' + fmt(row.total_trx || 0) + '</td>'
                      + '<td class="text-right">' + fmt(row.total_noa || 0) + '</td>'
                      + '<td class="text-right font-black text-blue-700">' + fmt(row.total_nominal || 0) + '</td>'
                      + '<td class="text-right">' + fmt(row.total_adm || 0) + '</td>'
                      + '<td class="pr-4 font-semibold text-slate-500">' + esc(row.last_transaksi || '-') + '</td>'
                      + '</tr>';
              }).join('');
          }

          renderUserSummary(json.data.top_users || []);

          const pg = json.data.pagination || {};
          const totalPages = pg.total_pages || 1;
          if (branchlessPage > totalPages && totalPages > 0) {
              branchlessPage = totalPages;
              return fetchBranchlessRekap();
          }
          info.textContent = 'Halaman ' + (pg.current_page || 1) + ' dari ' + totalPages + ' • ' + fmt(pg.total_records || 0) + ' baris';
          document.getElementById('btnBranchlessPrev').disabled = (pg.current_page || 1) <= 1;
          document.getElementById('btnBranchlessNext').disabled = (pg.current_page || 1) >= totalPages;
      } catch (e) {
          console.error(e);
          document.getElementById('bodyBranchlessRekap').innerHTML = '<tr><td colspan="11" class="py-6 text-center text-slate-400">Gagal memuat rekap branchless.</td></tr>';
          renderUserSummary([]);
          document.getElementById('btnBranchlessPrev').disabled = true;
          document.getElementById('btnBranchlessNext').disabled = true;
      } finally {
          hideLoad('loadBranchlessRekap');
          hideLoad('loadBranchlessTopUser');
      }
  }

  function openBranchlessDetail(deviceId, aoName) {
      branchlessCurrentDevice = deviceId || '';
      branchlessDetailPage = 1;
      document.getElementById('branchlessDetailTitle').textContent = 'Device: ' + (deviceId || '-') + ' • User: ' + (aoName || '-');
      const modal = document.getElementById('branchlessDetailModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      fetchBranchlessDetail();
  }

  function closeBranchlessDetail() {
      const modal = document.getElementById('branchlessDetailModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }

  async function fetchBranchlessDetail() {
      if (!branchlessCurrentDevice) return;
      showLoad('loadBranchlessDetail');
      const area = parseAreaValue();
      const payload = {
          type: 'detail_device_branchless',
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date').value,
          kode_kantor: area.kode_kantor,
          korwil: area.korwil,
          device_id: branchlessCurrentDevice,
          page: branchlessDetailPage,
          limit: 15
      };
      try {
          const res = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const json = await res.json();
          const tbody = document.getElementById('bodyBranchlessDetail');
          if (json.status !== 200 || !json.data) {
              tbody.innerHTML = '<tr><td colspan="10" class="py-6 text-center text-slate-400">Detail device belum tersedia.</td></tr>';
              document.getElementById('btnDetailPrev').disabled = true;
              document.getElementById('btnDetailNext').disabled = true;
              return;
          }
          const rows = json.data.data || [];
          tbody.innerHTML = rows.length === 0
              ? '<tr><td colspan="10" class="py-6 text-center text-slate-400">Detail device belum tersedia.</td></tr>'
              : rows.map(row => '<tr>'
                  + '<td class="pl-4">' + esc(row.tgl_transaksi || '-') + '</td>'
                  + '<td>' + esc(row.jam_transaksi || '-') + '</td>'
                  + '<td>' + esc(row.no_rekening || '-') + '</td>'
                  + '<td>' + esc(row.kode_transaksi || '-') + '</td>'
                  + '<td class="text-right">' + fmt(row.jumlah || 0) + '</td>'
                  + '<td class="text-right">' + fmt(row.adm || 0) + '</td>'
                  + '<td>' + esc(row.no_bukti || '-') + '</td>'
                  + '<td>' + esc(row.user_id || '-') + '</td>'
                  + '<td>' + esc(row.kantor || '-') + '</td>'
                  + '<td class="pr-4">' + esc(row.kankas || '-') + '</td>'
                  + '</tr>').join('');

          const pg = json.data.pagination || {};
          const totalPages = pg.total_pages || 1;
          if (branchlessDetailPage > totalPages && totalPages > 0) {
              branchlessDetailPage = totalPages;
              return fetchBranchlessDetail();
          }
          document.getElementById('branchlessDetailPaginationInfo').textContent = 'Halaman ' + (pg.current_page || 1) + ' dari ' + totalPages + ' • ' + fmt(pg.total_records || 0) + ' transaksi';
          document.getElementById('btnDetailPrev').disabled = (pg.current_page || 1) <= 1;
          document.getElementById('btnDetailNext').disabled = (pg.current_page || 1) >= totalPages;
      } catch (e) {
          console.error(e);
          document.getElementById('bodyBranchlessDetail').innerHTML = '<tr><td colspan="10" class="py-6 text-center text-slate-400">Gagal memuat detail device.</td></tr>';
          document.getElementById('btnDetailPrev').disabled = true;
          document.getElementById('btnDetailNext').disabled = true;
      } finally {
          hideLoad('loadBranchlessDetail');
      }
  }

  function initCharts() {
      chartTrendObj = new ApexCharts(document.querySelector('#chartTrend'), {
          series: [], chart: { type: 'area', height: 340, toolbar: { show: false }, zoom: { enabled: false } },
          colors: ['#10b981'], dataLabels: { enabled: false }, legend: { show: false },
          stroke: { curve: 'smooth', width: 3.5, lineCap: 'round' },
          markers: { size: 4, strokeWidth: 2, hover: { size: 6 }, colors: ['#ffffff'], strokeColors: ['#10b981'] },
          fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 100] } },
          grid: { borderColor: '#d1fae5', strokeDashArray: 4, padding: { bottom: 15, left: 10, right: 12, top: 8 } },
          xaxis: { categories: [], labels: { style: { fontSize: '10px', fontWeight: 700, colors: '#64748b' } }, axisBorder: { show: false }, axisTicks: { show: false } },
          yaxis: { labels: { formatter: (val) => fmtCompact(val, val >= 1000000000 ? 1 : 0), style: { fontSize: '10px', fontWeight: 700, colors: ['#94a3b8'] } } },
          tooltip: { y: { formatter: (val) => 'Rp ' + nf.format(val) } }
      });
      chartTrendObj.render();

      chartDonutObj = new ApexCharts(document.querySelector('#chartDonut'), {
          series: [], chart: { type: 'donut', height: 280 }, labels: [],
          colors: ['#10b981','#0ea5e9','#f59e0b','#ef4444','#64748b','#1d4ed8'],
          plotOptions: { pie: { donut: { size: '70%', labels: { show: true, name: { show: true, fontSize: '12px', fontWeight: 800, color: '#64748b' }, value: { show: true, fontSize: '18px', fontWeight: 900, color: '#0f172a', formatter: (val) => 'Rp ' + fmtCompact(val, 1) }, total: { show: true, label: 'Total', fontSize: '12px', fontWeight: 800, color: '#64748b', formatter: function(w) { const total = w.globals.seriesTotals.reduce((sum, item) => sum + item, 0); return 'Rp ' + fmtCompact(total, 1); } } } } } }, dataLabels: { enabled: false },
          legend: { show: true, position: 'bottom', fontSize: '9.5px' }
      });
      chartDonutObj.render();
  }

  async function fetchTrend() {
      showLoad('loadTrend');
      const area = parseAreaValue();
      const periode = document.getElementById('trendPeriode').value;
      const isMonthly = periode === 'bulanan';
      const payload = { type: 'tren_nominal_va', harian_date: document.getElementById('harian_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, periode, channel: CHANNEL };
      try {
          const r = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await r.json();
          if(j.status===200 && j.data && j.data.chart_nominal) {
              chartTrendObj.updateOptions({
                  xaxis: { categories: j.data.chart_nominal.labels, labels: { rotate: isMonthly ? 0 : -25, hideOverlappingLabels: false, style: { fontSize: isMonthly ? '10px' : '9px', fontWeight: 700, colors: '#64748b' } } },
                  markers: { size: isMonthly ? 5 : 3, hover: { size: isMonthly ? 7 : 5 } },
                  dataLabels: { enabled: isMonthly, offsetY: -10, background: { enabled: true, foreColor: '#0f172a', borderRadius: 8, padding: 6, opacity: 0.88, borderWidth: 1, borderColor: '#bbf7d0' }, style: { fontSize: '10px', fontWeight: 900, colors: ['#047857'] }, formatter: (val) => fmtCompact(val, val >= 1000000000 ? 3 : 1) },
                  fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: isMonthly ? 0.22 : 0.3, opacityTo: 0.04, stops: [0, 100] } }
              });
              document.getElementById('trendNote')?.classList.toggle('hidden', !isMonthly);
              chartTrendObj.updateSeries(j.data.chart_nominal.series);
          }
      } catch(e){} finally { hideLoad('loadTrend'); }
  }

  async function fetchDistribusi() {
      showLoad('loadDist');
      const area = parseAreaValue();
      const payload = { type: 'distribusi_va', harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: CHANNEL };
      try {
          const r = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await r.json();
          if(j.status===200 && j.data) {
              const d = j.data;
              chartDonutObj.updateOptions({ labels: d.donut_chart.labels });
              chartDonutObj.updateSeries(d.donut_chart.series);
              const listC = document.getElementById('listTop5'); listC.innerHTML = '';
              const colors = ['bg-violet-500','bg-sky-500','bg-emerald-500','bg-amber-500','bg-rose-500'];
              if(!d.top_5 || d.top_5.length===0) { listC.innerHTML = '<p class="text-xs text-slate-400">Data kosong</p>'; return; }
              const maxNom = d.top_5[0].nominal;
              d.top_5.forEach((t,i) => {
                  const wPct = (t.nominal/maxNom)*100;
                  let fNom = t.nominal >= 1000000000 ? (t.nominal/1000000000).toFixed(2)+' M' : (t.nominal >= 1000000 ? (t.nominal/1000000).toFixed(1)+' jt' : nf.format(t.nominal));
                  listC.innerHTML += '<div class="flex flex-col text-xs"><div class="flex justify-between items-end mb-1"><div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full ' + colors[i%colors.length] + ' text-white flex items-center justify-center text-[9px] font-bold">' + (i+1) + '</span><span class="font-bold text-slate-700">' + esc(t.label) + '</span></div><div class="text-right leading-none"><span class="font-black text-slate-800">Rp ' + fNom + '</span><br><span class="text-[9px] text-slate-400">' + nf.format(t.trx) + ' Trx</span></div></div><div class="w-full bg-slate-100 rounded-full h-1.5"><div class="' + colors[i%colors.length] + ' h-1.5 rounded-full" style="width:' + wPct + '%"></div></div></div>';
              });
          }
      } catch(e){} finally { hideLoad('loadDist'); }
  }

  async function fetchUnifiedBreakdown() {
      showLoad('loadTable');
      const area = parseAreaValue();
      const basePl = { harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: CHANNEL };
      try {
          const [resMom, resYoy] = await Promise.all([
              fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'detail_breakdown_transaksi', ...basePl}) }),
              fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'yoy_transaksi', ...basePl}) })
          ]);
          const jMom = await resMom.json();
          const jYoy = await resYoy.json();
          const tbody = document.getElementById('bodyUnified');
          tbody.innerHTML = '';

          // Remove any previous warning banner
          const prevWarning = tbody.closest('.overflow-x-auto').previousElementSibling;
          if (prevWarning && prevWarning.classList.contains('partial-fetch-warning')) prevWarning.remove();

          // Show warning if either data source failed
          let warningHtml = '';
          if (jMom.status !== 200) warningHtml += '<div class="bg-amber-50 border border-amber-200 text-amber-700 px-3 py-2 rounded text-xs font-bold mb-2">Data MoM tidak tersedia</div>';
          if (jYoy.status !== 200) warningHtml += '<div class="bg-amber-50 border border-amber-200 text-amber-700 px-3 py-2 rounded text-xs font-bold mb-2">Data YoY tidak tersedia</div>';
          if (warningHtml) {
              const warningContainer = document.createElement('div');
              warningContainer.className = 'px-4 pt-3 partial-fetch-warning';
              warningContainer.innerHTML = warningHtml;
              tbody.closest('.overflow-x-auto').insertAdjacentElement('beforebegin', warningContainer);
          }

          const optAreaVal = document.getElementById('opt_area').value;

          // Build merged data map by cabang kode
          const mergedMap = new Map();

          // Extract cabang from MoM data
          if (jMom.status === 200 && jMom.data && jMom.data.data) {
              const dt = jMom.data.data;
              if (optAreaVal === 'KONSOLIDASI') {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: cb.curr_nom || 0, mom_prev_nom: cb.prev_nom || 0, mom_growth: cb.growth_nom || 0, yoy_curr_nom: 0, yoy_prev_nom: 0, yoy_growth: 0 });
                      });
                  });
              } else if (optAreaVal.startsWith('KORWIL_')) {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: cb.curr_nom || 0, mom_prev_nom: cb.prev_nom || 0, mom_growth: cb.growth_nom || 0, yoy_curr_nom: 0, yoy_prev_nom: 0, yoy_growth: 0 });
                      });
                  });
              } else {
                  // Specific cabang - show kankas breakdown
                  dt.forEach(kk => {
                      mergedMap.set(kk.kode || kk.nama, { kode: kk.kode || '', nama: kk.nama, mom_curr_nom: kk.curr_nom || 0, mom_prev_nom: kk.prev_nom || 0, mom_growth: kk.growth_nom || 0, yoy_curr_nom: 0, yoy_prev_nom: 0, yoy_growth: 0 });
                  });
              }
          }

          // Enrich with YoY data
          if (jYoy.status === 200 && jYoy.data && jYoy.data.data) {
              const dt = jYoy.data.data;
              if (optAreaVal === 'KONSOLIDASI') {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          if (mergedMap.has(cb.kode)) {
                              const existing = mergedMap.get(cb.kode);
                              existing.yoy_curr_nom = cb.curr_nom || 0;
                              existing.yoy_prev_nom = cb.prev_nom || 0;
                              existing.yoy_growth = cb.yoy_growth_nom || 0;
                          } else {
                              mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: 0, mom_prev_nom: 0, mom_growth: 0, yoy_curr_nom: cb.curr_nom || 0, yoy_prev_nom: cb.prev_nom || 0, yoy_growth: cb.yoy_growth_nom || 0 });
                          }
                      });
                  });
              } else if (optAreaVal.startsWith('KORWIL_')) {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          if (mergedMap.has(cb.kode)) {
                              const existing = mergedMap.get(cb.kode);
                              existing.yoy_curr_nom = cb.curr_nom || 0;
                              existing.yoy_prev_nom = cb.prev_nom || 0;
                              existing.yoy_growth = cb.yoy_growth_nom || 0;
                          } else {
                              mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: 0, mom_prev_nom: 0, mom_growth: 0, yoy_curr_nom: cb.curr_nom || 0, yoy_prev_nom: cb.prev_nom || 0, yoy_growth: cb.yoy_growth_nom || 0 });
                          }
                      });
                  });
              } else {
                  dt.forEach(kk => {
                      const key = kk.kode || kk.nama;
                      if (mergedMap.has(key)) {
                          const existing = mergedMap.get(key);
                          existing.yoy_curr_nom = kk.curr_nom || 0;
                          existing.yoy_prev_nom = kk.prev_nom || 0;
                          existing.yoy_growth = kk.yoy_growth_nom || 0;
                      } else {
                          mergedMap.set(key, { kode: kk.kode || '', nama: kk.nama, mom_curr_nom: 0, mom_prev_nom: 0, mom_growth: 0, yoy_curr_nom: kk.curr_nom || 0, yoy_prev_nom: kk.prev_nom || 0, yoy_growth: kk.yoy_growth_nom || 0 });
                      }
                  });
              }
          }

          if (mergedMap.size === 0) {
              tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Data kosong.</td></tr>';
              return;
          }

          // Render Grand Total row
          const gtMom = (jMom.status === 200 && jMom.data) ? jMom.data.grand_total : null;
          const gtYoy = (jYoy.status === 200 && jYoy.data) ? jYoy.data.grand_total : null;
          const gtYoyPrev = gtYoy ? (gtYoy.prev_nom || 0) : 0;
          const gtYoyCurr = gtYoy ? (gtYoy.curr_nom || 0) : 0;
          const gtYoyGrowth = gtYoy ? (gtYoy.yoy_growth_nom || 0) : 0;
          const gtMomPrev = gtMom ? (gtMom.prev_nom || 0) : 0;
          const gtMomCurr = gtMom ? (gtMom.curr_nom || 0) : 0;
          const gtMomGrowth = gtMom ? (gtMom.growth_nom || 0) : 0;

          const growthBadge = (g) => {
              if (g > 0) return '<span class="text-emerald-600 font-bold">▲ ' + Math.abs(g) + '%</span>';
              if (g < 0) return '<span class="text-red-600 font-bold">▼ ' + Math.abs(g) + '%</span>';
              return '-';
          };

          tbody.innerHTML += '<tr class="bg-emerald-50 font-bold"><td class="text-center pl-4">-</td><td class="font-black text-slate-900">GRAND TOTAL</td><td class="text-right">' + fmt(gtYoyPrev) + '</td><td class="text-right text-emerald-700">' + fmt(gtYoyCurr) + '</td><td class="text-center text-[11px]">' + growthBadge(gtYoyGrowth) + '</td><td class="text-right">' + fmt(gtMomPrev) + '</td><td class="text-right text-emerald-700">' + fmt(gtMomCurr) + '</td><td class="text-center text-[11px] pr-4">' + growthBadge(gtMomGrowth) + '</td></tr>';

          // Render data rows
          let idx = 1;
          mergedMap.forEach((row) => {
              tbody.innerHTML += '<tr><td class="text-center pl-4 text-slate-400">' + idx + '</td><td class="font-bold text-slate-700">' + esc(row.nama) + '</td><td class="text-right text-slate-500">' + fmt(row.yoy_prev_nom) + '</td><td class="text-right text-blue-700">' + fmt(row.yoy_curr_nom) + '</td><td class="text-center text-[11px]">' + growthBadge(row.yoy_growth) + '</td><td class="text-right text-slate-500">' + fmt(row.mom_prev_nom) + '</td><td class="text-right text-blue-700">' + fmt(row.mom_curr_nom) + '</td><td class="text-center text-[11px] pr-4">' + growthBadge(row.mom_growth) + '</td></tr>';
              idx++;
          });
      } catch(e){ console.error(e); document.getElementById('bodyUnified').innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Gagal memuat data.</td></tr>'; } finally { hideLoad('loadTable'); }
  }
</script>
