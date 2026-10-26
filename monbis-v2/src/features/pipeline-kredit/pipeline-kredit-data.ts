import { apiEndpoint } from "@/lib/api-base"
import { downloadReportExcel } from "@/lib/download-report-excel"

import type { PipelineApiEnvelope, PipelineBreakdown, PipelineDetailPayload, PipelineFilters, PipelineRow } from "./pipeline-kredit-types"

export const pipelineNumberFormat = new Intl.NumberFormat("id-ID")

export async function requestPipelineJson<T>(options: RequestInit): Promise<PipelineApiEnvelope<T>> {
  const requestWithAuth = typeof window !== "undefined" && window.apiFetch ? window.apiFetch.bind(window) : fetch
  const response = await requestWithAuth(apiEndpoint("pipelane"), {
    ...options,
    headers: { Accept: "application/json", "Content-Type": "application/json", ...options.headers },
  })
  const payload = await response.json() as PipelineApiEnvelope<T>
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return payload
}

export async function requestPipelineOffices(signal?: AbortSignal) {
  const requestWithAuth = typeof window !== "undefined" && window.apiFetch ? window.apiFetch.bind(window) : fetch
  const response = await requestWithAuth(apiEndpoint("kode"), {
    method: "POST",
    headers: { Accept: "application/json", "Content-Type": "application/json" },
    body: JSON.stringify({ type: "kode_kantor" }),
    signal,
  })
  const payload = await response.json() as PipelineApiEnvelope<Array<{ kode_kantor?: string; nama_kantor?: string }>>
  if (!response.ok || Number(payload.status) !== 200) throw new Error(payload.message || `Request gagal (${response.status})`)
  return Array.isArray(payload.data) ? payload.data : []
}

export function formatPipelineMoney(value: unknown) {
  const amount = Number(value ?? 0)
  return amount === 0 ? "-" : pipelineNumberFormat.format(Math.round(amount))
}

export function formatPipelineCount(value: unknown) {
  const amount = Number(value ?? 0)
  return amount === 0 ? "-" : pipelineNumberFormat.format(Math.round(amount))
}

export function formatPipelineDate(value: string) {
  const match = String(value || "").match(/^(\d{4})-(\d{2})-(\d{2})/)
  return match ? `${match[3]}/${match[2]}/${match[1]}` : value || "-"
}

export function defaultPipelineFilters(): PipelineFilters {
  const now = new Date()
  const actualDate = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`
  const closing = new Date(now.getFullYear(), now.getMonth(), 0)
  return {
    actualDate,
    closingDate: `${closing.getFullYear()}-${String(closing.getMonth() + 1).padStart(2, "0")}-${String(closing.getDate()).padStart(2, "0")}`,
    year: String(now.getFullYear()),
    area: "ALL",
    includeDp: false,
  }
}

export function buildPipelinePayload(filters: PipelineFilters, userCode: string, breakdown: PipelineBreakdown) {
  const area = filters.area.toUpperCase()
  const payload: Record<string, unknown> = {
    type: "rekap_pipeline",
    closing_date: filters.closingDate,
    harian_date: filters.actualDate,
    tahun_jt: filters.year,
    kode_kantor: area.startsWith("CAB-") ? area.replace("CAB-", "") : userCode !== "000" && area === "ALL" ? userCode : null,
    korwil: area.startsWith("KOR-") ? area.replace("KOR-", "") : null,
    breakdown_by: breakdown,
    kolektibilitas: filters.includeDp ? ["L", "DP"] : ["L"],
  }
  return payload
}

export function buildPipelineDetailPayload(filters: PipelineFilters, selection: { kode: string; groupCode: string }, userCode: string, status: string, page: number) {
  const area = filters.area.toUpperCase()
  return {
    type: "detail_pipeline",
    closing_date: filters.closingDate,
    harian_date: filters.actualDate,
    tahun_jt: filters.year,
    kode_kantor: area.startsWith("CAB-") ? area.replace("CAB-", "") : selection.kode || (userCode !== "000" ? userCode : null),
    kode_kankas: selection.groupCode || null,
    filter_status: status || null,
    kolektibilitas: filters.includeDp ? ["L", "DP"] : ["L"],
    page,
    limit: 20,
  }
}

export function pipelineRowsTotal(rows: PipelineRow[]) {
  const keys = ["noa_target", "plafon_closing", "noa_sudah", "nominal_sudah", "noa_lunas", "nominal_lunas", "noa_topup", "os_topup", "noa_retensi", "os_retensi", "noa_drop", "os_drop"]
  return rows.reduce<PipelineRow>((total, row) => {
    keys.forEach((key) => { total[key] = Number(total[key] ?? 0) + Number(row[key] ?? 0) })
    return total
  }, {})
}

export function downloadPipelineExcel(rows: PipelineRow[], filters: PipelineFilters) {
  if (!rows.length) return false
  const headers = ["Kode", "Nama Kantor / Breakdown", "Target NOA", "Target Plafon", "Refinancing NOA", "Refinancing Nominal", "Lunas NOA", "Lunas Nominal", "Top Up NOA", "Top Up Nominal", "Retensi NOA", "Retensi Nominal", "Drop NOA", "Drop Nominal"]
  const values = rows.map((row) => [String(row.kode_cabang || row.group_code || ""), row.nama_kantor || row.group_label || "", Number(row.noa_target || 0), Number(row.plafon_closing || 0), Number(row.noa_sudah || 0), Number(row.nominal_sudah || 0), Number(row.noa_lunas || 0), Number(row.nominal_lunas || 0), Number(row.noa_topup || 0), Number(row.os_topup || 0), Number(row.noa_retensi || 0), Number(row.os_retensi || 0), Number(row.noa_drop || 0), Number(row.os_drop || 0)])
  return downloadReportExcel(`Pipeline_Kredit_${filters.year}`, headers, values, [0])
}

export type { PipelineApiEnvelope, PipelineDetailPayload }
