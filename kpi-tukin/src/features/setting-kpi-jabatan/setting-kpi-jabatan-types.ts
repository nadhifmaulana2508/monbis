import type { KpiTukinRule } from "@/features/kpi-types"

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
  skor: number | string
  min_indeks: number | string
  max_indeks: number | string
  predikat: string
  aktif: number | string
  unit?: string
}

export type SettingKpiResponse = {
  jabatan: KpiJob[]
  indikator: KpiIndicator[]
  parameter_skor: KpiScoreParameter[]
  tukin_rules: KpiTukinRule[]
  can_manage?: boolean
}
