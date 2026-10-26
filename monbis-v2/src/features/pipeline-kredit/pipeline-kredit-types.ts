export type PipelineBreakdown = "CABANG" | "KANKAS" | "AO"

export type PipelineFilters = {
  closingDate: string
  actualDate: string
  year: string
  area: string
  includeDp: boolean
}

export type PipelineRow = {
  kode_cabang?: string
  kode_kantor?: string
  nama_kantor?: string
  group_code?: string
  group_label?: string
  noa_target?: number | string
  plafon_closing?: number | string
  noa_sudah?: number | string
  nominal_sudah?: number | string
  noa_lunas?: number | string
  nominal_lunas?: number | string
  noa_topup?: number | string
  os_topup?: number | string
  noa_retensi?: number | string
  os_retensi?: number | string
  noa_drop?: number | string
  os_drop?: number | string
  [key: string]: unknown
}

export type PipelineDetailRow = {
  no_rekening?: string
  nama_nasabah?: string
  nama_produk?: string
  alamat?: string
  no_hp?: string | number
  nama_kankas?: string
  nama_ao?: string
  plafon_awal?: number | string
  tgl_jatuh_tempo?: string
  os_actual?: number | string
  status_ket?: string
  plafon_baru?: number | string
  tgl_baru?: string
}

export type PipelineSelection = {
  kode: string
  label: string
  breakdown: PipelineBreakdown | "TOTAL"
  groupCode: string
  status?: string
  isTotal?: boolean
}

export type PipelineApiEnvelope<T> = {
  status: number | string
  message?: string
  data?: T
}

export type PipelineDetailPayload = {
  data?: PipelineDetailRow[]
  list_ao?: Array<{ kode_group2?: string; nama_ao?: string }>
  pagination?: { total_pages?: number; total_records?: number; current_page?: number }
  stats?: Record<string, number | string>
}
