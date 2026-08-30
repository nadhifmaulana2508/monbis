<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$head = mb_build_grouped_thead([
    ['label'=>'INDIKATOR','class'=>'mb-sticky-left mb-text-left'],
    ['label'=>'TARGET PERIODE','class'=>'mb-group--blue'],
    ['label'=>'REALISASI PERIODE','class'=>'mb-group--green'],
    ['label'=>'INDEKS'], ['label'=>'SKOR'], ['label'=>'BOBOT'],
    ['label'=>'NILAI'], ['label'=>'BULAN TERISI']
]);

mb_render_report_page([
    'id'=>'rekapKpiAoPage',
    'header'=>[
        'id'=>'rekapKpiAoHeader', 'title'=>'Rekap KPI AO Kredit',
        'subtitle'=>'Penilaian resmi triwulanan dari data KPI bulanan.',
        'icon'=>mb_svg('chart'),
        'filters'=>[
            ['id'=>'rekapKpiYear','label'=>'Tahun','type'=>'number','width'=>'85px','value'=>date('Y')],
            ['id'=>'rekapKpiQuarter','label'=>'Periode','type'=>'select','width'=>'120px','options'=>['1'=>'Q1 · Jan–Mar','2'=>'Q2 · Apr–Jun','3'=>'Q3 · Jul–Sep','4'=>'Q4 · Okt–Des']],
            ['id'=>'rekapKpiKantor','label'=>'Kantor','type'=>'select','width'=>'150px','options'=>[''=>'Pilih kantor dahulu']],
            ['id'=>'rekapKpiAo','label'=>'AO Kredit','type'=>'select','width'=>'260px','options'=>[''=>'Pilih kantor dahulu']]
        ]
    ],
    'toolbar'=>['title'=>'Raport Triwulanan','search'=>['id'=>'rekapKpiSearch','placeholder'=>'Cari indikator / AO...']],
    'legend_html'=>'<div class="mb-summary">
        <div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">AO DINILAI</div><div id="rekapKpiCount" class="mb-summary-card__value">-</div></div>
        <div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">PERIODE</div><div id="rekapKpiPeriod" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">3 bulan per penilaian</div></div>
        <div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">NILAI AKHIR</div><div id="rekapKpiFinal" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Total nilai berbobot / 100</div></div>
        <div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">METODE</div><div class="mb-summary-card__value">Akumulasi + Rata-rata</div><div class="mb-summary-card__meta">Sesuai jenis indikator</div></div>
    </div>',
    'table'=>['wrapper_id'=>'rekapKpiTableWrap','table_id'=>'rekapKpiTable','loading_id'=>'rekapKpiLoading','loading_text'=>'Memuat rekap KPI...','thead_html'=>$head,'tbody_ids'=>['rekapKpiBody']]
]);
?>
<script>
(() => {
    const API = './api/kpi/';
    const state = {boot:null};
    const el = id => document.getElementById(id);
    const esc = value => window.MonbisUI?.escape ? window.MonbisUI.escape(value) : String(value ?? '');
    const fmt = value => new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2}).format(Number(value || 0));
    const pct = value => fmt(Number(value || 0) * 100) + '%';
    const money = value => 'Rp ' + fmt(value);
    const post = async body => {
        const response = await fetch(API, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)});
        const json = await response.json();
        if (!response.ok || json.status !== 200) throw Error(json.message || 'Gagal memuat data');
        return json.data || {};
    };

    function fill(data) {
        el('rekapKpiAo').innerHTML = '<option value="">Semua AO</option>' +
            (data.ao_kredit || []).map(a => `<option value="${esc(a.kode_ao)}">${esc(a.kode_ao)} · ${esc(a.nama_ao)}</option>`).join('');
    }

    function render(data) {
        const rows = data.data || [];
        const aos = data.ao || [];
        el('rekapKpiCount').textContent = fmt(aos.length);
        el('rekapKpiPeriod').textContent = `Q${data.quarter} · ${data.year}`;

        // Nilai akhir = jumlah nilai berbobot semua indikator pada periode.
        const total = rows.reduce((sum, row) => sum + Number(row.nilai_100 || 0), 0);
        el('rekapKpiFinal').textContent = rows.length ? `${fmt(total)} / 100` : '-';

        el('rekapKpiBody').innerHTML = rows.length ? rows.map(row => `<tr>
            <td class="mb-sticky-left mb-text-left"><strong>${esc(row.nama)}</strong><small class="mb-subvalue">${esc(row.kelompok)}</small></td>
            <td class="mb-num">${row.unit==='RUPIAH' ? money(row.target) : row.unit==='PERSEN' ? pct(row.target) : fmt(row.target)}</td>
            <td class="mb-num mb-strong">${row.unit==='RUPIAH' ? money(row.realisasi) : row.unit==='PERSEN' ? pct(row.realisasi) : fmt(row.realisasi)}</td>
            <td class="mb-num">${row.unit==='PERSEN' ? pct(row.indeks) : fmt(row.indeks)}</td>
            <td class="mb-num">${fmt(row.skor)} / 5</td>
            <td class="mb-num">${pct(row.bobot)}</td>
            <td class="mb-num mb-strong">${fmt(row.nilai_100)}</td>
            <td class="mb-num">${fmt(row.bulan_terisi)} / 3</td>
        </tr>`).join('') : '<tr><td colspan="8" class="mb-empty">Belum ada hasil KPI untuk periode ini.</td></tr>';
    }

    async function load() {
        if (!el('rekapKpiKantor').value) {
            el('rekapKpiCount').textContent = '0';
            el('rekapKpiFinal').textContent = '-';
            el('rekapKpiBody').innerHTML = '<tr><td colspan="8" class="mb-empty">Pilih kantor terlebih dahulu.</td></tr>';
            return;
        }
        try {
            render(await post({type:'quarterly', year:el('rekapKpiYear').value, quarter:el('rekapKpiQuarter').value, kode_ao:el('rekapKpiAo').value, kode_kantor:el('rekapKpiKantor').value}));
        } catch (error) {
            el('rekapKpiFinal').textContent = '-';
            el('rekapKpiBody').innerHTML = `<tr><td colspan="8" class="mb-empty mb-negative">${esc(error.message)}</td></tr>`;
        }
    }

    async function boot() {
        try {
            const data = await post({type:'bootstrap', year:el('rekapKpiYear').value});
            state.boot = data;
            el('rekapKpiKantor').innerHTML = '<option value="">Pilih kantor dahulu</option>' + (data.kantor || []).map(k => `<option value="${esc(k.kode_kantor)}">${esc(k.kode_kantor)} · ${esc(k.nama_kantor)}</option>`).join('');
            el('rekapKpiAo').innerHTML = '<option value="">Pilih kantor dahulu</option>';
            await load();
        }
        catch (error) { el('rekapKpiBody').innerHTML = `<tr><td colspan="8" class="mb-empty mb-negative">${esc(error.message)}</td></tr>`; }
    }

    el('rekapKpiYear')?.addEventListener('change', boot);
    el('rekapKpiQuarter')?.addEventListener('change', load);
    el('rekapKpiAo')?.addEventListener('change', load);
    el('rekapKpiKantor')?.addEventListener('change', () => {
        const branch = el('rekapKpiKantor').value;
        const list = (state.boot?.ao_kredit || []).filter(a => !branch || a.kode_kantor === branch);
        el('rekapKpiAo').innerHTML = '<option value="">Semua AO</option>' + list.map(a => `<option value="${esc(a.kode_ao)}">${esc(a.kode_ao)} · ${esc(a.nama_ao)}</option>`).join('');
        load();
    });
    el('rekapKpiSearch')?.addEventListener('input', event => {
        const query = event.target.value.toLowerCase();
        document.querySelectorAll('#rekapKpiBody tr').forEach(row => row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none');
    });
    boot();
})();
</script>
