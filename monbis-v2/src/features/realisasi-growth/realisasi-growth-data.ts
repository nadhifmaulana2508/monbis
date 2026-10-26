import type {
  ApiEnvelope,
  Filters,
  ReportRow,
  SortDirection,
  SortKey,
} from "./realisasi-growth-types"
import { apiEndpoint } from "@/lib/api-base"

declare global {
  interface Window {
    apiFetch?: (input: RequestInfo | URL, init?: RequestInit) => Promise<Response>
  }
}

export const numberFormat = new Intl.NumberFormat("id-ID")

export async function requestJson<T>(
  request: string,
  options: RequestInit = {},
): Promise<ApiEnvelope<T>> {
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
  let payload: ApiEnvelope<T>
  try {
    payload = (await response.json()) as ApiEnvelope<T>
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

export function previousMonthEnd(dateValue: string) {
  const [year, month] = dateValue.split("-").map(Number)
  if (!year || !month) return formatDateInput(new Date())
  return formatDateInput(new Date(year, month - 1, 0))
}

export function defaultFilters(): Filters {
  const today = formatDateInput(new Date())
  return {
    actualDate: today,
    closingDate: previousMonthEnd(today),
    area: "ALL",
    nominalField: "saldo_bank",
  }
}

export function formatNominal(value: unknown) {
  const number = Number(value ?? 0)
  return number === 0 ? "-" : numberFormat.format(Math.round(number))
}

export function formatCount(value: unknown) {
  const number = Number(value ?? 0)
  return number === 0 ? "-" : numberFormat.format(Math.round(number))
}

export function formatGrowth(value: unknown) {
  const number = Number(value ?? 0)
  if (number === 0) return "-"
  const formatted = numberFormat.format(Math.round(Math.abs(number)))
  return number < 0 ? `(${formatted})` : formatted
}

export function formatDateLabel(value: string) {
  const [year, month, day] = value.split("-")
  return year && month && day ? `${day}/${month}/${year}` : value
}

export function rowNumber(row: ReportRow, key: string) {
  return Number(row[key] ?? 0)
}

function getSortValue(row: ReportRow, key: SortKey) {
  if (key === "nama_kantor" || key === "kode_kantor") {
    return String(row[key] ?? "").toLowerCase()
  }
  return rowNumber(row, key)
}

export function sortRows(rows: ReportRow[], key: SortKey, direction: SortDirection) {
  return [...rows].sort((left, right) => {
    const a = getSortValue(left, key)
    const b = getSortValue(right, key)
    const result = typeof a === "string" && typeof b === "string"
      ? a.localeCompare(b)
      : Number(a) - Number(b)
    return direction === "asc" ? result : -result
  })
}
