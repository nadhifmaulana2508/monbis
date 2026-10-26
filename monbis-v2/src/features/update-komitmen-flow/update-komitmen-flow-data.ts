import { apiEndpoint } from "@/lib/api-base"

export type UpdateFlowRequest = {
  source?: string
  kode_kantor?: string
  kode_kankas?: string
  kode_ao?: string
  korwil?: string
  klasifikasi_flow?: string
  status_filter?: string
  closing_date?: string
  harian_date?: string
}

export type UpdateFlowRow = {
  no_rekening: string
  nama_nasabah: string
  alamat: string
  kolek_harian: string
  baki_debet: number
  tunggakan_pokok: number
  tunggakan_bunga: number
  hari_menunggak_pokok: number
  hari_menunggak_bunga: number
  tgl_jatuh_tempo: string
  komitmen: string
  tgl_pembayaran: string
  nominal: number
  alasan: string
}

type ApiEnvelope<T> = { status?: number | string; message?: string; data?: T }

declare global {
  interface Window {
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
  }
}

export const updateFlowNumber = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })
export const updateFlowMoney = (value: unknown) => updateFlowNumber.format(Number(value || 0))
const toNumber = (value: unknown) => Number(value || 0)

export async function requestUpdateFlow<T>(endpoint: string, options: RequestInit = {}) {
  const fetcher = window.apiFetch?.bind(window) || fetch
  const response = await fetcher(apiEndpoint(endpoint), {
    ...options,
    headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...options.headers },
  })
  let payload: ApiEnvelope<T>
  try { payload = await response.json() as ApiEnvelope<T> } catch { throw new Error(`API ${endpoint} tidak mengembalikan JSON (${response.status})`) }
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return payload
}

export function updateFlowAccess(payload: Record<string, unknown> | null, source: "flow_par" | "potensi_npl", user: Record<string, unknown> | null) {
  if (!payload) return false
  if (payload.admin === true) return true
  const permissions = payload.permissions as Record<string, { can_view?: boolean | number }> | undefined
  const keys = source === "potensi_npl" ? ["page:potensi_npl", "page:report_potensi_npl"] : ["page:flow_par", "page:report_flowpar"]
  if (keys.some((key) => Number(permissions?.[key]?.can_view) === 1)) return true
  const profile = user || {}
  const fields = [profile.job_position, profile.unit_kerja, profile.branch_name, profile.role].map((value) => String(value || "").toLowerCase())
  return fields.some((value) => value.includes("divisi operasional")) || fields.includes("dev")
}

export function updateFlowUser() {
  const currentUser = window.getUser?.()
  if (currentUser) return currentUser as Record<string, unknown>
  for (const key of ["dpk_user", "app_user", "user"]) {
    try {
      const value = localStorage.getItem(key)
      if (value) return JSON.parse(value) as Record<string, unknown>
    } catch { /* Invalid cached profile is ignored; RBAC remains authoritative. */ }
  }
  return null
}

export function updateFlowPayload(request: UpdateFlowRequest, source: "flow_par" | "potensi_npl") {
  return source === "potensi_npl"
    ? {
        type: "Debitur Potensi NPL",
        kode_kantor: request.kode_kantor || "",
        kode_kankas: request.kode_kankas || "",
        kode_ao: request.kode_ao || "",
        closing_date: request.closing_date,
        harian_date: request.harian_date,
      }
    : {
        type: "KL Baru",
        kode_kantor: request.kode_kantor || "",
        kode_kankas: request.kode_kankas || "",
        korwil: request.korwil || "",
        klasifikasi_flow: request.klasifikasi_flow || "",
        closing_date: request.closing_date,
        harian_date: request.harian_date,
      }
}

export function normalizeUpdateFlowRows(rows: Record<string, unknown>[], source: "flow_par" | "potensi_npl", statusFilter = "ALL"): UpdateFlowRow[] {
  const selectedRows = source === "potensi_npl" && statusFilter !== "ALL"
    ? rows.filter((row) => statusFilter === "AMAN"
      ? row.status_potensi === "AMAN" || row.status_potensi === "LUNAS / AMAN"
      : row.status_potensi === statusFilter)
    : rows

  return selectedRows.map((row) => ({
    no_rekening: String(row.no_rekening || ""),
    nama_nasabah: String(row.nama_nasabah || "-"),
    alamat: String(row.alamat || "-"),
    kolek_harian: String(row.kolek_harian || row.kolektibilitas || row.kolek || row.status_potensi || "-"),
    baki_debet: toNumber(row.baki_debet_harian || row.baki_debet || row.baki_debet_closing),
    tunggakan_pokok: toNumber(row.tunggakan_pokok),
    tunggakan_bunga: toNumber(row.tunggakan_bunga),
    hari_menunggak_pokok: toNumber(row.hmp_harian ?? row.hari_menunggak_pokok),
    hari_menunggak_bunga: toNumber(row.hmb_harian ?? row.hari_menunggak_bunga),
    tgl_jatuh_tempo: String(row.jt_harian || row.tgl_jatuh_tempo || row.jt_closing || ""),
    komitmen: String(row.komitmen || ""),
    tgl_pembayaran: String(row.tgl_pembayaran || ""),
    nominal: toNumber(row.nominal),
    alasan: String(row.alasan || ""),
  }))
}

export function updateFlowHasCommitment(row: UpdateFlowRow) {
  return Boolean(row.komitmen.trim() || row.tgl_pembayaran.trim() || row.nominal > 0)
}

export function updateFlowIsRisk(row: UpdateFlowRow, harianDate?: string) {
  if (row.hari_menunggak_pokok >= 90 || row.hari_menunggak_bunga >= 90) return true
  if (!row.tgl_jatuh_tempo) return false
  const dueDate = new Date(row.tgl_jatuh_tempo)
  const referenceDate = harianDate ? new Date(harianDate) : new Date()
  if (Number.isNaN(dueDate.getTime()) || Number.isNaN(referenceDate.getTime())) return false
  const currentMonth = dueDate.getMonth() === referenceDate.getMonth() && dueDate.getFullYear() === referenceDate.getFullYear()
  const previousMonth = new Date(referenceDate.getFullYear(), referenceDate.getMonth() - 1, 1)
  return currentMonth || (dueDate.getMonth() === previousMonth.getMonth() && dueDate.getFullYear() === previousMonth.getFullYear())
}

export function updateFlowDate(value: string) {
  if (!value || value === "-") return "-"
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return `${String(date.getDate()).padStart(2, "0")}/${String(date.getMonth() + 1).padStart(2, "0")}/${date.getFullYear()}`
}

export function updateFlowCommitmentOptions(row: UpdateFlowRow, harianDate?: string) {
  const dueDate = row.tgl_jatuh_tempo ? new Date(row.tgl_jatuh_tempo) : null
  const referenceDate = harianDate ? new Date(harianDate) : new Date()
  const dueThisMonth = dueDate && !Number.isNaN(dueDate.getTime()) && dueDate.getMonth() === referenceDate.getMonth() && dueDate.getFullYear() === referenceDate.getFullYear()
  const options = dueThisMonth
    ? [["", "-- Pilih Komitmen --"], ["E_DPD 91-120", "Flow (E_DPD 91-120)"], ["O_Lunas", "O_Lunas"]]
    : [["", "-- Pilih Komitmen --"], ["A_DPD 0", "A_DPD 0"], ["B_DPD 1-30", "B_DPD 1-30"], ["C_DPD 31-60", "C_DPD 31-60"], ["D_DPD 61-90", "D_DPD 61-90"], ["E_DPD 91-120", "E_DPD 91-120"], ["O_Lunas", "O_Lunas"]]
  if (row.komitmen && !options.some(([value]) => value === row.komitmen)) options.push([row.komitmen, row.komitmen])
  return options as [string, string][]
}
