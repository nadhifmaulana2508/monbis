import type {
  JtApiEnvelope,
  JtAreaOption,
  JtBreakdown,
  JtCategory,
  JtDetailRow,
  JtFilters,
  JtNominalField,
  JtReportRow,
} from "./jatuh-tempo-types"
import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

declare global {
  interface Window {
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
    getUser?: () => { kode?: string; kode_kantor?: string } | null
  }
}

export const jtNumberFormat = new Intl.NumberFormat("id-ID")

export async function requestJtJson<T>(
  request: string,
  options: RequestInit = {},
): Promise<JtApiEnvelope<T>> {
  const requestUrl = apiEndpoint(request)
  const requestWithAuth = typeof window !== "undefined" && window.apiFetch
    ? window.apiFetch.bind(window)
    : fetch
  const response = await requestWithAuth(requestUrl, {
    ...options,
    headers: {
      Accept: "application/json",
      ...(options.body ? { "Content-Type": "application/json" } : {}),
      ...options.headers,
    },
  })
  let payload: JtApiEnvelope<T>
  try {
    payload = (await response.json()) as JtApiEnvelope<T>
  } catch {
    throw new Error(`API ${request} tidak mengembalikan JSON (${response.status})`)
  }
  if (!response.ok || Number(payload.status) !== 200) {
    throw new Error(payload.message || `Request gagal (${response.status})`)
  }
  return payload
}

function formatDateInput(date: Date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, "0")
  const day = String(date.getDate()).padStart(2, "0")
  return `${year}-${month}-${day}`
}

export function previousMonthEnd(value: string) {
  const [year, month] = value.split("-").map(Number)
  if (!year || !month) return formatDateInput(new Date())
  return formatDateInput(new Date(year, month - 1, 0))
}

export function defaultJtFilters(): JtFilters {
  const today = formatDateInput(new Date())
  const [year, month] = today.split("-")
  return {
    actualDate: today,
    closingDate: previousMonthEnd(today),
    month,
    year,
    nominalField: "saldo_bank",
    area: "ALL",
    includeDp: false,
  }
}

export function formatJtNominal(value: unknown) {
  const number = Number(value ?? 0)
  return number === 0 ? "-" : jtNumberFormat.format(Math.round(number))
}

export function formatJtCount(value: unknown) {
  const number = Number(value ?? 0)
  return number === 0 ? "-" : jtNumberFormat.format(Math.round(number))
}

export function formatJtPercent(value: unknown) {
  return `${Number(value ?? 0).toFixed(2).replace(".", ",")}%`
}

export function formatJtDate(value: string | undefined) {
  const raw = String(value || "")
  const match = raw.match(/^(\d{4})-(\d{2})-(\d{2})/)
  return match ? `${match[3]}/${match[2]}/${match[1]}` : raw || "-"
}

export function getJtNominalLabel(field: JtNominalField) {
  return field === "baki_debet" ? "Baki Debet" : "Saldo Bank"
}

export function getJtCategoryLabel(category: JtCategory) {
  return {
    POTENSI: "Potensi Kredit",
    REFINANCING: "Refinancing",
    LUNAS: "Lunas (Prospek)",
    BELUM_LUNAS: "Belum Lunas",
  }[category]
}

export function isJtClear(row: JtDetailRow) {
  const status = String(row.keterangan_status || "").toUpperCase()
  return status.includes("SUDAH") || status === "LUNAS" || status === "LUNAS (POTENSI)"
}

export function isJtDrop(row: JtDetailRow) {
  return String(row.keterangan_status || "").toUpperCase().includes("DROP")
}

export function getJtStatusTone(row: JtDetailRow) {
  const status = String(row.keterangan_status || "").toUpperCase()
  if (status.includes("SUDAH")) return "border-signal-green/40 bg-signal-green/10 text-signal-green-foreground"
  if (status === "LUNAS" || status === "LUNAS (POTENSI)") return "border-primary/30 bg-primary/10 text-primary"
  if (status.includes("TOP UP")) return "border-primary/30 bg-primary/10 text-primary"
  if (status.includes("BELUM")) return "border-destructive/30 bg-destructive/10 text-destructive"
  return "border-border bg-muted text-muted-foreground"
}

export function getUserOfficeCode() {
  const user = typeof window !== "undefined" ? window.getUser?.() : null
  const value = user?.kode_kantor || user?.kode || "000"
  return String(value).padStart(3, "0")
}

export function buildJtAreaOptions(data: Array<{ kode_kantor?: string; nama_kantor?: string }>) {
  const offices: JtAreaOption[] = data
    .filter((item) => item.kode_kantor && item.kode_kantor !== "000")
    .sort((left, right) => String(left.kode_kantor).localeCompare(String(right.kode_kantor)))
    .map((item) => {
      const code = String(item.kode_kantor).padStart(3, "0")
      return { value: `CAB-${code}`, label: `${code} - ${item.nama_kantor || `Cabang ${code}`}` }
    })
  return offices
}

export function buildJtRekapPayload(filters: JtFilters, userCode: string, breakdown: string | null) {
  const payload: Record<string, unknown> = {
    type: "rekap prospek jatuh tempo",
    closing_date: filters.closingDate,
    harian_date: filters.actualDate,
    bulan: filters.month,
    tahun: filters.year,
    nominal_field: filters.nominalField,
    hitung_berdasarkan: filters.nominalField,
    kolektibilitas: filters.includeDp ? ["L", "DP"] : ["L"],
    kode_kantor: null,
    korwil: null,
    breakdown_by: breakdown,
  }
  if (filters.area.startsWith("KOR-")) payload.korwil = filters.area.replace("KOR-", "")
  else if (filters.area.startsWith("CAB-")) payload.kode_kantor = filters.area.replace("CAB-", "")
  if (userCode !== "000" && !payload.kode_kantor && !payload.korwil) payload.kode_kantor = userCode
  return payload
}

export function buildJtDetailPayload(
  filters: JtFilters,
  userCode: string,
  selection: { kode: string; groupType: string; groupCode: string; category: JtCategory; isTotal?: boolean },
  page: number,
  kodeKankas: string,
  kodeAo: string,
) {
  const payload: Record<string, unknown> = {
    type: "detail prospek jatuh tempo",
    closing_date: filters.closingDate,
    harian_date: filters.actualDate,
    bulan: filters.month,
    tahun: filters.year,
    nominal_field: filters.nominalField,
    hitung_berdasarkan: filters.nominalField,
    kolektibilitas: filters.includeDp ? ["L", "DP"] : ["L"],
    kode_kantor: null,
    korwil: null,
    kode_kankas: selection.isTotal || selection.groupType !== "KANKAS" ? null : selection.groupCode,
    kode_ao: selection.isTotal || selection.groupType !== "AO" ? null : selection.groupCode,
    kategori: selection.category,
    page,
    limit: 20,
  }
  if (filters.area.startsWith("KOR-")) payload.korwil = filters.area.replace("KOR-", "")
  else if (filters.area.startsWith("CAB-")) payload.kode_kantor = filters.area.replace("CAB-", "")
  else if (selection.isTotal && userCode !== "000") payload.kode_kantor = userCode
  else if (!selection.isTotal && selection.kode && selection.kode !== "ALL") payload.kode_kantor = selection.kode
  if (kodeKankas) payload.kode_kankas = kodeKankas
  if (kodeAo) payload.kode_ao = kodeAo
  return payload
}

export function downloadJtDetailExcel(rows: JtDetailRow[], filters: JtFilters, code: string) {
  if (rows.length === 0) return false
  const sortedRows = [...rows].sort((left, right) => String(left.tgl_jatuh_tempo || "").localeCompare(String(right.tgl_jatuh_tempo || "")))
  const headers = ["No Rekening", "Nama Nasabah", "Nama Produk", "Alamat", "No HP", "Kankas", "Nama AO", "Plafond Lama", "Sisa Baki Debet", "Tanggal Jatuh Tempo", "Status", "Plafond Baru", "Tanggal Realisasi Baru"]
  const values = sortedRows.map((row) => {
    const clear = isJtClear(row)
    const sisa = clear ? 0 : Math.round(Number(row.baki_debet_lama || 0))
    return [String(row.no_rekening_lama || "-"), row.nama_nasabah || "-", row.nama_produk || (row.kode_produk ? `PRODUK ${row.kode_produk}` : "-"), row.alamat || "-", String(row.no_hp || "-"), row.nama_kankas || row.kankas || "-", row.nama_ao || "-", Math.round(Number(row.plafond_lama || 0)), sisa, row.tgl_jatuh_tempo || "-", row.keterangan_status || "-", Math.round(Number(row.plafond_baru || 0)), row.tgl_realisasi_baru || "-"]
  })
  return downloadReportExcel(`Detail_JT_${code || "ALL"}_${filters.month}`, headers, values, [0, 4])
}

export function downloadJtRekapExcel(rows: JtReportRow[], filters: JtFilters, breakdown: JtBreakdown) {
  if (rows.length === 0) return false
  const label = breakdown === "AO" ? "AO Kredit" : breakdown === "KANKAS" ? "Kankas" : "Nama Kantor"
  const headers = ["Kode", label, "Potensi Kredit NOA", "Potensi Kredit Plafon", "Refinancing NOA", "Refinancing Plafon", "Lunas (Prospek) NOA", "Lunas (Prospek) Plafon", `Belum Lunas Sisa ${getJtNominalLabel(filters.nominalField)}`, "Belum Lunas NOA", "Belum Lunas Plafon", "Refinancing (%)"]
  const values = rows.map((row) => [
    String(row.kode_kantor || ""),
    row.group_label || row.nama_kantor || "",
    Number(row.noa_potensi || 0),
    Math.round(Number(row.plafon_potensi || 0)),
    Number(row.noa_refinancing || 0),
    Math.round(Number(row.plafon_refinancing || 0)),
    Number(row.noa_lunas || 0),
    Math.round(Number(row.plafon_lunas || 0)),
    Math.round(Number(row.sisa_belum_lunas || 0)),
    Number(row.noa_belum_lunas || 0),
    Math.round(Number(row.plafon_belum_lunas || 0)),
    Number(row.persentase || 0),
  ])
  return downloadReportExcel(`Rekap_JatuhTempo_${filters.year}`, headers, values, [0])
}

export function formatJtMoney(value: unknown) {
  return Number(value ?? 0) === 0 ? "-" : formatJtNominal(value)
}
