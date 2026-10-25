export type NominalField = "saldo_bank" | "baki_debet"
export type SortDirection = "asc" | "desc"
export type SortKey =
  | "kode_kantor"
  | "nama_kantor"
  | "nominal_closing"
  | "total_realisasi"
  | "total_restruck"
  | "pelunasan"
  | "angsuran_murni"
  | "total_run_off"
  | "nominal_actual"
  | "growth"

export type Filters = {
  closingDate: string
  actualDate: string
  area: string
  nominalField: NominalField
}

export type ReportRow = {
  kode_kantor?: string
  kode_kankas?: string | null
  nama_kantor?: string
  nominal_closing?: number | string
  nominal_actual?: number | string
  saldo_bank_closing?: number | string
  saldo_bank_actual?: number | string
  total_realisasi?: number | string
  noa_realisasi?: number | string
  total_restruck?: number | string
  noa_restruck?: number | string
  pelunasan?: number | string
  noa_pelunasan?: number | string
  angsuran_murni?: number | string
  noa_angsuran_murni?: number | string
  total_run_off?: number | string
  noa_run_off?: number | string
  growth?: number | string
  [key: string]: unknown
}

export type DetailRow = {
  no_rekening?: string
  nama_nasabah?: string
  alamat?: string
  nama_kankas?: string
  nama_ao?: string
  tgl_realisasi?: string
  plafond?: number | string
}

export type ApiEnvelope<T> = {
  status: number | string
  message?: string
  data?: T
}

export type ReportPayload = {
  data?: ReportRow[]
  grand_total?: ReportRow
  meta?: { nominal_field?: string }
}

export type DetailPayload = {
  data?: DetailRow[]
  total_records?: number
  total_pages?: number
  current_page?: number
}

export type AreaOption = {
  value: string
  label: string
}

export type DetailSelection = {
  type: string
  row: ReportRow
}

export const areaDefaults: AreaOption[] = [
  { value: "ALL", label: "Konsolidasi" },
  { value: "KOR-SEMARANG", label: "Korwil Semarang" },
  { value: "KOR-SOLO", label: "Korwil Solo" },
  { value: "KOR-BANYUMAS", label: "Korwil Banyumas" },
  { value: "KOR-PEKALONGAN", label: "Korwil Pekalongan" },
]

export const tableColumns: Array<{ key: SortKey; label: string; align?: string }> = [
  { key: "kode_kantor", label: "Kode" },
  { key: "nama_kantor", label: "Nama kantor" },
  { key: "nominal_closing", label: "Saldo closing", align: "text-right" },
  { key: "total_realisasi", label: "Realisasi", align: "text-right" },
  { key: "total_restruck", label: "Restrukturisasi", align: "text-right" },
  { key: "pelunasan", label: "Pelunasan", align: "text-right" },
  { key: "angsuran_murni", label: "Angsuran", align: "text-right" },
  { key: "total_run_off", label: "Run off", align: "text-right" },
  { key: "nominal_actual", label: "Saldo actual", align: "text-right" },
  { key: "growth", label: "Growth", align: "text-right" },
]
