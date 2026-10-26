import { downloadReportExcel } from "@/lib/download-report-excel"
import { apiEndpoint } from "@/lib/api-base"

export type FlowParRow = Record<string, unknown> & { kode_cabang?: string; kode_unit?: string; nama_kantor?: string; nama_unit?: string }
export type FlowParSort = { column: string; direction: "asc" | "desc" }
export type FlowParFilters = { closingDate: string; actualDate: string; nominalField: "saldo_bank" | "baki_debet"; area: string }
export type FlowParOffice = { kode_kantor?: string; nama_kantor?: string; nama?: string }
export type FlowParDetailSelection = { kode: string; korwil: string; klasifikasi: string; label: string }
type Envelope<T> = { status?: number | string; message?: string; data?: T }

export const flowParCategories = [
  { noa: "noa_jt_lain", nominal: "nom_jt_lain", kind: "jt_lain", label: "Jatuh Tempo / Lainnya" },
  { noa: "noa_pokok_90", nominal: "nom_pokok_90", kind: "pokok_90", label: "DPD Pokok > 90" },
  { noa: "noa_bunga_90", nominal: "nom_bunga_90", kind: "bunga_90", label: "DPD Bunga > 90" },
  { noa: "noa_pokok_bunga_90", nominal: "nom_pokok_bunga_90", kind: "pokok_bunga_90", label: "DPD Pokok + Bunga > 90" },
] as const

const korwil = [
  { key: "SEMARANG", label: "Korwil Semarang", start: 1, end: 7 }, { key: "SOLO", label: "Korwil Solo", start: 8, end: 14 },
  { key: "BANYUMAS", label: "Korwil Banyumas", start: 15, end: 21 }, { key: "PEKALONGAN", label: "Korwil Pekalongan", start: 22, end: 28 },
] as const

export const flowParNumber = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })
export const flowParCode = (row: FlowParRow) => String(row.kode_cabang ?? row.kode_unit ?? "").padStart(3, "0")
export const flowParName = (row: FlowParRow) => String(row.nama_kantor ?? row.nama_unit ?? "-")
export const flowParValue = (row: FlowParRow, key: string) => Number(row[key] ?? 0)

export function flowParCurrentUser() {
  const current = window.getUser?.() as Record<string, unknown> | undefined
  if (current) return current
  try { const stored = localStorage.getItem("app_user"); return stored ? JSON.parse(stored) as Record<string, unknown> : null } catch { return null }
}
export function flowParUserCode(user = flowParCurrentUser()) {
  const raw = String(user?.kode ?? user?.kode_kantor ?? user?.kode_cabang ?? "000").replace(/\D/g, "")
  const code = raw ? raw.padStart(3, "0") : "000"
  return code === "099" ? "000" : code
}
export function flowParAccess(payload: Record<string, unknown> | null) {
  if (!payload) return false
  if (payload.admin === true) return true
  const permissions = payload.permissions as Record<string, { can_view?: boolean | number }> | undefined
  return Number(permissions?.["page:flow_par"]?.can_view) === 1
}
export async function requestFlowPar<T>(endpoint: string, options: RequestInit = {}) {
  const fetcher = window.apiFetch?.bind(window) || fetch
  const response = await fetcher(apiEndpoint(endpoint), { ...options, headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...options.headers } })
  let result: Envelope<T>
  try { result = await response.json() as Envelope<T> } catch { throw new Error(`API ${endpoint} tidak mengembalikan JSON (${response.status})`) }
  if (!response.ok || Number(result.status) !== 200) throw new Error(result.message || `Request gagal (${response.status})`)
  return result
}
export function flowParAreas(offices: FlowParOffice[], userCode: string) {
  if (userCode !== "000") {
    const own = offices.find((item) => String(item.kode_kantor ?? "").padStart(3, "0") === userCode)
    return [{ value: `CAB-${userCode}`, label: `${userCode} - ${own?.nama_kantor || own?.nama || "Cabang saya"}` }]
  }
  return [
    { value: "ALL", label: "Konsolidasi" }, ...korwil.map((item) => ({ value: `KOR-${item.key}`, label: item.label })),
    ...offices.filter((item) => String(item.kode_kantor ?? "").padStart(3, "0") !== "000").sort((a, b) => String(a.kode_kantor ?? "").localeCompare(String(b.kode_kantor ?? ""), "id-ID", { numeric: true })).map((item) => {
      const code = String(item.kode_kantor ?? "").padStart(3, "0")
      return { value: `CAB-${code}`, label: `${code} - ${item.nama_kantor || item.nama || `Cabang ${code}`}` }
    }),
  ]
}
export function flowParPayload(filters: FlowParFilters) {
  return { type: "Flow Par", closing_date: filters.closingDate, harian_date: filters.actualDate, hitung_berdasarkan: filters.nominalField,
    ...(filters.area.startsWith("KOR-") ? { korwil: filters.area.slice(4) } : {}), ...(filters.area.startsWith("CAB-") ? { kode_kantor: filters.area.slice(4) } : {}) }
}
export function flowParDetailPayload(selection: FlowParDetailSelection, filters: FlowParFilters, kankas = "") {
  return { type: "KL Baru", kode_kantor: selection.kode === "000" ? "" : selection.kode.slice(0, 3), korwil: selection.kode === "000" ? selection.korwil : "", kode_kankas: kankas, closing_date: filters.closingDate, harian_date: filters.actualDate, hitung_berdasarkan: filters.nominalField, klasifikasi_flow: selection.klasifikasi }
}
export function flowParSortRows(rows: FlowParRow[], sort: FlowParSort) {
  const direction = sort.direction === "asc" ? 1 : -1
  return [...rows].sort((a, b) => {
    const left = !sort.column || sort.column === "kode" ? flowParCode(a) : sort.column === "nama" ? flowParName(a) : flowParValue(a, sort.column === "noa" ? "noa_flow" : "baki_debet_flow")
    const right = !sort.column || sort.column === "kode" ? flowParCode(b) : sort.column === "nama" ? flowParName(b) : flowParValue(b, sort.column === "noa" ? "noa_flow" : "baki_debet_flow")
    const delta = typeof left === "string" && typeof right === "string" ? left.localeCompare(right, "id-ID", { numeric: true }) : Number(left) - Number(right)
    return delta * direction
  })
}
export function flowParHasCommitment(row: FlowParRow) {
  const text = String(row.komitmen ?? "").trim().toLowerCase()
  return Boolean((text && text !== "-" && !["belum ada", "belum komitmen", "tidak ada"].includes(text)) || row.tgl_pembayaran || Number(row.nominal ?? 0) > 0)
}
export function flowParDate(value: unknown) {
  const text = String(value ?? "")
  if (!text || text === "-") return "-"
  const date = new Date(text.length === 10 ? `${text}T00:00:00` : text)
  return Number.isNaN(date.getTime()) ? text : `${String(date.getDate()).padStart(2, "0")}/${String(date.getMonth() + 1).padStart(2, "0")}/${date.getFullYear()}`
}
export function exportFlowPar(rows: FlowParRow[], total: FlowParRow | null, filters: FlowParFilters, sort: FlowParSort) {
  const nominal = filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"
  const headers = ["Kode", "Nama Kantor", "NOA Flow", `${nominal} Flow`, ...flowParCategories.flatMap((item) => [`NOA ${item.label}`, `${nominal} ${item.label}`])]
  const dataset = [...(total ? [total] : []), ...flowParSortRows(rows, sort)]
  const values = dataset.map((row) => [row === total ? "" : flowParCode(row), row === total ? "GRAND TOTAL" : flowParName(row), flowParValue(row, "noa_flow"), flowParValue(row, "baki_debet_flow"), ...flowParCategories.flatMap((item) => [flowParValue(row, item.noa), flowParValue(row, item.nominal)])])
  downloadReportExcel(`Rekap_Flow_PAR_${filters.actualDate || "actual"}`, headers, values, [0, 1])
}
export function exportFlowParDetail(rows: FlowParRow[], filters: FlowParFilters) {
  const nominal = filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"
  const headers = ["No Rekening", "Nama Nasabah", "Kolektibilitas", "Alamat", nominal, "Tunggakan Pokok", "Tunggakan Bunga", "Total Tunggakan", "Saldo Tabungan", "Tanggal Jatuh Tempo", "DPD", "DPD Pokok", "DPD Bunga", "Angsuran Pokok", "Angsuran Bunga", "Tanggal Transaksi", "Status Komitmen", "Komitmen", "Tanggal Janji Bayar", "Nominal Janji Bayar"]
  const values = rows.map((row) => [row.no_rekening, row.nama_nasabah, row.kolek_harian || row.kolektibilitas || row.kolek, row.alamat, row.baki_debet, row.tunggakan_pokok, row.tunggakan_bunga, row.total_tunggakan, row.saldo_akhir, row.tgl_jatuh_tempo, row.hari_menunggak, row.hari_menunggak_pokok, row.hari_menunggak_bunga, row.angsuran_pokok, row.angsuran_bunga, row.tgl_trans, flowParHasCommitment(row) ? "Sudah Komitmen" : "Belum Komitmen", row.komitmen, row.tgl_pembayaran, row.nominal] as Array<string | number | null | undefined>)
  downloadReportExcel(`Detail_FlowPAR_${filters.actualDate}`, headers, values, [0, 1, 2, 3, 9, 15, 16, 17, 18])
}
