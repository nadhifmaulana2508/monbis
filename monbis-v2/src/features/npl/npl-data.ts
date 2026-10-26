import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

export type NplView = "kolek" | "npl"
export type NplSort = { column: string; direction: "asc" | "desc" }
export type NplRow = Record<string, unknown> & {
  kode_unit?: string
  kode_cabang?: string
  nama_unit?: string
  nama_kantor?: string
}
export type NplFilters = { actualDate: string; closingDate: string; nominalField: "baki_debet" | "saldo_bank"; area: string }
export type NplOffice = { kode_kantor?: string; nama_kantor?: string }
export type NplEnvelope<T> = { status: number | string; message?: string; data?: T }

declare global {
  interface Window {
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
  }
}

export const nplNumber = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })
export const nplDecimal = new Intl.NumberFormat("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 })

export async function requestNplJson<T>(request: string, options: RequestInit = {}) {
  const fetcher = typeof window !== "undefined" && window.apiFetch ? window.apiFetch.bind(window) : fetch
  const response = await fetcher(apiEndpoint(request), {
    ...options,
    headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...options.headers },
  })
  const payload = await response.json() as NplEnvelope<T>
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return payload
}

export function userOfficeCode() {
  const raw = String(window.getUser?.()?.kode_kantor || window.getUser?.()?.kode || "000").padStart(3, "0")
  return raw === "099" ? "000" : raw
}

export function hasNplAccess(user: Record<string, unknown> | null) {
  if (!user) return true
  return [user.job_position, user.unit_kerja, user.branch_name, user.role]
    .map((value) => String(value || "").toLowerCase())
    .some((value) => value.includes("divisi operasional") || value === "dev")
}

export function nplCode(row: NplRow) {
  return String(row.kode_unit || row.kode_cabang || "").padStart(3, "0")
}

export function nplName(row: NplRow) {
  return String(row.nama_unit || row.nama_kantor || "-")
}

export function formatNplMoney(value: unknown) {
  const amount = Number(value ?? 0)
  return amount === 0 ? "-" : nplNumber.format(amount)
}

export function formatNplPercent(value: unknown) {
  return `${nplDecimal.format(Number(value ?? 0))}%`
}

export function nplSortRows(rows: NplRow[], sort: NplSort) {
  const direction = sort.direction === "asc" ? 1 : -1
  return [...rows].sort((left, right) => {
    const a = sort.column === "kode_unit" ? nplCode(left) : sort.column === "nama_unit" ? nplName(left) : left[sort.column]
    const b = sort.column === "kode_unit" ? nplCode(right) : sort.column === "nama_unit" ? nplName(right) : right[sort.column]
    if (sort.column === "kode_unit" || sort.column === "nama_unit") return String(a || "").localeCompare(String(b || ""), "id-ID", { numeric: true }) * direction
    return (Number(a ?? 0) - Number(b ?? 0)) * direction
  })
}

export function nplPayload(view: NplView, filters: NplFilters) {
  const area = filters.area
  return view === "kolek"
    ? { type: "kolektibilitas", harian_date: filters.actualDate, hitung_berdasarkan: filters.nominalField, kode_kantor: area.startsWith("CAB-") ? area.slice(4) : "", korwil: area.startsWith("KOR-") ? area.slice(4) : "" }
    : { type: "NPL", closing_date: filters.closingDate, harian_date: filters.actualDate, hitung_berdasarkan: filters.nominalField, kode_kantor: area.startsWith("CAB-") ? area.slice(4) : "", korwil: area.startsWith("KOR-") ? area.slice(4) : "" }
}

export function exportNpl(rows: NplRow[], view: NplView, actualDate: string) {
  const headers = view === "kolek"
    ? ["Kode", "Nama Kantor", "Lancar", "Noa Lancar", "DPK", "Noa DPK", "KL", "Noa KL", "D", "Noa D", "M", "Noa M", "Total NPL", "Noa NPL", "Portofolio", "Noa Portofolio", "% NPL"]
    : ["Kode", "Nama Kantor", "Closing Baki Debet", "Closing %", "Actual Baki Debet", "Actual %", "Growth Baki Debet", "Growth %", "Status"]
  const values = rows.map((row) => view === "kolek"
    ? [row.kode_unit || nplCode(row), nplName(row), Number(row.bd_L || 0), Number(row.noa_L || 0), Number(row.bd_DP || 0), Number(row.noa_DP || 0), Number(row.bd_KL || 0), Number(row.noa_KL || 0), Number(row.bd_D || 0), Number(row.noa_D || 0), Number(row.bd_M || 0), Number(row.noa_M || 0), Number(row.bd_npl || 0), Number(row.noa_npl || 0), Number(row.total_bd || 0), Number(row.total_noa || 0), Number(row.persentase_npl || 0)]
    : [row.kode_unit || nplCode(row), nplName(row), Number(row.npl_closing || 0), Number(row.npl_closing_persen || 0), Number(row.npl_harian || 0), Number(row.npl_harian_persen || 0), Number(row.selisih_npl || 0), Number(row.selisih_npl_persen || 0), Number(row.selisih_npl) > 0 ? "Naik" : Number(row.selisih_npl) < 0 ? "Turun" : "Tetap"])
  downloadReportExcel(`${view === "kolek" ? "Kolektibilitas" : "Perbandingan_NPL"}_${actualDate || "actual"}`, headers, values, [0])
}
