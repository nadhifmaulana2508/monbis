import { Fragment, useCallback, useEffect, useMemo, useState } from "react"
import { Download, Info } from "lucide-react"

import { useKpiToolbar } from "@/components/app/kpi-shell"
import { KpiField, KpiModal, KpiPage, KpiPanel, KpiSelect, KpiState } from "@/components/app/kpi-ui"
import { loadKpiReport, loadReportDate, loadReportEmployeeSummary, loadReportOffices } from "@/features/kpi-data"
import type { KpiReport, KpiReportRow, ReportOffice } from "@/features/kpi-types"
import { userOfficeCode } from "@/lib/auth"
import { formatNumber, formatTarget } from "@/lib/utils"

const monthLabels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"]
const korwilOptions = [["SEMARANG", "Korwil Semarang"], ["SOLO", "Korwil Solo"], ["BANYUMAS", "Korwil Banyumas"], ["PEKALONGAN", "Korwil Pekalongan"]] as const

function isAbortError(reason: unknown) { return reason instanceof DOMException && reason.name === "AbortError" }
function numeric(value: unknown) { const number = Number(value); return Number.isFinite(number) ? number : null }
function reportValue(value: number | null | undefined, format?: string, signed = false) {
  const number = numeric(value)
  if (number === null) return "-"
  const prefix = signed && number > 0 ? "+" : ""
  return `${prefix}${format === "ratio" ? `${formatNumber(number)}%` : format === "nominal" ? formatTarget(number, "RUPIAH") : formatNumber(number)}`
}

function addSdmRows(report: KpiReport, employeeCount: number | null) {
  const rows = [...(report.rows || [])]
  const meta = report.meta || {}
  const count = numeric(employeeCount)
  const monthMeta = meta.months || []
  const profit = rows.find((row) => row.key === "laba")
  const headcount = monthMeta.map((month) => month.available && count !== null ? count : null)
  const productivity = headcount.map((value, index) => {
    const profitValue = numeric(profit?.monthly?.[index])
    return value && value > 0 && profitValue !== null && monthMeta[index]?.available ? profitValue / value : null
  })
  const latest = Number(meta.latest_actual_month || 0)
  const sdmRows: KpiReportRow[] = [
    { section: "SDM", key: "sdm_count", label: "a. Jumlah SDM", format: "count", monthly: headcount, comparison: false },
    { section: "SDM", key: "sdm_productivity", label: "b. Produktivitas SDM", format: "nominal", monthly: productivity, comparison: false },
  ].map((row) => ({ ...row, current_actual: latest ? row.monthly?.[latest - 1] : null, year_actual: latest ? row.monthly?.[latest - 1] : null }))
  const ratioIndex = rows.findIndex((row) => row.section === "Rasio Keuangan")
  rows.splice(ratioIndex < 0 ? rows.length : ratioIndex, 0, ...sdmRows)
  return rows
}

function makeReportPayload(date: string, office: string) {
  const isKorwil = korwilOptions.some(([code]) => code === office)
  return isKorwil ? { harian_date: date, korwil: office } : { harian_date: date, kode_kantor: office || "000" }
}

function escapeCell(value: unknown) { return String(value ?? "").replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;") }
function excelCell(value: number | null | undefined, format?: string, signed = false) {
  const number = numeric(value)
  if (number === null) return "<td></td>"
  const numberFormat = format === "ratio" ? "0.00%" : "#,##0.00;[Red]-#,##0.00"
  const shown = signed && number > 0 ? `+${number}` : String(number)
  return `<td x:num="${number}" style="mso-number-format:'${numberFormat}';text-align:right">${escapeCell(shown)}</td>`
}

function downloadExcel(report: KpiReport, rows: KpiReportRow[], office: string, date: string) {
  const meta = report.meta || {}
  const months = meta.months || []
  const header = `<tr><th rowspan="2">Indikator Kinerja</th><th rowspan="2">Desember ${Number(meta.year || 0) - 1}</th><th rowspan="2">RBB ${escapeCell(meta.year || "")}</th><th colspan="12">Realisasi ${escapeCell(meta.year || "")}</th><th colspan="2">Pencapaian RBB Bulan Berjalan</th><th colspan="2">Pencapaian RBB Tahun</th></tr><tr>${months.map((month) => `<th>${escapeCell(month.label)}</th>`).join("")}<th>Target (+/-)</th><th>%</th><th>Target (+/-)</th><th>%</th></tr>`
  let section = ""
  const body = rows.map((row) => {
    const sectionRow = row.section !== section ? `<tr class="section"><td colspan="19">${escapeCell(row.section)}</td></tr>` : ""
    section = row.section || section
    return `${sectionRow}<tr><td>${escapeCell(row.label)}</td>${excelCell(row.previous, row.format)}${excelCell(row.year_target, row.format)}${(row.monthly || []).map((value) => excelCell(value, row.format)).join("")}${excelCell(row.current_delta, row.format, true)}${excelCell(row.current_achievement, "ratio")}${excelCell(row.year_delta, row.format, true)}${excelCell(row.year_achievement, "ratio")}</tr>`
  }).join("")
  const html = `<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"><style>table{border-collapse:collapse;font:10pt Arial}th,td{border:1px solid #aab4bf;padding:5px}th{background:#0070f3;color:#fff}.section{background:#f2f2f2;font-weight:bold}</style></head><body><table><tr><td colspan="19"><strong>RAPORT CABANG · ${escapeCell(meta.scope_label || "")}</strong></td></tr><tr><td colspan="19">Periode ${escapeCell(date)} · Closing terakhir ${escapeCell(meta.latest_actual_date || "-")}</td></tr>${header}${body}<tr><td colspan="19">SDM memakai roster pegawai aktif terkini; histori headcount per bulan tidak tersedia.</td></tr></table></body></html>`
  const link = document.createElement("a")
  link.href = URL.createObjectURL(new Blob([`\ufeff${html}`], { type: "application/vnd.ms-excel;charset=utf-8" }))
  link.download = `Raport_Cabang_${office}_${date}.xls`
  link.click()
  window.setTimeout(() => URL.revokeObjectURL(link.href), 1000)
}

export function ReportCabangPage() {
  const { setToolbar } = useKpiToolbar()
  const [date, setDate] = useState("")
  const [office, setOffice] = useState("000")
  const [offices, setOffices] = useState<ReportOffice[]>([])
  const [data, setData] = useState<KpiReport | null>(null)
  const [employeeCount, setEmployeeCount] = useState<number | null>(null)
  const [loading, setLoading] = useState(true)
  const [booting, setBooting] = useState(true)
  const [error, setError] = useState("")
  const [infoOpen, setInfoOpen] = useState(false)

  useEffect(() => {
    const controller = new AbortController()
    Promise.all([loadReportDate(controller.signal), loadReportOffices(controller.signal)])
      .then(([dateData, officeData]) => {
        const available = Array.isArray(officeData) ? officeData : []
        setOffices(available)
        const defaultDate = dateData.last_created || dateData.last_closing || new Date().toISOString().slice(0, 10)
        const storedUser = typeof window !== "undefined" ? window.getUser?.() ?? null : null
        const storedOffice = userOfficeCode(storedUser)
        setDate(defaultDate.slice(0, 10))
        if (storedOffice !== "000" && available.some((item) => String(item.kode_kantor).padStart(3, "0") === storedOffice)) setOffice(storedOffice)
      })
      .catch((reason) => { if (!isAbortError(reason)) { setError(reason instanceof Error ? reason.message : "Filter report gagal dimuat"); setLoading(false) } })
      .finally(() => setBooting(false))
    return () => controller.abort()
  }, [])

  const load = useCallback(() => {
    if (!date) return undefined
    const controller = new AbortController()
    setLoading(true)
    setError("")
    const payload = makeReportPayload(date, office)
    loadKpiReport(payload, controller.signal)
      .then(async (result) => {
        setData(result)
        setEmployeeCount(null)
        try {
          const summary = await loadReportEmployeeSummary({ as_of: result.meta?.latest_actual_date || date, ...payload }, controller.signal)
          setEmployeeCount(numeric(summary.meta?.total))
        } catch (reason) {
          if (isAbortError(reason)) throw reason
        }
      })
      .catch((reason) => { if (!isAbortError(reason)) setError(reason instanceof Error ? reason.message : "Report cabang gagal dimuat") })
      .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [date, office])

  useEffect(() => load(), [load])

  const rows = useMemo(() => data ? addSdmRows(data, employeeCount) : [], [data, employeeCount])
  const sections = useMemo(() => rows.reduce<Record<string, KpiReportRow[]>>((result, row) => { const key = row.section || "Indikator"; (result[key] ||= []).push(row); return result }, {}), [rows])

  useEffect(() => {
    setToolbar(<div className="flex min-w-0 flex-wrap items-end gap-3"><KpiField label="Tanggal actual"><input type="date" value={date} onChange={(event) => setDate(event.target.value)} className="h-9 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2.5 text-sm outline-none focus:border-[var(--accent)]" /></KpiField><KpiSelect label="Kantor / Korwil" value={office} onChange={setOffice}><option value="000">Konsolidasi</option><optgroup label="Korwil">{korwilOptions.map(([value, label]) => <option key={value} value={value}>{label}</option>)}</optgroup><optgroup label="Cabang">{offices.map((item) => <option key={item.kode_kantor} value={String(item.kode_kantor).padStart(3, "0")}>{String(item.kode_kantor).padStart(3, "0")} · {item.nama_kantor}</option>)}</optgroup></KpiSelect></div>)
    return () => setToolbar(null)
  }, [date, office, offices, setToolbar])

  const exportCurrent = () => { if (data && rows.length) downloadExcel(data, rows, office, date) }
  const latestDate = data?.meta?.latest_actual_date || date || "-"

  return <KpiPage title="Report Cabang" description="Perbandingan closing bulanan terhadap RBB dan posisi akhir tahun." actions={<><button className="inline-flex size-9 items-center justify-center rounded-md text-[var(--muted)] hover:bg-[var(--surface-subtle)]" aria-label="Tentang report cabang" onClick={() => setInfoOpen(true)}><Info size={16} /></button><button className="inline-flex h-9 items-center gap-2 rounded-md bg-[var(--accent)] px-3 text-sm font-medium text-white hover:bg-[var(--accent-strong)] disabled:cursor-not-allowed disabled:opacity-50" disabled={!rows.length || loading} onClick={exportCurrent}><Download size={14} /> <span className="hidden sm:inline">Export Excel</span><span className="sm:hidden">Excel</span></button></>}> 
    <KpiPanel title={data?.meta?.scope_label || "Raport kinerja"} description={`Closing terakhir ${latestDate} · Tanggal actual ${data?.meta?.harian_date || date || "-"}`}>
      <KpiState loading={booting || loading} error={error} empty={!rows.length && !loading ? "Belum ada data report untuk filter ini." : undefined} onRetry={load}>
        <div className="max-h-[calc(100dvh-270px)] overflow-auto overscroll-contain">
          <table className="w-full min-w-[1480px] border-separate border-spacing-0 text-sm tabular-nums">
            <thead className="sticky top-0 z-10 bg-[var(--surface)] text-left text-xs text-[var(--muted)]"><tr><th className="sticky left-0 z-20 min-w-64 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Indikator kinerja</th><th className="border-b border-[var(--line)] px-3 py-3 font-medium">Tahun lalu</th><th className="border-b border-[var(--line)] px-3 py-3 font-medium">RBB {data?.meta?.year || ""}</th>{monthLabels.map((month) => <th key={month} className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">{month}</th>)}<th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">Delta bulan</th><th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">% bulan</th><th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">Delta tahun</th><th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">% tahun</th></tr></thead>
            <tbody>{Object.entries(sections).map(([section, sectionRows]) => <Fragment key={section}><tr><th colSpan={19} className="border-b border-[var(--line-soft)] bg-[var(--surface-subtle)] px-4 py-2 text-left text-xs font-semibold uppercase tracking-[0.12em] text-[var(--muted)]">{section}</th></tr>{sectionRows.map((row) => <tr key={row.key || row.label} className="group hover:bg-[var(--surface-subtle)]"><td className="sticky left-0 z-[1] border-b border-[var(--line-soft)] bg-[var(--surface)] px-4 py-3 font-medium group-hover:bg-[var(--surface-subtle)]">{row.label || "-"}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{reportValue(row.previous, row.format)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{reportValue(row.year_target, row.format)}</td>{monthLabels.map((_, index) => <td key={index} className="border-b border-[var(--line-soft)] px-3 py-3 text-right">{reportValue(row.monthly?.[index], row.format)}</td>)}<td className="border-b border-[var(--line-soft)] px-3 py-3 text-right">{reportValue(row.current_delta, row.format, true)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 text-right">{reportValue(row.current_achievement, "ratio")}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 text-right">{reportValue(row.year_delta, row.format, true)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 text-right">{reportValue(row.year_achievement, "ratio")}</td></tr>)}</Fragment>)}</tbody>
          </table>
        </div>
      </KpiState>
    </KpiPanel>
    <KpiModal open={infoOpen} title="Report Cabang" description="Perhitungan berasal dari layanan backend." onClose={() => setInfoOpen(false)}><p className="text-sm leading-6 text-[var(--muted)]">React mengambil dan menampilkan response report tahunan, termasuk closing bulanan, RBB, pencapaian, Korwil, dan ringkasan SDM. Rumus bisnis tidak dihitung ulang di browser.</p></KpiModal>
  </KpiPage>
}
