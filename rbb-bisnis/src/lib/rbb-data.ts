import { postApi } from "@/lib/api-base"
import type { Office, RbbResponse, RbbTab } from "@/lib/rbb-types"

export const monthLabels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"]

export const projectionCategories = [
  ["NERACA", "Neraca"],
  ["LABA_RUGI", "Laba Rugi"],
  ["ASET", "Aset"],
  ["LIABILITAS", "Liabilitas"],
  ["EKUITAS", "Ekuitas"],
  ["PENDAPATAN", "Pendapatan"],
  ["BEBAN", "Beban"],
  ["ALL", "Semua kategori"],
] as const

export const detailCategories = [
  ["ASET", "Aset"],
  ["LIABILITAS", "Liabilitas"],
  ["EKUITAS", "Ekuitas"],
  ["KREDIT", "Kredit"],
  ["DAMAS", "DAMAS"],
  ["PENDAPATAN", "Pendapatan"],
  ["BEBAN", "Beban"],
  ["IKHTISAR", "Ikhtisar"],
  ["ALL", "Semua kategori"],
] as const

export function normalizeOffice(office: Office): Office {
  return { ...office, kode_kantor: String(office.kode_kantor).padStart(3, "0") }
}

export async function loadOffices(signal?: AbortSignal) {
  const result = await postApi<Office[] | { data?: Office[] }>("kode/", { type: "kode_kantor" }, signal)
  const offices = Array.isArray(result) ? result : result.data || []
  return offices.filter((item) => String(item.kode_kantor) !== "000").map(normalizeOffice)
}

export function buildPeriods(startYear: number, startMonth: number) {
  const periods: Array<{ year: number; month: number; key: string }> = []
  for (let month = startMonth; month <= 12; month += 1) periods.push({ year: startYear, month, key: `${startYear}-${String(month).padStart(2, "0")}` })
  for (let month = 1; month <= 12; month += 1) periods.push({ year: startYear + 1, month, key: `${startYear + 1}-${String(month).padStart(2, "0")}` })
  return periods
}

export function periodTitle(period: { year: number; month: number }) {
  return `${monthLabels[period.month - 1]} ${period.year}`
}

export function previousActualLabel(date?: string | null) {
  if (!date) return "Realisasi sebelumnya"
  const [year, month] = date.split("-").map(Number)
  return `${monthLabels[month - 1] || "-"} ${year}`
}

export function rowValues(values: Record<string, number> | undefined) {
  return Object.fromEntries(Array.from({ length: 12 }, (_, index) => [String(index + 1), Number(values?.[index + 1] ?? values?.[String(index + 1)] ?? 0)]))
}

export async function loadRbbYear(tab: RbbTab, year: number, startYear: number, startMonth: number, branch: string, category: string, signal?: AbortSignal) {
  const common = { tahun: year, tahun_mulai: startYear, bulan_mulai: startMonth, kode_kantor: branch, kategori: category }
  if (tab === "projection") return postApi<RbbResponse>("rbb_v2/", { type: "rbb_projection_data", ...common }, signal)
  if (tab === "detail") return postApi<RbbResponse>("rbb_v2/", { type: "rbb_planning_data", ...common }, signal)
  return postApi<RbbResponse>("rbb_v2/", { type: "rbb_aba_data", tahun: year, kode_kantor: branch, tipe: "PLACEMENT" }, signal)
}
