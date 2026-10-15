export type KpiJobCode = "AO_KREDIT" | "AO_DANA" | "AO_REMEDIAL"
export type KpiUnit = "" | "RUPIAH" | "NOA" | "PERSEN" | "JUMLAH"

export type KpiJob = {
  id: number
  kode: KpiJobCode
  nama: string
  deskripsi?: string
  aktif: number
}

export type KpiIndicator = {
  id: number
  jabatan_kode: KpiJobCode
  jabatan_nama: string
  kelompok: string
  nama: string
  definisi?: string
  bobot: number
  target_default: number
  arah: string
  unit: string
  status: "PILOT" | "AKTIF" | "NONAKTIF" | string
  formula_key?: string
  sumber_data?: string
}

export type KpiScoreParameter = {
  id: number
  jabatan_kode: KpiJobCode
  indikator_id: number
  skor: number
  min_indeks: number
  max_indeks: number
  predikat: string
  aktif: number
  unit?: string
}

export type SettingKpiResponse = {
  jabatan: KpiJob[]
  indikator: KpiIndicator[]
  parameter_skor: KpiScoreParameter[]
  can_manage?: boolean
}
