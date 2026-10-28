import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

export type RbbRow = Record<string, unknown> & { kode_kantor?: string; nama_kantor?: string; periode?: string }
export type RbbMode = "auto" | "rbb" | "history"
export type RbbFilters = { actualDate: string; area: string; compareMode: RbbMode }
export type RbbSort = { column: string; direction: "asc" | "desc" }
export type Office = { kode_kantor?: string; nama_kantor?: string }
export type RbbPayload = { data?: RbbRow[]; monthly_breakdown?: RbbRow[]; grand_total?: RbbRow; meta?: Record<string, unknown> }
export type Envelope<T> = { status: number | string; message?: string; data?: T }

declare global {
  interface Window {
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
    getUser?: () => { kode?: string; kode_kantor?: string } | null
    AUTH_TOKEN?: string
  }
}

function authToken() {
  const configured = String(window.AUTH_TOKEN || "").trim()
  if (configured) return configured
  try {
    const stored = String(localStorage.getItem("dpk_token") || "").trim()
    if (stored) return stored
  } catch { /* Storage may be unavailable in restricted browser contexts. */ }
  const cookie = document.cookie.match(/(?:^|;\s*)sso_token=([^;]+)/)
  if (!cookie) return ""
  try { return decodeURIComponent(cookie[1]) } catch { return cookie[1] }
}

export function rbbUser() {
  const current = window.getUser?.()
  if (current) return current as Record<string, unknown>
  for (const key of ["dpk_user", "app_user", "user"]) {
    try {
      const value = localStorage.getItem(key)
      if (value) return JSON.parse(value) as Record<string, unknown>
    } catch { /* Ignore malformed cached profile; RBAC remains authoritative. */ }
  }
  return null
}

export async function requestRbb<T>(request: string, options: RequestInit = {}) {
  const fetcher = window.apiFetch?.bind(window) || fetch
  const token = authToken()
  const response = await fetcher(apiEndpoint(request), {
    ...options,
    credentials: "same-origin",
    headers: {
      Accept: "application/json",
      ...(options.body ? { "Content-Type": "application/json" } : {}),
      ...(token ? { Authorization: /^Bearer\s/i.test(token) ? token : `Bearer ${token}` } : {}),
      ...options.headers,
    },
  })
  let payload: Envelope<T>
  try { payload = await response.json() as Envelope<T> } catch { throw new Error(`API ${request} tidak mengembalikan JSON (${response.status})`) }
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return payload
}

export function officeCode() {
  const user = rbbUser() as Record<string, unknown> | null
  const raw = user?.kode ?? user?.kode_kantor ?? user?.branch_code ?? user?.kode_cabang ?? ""
  if (!raw) return "000"
  const code = String(raw).replace(/\D/g, "").padStart(3, "0").slice(-3)
  return code === "099" ? "000" : code
}

export function rbbAccess(payload: Record<string, unknown> | null) {
  if (!payload) return false
  if (payload.admin === true) return true
  const permissions = payload.permissions as Record<string, { can_view?: boolean }> | undefined
  return Number(permissions?.["page:realisasi_rbb"]?.can_view) === 1
}

export const numberFormat = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })
export const percentFormat = new Intl.NumberFormat("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 })
export const nominal = (value: unknown) => Number(value ?? 0) === 0 ? "-" : numberFormat.format(Math.round(Number(value)))
export const percent = (value: unknown) => `${percentFormat.format(Number(value ?? 0))}%`
export const signedNominal = (value: unknown) => {
  const amount = Number(value ?? 0)
  return amount === 0 ? "-" : `${amount > 0 ? "+" : "−"} ${numberFormat.format(Math.abs(Math.round(amount)))}`
}
export const dateLabel = (value: string) => {
  const [year, month, day] = value.split("-")
  return year && month && day ? `${day}/${month}/${year}` : value
}
export function monthLabel(value: unknown) {
  const text = String(value ?? "")
  const date = new Date(`${text.slice(0, 10)}T00:00:00`)
  return Number.isNaN(date.getTime()) ? text : new Intl.DateTimeFormat("id-ID", { month: "long", year: "numeric" }).format(date)
}
export function sortRbbRows(rows: RbbRow[], sort: RbbSort) {
  const direction = sort.direction === "asc" ? 1 : -1
  return [...rows].sort((left, right) => {
    const a = left[sort.column]
    const b = right[sort.column]
    const numericA = Number(a)
    const numericB = Number(b)
    const numeric = a !== "" && b !== "" && Number.isFinite(numericA) && Number.isFinite(numericB)
    const comparison = numeric ? numericA - numericB : String(a ?? "").localeCompare(String(b ?? ""), "id-ID", { numeric: true })
    return comparison * direction
  })
}
export function exportRbb(rows: RbbRow[], monthly: boolean, history: boolean, meta: Record<string, unknown>, filename: string) {
  if (!rows.length) return
  const idHeaders = monthly ? ["Bulan", "Kantor"] : ["Kode", "Kantor"]
  const idValues = (row: RbbRow): (string | number)[] => monthly ? [monthLabel(row.periode), String(row.nama_kantor ?? "")] : [String(row.kode_kantor ?? ""), String(row.nama_kantor ?? "")]
  let headers: string[]
  let values: (string | number)[][]
  if (history) {
    headers = [...idHeaders, `Realisasi ${meta.tahun || "Tahun Ini"}`, "Run Off", "Growth", "% Growth", `Realisasi ${meta.tahun_pembanding || "Tahun Lalu"}`, "Selisih YoY", "% YoY"]
    values = rows.map((row) => [...idValues(row), ...["realisasi_bulan_ini", "run_off", "growth"].map((key) => Number(row[key] ?? 0)), Number(row.growth_persen ?? 0), Number(row.realisasi_tahun_lalu ?? 0), Number(row.selisih ?? 0), Number(row.yoy_persen ?? 0)])
  } else if (monthly) {
    headers = [...idHeaders, "Nilai RBB", "Realisasi", "% RBB", "Selisih", "Kekurangan"]
    values = rows.map((row) => [...idValues(row), ...["nilai_rbb", "realisasi_bulan_ini", "persentase_rbb_bulan_ini", "selisih", "kekurangan"].map((key) => Number(row[key] ?? 0))])
  } else {
    headers = [...idHeaders, "Nilai RBB", "Realisasi Bulan Ini", "% RBB", "Kekurangan s/d Bulan Lalu", "Total Beban Target", "% Beban"]
    values = rows.map((row) => [...idValues(row), ...["nilai_rbb", "realisasi_bulan_ini", "persentase_rbb_bulan_ini", "kekurangan_sd_bulan_lalu", "total_beban_target", "persentase_rbb_plus_kekurangan"].map((key) => Number(row[key] ?? 0))])
  }
  downloadReportExcel(filename, headers, values, [0])
}
