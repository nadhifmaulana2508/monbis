<div class="w-full min-h-screen bg-slate-50 p-3 md:p-5 font-sans text-slate-900">
  <div class="mx-auto flex h-[calc(100vh-32px)] w-full max-w-7xl flex-col gap-3 md:h-[calc(100vh-40px)] md:gap-4">

    <!-- HEADER -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between gap-3 px-3 py-3 md:px-4">

        <!-- TITLE -->
        <div class="flex min-w-0 items-center gap-2 md:gap-3">
          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white shadow-md md:h-10 md:w-10">
            <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 13h4v8H3v-8zm7-6h4v14h-4V7zm7-4h4v18h-4V3z"></path>
            </svg>
          </div>

          <div class="flex min-w-0 items-center gap-2">
            <h1 class="truncate text-base font-black leading-tight tracking-tight text-slate-900 md:text-xl">
              Realisasi AO
            </h1>

            <button
              type="button"
              onclick="toggleInfoPanel(event)"
              onmouseenter="showInfoPanel()"
              onmouseleave="scheduleCloseInfoPanel()"
              class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-blue-500 text-[10px] font-black text-white hover:bg-blue-600"
              title="Panduan kolom"
            >
              i
            </button>
          </div>
        </div>

        <!-- DESKTOP FILTER -->
        <div class="hidden items-end gap-2 lg:flex">
          <div class="flex flex-col gap-1">
            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700">Closing (M-1)</label>
            <input
              type="date"
              id="tgl_awal"
              onchange="fetchTopData(1)"
              class="h-9 w-32 rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
          </div>

          <div class="flex flex-col gap-1">
            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700">Harian (Actual)</label>
            <input
              type="date"
              id="tgl_akhir"
              onchange="syncClosingFromHarian(); syncMobileFiltersFromDesktop(); fetchTopData(1)"
              class="h-9 w-32 rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
          </div>

          <div class="flex flex-col gap-1">
              <label class="text-[9px] font-black uppercase tracking-widest text-slate-700">Area/Cabang</label>
            <select
              id="filter_kantor"
              onchange="syncMobileFiltersFromDesktop(); fetchTopData(1)"
              class="h-9 w-48 rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            ></select>
          </div>

          <button
            onclick="exportFullData()"
            class="flex h-9 w-10 items-center justify-center rounded-lg bg-emerald-600 text-white transition hover:bg-emerald-700 disabled:cursor-wait disabled:opacity-70"
            title="Export Excel"
          >
            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <path d="M14 2v6h6"></path>
              <path d="M8 13l4 4"></path>
              <path d="M12 13l-4 4"></path>
              <path d="M15 13h2"></path>
              <path d="M15 17h2"></path>
            </svg>
          </button>
        </div>

        <!-- MOBILE FILTER BUTTON -->
        <button
          type="button"
          onclick="toggleMobileFilter()"
          class="flex h-9 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-[11px] font-black text-slate-700 shadow-sm lg:hidden"
        >
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
          </svg>
          Filter
        </button>
      </div>

      <!-- MOBILE FILTER PANEL -->
      <div id="mobileFilterPanel" class="hidden border-t border-slate-200 px-3 pb-3 lg:hidden">
        <div class="grid grid-cols-2 gap-2 pt-3">

          <div class="flex flex-col gap-1">
            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700">Closing</label>
            <input
              type="date"
              id="tgl_awal_mobile"
              onchange="syncDateFromMobile(); fetchTopData(1)"
              class="h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-[11px] font-bold text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
          </div>

          <div class="flex flex-col gap-1">
            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700">Harian</label>
            <input
              type="date"
              id="tgl_akhir_mobile"
              onchange="syncDateFromMobile(); fetchTopData(1)"
              class="h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-[11px] font-bold text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
          </div>

          <div class="col-span-2 grid grid-cols-[1fr_44px] gap-2">
            <div class="flex min-w-0 flex-col gap-1">
            <label class="text-[9px] font-black uppercase tracking-widest text-slate-700">Area/Cabang</label>
              <select
                id="filter_kantor_mobile"
                onchange="syncKantorFromMobile(); fetchTopData(1)"
                class="h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-[11px] font-bold text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
              ></select>
            </div>

            <div class="flex flex-col justify-end">
              <button
                onclick="exportFullData()"
                class="flex h-9 w-11 items-center justify-center rounded-lg bg-emerald-600 text-white transition hover:bg-emerald-700 disabled:cursor-wait disabled:opacity-70"
                title="Export Excel"
              >
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <path d="M14 2v6h6"></path>
                  <path d="M8 13l4 4"></path>
                  <path d="M12 13l-4 4"></path>
                  <path d="M15 13h2"></path>
                  <path d="M15 17h2"></path>
                </svg>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- INFO PANEL -->
    <div
      id="infoPanel"
      onmouseenter="cancelCloseInfoPanel()"
      onmouseleave="scheduleCloseInfoPanel()"
      class="fixed left-3 right-3 top-16 z-[9998] hidden rounded-xl border border-slate-200 bg-white p-4 shadow-2xl md:left-auto md:right-auto md:top-24 md:w-[360px]"
    >
      <div class="mb-3 flex items-center justify-between border-b border-slate-200 pb-2">
        <div class="flex items-center gap-2">
          <span class="text-base">💡</span>
          <h3 class="text-sm font-black text-slate-800">Panduan Kamus Kolom</h3>
        </div>

        <button onclick="closeInfoPanel()" class="text-sm font-black text-slate-400 hover:text-red-500">
          ✕
        </button>
      </div>

      <div class="space-y-2 text-[11px] leading-relaxed text-slate-600">
        <p>
          <b class="text-slate-800">Closing (M-1):</b>
          tanggal penutupan bulan sebelumnya sebagai batas awal monitoring realisasi.
        </p>

        <p>
          <b class="text-slate-800">Harian (Actual):</b>
          posisi data harian yang digunakan dari tabel nominatif.
        </p>

        <p>
          <b class="text-slate-800">Area/Cabang:</b>
          filter kantor cabang. Pilih <b>Konsolidasi</b> untuk melihat seluruh cabang.
        </p>

        <p>
          <b class="text-slate-800">NOA:</b>
          jumlah rekening / debitur yang melakukan realisasi kredit baru.
        </p>

        <p>
          <b class="text-slate-800">Nominal:</b>
          total nominal pencairan kredit dari transaksi realisasi dengan <b>kode_trans 110</b>.
        </p>

        <p>
          <b class="text-slate-800">Nama AO:</b>
          klik nama AO untuk membuka detail rekening realisasi AO tersebut.
        </p>

        <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 text-blue-900">
          <div class="mb-1 text-[11px] font-black">Formula Nominal Realisasi</div>
          <div class="text-[10px]">
            SUM(realisasi_pokok) dari update_realisasi_kredit dengan kode_trans = 110
          </div>
        </div>
      </div>
    </div>

    <!-- TABLE CARD -->
    <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

      <!-- LOADING -->
      <div id="loadingAO" class="absolute inset-0 z-50 hidden items-center justify-center bg-white/80 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-2 text-blue-600">
          <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>
          <span class="text-[10px] font-black uppercase tracking-widest">Memproses...</span>
        </div>
      </div>

      <!-- MAIN TABLE -->
      <div class="min-h-0 flex-1 overflow-auto">
        <table class="w-full min-w-[430px] border-separate border-spacing-0 text-[11px] md:min-w-[760px]">

          <thead>
            <tr>
              <th class="hidden md:table-cell sticky top-0 z-30 border-b border-r border-slate-200 bg-sky-50 px-3 py-3 text-center text-[10px] font-black uppercase tracking-wider text-blue-950">
                NO
              </th>

              <th class="hidden md:table-cell sticky top-0 z-30 border-b border-r border-slate-200 bg-sky-50 px-3 py-3 text-left text-[10px] font-black uppercase tracking-wider text-blue-950">
                KANTOR CABANG
              </th>

              <th class="sticky left-0 top-0 z-40 w-[190px] max-w-[190px] border-b border-r border-slate-200 bg-sky-50 px-3 py-3 text-left text-[10px] font-black uppercase tracking-wider text-blue-950 md:left-auto md:w-auto md:max-w-none">
                NAMA AO
              </th>

              <th class="sticky right-[112px] top-0 z-40 w-[58px] border-b border-r border-slate-200 bg-sky-50 px-2 py-3 text-center text-[10px] font-black uppercase tracking-wider text-blue-950 md:right-auto md:w-auto md:px-3">
                NOA
              </th>

              <th class="sticky right-0 top-0 z-40 w-[112px] border-b border-slate-200 bg-sky-50 px-2 py-3 text-right text-[10px] font-black uppercase tracking-wider text-blue-950 md:right-auto md:w-auto md:px-3">
                NOMINAL
              </th>
            </tr>

            <!-- TOTAL ROW -->
            <tr id="totalRow" class="hidden">
              <th class="hidden md:table-cell sticky top-[41px] z-20 border-b border-r border-blue-300 bg-blue-100 px-3 py-3 text-center text-[10px] font-black uppercase text-blue-950">
                ALL
              </th>

              <th class="hidden md:table-cell sticky top-[41px] z-20 border-b border-r border-blue-300 bg-blue-100 px-3 py-3 text-left text-[11px] font-black uppercase text-blue-950">
                GRAND TOTAL
              </th>

              <th class="sticky left-0 top-[41px] z-30 w-[190px] max-w-[190px] border-b border-r border-blue-300 bg-blue-100 px-3 py-3 text-left text-[10px] font-black uppercase text-blue-950 md:left-auto md:w-auto md:max-w-none">
                <span id="totalAO">0 AO</span>
              </th>

              <th class="sticky right-[112px] top-[41px] z-30 w-[58px] border-b border-r border-blue-300 bg-blue-100 px-2 py-3 text-center text-[11px] font-black text-blue-700 md:right-auto md:w-auto md:px-3">
                <span id="totalNOA">0</span>
              </th>

              <th class="sticky right-0 top-[41px] z-30 w-[112px] border-b border-blue-300 bg-blue-100 px-2 py-3 text-right text-[11px] font-black text-blue-700 md:right-auto md:w-auto md:px-3">
                <span id="totalRealisasi">0</span>
              </th>
            </tr>
          </thead>

          <tbody id="tbodyAO" class="divide-y divide-slate-100"></tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div id="paginationWrap" class="hidden items-center justify-between border-t border-slate-200 bg-slate-50 p-3">
        <span id="pageInfo" class="text-[10px] font-black uppercase text-slate-500">Hal 1 / 1</span>
        <div class="flex gap-1">
          <button
            id="btnPrev"
            onclick="changePage(-1)"
            class="h-8 rounded-lg border border-slate-300 bg-white px-3 text-[10px] font-black text-slate-600 disabled:opacity-30"
          >
            PREV
          </button>
          <button
            id="btnNext"
            onclick="changePage(1)"
            class="h-8 rounded-lg border border-slate-300 bg-white px-3 text-[10px] font-black text-slate-600 disabled:opacity-30"
          >
            NEXT
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL DETAIL -->
<div id="modalDetail" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 p-3 backdrop-blur-sm">
  <div class="flex max-h-[88vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 p-4">
      <div class="min-w-0">
        <h3 id="modalSub" class="truncate font-black text-slate-900">Detail Realisasi AO</h3>
        <p id="modalSummary" class="truncate text-[10px] font-bold text-slate-500">-</p>
      </div>

      <button onclick="closeModal()" class="ml-3 shrink-0 text-xl font-black text-slate-400 hover:text-red-500">
        ✕
      </button>
    </div>

    <div class="overflow-auto">
      <table class="w-full min-w-[390px] border-separate border-spacing-0 text-xs md:min-w-[700px]">
        <thead>
          <tr>
            <th class="hidden md:table-cell sticky top-0 z-20 border-b bg-sky-50 px-4 py-3 text-center text-[10px] font-black uppercase text-blue-950">
              NO
            </th>

            <th class="sticky left-0 top-0 z-30 w-[170px] max-w-[170px] border-b border-r border-slate-200 bg-sky-50 px-3 py-3 text-left text-[10px] font-black uppercase text-blue-950 md:left-auto md:w-auto md:max-w-none md:px-4">
              NAMA
            </th>

            <th class="hidden md:table-cell sticky top-0 z-20 border-b bg-sky-50 px-4 py-3 text-center text-[10px] font-black uppercase text-blue-950">
              REKENING
            </th>

            <th class="sticky right-[105px] top-0 z-30 w-[95px] border-b border-r border-slate-200 bg-sky-50 px-2 py-3 text-center text-[10px] font-black uppercase text-blue-950 md:right-auto md:w-auto md:px-4">
              TANGGAL
            </th>

            <th class="sticky right-0 top-0 z-30 w-[105px] border-b bg-sky-50 px-2 py-3 text-right text-[10px] font-black uppercase text-blue-950 md:right-auto md:w-auto md:px-4">
              PLAFON
            </th>
          </tr>
        </thead>

        <tbody id="modalBody" class="divide-y divide-slate-100"></tbody>
      </table>
    </div>
  </div>
</div>

<script>
  let currentPage = 1;
  let totalPage = 1;
  let lastTopData = [];
  let infoCloseTimer = null;

  const id = (x) => document.getElementById(x);
  const fmt = (n) => new Intl.NumberFormat("id-ID").format(+n || 0);
  const formatLocalDate = (date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  };

  window.addEventListener("DOMContentLoaded", async () => {
    setDefaultDates();
    await populateKantor();
    syncMobileFiltersFromDesktop();
    await fetchTopData(1);
  });

  function setDefaultDates() {
    const now = new Date();

    id("tgl_akhir").value = formatLocalDate(now);

    const closing = new Date(now.getFullYear(), now.getMonth(), 0);
    id("tgl_awal").value = formatLocalDate(closing);
  }

  function syncClosingFromHarian() {
    const harian = id("tgl_akhir").value;
    if (!harian) return;

    const [year, month] = harian.split("-").map(Number);
    if (!year || !month) return;

    id("tgl_awal").value = formatLocalDate(new Date(year, month - 1, 0));
  }

  function toggleMobileFilter() {
    id("mobileFilterPanel").classList.toggle("hidden");
  }

  function showInfoPanel() {
    cancelCloseInfoPanel();
    id("infoPanel").classList.remove("hidden");
  }

  function toggleInfoPanel(event) {
    if (event) event.stopPropagation();

    const panel = id("infoPanel");
    if (panel.classList.contains("hidden")) {
      showInfoPanel();
    } else {
      closeInfoPanel();
    }
  }

  function scheduleCloseInfoPanel() {
    cancelCloseInfoPanel();
    infoCloseTimer = setTimeout(() => {
      closeInfoPanel();
    }, 180);
  }

  function cancelCloseInfoPanel() {
    if (infoCloseTimer) {
      clearTimeout(infoCloseTimer);
      infoCloseTimer = null;
    }
  }

  function closeInfoPanel() {
    id("infoPanel").classList.add("hidden");
  }

  document.addEventListener("click", function(e) {
    const panel = id("infoPanel");
    if (!panel || panel.classList.contains("hidden")) return;

    if (!panel.contains(e.target)) {
      panel.classList.add("hidden");
    }
  });

  function getCurrentKantor() {
    const val = String(id("filter_kantor").value || "ALL");
    if (val === "ALL" || val.startsWith("KOR-")) return "000";
    return val.replace("CAB-", "").padStart(3, "0");
  }

  function getCurrentKorwil() {
    const val = String(id("filter_kantor").value || "ALL");
    return val.startsWith("KOR-") ? val.replace("KOR-", "") : "";
  }

  async function populateKantor() {
    const desktop = id("filter_kantor");
    const mobile = id("filter_kantor_mobile");

    const user =
      (window.getUser && window.getUser()) ||
      JSON.parse(localStorage.getItem("user") || "{}") ||
      { kode: "000" };

    const userKode = String(user.kode || user.kode_kantor || "000").padStart(3, "0");

    try {
      const r = await fetch("./api/kode/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type: "kode_kantor" })
      });

      const j = await r.json();
      const listKantor = j.data || [];

      let html = "";

      if (userKode === "000") {
        html = `
          <option value="ALL">Konsolidasi</option>
          <option value="KOR-SEMARANG">Korwil Semarang</option>
          <option value="KOR-SOLO">Korwil Solo</option>
          <option value="KOR-BANYUMAS">Korwil Banyumas</option>
          <option value="KOR-PEKALONGAN">Korwil Pekalongan</option>
        `;

        listKantor
          .filter(x => String(x.kode_kantor).padStart(3, "0") !== "000")
          .forEach(x => {
            const kode = String(x.kode_kantor).padStart(3, "0");
            html += `<option value="CAB-${escapeAttr(kode)}">${kode} - ${x.nama_kantor}</option>`;
          });

        desktop.innerHTML = html;
        mobile.innerHTML = html;
        desktop.disabled = false;
        mobile.disabled = false;
      } else {
        const cabangUser = listKantor.find(x => String(x.kode_kantor).padStart(3, "0") === userKode);

        if (cabangUser) {
          html = `<option value="CAB-${escapeAttr(userKode)}">${userKode} - ${cabangUser.nama_kantor}</option>`;
        } else {
          html = `<option value="CAB-${escapeAttr(userKode)}">Cabang ${userKode}</option>`;
        }

        desktop.innerHTML = html;
        mobile.innerHTML = html;
        desktop.disabled = true;
        mobile.disabled = true;
      }
    } catch (e) {
      const html = `<option value="ALL">Konsolidasi</option>`;
      desktop.innerHTML = html;
      mobile.innerHTML = html;
    }
  }

  function syncMobileFiltersFromDesktop() {
    id("tgl_awal_mobile").value = id("tgl_awal").value;
    id("tgl_akhir_mobile").value = id("tgl_akhir").value;
    id("filter_kantor_mobile").value = id("filter_kantor").value;
  }

  function syncDateFromMobile() {
    id("tgl_awal").value = id("tgl_awal_mobile").value;
    id("tgl_akhir").value = id("tgl_akhir_mobile").value;
    syncClosingFromHarian();
    id("tgl_awal_mobile").value = id("tgl_awal").value;
  }

  function syncKantorFromMobile() {
    id("filter_kantor").value = id("filter_kantor_mobile").value;
  }

  function buildPayload(page = 1, customLimit = null) {
    const payload = {
      type: "top realisasi",
      closing_date: id("tgl_awal").value,
      harian_date: id("tgl_akhir").value,
      page: page,
      limit: customLimit || 10
    };

    const kodeKantor = getCurrentKantor();
    const korwil = getCurrentKorwil();

    if (kodeKantor !== "000") payload.kode_kantor = kodeKantor;
    if (korwil) payload.korwil = korwil;

    return payload;
  }

  async function fetchTopData(page) {
    currentPage = page;
    syncMobileFiltersFromDesktop();

    id("loadingAO").classList.remove("hidden");
    id("loadingAO").classList.add("flex");

    id("tbodyAO").innerHTML = `
      <tr>
        <td colspan="5" class="py-16 text-center text-xs font-bold text-slate-400">
          Loading data...
        </td>
      </tr>
    `;

    try {
      const r = await fetch("./api/kredit/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(buildPayload(page))
      });

      const res = await r.json();
      const wrapper = res.data || {};
      const list = wrapper.data || [];
      const pag = wrapper.pagination || {};
      const summary = wrapper.summary || {};

      lastTopData = list;

      renderTable(list, (page - 1) * 10, summary);
      updatePaginationUI(pag);
    } catch (e) {
      id("tbodyAO").innerHTML = `
        <tr>
          <td colspan="5" class="py-16 text-center text-xs font-black text-red-500">
            Gagal load data
          </td>
        </tr>
      `;
      updateTotalRow([]);
    } finally {
      id("loadingAO").classList.add("hidden");
      id("loadingAO").classList.remove("flex");
    }
  }

  function renderTable(rows, startIdx, summary) {
    const tbody = id("tbodyAO");
    rows = rows || [];

    updateTotalRow(summary);

    if (!rows.length) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="py-16 text-center text-xs font-black text-slate-500">
            DATA KOSONG
          </td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = "";

    rows.forEach((row, i) => {
      const kodeKantor = row.kode_kantor || row.kode_cabang || "-";
      const namaKantor = row.nama_kantor || row.nama_cabang || "CABANG";
      const kodeAO = row.kode_ao || row.kode_group2 || "";
      const namaAO = row.nama_ao || kodeAO || "TANPA AO";

      tbody.insertAdjacentHTML("beforeend", `
        <tr class="transition hover:bg-blue-50/40">

          <td class="hidden md:table-cell border-r border-slate-100 bg-white px-3 py-3 text-center font-mono text-[11px] text-slate-500">
            ${startIdx + i + 1}
          </td>

          <td class="hidden md:table-cell border-r border-slate-100 bg-white px-3 py-3 text-[11px] font-black uppercase text-slate-800">
            <div class="max-w-[220px] truncate" title="${escapeAttr(kodeKantor + ' - ' + namaKantor)}">
              ${namaKantor}
            </div>
            <div class="mt-0.5 font-mono text-[9px] font-bold text-slate-400">
              ${kodeKantor}
            </div>
          </td>

          <td class="sticky left-0 z-10 w-[190px] max-w-[190px] border-r border-slate-100 bg-white px-3 py-3 md:static md:z-auto md:w-auto md:max-w-none">
            <button
              type="button"
              onclick="openDetailAO('${escapeAttr(kodeAO)}', '${escapeAttr(namaAO)}')"
              class="block max-w-[165px] truncate text-left text-[11px] font-black uppercase text-blue-700 hover:text-blue-900 md:max-w-[360px] lg:max-w-none"
              title="${escapeAttr(namaAO)}"
            >
              ${namaAO}
            </button>

            <div class="mt-0.5 flex items-center gap-1 font-mono text-[9px] font-bold text-slate-400">
              <span>${kodeAO}</span>
              <span class="md:hidden">•</span>
              <span class="max-w-[80px] truncate md:hidden" title="${escapeAttr(namaKantor)}">${namaKantor}</span>
            </div>
          </td>

          <td class="sticky right-[112px] z-10 w-[58px] border-r border-slate-100 bg-white px-2 py-3 text-center font-mono text-[11px] font-black text-blue-700 md:static md:z-auto md:w-auto md:px-3">
            ${fmt(row.total_noa || 0)}
          </td>

          <td class="sticky right-0 z-10 w-[112px] bg-white px-2 py-3 text-right font-mono text-[11px] font-black text-blue-700 md:static md:z-auto md:w-auto md:px-3">
            ${fmt(row.total_realisasi || 0)}
          </td>
        </tr>
      `);
    });
  }

  function updateTotalRow(summary) {
    const totalRow = id("totalRow");

    if (!summary || Number(summary.total_ao || 0) <= 0) {
      totalRow.classList.add("hidden");
      return;
    }

    totalRow.classList.remove("hidden");

    id("totalAO").innerText = `${fmt(summary.total_ao || 0)} AO`;
    id("totalNOA").innerText = fmt(summary.total_noa || 0);
    id("totalRealisasi").innerText = fmt(summary.total_realisasi || 0);
  }

  async function openDetailAO(kodeAO, namaAO) {
    id("modalDetail").classList.remove("hidden");
    id("modalDetail").classList.add("flex");

    id("modalSub").innerText = `Detail Realisasi AO - ${namaAO}`;
    id("modalSummary").innerText = `Kode AO: ${kodeAO}`;

    const body = id("modalBody");

    body.innerHTML = `
      <tr>
        <td colspan="5" class="py-12 text-center text-xs font-bold italic text-slate-400">
          Loading detail...
        </td>
      </tr>
    `;

    try {
      const payload = {
        type: "detail realisasi ao",
        kode_ao: kodeAO,
        closing_date: id("tgl_awal").value,
        harian_date: id("tgl_akhir").value,
      };

      const kodeKantor = getCurrentKantor();
      const korwil = getCurrentKorwil();
      if (kodeKantor !== "000") payload.kode_kantor = kodeKantor;
      if (korwil) payload.korwil = korwil;

      const r = await fetch("./api/kredit/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const j = await r.json();

      const wrapper = j.data || {};
      const list = Array.isArray(wrapper) ? wrapper : (wrapper.data || []);
      const summary = wrapper.summary || null;

      if (summary) {
        id("modalSummary").innerText =
          `Kode AO: ${kodeAO} | NOA: ${fmt(summary.total_noa || 0)} | Total: ${fmt(summary.total_realisasi || 0)}`;
      }

      if (!list.length) {
        body.innerHTML = `
          <tr>
            <td colspan="5" class="py-12 text-center text-xs font-black text-slate-500">
              DETAIL KOSONG
            </td>
          </tr>
        `;
        return;
      }

      body.innerHTML = list.map((d, i) => `
        <tr class="transition hover:bg-blue-50/40">

          <td class="hidden md:table-cell px-4 py-3 text-center font-mono text-slate-500">
            ${i + 1}
          </td>

          <td class="sticky left-0 z-10 w-[170px] max-w-[170px] border-r border-slate-100 bg-white px-3 py-3 md:static md:z-auto md:w-auto md:max-w-none md:px-4">
            <div class="max-w-[150px] truncate font-black uppercase text-slate-800 md:max-w-[320px]" title="${escapeAttr(d.nama_nasabah || "-")}">
              ${d.nama_nasabah || "-"}
            </div>
            <div class="mt-0.5 font-mono text-[9px] font-bold text-slate-400 md:hidden">
              ${d.no_rekening || "-"}
            </div>
          </td>

          <td class="hidden md:table-cell px-4 py-3 text-center font-mono text-slate-600">
            ${d.no_rekening || "-"}
          </td>

          <td class="sticky right-[105px] z-10 w-[95px] border-r border-slate-100 bg-white px-2 py-3 text-center font-mono text-[11px] text-slate-600 md:static md:z-auto md:w-auto md:px-4">
            ${formatDateOnly(d.tanggal_realisasi)}
          </td>

          <td class="sticky right-0 z-10 w-[105px] bg-white px-2 py-3 text-right font-mono text-[11px] font-black text-blue-700 md:static md:z-auto md:w-auto md:px-4">
            ${fmt(d.plafond || 0)}
          </td>
        </tr>
      `).join("");
    } catch (e) {
      body.innerHTML = `
        <tr>
          <td colspan="5" class="py-12 text-center text-xs font-black text-red-500">
            Error load detail
          </td>
        </tr>
      `;
    }
  }

  async function exportFullData() {
    const btns = document.querySelectorAll('button[onclick="exportFullData()"]');

    btns.forEach(btn => {
      btn.dataset.original = btn.innerHTML;
      btn.innerHTML = "...";
      btn.disabled = true;
    });

    try {
      const payload = buildPayload(1, 10000);

      const r = await fetch("./api/kredit/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const res = await r.json();
      const list = res.data?.data || [];

      const totalNOA = list.reduce((sum, row) => sum + Number(row.total_noa || 0), 0);
      const totalRealisasi = list.reduce((sum, row) => sum + Number(row.total_realisasi || 0), 0);

      let rowsHtml = "";

      rowsHtml += `
        <tr>
          <td style="font-weight:bold;background:#dbeafe;text-align:center;">ALL</td>
          <td style="font-weight:bold;background:#dbeafe;">GRAND TOTAL</td>
          <td style="font-weight:bold;background:#dbeafe;">${list.length} AO</td>
          <td style="font-weight:bold;background:#dbeafe;text-align:center;">${totalNOA}</td>
          <td style="font-weight:bold;background:#dbeafe;text-align:right;">${totalRealisasi}</td>
        </tr>
      `;

      list.forEach((row, index) => {
        const kodeKantor = row.kode_kantor || row.kode_cabang || "";
        const namaKantor = row.nama_kantor || row.nama_cabang || "";
        const kodeAO = row.kode_ao || row.kode_group2 || "";
        const namaAO = row.nama_ao || "";
        const noa = Number(row.total_noa || 0);
        const realisasi = Number(row.total_realisasi || 0);

        rowsHtml += `
          <tr>
            <td style="text-align:center;">${index + 1}</td>
            <td>${escapeExcel(kodeKantor)} - ${escapeExcel(namaKantor)}</td>
            <td>${escapeExcel(kodeAO)} - ${escapeExcel(namaAO)}</td>
            <td style="text-align:center;">${noa}</td>
            <td style="text-align:right;">${realisasi}</td>
          </tr>
        `;
      });

      const htmlExcel = `
        <html>
          <head>
            <meta charset="UTF-8">
            <style>
              table {
                border-collapse: collapse;
                font-family: Arial, sans-serif;
                font-size: 11px;
              }
              th {
                background: #e0f2fe;
                color: #172554;
                font-weight: bold;
                text-align: center;
                border: 1px solid #94a3b8;
                padding: 8px;
              }
              td {
                border: 1px solid #cbd5e1;
                padding: 7px;
              }
              .text {
                mso-number-format: "\\@";
              }
              .num {
                mso-number-format: "#,##0";
              }
              .title {
                font-size: 16px;
                font-weight: bold;
                color: #0f172a;
              }
              .subtitle {
                font-size: 11px;
                color: #475569;
              }
            </style>
          </head>
          <body>
            <table>
              <tr>
                <td colspan="5" class="title">Rekap Realisasi AO</td>
              </tr>
              <tr>
                <td colspan="5" class="subtitle">
                  Closing: ${escapeExcel(id("tgl_awal").value)} | Harian: ${escapeExcel(id("tgl_akhir").value)} | Kantor: ${escapeExcel(getCurrentKantor())}
                </td>
              </tr>
              <tr></tr>
              <tr>
                <th>NO</th>
                <th>KANTOR CABANG</th>
                <th>NAMA AO</th>
                <th>NOA</th>
                <th>NOMINAL</th>
              </tr>
              ${rowsHtml}
            </table>
          </body>
        </html>
      `;

      const blob = new Blob(["\ufeff" + htmlExcel], {
        type: "application/vnd.ms-excel;charset=utf-8;"
      });

      const a = document.createElement("a");
      a.href = URL.createObjectURL(blob);
      a.download = `REKAP_REALISASI_AO_${id("tgl_akhir").value}.xls`;
      a.click();
      URL.revokeObjectURL(a.href);
    } finally {
      btns.forEach(btn => {
        btn.innerHTML = btn.dataset.original || "";
        btn.disabled = false;
      });
    }
  }

  function updatePaginationUI(p) {
    const wrap = id("paginationWrap");

    if (!p || !p.is_konsolidasi) {
      wrap.classList.add("hidden");
      wrap.classList.remove("flex");
      return;
    }

    wrap.classList.remove("hidden");
    wrap.classList.add("flex");

    totalPage = Number(p.total_page || 1);
    currentPage = Number(p.current_page || 1);

    id("pageInfo").innerText = `Hal ${currentPage} / ${totalPage}`;
    id("btnPrev").disabled = currentPage <= 1;
    id("btnNext").disabled = currentPage >= totalPage;
  }

  function changePage(dir) {
    const target = currentPage + dir;

    if (target >= 1 && target <= totalPage) {
      fetchTopData(target);
    }
  }

  function closeModal() {
    id("modalDetail").classList.add("hidden");
    id("modalDetail").classList.remove("flex");
  }

  function formatDateOnly(value) {
    if (!value) return "-";
    return String(value).split(" ")[0];
  }

  function escapeAttr(value) {
    return String(value || "")
      .replace(/\\/g, "\\\\")
      .replace(/'/g, "\\'")
      .replace(/"/g, "&quot;");
  }

  function escapeExcel(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }
</script>
