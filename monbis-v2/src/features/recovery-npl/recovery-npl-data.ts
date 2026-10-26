import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

export type RecoveryRow = Record<string, unknown> & {
  kode_cabang?: string
  kode_unit?: string
  nama_kantor?: string
  nama_unit?: string
}
export type RecoveryOffice = { kode_kantor?: string; nama_kantor?: string; nama?: string }
export type RecoveryFilters = { closingDate: string; actualDate: string; nominalField: "saldo_bank" | "baki_debet"; area: string }
export type RecoverySort = { column: string; direction: "asc" | "desc" }
export type RecoveryDetailType = "lunas" | "backflow" | "angsuran" | "total_recovery"
export type RecoveryOption = { value: string; label: string }
type Envelope<T> = { status: number | string; message?: string; data?: T }

declare global {
  interface Window {
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
    getUser?: () => { kode?: string; kode_kantor?: string } | null
  }
}

const korwil = [
  { key: "SEMARANG", label: "Korwil Semarang", start: 1, end: 7 },
  { key: "SOLO", label: "Korwil Solo", start: 8, end: 14 },
  { key: "BANYUMAS", label: "Korwil Banyumas", start: 15, end: 21 },
  { key: "PEKALONGAN", label: "Korwil Pekalongan", start: 22, end: 28 },
] as const

export async function requestRecovery<T>(request: string, options: RequestInit = {}) {
  const fetcher = window.apiFetch?.bind(window) || fetch
  const response = await fetcher(apiEndpoint(request), {
    ...options,
    headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...options.headers },
  })
  let payload: Envelope<T>
  try { payload = await response.json() as Envelope<T> } catch { throw new Error(`API ${request} tidak mengembalikan JSON (${response.status})`) }
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return payload
}

export function recoveryUserCode(user = window.getUser?.()) {
  const value = String(user?.kode ?? "000").replace(/\D/g, "")
  return value ? value.padStart(3, "0") : "000"
}

export function recoveryCurrentUser() {
  const current = window.getUser?.()
  if (current) return current
  try {
    const stored = localStorage.getItem("app_user")
    return stored ? JSON.parse(stored) as Record<string, unknown> : null
  } catch { return null }
}

export function recoveryAccess(payload: Record<string, unknown> | null) {
  if (!payload) return false
  if (payload.admin === true) return true
  const permissions = payload.permissions as Record<string, { can_view?: boolean | number }> | undefined
  return Number(permissions?.["page:recovery_npl"]?.can_view) === 1
}

export const recoveryNumber = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })
export const recoveryPercent = new Intl.NumberFormat("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 })
export const recoveryMoney = (value: unknown) => Number(value ?? 0) === 0 ? "-" : recoveryNumber.format(Math.round(Number(value)))
export const recoveryCode = (row: RecoveryRow) => String(row.kode_cabang ?? row.kode_unit ?? "")
export const recoveryName = (row: RecoveryRow) => String(row.nama_kantor ?? row.nama_unit ?? "-")
export const recoveryMetric = (row: RecoveryRow, key: string) => Number(row[key] ?? 0)

export function recoverySortRows(rows: RecoveryRow[], sort: RecoverySort) {
  const codeCompare = (a: RecoveryRow, b: RecoveryRow) => Number(recoveryCode(a).replace(/\D/g, "") || 0) - Number(recoveryCode(b).replace(/\D/g, "") || 0)
  if (sort.column === "total_noa" || sort.column === "total_baki") {
    const key = sort.column === "total_noa" ? "total_noa_recovery" : "total_recovery"
    const noaKeys = ["noa_lunas", "noa_backflow", "noa_angsuran_npl"]
    const moneyKeys = ["baki_debet_lunas", "baki_debet_backflow", "baki_debet_angsuran_npl"]
    return [...rows].sort((a, b) => {
      const aValue = Number(a[key] ?? (sort.column === "total_noa" ? noaKeys : moneyKeys).reduce((sum, field) => sum + recoveryMetric(a, field), 0))
      const bValue = Number(b[key] ?? (sort.column === "total_noa" ? noaKeys : moneyKeys).reduce((sum, field) => sum + recoveryMetric(b, field), 0))
      const delta = (aValue - bValue) * (sort.direction === "asc" ? 1 : -1)
      return delta || codeCompare(a, b)
    })
  }
  return [...rows].sort(codeCompare)
}

export function recoveryAreas(offices: RecoveryOffice[], userCode: string): RecoveryOption[] {
  const ownOffice = offices.find((office) => String(office.kode_kantor ?? "").padStart(3, "0") === userCode)
  const ownKorwil = korwil.find((item) => Number(userCode) >= item.start && Number(userCode) <= item.end)
  if (userCode !== "000") {
    const options = [{ value: `CAB-${userCode}`, label: `${userCode} - ${ownOffice?.nama_kantor || ownOffice?.nama || "Cabang Login"}` }]
    if (ownKorwil) options.push({ value: `KOR-${ownKorwil.key}`, label: ownKorwil.label })
    return options
  }
  return [
    { value: "ALL", label: "Konsolidasi" },
    ...korwil.map((item) => ({ value: `KOR-${item.key}`, label: item.label })),
    ...offices
      .filter((office) => String(office.kode_kantor ?? "") !== "000")
      .sort((a, b) => String(a.kode_kantor ?? "").localeCompare(String(b.kode_kantor ?? "")))
      .map((office) => {
        const code = String(office.kode_kantor ?? "").padStart(3, "0")
        return { value: `CAB-${code}`, label: `${code} - ${office.nama_kantor || office.nama || `Cabang ${code}`}` }
      }),
  ]
}

export function recoveryMainPayload(filters: RecoveryFilters) {
  return {
    type: "Recovery NPL",
    closing_date: filters.closingDate,
    harian_date: filters.actualDate,
    nominal_field: filters.nominalField,
    ...(filters.area.startsWith("CAB-") ? { kode_kantor: filters.area.slice(4) } : {}),
    ...(filters.area.startsWith("KOR-") ? { korwil: filters.area.slice(4) } : {}),
  }
}

export function recoveryDetailPayload(selection: { row: RecoveryRow; type: RecoveryDetailType }, filters: RecoveryFilters, userCode: string, status = "all") {
  const rawCode = recoveryCode(selection.row)
  const payload: Record<string, string> = {
    type: selection.type,
    closing_date: filters.closingDate,
    harian_date: filters.actualDate,
    nominal_field: filters.nominalField,
  }
  const isTotal = rawCode.toUpperCase() === "TOTAL" || recoveryName(selection.row).toUpperCase().includes("TOTAL")
  if (rawCode.length > 3 && !isTotal) {
    payload.kode_kantor = rawCode.slice(0, 3)
    payload.kode_kankas = rawCode
  } else if (!isTotal && rawCode && rawCode !== "000") {
    payload.kode_kantor = rawCode.padStart(3, "0")
  } else if (filters.area.startsWith("CAB-")) {
    payload.kode_kantor = filters.area.slice(4)
  } else if (filters.area.startsWith("KOR-")) {
    payload.korwil = filters.area.slice(4)
  }
  if (userCode !== "000" && selection.type === "backflow") payload.jt_status = status
  else if (selection.type === "backflow") payload.jt_status = status
  return payload
}

export function recoveryRatio(row: RecoveryRow) {
  const closing = recoveryMetric(row, "npl_closing")
  return closing > 0 ? recoveryMetric(row, "baki_debet_flow_npl") / closing * 100 : 0
}

export function exportRecovery(rows: RecoveryRow[], total: RecoveryRow | null, filters: RecoveryFilters, sort: RecoverySort) {
  const headers = ["Kode", "Nama Kantor", "NOA Lunas", `${filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"} Lunas`, "NOA Backflow", `${filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"} Backflow`, "NOA Angsuran NPL", `${filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"} Angsuran NPL`, "Total NOA Recovery", "Total Recovery", "NOA Flow NPL", "Flow NPL", "% Flow PAR / NPL M-1", "Perbaikan (-) / Pemburukan (+)"]
  const values = [...(total ? [total] : []), ...recoverySortRows(rows, sort)].map((row) => [
    row === total ? "" : recoveryCode(row), row === total ? "GRAND TOTAL" : recoveryName(row),
    recoveryMetric(row, "noa_lunas"), recoveryMetric(row, "baki_debet_lunas"),
    recoveryMetric(row, "noa_backflow"), recoveryMetric(row, "baki_debet_backflow"),
    recoveryMetric(row, "noa_angsuran_npl"), recoveryMetric(row, "baki_debet_angsuran_npl"),
    recoveryMetric(row, "total_noa_recovery") || ["noa_lunas", "noa_backflow", "noa_angsuran_npl"].reduce((sum, key) => sum + recoveryMetric(row, key), 0),
    recoveryMetric(row, "total_recovery") || ["baki_debet_lunas", "baki_debet_backflow", "baki_debet_angsuran_npl"].reduce((sum, key) => sum + recoveryMetric(row, key), 0),
    recoveryMetric(row, "noa_flow_npl"), recoveryMetric(row, "baki_debet_flow_npl"), recoveryRatio(row), recoveryMetric(row, "net_flow_recovery"),
  ])
  downloadReportExcel(`Rekap_Recovery_NPL_${filters.actualDate || "actual"}`, headers, values, [0, 1])
}

export function recoveryDetailHasAccess(row: RecoveryRow, filters: RecoveryFilters, userCode: string) {
  if (userCode === "000") return true
  const code = recoveryCode(row)
  const total = code.toUpperCase() === "TOTAL" || recoveryName(row).toUpperCase().includes("TOTAL")
  if (total && filters.area.startsWith("KOR-")) return false
  const branch = total ? filters.area.startsWith("CAB-") ? filters.area.slice(4) : "" : code.length > 3 ? code.slice(0, 3) : code.padStart(3, "0")
  return branch === userCode
}

export function recoveryDetailLabel(type: RecoveryDetailType) {
  return type === "lunas" ? "Lunas NPL" : type === "backflow" ? "Backflow" : type === "angsuran" ? "Angsuran NPL" : "Total Recovery"
}
