import { getApi, postApi, postKpi, postReport } from "@/lib/api-base"

import type { AnnualKpiResponse, KpiDetail, KpiDirectory, KpiEvaluation, KpiReport, KpiJobCode, ReportDate, ReportEmployeeSummary, ReportOffice } from "./kpi-types"

export function loadKpiDirectory(input: { year: number; jabatan_kode: KpiJobCode; kode_kantor?: string; include_all_ao?: boolean; include_generated?: boolean }, signal?: AbortSignal) {
  return postKpi<KpiDirectory>({ type: "directory", ...input }, signal)
}

export function loadKpiAoList(input: { jabatan_kode: KpiJobCode; kode_kantor: string }, signal?: AbortSignal) {
  return postKpi<Pick<KpiDirectory, "jabatan_terpilih" | "ao">>({ type: "ao_list", ...input }, signal)
}

export function loadKpiEvaluation(input: { year: number; jabatan_kode: KpiJobCode; kode_ao?: string; id_peg?: string; kode_kantor?: string; closing_date?: string }, signal?: AbortSignal) {
  return postKpi<{ year: number; closing_dates: string[]; data: KpiEvaluation[] }>({ type: "evaluation", ...input }, signal)
}

export function loadKpiDetail(penilaian_id: number, signal?: AbortSignal) {
  return postKpi<{ data: KpiDetail[] }>({ type: "detail", penilaian_id }, signal)
}

export function calculateKpi(input: { year: number; jabatan_kode: KpiJobCode; kode_ao: string; id_peg?: string; kode_kantor?: string; closing_date?: string; skip_existing?: boolean }, signal?: AbortSignal) {
  return postKpi<{ year: number; kode_ao: string; data: Array<{ closing_date: string; status: string; nilai_dasar?: number; nilai_akhir?: number; keterangan?: string }> }>({ type: "calculate", ...input }, signal)
}

export function saveKpiIndicator(input: { id: number; bobot: number; target: number; status: string }, signal?: AbortSignal) {
  return postKpi<Record<string, never>>({ type: "save_indicator", ...input }, signal)
}

export function saveKpiScore(input: { id: number; min_indeks: number; max_indeks: number; predikat: string; aktif?: number }, signal?: AbortSignal) {
  return postKpi<Record<string, never>>({ type: "save_score", ...input }, signal)
}

export function loadAnnualKpi(input: { year: number; jabatan_kode: KpiJobCode; kode_kantor?: string; kode_ao?: string; include_breakdown?: boolean }, signal?: AbortSignal) {
  return postKpi<AnnualKpiResponse>({ type: "annual", ...input }, signal)
}

export function loadKpiReport(input: { harian_date: string; kode_kantor?: string; korwil?: string }, signal?: AbortSignal) {
  return postReport<KpiReport>({ type: "annual_report", ...input }, signal)
}

export function loadReportDate(signal?: AbortSignal) {
  return getApi<ReportDate>("date/", signal)
}

export function loadReportOffices(signal?: AbortSignal) {
  return postApi<ReportOffice[]>("kode/", { type: "kode_kantor" }, signal)
}

export function loadReportEmployeeSummary(input: { as_of: string; kode_kantor?: string; korwil?: string }, signal?: AbortSignal) {
  return postApi<ReportEmployeeSummary>("anggota/", { type: "rekap_anggota", summary_only: true, ...input }, signal)
}
