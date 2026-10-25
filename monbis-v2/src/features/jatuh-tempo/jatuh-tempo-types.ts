export type JtNominalField = "saldo_bank" | "baki_debet"
export type JtBreakdown = "CABANG" | "KANKAS" | "AO"
export type JtCategory = "POTENSI" | "REFINANCING" | "LUNAS" | "BELUM_LUNAS"

export type JtFilters = {
  closingDate: string
  actualDate: string
  month: string
  year: string
  nominalField: JtNominalField
  area: string
  includeDp: boolean
}

export type JtAreaOption = {
  value: string
  label: string
}

export type JtReportRow = {
  kode_kantor?: string
  nama_kantor?: string
  group_code?: string
  group_label?: string
  group_type?: JtBreakdown
  noa_potensi?: number | string
  plafon_potensi?: number | string
  noa_refinancing?: number | string
  plafon_refinancing?: number | string
  noa_lunas?: number | string
  plafon_lunas?: number | string
  sisa_belum_lunas?: number | string
  noa_belum_lunas?: number | string
  plafon_belum_lunas?: number | string
  persentase?: number | string
  [key: string]: unknown
}

export type JtDetailRow = {
  no_rekening_lama?: string
  nama_nasabah?: string
  nama_produk?: string
  kode_produk?: string | number
  alamat?: string
  no_hp?: string | number
  kankas?: string
  nama_kankas?: string
  kode_group2?: string
  nama_ao?: string
  plafond_lama?: number | string
  tgl_jatuh_tempo?: string
  baki_debet_lama?: number | string
  keterangan_status?: string
  plafond_baru?: number | string
  tgl_realisasi_baru?: string
}

export type JtApiEnvelope<T> = {
  status: number | string
  message?: string
  data?: T
}

export type JtDatePayload = {
  last_created?: string
  last_closing?: string
}

export type JtCodeOffice = {
  kode_kantor?: string
  nama_kantor?: string
}

export type JtKankasOption = {
  kode_group1?: string
  deskripsi_group1?: string
}

export type JtAoOption = {
  kode_group2?: string
  nama_ao?: string
}

export type JtBreakdownMeta = {
  applied?: JtBreakdown
  recommended?: JtBreakdown
  is_branch?: boolean
  kankas_count?: number
  ao_count?: number
}

export type JtRekapPayload = {
  grand_total?: JtReportRow
  rekap_per_cabang?: JtReportRow[]
  nominal_field?: JtNominalField
  breakdown?: JtBreakdownMeta
}

export type JtDetailPayload = {
  data?: JtDetailRow[]
  ao_list?: JtAoOption[]
  pagination?: {
    current_page?: number
    total_pages?: number
    total_records?: number
  }
}

export type JtDetailSelection = {
  kode: string
  label: string
  groupType: JtBreakdown | "TOTAL"
  groupCode: string
  category: JtCategory
  isTotal?: boolean
}

export const jtAreaDefaults: JtAreaOption[] = [
  { value: "ALL", label: "Konsolidasi" },
  { value: "KOR-SEMARANG", label: "Korwil Semarang" },
  { value: "KOR-SOLO", label: "Korwil Solo" },
  { value: "KOR-BANYUMAS", label: "Korwil Banyumas" },
  { value: "KOR-PEKALONGAN", label: "Korwil Pekalongan" },
]

export const jtMonths = [
  ["01", "Januari"], ["02", "Februari"], ["03", "Maret"], ["04", "April"],
  ["05", "Mei"], ["06", "Juni"], ["07", "Juli"], ["08", "Agustus"],
  ["09", "September"], ["10", "Oktober"], ["11", "November"], ["12", "Desember"],
] as const
