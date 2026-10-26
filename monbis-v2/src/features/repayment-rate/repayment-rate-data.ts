import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

export type RrRow = Record<string, unknown> & { kode?: string; nama?: string }
export type RrFilters = { closingDate: string; actualDate: string; nominalField: "saldo_bank" | "baki_debet"; area: string; breakdown: "KANKAS" | "AO" }
export type RrSort = { column: string; direction: "asc" | "desc" }
export type RrOffice = { kode_kantor?: string; nama_kantor?: string }
type Envelope<T> = { status: number | string; message?: string; data?: T }

declare global { interface Window { apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response> } }

export async function requestRr<T>(request: string, options: RequestInit = {}) {
  const fetcher = window.apiFetch?.bind(window) || fetch
  const response = await fetcher(apiEndpoint(request), { ...options, headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...options.headers } })
  let payload: Envelope<T>
  try { payload = await response.json() as Envelope<T> } catch { throw new Error(`API ${request} tidak mengembalikan JSON (${response.status})`) }
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return payload
}

export function rrOfficeCode() {
  const user = window.getUser?.()
  const raw = user?.kode ?? user?.kode_kantor ?? (user as Record<string, unknown> | null)?.kode_cabang ?? ""
  if (!raw) return "000"
  const code = String(raw).replace(/\D/g, "").padStart(3, "0").slice(-3)
  return code === "099" ? "000" : code
}

export function rrAccess(payload: Record<string, unknown> | null) {
  if (!payload) return false
  if (payload.admin === true) return true
  const permissions = payload.permissions as Record<string, { can_view?: boolean }> | undefined
  return Number(permissions?.["page:rekap_rr"]?.can_view) === 1
}

export const rrNumber = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })
export const rrPercent = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 2 })
export const rrMoney = (value: unknown) => rrNumber.format(Math.round(Number(value ?? 0)))
export const rrCode = (value: unknown) => String(value ?? "").padStart(3, "0")
export const rrDateLabel = (value: string) => { const [year, month, day] = value.split("-"); return year && month && day ? `${day}/${month}/${year}` : value }

export function sortRrRows(rows: RrRow[], sort: RrSort) {
  const direction = sort.direction === "asc" ? 1 : -1
  return [...rows].sort((left, right) => {
    const a = left[sort.column]; const b = right[sort.column]
    const numeric = a !== "" && b !== "" && Number.isFinite(Number(a)) && Number.isFinite(Number(b))
    const leftText = String(a ?? "").toLowerCase(); const rightText = String(b ?? "").toLowerCase()
    const comparison = numeric ? Number(a ?? 0) - Number(b ?? 0) : leftText < rightText ? -1 : leftText > rightText ? 1 : 0
    return comparison * direction
  })
}

export function exportRr(rows: RrRow[], labelName: string, filename: string) {
  const headers = ["Kode", labelName, "Closing · Nominal Lancar", "Closing · NOA", "Closing · RR %", "Actual · Nominal Lancar", "Actual · NOA", "Actual · RR %", "Migrasi · Nominal", "Migrasi · NOA", "Perubahan RR · %"]
  const values = rows.map((row) => [String(row.kode ?? ""), String(row.nama ?? ""), Number(row.m1_lancar_os ?? 0), Number(row.m1_all_noa ?? 0), Number(row.m1_pct ?? 0), Number(row.cur_lancar_os ?? 0), Number(row.cur_all_noa ?? 0), Number(row.cur_pct ?? 0), Number(row.delta_os_lancar ?? 0), Number(row.delta_noa ?? 0), Number(row.delta_pct ?? 0)])
  downloadReportExcel(filename, headers, values, [0, 1])
}

export function detailExport(rows: RrRow[], filename: string, officeFallback = "") {
  const columns = ["kode_kantor", "no_rekening", "nama_nasabah", "alamat", "no_hp", "kankas", "nama_ao", "tgl_realisasi", "tgl_jatuh_tempo", "status_pembayaran", "trx_bulan_ini", "tgl_bayar_ini", "jml_pinjaman", "os_m1", "os_curr", "totung", "tabungan", "dpd_curr", "status_ket"]
  const labels = ["Kode Kantor", "No. Rekening", "Nama Nasabah", "Alamat", "No. HP", "Kankas", "Nama AO", "Tgl. Realisasi", "Tgl. Jatuh Tempo", "Status Pembayaran", "Angsuran", "Tgl. Bayar", "Plafond", "Target M-1", "Actual", "Total Tunggakan", "Tabungan", "DPD", "Status Tagih"]
  downloadReportExcel(filename, labels, rows.map((row) => columns.map((column) => column === "kode_kantor" ? String(row[column] || officeFallback) : row[column] as string | number | null | undefined)), [0, 1, 3, 4])
}
