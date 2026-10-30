export type RbbTab = "projection" | "detail" | "aba"
export type AbaView = "placement" | "ckpn" | "interest" | "history"

export type Office = {
  kode_kantor: string | number
  nama_kantor?: string
}

export type RbbPlan = {
  id: number
  status: string
  catatan?: string | null
  approvals?: Array<Record<string, unknown>>
}

export type RbbRow = {
  id_ref?: number
  kode_monbis: string
  kode_perkiraan?: string | null
  sandi_lbbpr?: string | null
  kategori?: string | null
  keterangan?: string | null
  input_mode?: "AUTO" | "MANUAL" | string
  values: Record<string, number>
  interest_values?: Record<string, number>
  history?: Record<string, number>
  acc_history?: Record<string, number>
  actual_previous?: number | null
  actualPrevious?: number | null
  periodValues?: Record<string, number>
  yearValues?: Record<number, Record<string, number>>
}

export type RbbResponse = {
  tahun?: number
  year?: number
  kode_kantor?: string
  kategori?: string
  months?: string[]
  actual_previous_date?: string | null
  plan?: RbbPlan | null
  permissions?: { can_edit?: boolean; user?: string }
  offices?: Office[]
  rows?: RbbRow[]
  interest_rate?: number
  interestRate?: number
  history?: AbaHistory
  aba_summary?: Record<string, Record<string, number>>
}

export type AbaRow = {
  id: number
  no_urut: number
  jenis_penempatan: string
  nama_bank: string
  no_rekening_aba: string
  no_rekening_cbs: string
  values: Record<string, number>
  interest_values: Record<string, number>
}

export type AbaHistory = {
  years: number[]
  nominal: Record<number, Record<string, number>>
  ckpn: Record<number, Record<string, number>>
  interest: Record<number, Record<string, number>>
}
