<?php
/**
 * Contoh integrasi. Jangan dijadikan API logic; ini hanya contoh struktur page.
 * Sesuaikan $assetBase jika index.php kamu tidak berada di root project.
 */
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

mb_render_page_header([
    'id' => 'demoHeader',
    'title' => 'Recovery NPL',
    'subtitle' => 'Posisi recovery, flow NPL, dan tindak lanjut harian.',
    'info_modal_id' => 'demoInfo',
    'filters' => [
        ['id'=>'demoClosing','label'=>'Closing (M-1)','type'=>'date','width'=>'118px'],
        ['id'=>'demoActual','label'=>'Harian (Actual)','type'=>'date','width'=>'118px'],
        ['id'=>'demoArea','label'=>'Area / Cabang','type'=>'select','width'=>'220px','options'=>[
            'ALL'=>'Konsolidasi','KOR-SEMARANG'=>'Korwil Semarang','CAB-003'=>'003 - Kc. Pati'
        ]],
    ],
    'actions' => [
        ['icon'=>'download','tone'=>'success','title'=>'Export Excel','attrs'=>['onclick'=>'demoExport()']],
    ],
]);

$thead = '
<tr>
  <th class="mb-code-col mb-sticky-left" rowspan="2" style="width:56px">Kode</th>
  <th class="mb-sticky-left-2" rowspan="2" style="--mb-sticky-1:56px;width:170px;text-align:left">Nama Kantor</th>
  <th class="mb-group mb-group--blue" colspan="2">Total Recovery</th>
  <th class="mb-group mb-group--red" colspan="2">Flow NPL</th>
  <th class="mb-group mb-group--amber" rowspan="2">% Flow PAR</th>
</tr>
<tr>
  <th>NOA</th><th>Nominal</th><th>NOA</th><th>Nominal</th>
</tr>';

mb_render_table_shell([
    'wrapper_id'=>'demoScroller',
    'table_id'=>'demoTable',
    'loading_id'=>'demoLoading',
    'thead_html'=>$thead,
    'tbody_ids'=>['demoTotal','demoBody'],
]);

mb_render_info_modal([
    'id'=>'demoInfo',
    'title'=>'Panduan & Fokus Recovery NPL',
    'subtitle'=>'Ringkasan kondisi dan prioritas tindak lanjut.',
    'body_html'=>'
      <div class="mb-info-highlight"><b>Gunakan informasi ini untuk action plan, bukan hanya kamus kolom.</b></div>
      <div class="mb-info-grid">
        <div class="mb-info-card mb-group--green"><div class="mb-info-card__title">Total Recovery</div><div class="mb-info-card__text">Lunas + backflow + angsuran NPL.</div></div>
        <div class="mb-info-card mb-group--red"><div class="mb-info-card__title">Flow NPL</div><div class="mb-info-card__text">Flow baru perlu ditekan agar posisi akhir bulan tidak memburuk.</div></div>
      </div>'
]);

mb_render_detail_modal([
    'id'=>'demoDetail',
    'title'=>'Detail Debitur',
    'subtitle'=>'Daftar rekening sesuai angka yang dipilih',
    'size'=>'lg',
    'search'=>['id'=>'demoSearch','placeholder'=>'Cari nama / rekening...'],
    'filters'=>[
        ['id'=>'demoKankas','label'=>'Kankas','type'=>'select','width'=>'140px','options'=>[''=>'Semua Kankas']],
    ],
    'actions'=>[
        ['icon'=>'download','tone'=>'success','title'=>'Export detail','attrs'=>['onclick'=>'demoExportDetail()']],
    ],
]);
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('demoTotal').innerHTML = `
    <tr class="mb-total-row">
      <td class="mb-code-col mb-sticky-left">-</td>
      <td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:56px">TOTAL</td>
      <td class="mb-noa">79</td><td class="mb-num">472.248.503</td>
      <td class="mb-noa mb-negative">75</td><td class="mb-num mb-negative">4.492.692.003</td>
      <td class="mb-noa">6,41%</td>
    </tr>`;

  document.getElementById('demoBody').innerHTML = `
    <tr>
      <td class="mb-code-col mb-sticky-left">003</td>
      <td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:56px">Kc. Pati</td>
      <td class="mb-noa">3</td><td class="mb-num">125.573.000</td>
      <td class="mb-noa mb-negative">1</td><td class="mb-num mb-negative">149.939.016</td>
      <td class="mb-noa">3,05%</td>
    </tr>`;
});

function demoExport(){ alert('Hubungkan ke fungsi export page.'); }
function demoExportDetail(){ alert('Hubungkan ke fungsi export detail page.'); }
</script>
