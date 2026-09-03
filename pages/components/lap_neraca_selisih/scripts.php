<script>
  const LNI_API = './api/lapkeu';
  const LNI_TOLERANCE = 0.01;
  const lniPage = document.getElementById('lnIssuePage');
  const lniTanggal = lniPage?.dataset.tanggal || '';
  const lniKantor = String(lniPage?.dataset.kantor || '').padStart(3, '0');

  function lniSafe(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[char]));
  }
  function lniRp(value) { return 'Rp ' + Number(value || 0).toLocaleString('id-ID', { maximumFractionDigits:0 }); }
  function lniAbs(value) { return Math.abs(Number(value || 0)); }
  function lniSetStatus(text, type = '') {
    const el = document.getElementById('lniStatus');
    if (!el) return;
    el.textContent = text;
    el.className = `lni-status ${type}`;
  }
  function lniRenderSummary(branch) {
    const a = branch.analisa || {};
    const formulaCheck = lniAbs(a.rekonsiliasi_akun_1) + lniAbs(a.rekonsiliasi_akun_2) + lniAbs(a.rekonsiliasi_akun_3);
    document.getElementById('lniMeta').textContent = `${branch.kode_kantor} - ${branch.nama_kantor} | Posisi ${lniTanggal} | Pembanding ${a.tanggal_sebelumnya || '-'}`;
    document.getElementById('lniSummary').innerHTML = `
      <div class="lni-stat alert"><span>Selisih Neraca</span><strong>${lniRp(branch.selisih)}</strong></div>
      <div class="lni-stat ${lniAbs(a.selisih_saldo_awal) > LNI_TOLERANCE ? 'alert' : ''}"><span>Selisih Saldo Awal</span><strong>${lniRp(a.selisih_saldo_awal)}</strong></div>
      <div class="lni-stat ${lniAbs(a.selisih_mutasi) > LNI_TOLERANCE ? 'alert' : ''}"><span>Dampak Mutasi Periode</span><strong>${lniRp(a.selisih_mutasi)}</strong></div>
      <div class="lni-stat"><span>Cek Rumus Saldo</span><strong>${formulaCheck > LNI_TOLERANCE ? 'Perlu cek' : 'Sesuai'}</strong></div>`;
  }
  function lniGetIndicatedCoa(branch) {
    const a = branch.analisa || {};
    const mutationExists = lniAbs(a.selisih_mutasi) > LNI_TOLERANCE;
    return (Array.isArray(branch.coa_breakdown) ? branch.coa_breakdown : [])
      .filter(item => item.is_leaf)
      .map(item => {
        const labels = Array.isArray(item.indikasi) ? [...item.indikasi] : [];
        if (mutationExists && lniAbs(item.impact_mutasi) > LNI_TOLERANCE) labels.push('Mutasi ikut membentuk selisih');
        return { ...item, labels };
      })
      .filter(item => item.labels.length)
      .sort((a, b) => {
        const scoreA = Math.max(lniAbs(a.selisih_saldo_awal_coa), lniAbs(a.rekonsiliasi_saldo), lniAbs(a.impact_mutasi));
        const scoreB = Math.max(lniAbs(b.selisih_saldo_awal_coa), lniAbs(b.rekonsiliasi_saldo), lniAbs(b.impact_mutasi));
        return scoreB - scoreA;
      });
  }
  function lniRenderRows(branch) {
    const rows = lniGetIndicatedCoa(branch);
    document.getElementById('lniCount').textContent = `${rows.length} COA terindikasi`;
    const body = document.getElementById('lniBody');
    if (!rows.length) {
      body.innerHTML = '<tr><td colspan="7" class="lni-empty">Tidak ada COA yang terindikasi dari pemeriksaan saldo awal, mutasi, dan rumus saldo.</td></tr>';
      return;
    }
    body.innerHTML = rows.map(item => {
      const badges = item.labels.map(label => `<span class="lni-badge ${label.startsWith('Mutasi') ? 'mutation' : ''}">${lniSafe(label)}</span>`).join('');
      return `<tr>
        <td data-label="COA"><div class="lni-coa-code">${lniSafe(item.kode_perk)}</div><div class="lni-coa-name">${lniSafe(item.nama_perkiraan)}</div></td>
        <td data-label="Indikasi">${badges}</td>
        <td data-label="Akhir sebelumnya" class="lni-num">${item.saldo_akhir_sebelumnya === null ? '-' : lniRp(item.saldo_akhir_sebelumnya)}</td>
        <td data-label="Saldo awal" class="lni-num">${lniRp(item.saldo_awal)}</td>
        <td data-label="Debet" class="lni-num">${lniRp(item.debet)}</td>
        <td data-label="Kredit" class="lni-num">${lniRp(item.kredit)}</td>
        <td data-label="Saldo akhir" class="lni-num">${lniRp(item.saldo)}</td>
      </tr>`;
    }).join('');
  }
  async function lniLoad() {
    if (!lniTanggal || !/^\d{3}$/.test(lniKantor)) {
      lniSetStatus('Parameter tidak lengkap', 'bad');
      document.getElementById('lniBody').innerHTML = '<tr><td colspan="7" class="lni-empty">Tanggal atau kode cabang tidak valid.</td></tr>';
      return;
    }
    try {
      const res = await fetch(LNI_API, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({ type:'lap_neraca_actual', harian_date:lniTanggal, kode_kantor:'000', detail_kantor:lniKantor })
      });
      const json = await res.json();
      if (!res.ok || Number(json.status) !== 200) throw new Error(json.message || 'Gagal memuat detail');
      const branch = Array.isArray(json?.data?.branch_breakdown) ? json.data.branch_breakdown[0] : null;
      if (!branch) throw new Error('Data cabang tidak ditemukan.');
      lniRenderSummary(branch);
      lniRenderRows(branch);
      const hasBalanceAudit = Number(branch.coa_issue_count || 0) > 0;
      const statusType = lniAbs(branch.selisih) > LNI_TOLERANCE ? 'bad' : (hasBalanceAudit ? 'audit' : 'ok');
      const statusText = lniAbs(branch.selisih) > LNI_TOLERANCE ? `Selisih ${lniRp(branch.selisih)}` : (hasBalanceAudit ? `${branch.coa_issue_count} COA perlu cek` : 'Balance');
      lniSetStatus(statusText, statusType);
    } catch (error) {
      console.error(error);
      lniSetStatus('Gagal memuat', 'bad');
      document.getElementById('lniBody').innerHTML = `<tr><td colspan="7" class="lni-empty">${lniSafe(error.message || 'Gagal memuat rincian.')}</td></tr>`;
    }
  }
  lniLoad();
</script>
