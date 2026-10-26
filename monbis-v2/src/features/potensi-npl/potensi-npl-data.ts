import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

export type PotensiRow = Record<string, unknown> & { kode_cabang?: string; kode_unit?: string; nama_cabang?: string; nama_kantor?: string }
export type PotensiOffice = { kode_kantor?: string; nama_kantor?: string; kode_group1?: string; deskripsi_group1?: string }
export type PotensiFilters = { closingDate: string; actualDate: string; nominalField: "saldo_bank" | "baki_debet"; area: string }
export type PotensiSort = { column: string; direction: "asc" | "desc" }
export type PotensiDetailFilters = { status: string; commitment: string; search: string; kankas: string; ao: string }
type Envelope<T> = { status: number | string; message?: string; data?: T }

declare global {
  interface Window {
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
  }
}

export const potensiNumber = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 })
export const potensiMoney = (value: unknown) => Number(value ?? 0) === 0 ? "-" : potensiNumber.format(Math.round(Number(value)))
export const potensiCode = (row: PotensiRow) => String(row.kode_cabang ?? row.kode_unit ?? "").padStart(3, "0")
export const potensiName = (row: PotensiRow) => String(row.nama_cabang ?? row.nama_kantor ?? "-")
export const potensiMetric = (row: PotensiRow, key: string) => Number(row[key] ?? 0)

export async function requestPotensi<T>(request: string, options: RequestInit = {}) {
  const fetcher = window.apiFetch?.bind(window) || fetch
  const response = await fetcher(apiEndpoint(request), { ...options, headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...options.headers } })
  let payload: Envelope<T>
  try { payload = await response.json() as Envelope<T> } catch { throw new Error(`API ${request} tidak mengembalikan JSON (${response.status})`) }
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return payload
}

export function potensiUser(user = window.getUser?.()) {
  if (user) return user as Record<string, unknown>
  try { return JSON.parse(localStorage.getItem("app_user") || "null") as Record<string, unknown> | null } catch { return null }
}

export function potensiUserCode(user: Record<string, unknown> | null) {
  const code = String(user?.kode ?? user?.kode_kantor ?? user?.kode_cabang ?? "000").replace(/\D/g, "")
  const normalized = code ? code.padStart(3, "0") : "000"
  return normalized === "099" ? "000" : normalized
}

export function potensiAccess(payload: Record<string, unknown> | null) {
  if (!payload) return false
  if (payload.admin === true) return true
  const permissions = payload.permissions as Record<string, { can_view?: boolean | number }> | undefined
  return Number(permissions?.["page:potensi_npl"]?.can_view) === 1
}

const korwil = [
  { key: "SEMARANG", label: "Korwil Semarang", start: 1, end: 7 },
  { key: "SOLO", label: "Korwil Solo", start: 8, end: 14 },
  { key: "BANYUMAS", label: "Korwil Banyumas", start: 15, end: 21 },
  { key: "PEKALONGAN", label: "Korwil Pekalongan", start: 22, end: 28 },
] as const

export function potensiAreas(offices: PotensiOffice[], userCode: string) {
  const ownOffice = offices.find((office) => String(office.kode_kantor ?? "").padStart(3, "0") === userCode)
  if (userCode !== "000") return [{ value: userCode, label: `${userCode} - ${ownOffice?.nama_kantor || "Cabang Login"}` }]
  return [
    { value: "", label: "Konsolidasi (Semua)" },
    ...korwil.map((item) => ({ value: `KOR-${item.key}`, label: item.label })),
    ...offices.filter((office) => String(office.kode_kantor ?? "").padStart(3, "0") !== "000")
      .sort((a, b) => String(a.kode_kantor ?? "").localeCompare(String(b.kode_kantor ?? ""), "id-ID", { numeric: true }))
      .map((office) => { const code = String(office.kode_kantor ?? "").padStart(3, "0"); return { value: code, label: `${code} - ${office.nama_kantor || `Kantor ${code}`}` } }),
  ]
}

export function potensiMainPayload(filters: PotensiFilters) {
  return {
    type: "Potensi NPL", closing_date: filters.closingDate, harian_date: filters.actualDate,
    kode_kantor: /^\d{3}$/.test(filters.area) && filters.area !== "000" ? filters.area : "",
    korwil: filters.area.startsWith("KOR-") ? filters.area.slice(4) : "",
    hitung_berdasarkan: filters.nominalField,
  }
}

export function potensiDetailPayload(filters: PotensiFilters, office: string, detail: PotensiDetailFilters) {
  return {
    type: "Debitur Potensi NPL", kode_kantor: office === "000" ? "" : office,
    korwil: filters.area.startsWith("KOR-") ? filters.area.slice(4) : "",
    kode_kankas: detail.kankas, kode_ao: detail.ao,
    closing_date: filters.closingDate, harian_date: filters.actualDate,
    hitung_berdasarkan: filters.nominalField, status_filter: "ALL", include_lunas: 1, include_aman_backflow: 1,
  }
}

export function potensiStatus(row: PotensiRow) {
  const status = String(row.status_potensi ?? row.status_saat_ini ?? row.status_update ?? row.status ?? "").trim().toUpperCase().replace(/[_-]+/g, " ").replace(/\s+/g, " ")
  if (Number(row.is_lunas ?? row.lunas ?? 0) === 1 || /LUNAS|AMAN|BACKFLOW|SELAMAT|PERBAIKAN/.test(status)) return "AMAN"
  if (/FLOW|KL\/D\/M|NPL/.test(status)) return "FLOW KOLEK"
  if (status.includes("JATUH TEMPO") || status === "JT") return "JATUH TEMPO"
  if (potensiMetric(row, "baki_debet_closing") > 0 && potensiMetric(row, "baki_debet_harian") <= 0) return "AMAN"
  return "MASIH POTENSI"
}

export function potensiDisplayStatus(row: PotensiRow) {
  return String(row.status_potensi ?? row.status_saat_ini ?? row.status_update ?? row.status ?? "").trim() || (potensiStatus(row) === "AMAN" ? "LUNAS / AMAN" : potensiStatus(row))
}

export function potensiCommitment(row: PotensiRow) {
  return {
    komitmen: String(row.komitmen ?? ""), tanggal: String(row.tgl_pembayaran ?? row.tgl_janji_bayar ?? ""),
    nominal: Number(row.nominal ?? row.nominal_janji_bayar ?? 0), alasan: String(row.alasan ?? row.alasan_keterlambatan ?? ""),
  }
}

export function potensiHasCommitment(row: PotensiRow) {
  const commitment = potensiCommitment(row)
  return Boolean(commitment.komitmen.trim() || commitment.tanggal.trim() || commitment.nominal > 0)
}

export function potensiSortRows(rows: PotensiRow[], sort: PotensiSort) {
  const direction = sort.direction === "asc" ? 1 : -1
  return [...rows].sort((a, b) => {
    if (sort.column === "kode_cabang") return (Number(potensiCode(a)) - Number(potensiCode(b))) * direction
    return (potensiMetric(a, sort.column) - potensiMetric(b, sort.column)) * direction
  })
}

export function exportPotensi(rows: PotensiRow[], total: PotensiRow | null, filters: PotensiFilters, sort: PotensiSort) {
  const headers = ["Kode", "Nama Kantor", "Total NOA", "Total Nominal", "Aman / Lunas NOA", "Aman / Lunas Nominal", "Jatuh Tempo NOA", "Jatuh Tempo Nominal", "Flow Kolek NOA", "Flow Kolek Nominal", "Masih Potensi NOA", "Masih Potensi Nominal"]
  const values = [...(total ? [total] : []), ...potensiSortRows(rows, sort)].map((row) => [
    row === total ? "" : potensiCode(row), row === total ? potensiName(row) : potensiName(row),
    potensiMetric(row, "total_noa"), potensiMetric(row, "total_baki"), potensiMetric(row, "noa_aman"), potensiMetric(row, "baki_aman"),
    potensiMetric(row, "noa_jt"), potensiMetric(row, "baki_jt"), potensiMetric(row, "noa_flow"), potensiMetric(row, "baki_flow"),
    potensiMetric(row, "noa_potensi"), potensiMetric(row, "baki_potensi"),
  ])
  downloadReportExcel(`Rekap_Potensi_NPL_${filters.actualDate || "actual"}`, headers, values, [0, 1])
}

export function exportPotensiDetail(rows: PotensiRow[], filters: PotensiFilters) {
  const nominalLabel = filters.nominalField === "saldo_bank" ? "Saldo Bank" : "Baki Debet"
  const headers = ["No Rekening", "Nama Nasabah", "Alamat", "Nama AO", "Status Potensi", "Kolek Closing", `${nominalLabel} Closing`, "Kolek Actual", `${nominalLabel} Actual`, "Tunggakan Pokok", "Tunggakan Bunga", "Saldo Tabungan", "Jatuh Tempo", "Hari Menunggak", "Tunggakan Pokok (hari)", "Tunggakan Bunga (hari)", "Angsuran Pokok", "Angsuran Bunga", "Transaksi Terakhir", "Status Komitmen", "Komitmen", "Tanggal Janji", "Nominal Janji", "Alasan"]
  const values = rows.map((row) => { const commitment = potensiCommitment(row); return [
    String(row.no_rekening ?? ""), String(row.nama_nasabah ?? ""), String(row.alamat ?? ""), String(row.nama_ao ?? ""), potensiDisplayStatus(row),
    String(row.kolek_closing ?? ""), potensiMetric(row, "baki_debet_closing"), String(row.kolek_harian ?? ""), potensiMetric(row, "baki_debet_harian"),
    potensiMetric(row, "tunggakan_pokok"), potensiMetric(row, "tunggakan_bunga"), potensiMetric(row, "saldo_akhir"), String(row.jt_harian ?? ""),
    potensiMetric(row, "hm_harian"), potensiMetric(row, "hmp_harian"), potensiMetric(row, "hmb_harian"), potensiMetric(row, "angsuran_pokok"),
    potensiMetric(row, "angsuran_bunga"), String(row.tgl_trans_terakhir ?? ""), potensiHasCommitment(row) ? "SUDAH" : "BELUM",
    commitment.komitmen, commitment.tanggal, commitment.nominal, commitment.alasan,
  ] })
  downloadReportExcel(`Detail_Potensi_NPL_${filters.actualDate}`, headers, values, [0])
}
