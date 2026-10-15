import type { KpiJobCode } from "./setting-kpi-jabatan/setting-kpi-jabatan-types"

export type { KpiJobCode }

export type KpiDirectory = {
  year: number
  closing_dates: string[]
  jabatan: Array<{ id: number; kode: KpiJobCode; nama: string; deskripsi?: string }>
  jabatan_terpilih?: { id: number; kode: KpiJobCode; nama: string; deskripsi?: string }
  ao: KpiAo[]
  kantor: Array<{ kode_kantor: string; nama_kantor: string }>
  generated: KpiGenerated[]
  tukin_rules: KpiTukinRule[]
}

export type KpiAo = { kode_ao: string; nama_ao: string; id_peg?: string; kode_kantor: string; spesialisasi?: string }
export type KpiGenerated = { id_peg?: string; kode_ao: string; kode_kantor?: string; closing_date: string; status?: string; nilai_akhir?: number | null }
export type KpiTukinRule = { min_skor: number; max_skor?: number | null; min_nilai?: number | null; max_nilai?: number | null; faktor_persen: number; label: string; urutan?: number }

export type KpiEvaluation = {
  id: number
  id_peg?: string
  kode_ao: string
  nama_ao: string
  kode_kantor?: string
  tahun: number
  bulan: number
  closing_date: string
  nilai_dasar?: number | null
  faktor_risiko?: number | null
  nilai_akhir?: number | null
  predikat?: string | null
  status?: string | null
  indikator_terisi?: number
}

export type KpiDetail = {
  id?: number
  indikator_id: number
  kelompok?: string
  nama: string
  bobot: number
  arah?: string
  unit: string
  formula_key?: string
  sumber_data?: string
  input_pa?: string | null
  target?: number | null
  realisasi?: number | null
  indeks?: number | null
  skor?: number | null
  nilai_tertimbang?: number | null
  nilai_100?: number | null
  catatan?: string | null
  os_mob_menunggak?: number | null
  os_mob_total?: number | null
  os_dpd0?: number | null
  os_kelolaan?: number | null
  os_run_off?: number | null
  os_dpd0_m1?: number | null
}

export type AnnualKpiRow = {
  id_peg: string
  kode_ao: string
  nama_ao: string
  kode_kantor: string
  bulan_terisi: number
  nilai_akhir: number | null
  skor_final: number | null
  tukin_persen: number | null
  tukin_label?: string | null
  monthly: Record<string, { nilai_akhir: number; skor: number; tukin_persen: number; status?: string; closing_date?: string; penilaian_id?: number }>
}

export type AnnualKpiResponse = {
  year: number
  kode_ao: string
  kode_kantor: string
  jabatan: { kode: KpiJobCode; nama: string }
  total_ao: number
  is_konsolidasi: boolean
  konsolidasi_top?: number
  ao: AnnualKpiRow[]
  months: Array<{ bulan: number; terisi: number; nilai_akhir: number | null; skor: number | null; tukin_persen: number | null }>
  indicator_breakdown: KpiBreakdownRow[]
  tukin_rules: KpiTukinRule[]
}

export type KpiBreakdownRow = KpiDetail & {
  kode_kantor: string
  nama_ao: string
  id_peg: string
  bulan: number
  closing_date: string
  nilai_akhir_bulan: number | null
  tukin_persen_bulan: number | null
}

export type KpiReportRow = {
  section?: string
  key?: string
  label?: string
  format?: string
  current_actual?: number | null
  current_target?: number | null
  year_actual?: number | null
  monthly?: Array<number | null>
  previous?: number | null
  year_target?: number | null
  current_delta?: number | null
  current_achievement?: number | null
  year_delta?: number | null
  year_achievement?: number | null
  comparison?: boolean
}

export type KpiReport = {
  meta?: { scope_label?: string; latest_actual_date?: string; harian_date?: string; year?: number; latest_actual_month?: number; months?: Array<{ label?: string; available?: boolean }> }
  rows?: KpiReportRow[]
}

export type ReportOffice = { kode_kantor: string; nama_kantor: string }

export type ReportDate = { last_created?: string; last_closing?: string; awal_bulan?: string }

export type ReportEmployeeSummary = { meta?: { total?: number | null } }
