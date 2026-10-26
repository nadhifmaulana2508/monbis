import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

export type LabaRugiRow = { kode_perk?: string; nama_perkiraan?: string; saldo?: number; depth?: number }
export type LabaRugiData = {
  tanggal?: string; kode_kantor?: string; korwil?: string; scope_label?: string; pajak_dihitung?: boolean
  pendapatan?: LabaRugiRow[]; biaya?: LabaRugiRow[]
  totals?: { pendapatan?: number; biaya?: number; laba_kotor?: number; pajak?: number; laba_bersih?: number }
}
export type LabaRugiArea = { value: string; label: string }
type Envelope<T> = { status?: number | string; message?: string; data?: T }
export const emptyLabaRugiRows: LabaRugiRow[] = []

export async function requestLabaRugi<T>(endpoint: string, options: RequestInit = {}) {
  const fetcher = window.apiFetch?.bind(window) || fetch
  const response = await fetcher(apiEndpoint(endpoint), { ...options, headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...options.headers } })
  let result: Envelope<T>
  try { result = await response.json() as Envelope<T> } catch { throw new Error(`API ${endpoint} tidak mengembalikan JSON (${response.status})`) }
  if (!response.ok || Number(result.status) !== 200) throw new Error(result.message || `Request gagal (${response.status})`)
  return result
}

export function labaRugiUser() {
  const current = window.getUser?.() as Record<string, unknown> | undefined
  if (current) return current
  try { const stored = localStorage.getItem("app_user"); return stored ? JSON.parse(stored) as Record<string, unknown> : null } catch { return null }
}

export function labaRugiUserCode(user = labaRugiUser()) {
  const raw = String(user?.kode ?? "000").replace(/\D/g, "")
  const code = raw ? raw.padStart(3, "0") : "000"
  return code
}

export function labaRugiAccess(payload: Record<string, unknown> | null) {
  if (!payload) return false
  if (payload.admin === true) return true
  const permissions = payload.permissions as Record<string, { can_view?: boolean | number }> | undefined
  return Number(permissions?.["page:lap_laba_rugi"]?.can_view) === 1
}

export function labaRugiAreas(offices: Array<{ kode_kantor?: string; nama_kantor?: string; nama?: string }>, userCode: string): LabaRugiArea[] {
  if (userCode !== "000") {
    const own = offices.find((item) => String(item.kode_kantor ?? "").padStart(3, "0") === userCode)
    return [{ value: userCode, label: `${userCode} - ${own?.nama_kantor || own?.nama || "Cabang"}` }]
  }
  const korwil = ["SEMARANG", "SOLO", "BANYUMAS", "PEKALONGAN"]
  return [
    { value: "000", label: "000 - Konsolidasi" }, { value: "pusat", label: "000 - Pusat" },
    ...korwil.map((key) => ({ value: `KW_${key}`, label: `Korwil ${key.charAt(0)}${key.slice(1).toLowerCase()}` })),
    ...offices.filter((item) => item.kode_kantor && String(item.kode_kantor).padStart(3, "0") !== "000")
      .sort((a, b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor), "id-ID", { numeric: true }))
      .map((item) => { const code = String(item.kode_kantor).padStart(3, "0"); return { value: code, label: `${code} - ${item.nama_kantor || item.nama || "Cabang"}` } }),
  ]
}

export function labaRugiPayload(date: string, area: string) {
  return { type: "lap_laba_rugi_actual", harian_date: date, kode_kantor: area.startsWith("KW_") ? "000" : area, korwil: area.startsWith("KW_") ? area.slice(3) : "" }
}

export function labaRugiCode(row: LabaRugiRow) { return String(row.kode_perk ?? "").trim() }
export function labaRugiAmount(row: LabaRugiRow) { return Number(row.saldo ?? 0) }
export const labaRugiNumber = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })

export function exportLabaRugi(data: LabaRugiData) {
  const revenue = data.pendapatan || []
  const expense = data.biaya || []
  const maxRows = Math.max(revenue.length, expense.length)
  const rows: Array<Array<string | number>> = []
  for (let index = 0; index < maxRows; index++) {
    const income = revenue[index]
    const cost = expense[index]
    rows.push([income?.kode_perk || "", income?.nama_perkiraan || "", income ? labaRugiAmount(income) : "", cost?.kode_perk || "", cost?.nama_perkiraan || "", cost ? labaRugiAmount(cost) : ""])
  }
  const totals = data.totals || {}
  rows.push(["", "Total Pendapatan", Number(totals.pendapatan || 0), "", "Total Biaya", Number(totals.biaya || 0)])
  rows.push(["LR_KOTOR", "LABA RUGI KOTOR", Number(totals.laba_kotor || 0), "", "", ""])
  if (data.pajak_dihitung) {
    rows.push(["PAJAK", "PAJAK", Number(totals.pajak || 0), "", "", ""])
    rows.push(["LR_BERSIH", "LABA RUGI BERSIH", Number(totals.laba_bersih || 0), "", "", ""])
  }
  return downloadReportExcel(`Laba_Rugi_Aktual_${data.tanggal || "data"}`, ["Kode Pendapatan", "Uraian Pendapatan", "Saldo Pendapatan", "Kode Biaya", "Uraian Biaya", "Saldo Biaya"], rows, [0, 1, 3, 4])
}
