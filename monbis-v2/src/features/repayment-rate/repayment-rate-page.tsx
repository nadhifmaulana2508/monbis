import { useEffect, useMemo, useRef, useState } from "react"
import { Info, LoaderCircle } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import type { SearchableOption } from "@/components/searchable-select"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"
import { RepaymentRateFilterPanel } from "./repayment-rate-filter-panel"
import { detailExport, exportRr, requestRr, rrAccess, rrDateLabel, rrOfficeCode, sortRrRows, type RrFilters, type RrOffice, type RrRow, type RrSort } from "./repayment-rate-data"
import { RepaymentRateDetailModal } from "./repayment-rate-modals"
import { RepaymentRateTable } from "./repayment-rate-table"

const korwil = ["SEMARANG", "SOLO", "BANYUMAS", "PEKALONGAN"] as const
type DetailSelection = { row: RrRow; status: "ALL" | "MIGRASI" }
type DetailResponse = { data?: RrRow[]; pagination?: { current_page?: number; total_pages?: number; total_records?: number } }

function localDate() {
  const date = new Date()
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`
}
function previousMonthEnd(date: string) {
  const [year, month] = date.split("-").map(Number)
  const previous = new Date(year, month - 1, 0)
  return `${previous.getFullYear()}-${String(previous.getMonth() + 1).padStart(2, "0")}-${String(previous.getDate()).padStart(2, "0")}`
}

export function RepaymentRatePage() {
  const userCode = rrOfficeCode()
  const [filters, setFilters] = useState<RrFilters>(() => ({ actualDate: localDate(), closingDate: previousMonthEnd(localDate()), nominalField: "saldo_bank", area: userCode === "000" ? "000" : userCode, breakdown: "KANKAS" }))
  const [offices, setOffices] = useState<RrOffice[]>([])
  const [defaultsReady, setDefaultsReady] = useState(false)
  const [access, setAccess] = useState<boolean | null>(null)
  const [rows, setRows] = useState<RrRow[]>([])
  const [total, setTotal] = useState<RrRow | null>(null)
  const [meta, setMeta] = useState<Record<string, unknown>>({})
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [sort, setSort] = useState<RrSort>({ column: "kode", direction: "asc" })
  const [infoOpen, setInfoOpen] = useState(false)
  const [detail, setDetail] = useState<DetailSelection | null>(null)
  const [detailRows, setDetailRows] = useState<RrRow[]>([])
  const [detailSearch, setDetailSearch] = useState("")
  const [detailStatus, setDetailStatus] = useState("ALL")
  const [detailPage, setDetailPage] = useState(1)
  const [detailPages, setDetailPages] = useState(1)
  const [detailCount, setDetailCount] = useState(0)
  const [detailLoading, setDetailLoading] = useState(false)
  const [detailError, setDetailError] = useState<string | null>(null)
  const [detailFilters, setDetailFilters] = useState({ kankas: "", ao: "" })
  const [kankasOptions, setKankasOptions] = useState<Array<{ value: string; label: string }>>([])
  const [aoOptions, setAoOptions] = useState<Array<{ value: string; label: string }>>([])
  const tableRef = useRef<HTMLTableElement>(null)
  const detailAbort = useRef<AbortController | null>(null)
  const detailContext = useRef<{ kode_kantor: string | null; korwil: string | null; kode_kankas: string | null; kode_ao: string | null; tgl_tagih: string; status: string } | null>(null)

  useEffect(() => {
    const controller = new AbortController()
    void Promise.allSettled([
      requestRr<Record<string, unknown>>("rbac", { method: "POST", body: JSON.stringify({ type: "me" }), signal: controller.signal }),
      requestRr<{ last_created?: string; last_closing?: string }>("date", { signal: controller.signal }),
      requestRr<RrOffice[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kantor" }), signal: controller.signal }),
    ]).then(([permission, date, office]) => {
      if (controller.signal.aborted) return
      if (permission.status === "fulfilled") setAccess(rrAccess(permission.value.data || null))
      else if (import.meta.env.DEV) setAccess(true)
      else { setAccess(false); setError(permission.reason instanceof Error ? permission.reason.message : "Hak akses belum dapat diverifikasi.") }
      const officeList = office.status === "fulfilled" && Array.isArray(office.value.data) ? office.value.data : []
      setOffices(officeList)
      if (date.status === "fulfilled" && date.value.data?.last_created) {
        const actualDate = date.value.data.last_created
        setFilters((current) => ({ ...current, actualDate, closingDate: date.value.data?.last_closing || previousMonthEnd(actualDate) }))
      }
      setDefaultsReady(true)
    })
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!defaultsReady || access !== true) return undefined
    const controller = new AbortController()
    const body: Record<string, string | null> = {
      type: "rr", closing_date: filters.closingDate, harian_date: filters.actualDate,
      hitung_berdasarkan: filters.nominalField,
      kode_kantor: filters.area.startsWith("KOR-") || filters.area === "000" ? null : filters.area,
      korwil: filters.area.startsWith("KOR-") ? filters.area.slice(4) : null,
      breakdown_by: filters.area !== "000" && !filters.area.startsWith("KOR-") ? filters.breakdown : null,
    }
    void requestRr<{ data?: RrRow[]; grand_total?: RrRow; meta?: Record<string, unknown> }>("rr", { method: "POST", body: JSON.stringify(body), signal: controller.signal })
      .then((response) => {
        if (controller.signal.aborted) return
        setRows((response.data?.data || []).filter((row) => Math.abs(Number(row.m1_lancar_os || 0)) > 0.000001 || Math.abs(Number(row.cur_lancar_os || 0)) > 0.000001))
        setTotal(response.data?.grand_total || null); setMeta(response.data?.meta || {})
        setSort({ column: "kode", direction: "asc" })
      })
      .catch((caught: unknown) => { if (!controller.signal.aborted) { setRows([]); setTotal(null); setError(caught instanceof Error ? caught.message : "Data Repayment Rate gagal dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [access, defaultsReady, filters])

  useEffect(() => {
    if (!detail) return undefined
    const controller = new AbortController()
    detailAbort.current?.abort(); detailAbort.current = controller
    const context = detailContext.current
    if (!context) return undefined
    const body = {
      type: "detail_rekap_rr", closing_date: filters.closingDate, harian_date: filters.actualDate,
      kode_kantor: context.kode_kantor, korwil: context.korwil, kode_kankas: detailFilters.kankas || context.kode_kankas,
      kode_ao: detailFilters.ao || context.kode_ao, tgl_tagih: context.tgl_tagih, status: context.status,
      hitung_berdasarkan: filters.nominalField, status_bayar: "all", status_tunggakan: "all",
      status_pembayaran: detailStatus, search: detailSearch.trim(), limit: 30, page: detailPage,
    }
    void requestRr<DetailResponse>("rr", { method: "POST", body: JSON.stringify(body), signal: controller.signal })
      .then((response) => {
        if (controller.signal.aborted) return
        const node = response.data
        setDetailError(null)
        setDetailRows(Array.isArray(node?.data) ? node.data : [])
        setDetailPages(Math.max(1, Number(node?.pagination?.total_pages || 1)))
        setDetailCount(Number(node?.pagination?.total_records || 0))
      })
      .catch((caught: unknown) => { if (!controller.signal.aborted) { setDetailRows([]); setDetailError(caught instanceof Error ? caught.message : "Detail gagal dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setDetailLoading(false) })
    return () => controller.abort()
  }, [detail, detailFilters, detailPage, detailSearch, detailStatus, filters.actualDate, filters.closingDate, filters.nominalField])

  useEffect(() => () => detailAbort.current?.abort(), [])

  useEffect(() => {
    if (!detail) return undefined
    const previous = document.body.style.overflow
    document.body.style.overflow = "hidden"
    const closeOnEscape = (event: KeyboardEvent) => { if (event.key === "Escape") setDetail(null) }
    window.addEventListener("keydown", closeOnEscape)
    return () => { document.body.style.overflow = previous; window.removeEventListener("keydown", closeOnEscape) }
  }, [detail])

  const sortedRows = useMemo(() => sortRrRows(rows, sort), [rows, sort])
  const isSingleOffice = filters.area !== "000" && !filters.area.startsWith("KOR-")
  const areas: SearchableOption[] = userCode === "000" ? [
    { value: "000", label: "Konsolidasi" },
    ...korwil.map((item) => ({ value: `KOR-${item}`, label: `Korwil ${item[0]}${item.slice(1).toLowerCase()}` })),
    ...offices.filter((office) => String(office.kode_kantor || "") !== "000").map((office) => ({ value: String(office.kode_kantor).padStart(3, "0"), label: `${String(office.kode_kantor).padStart(3, "0")} - ${office.nama_kantor || "Cabang"}` })),
  ] : offices.filter((office) => String(office.kode_kantor || "").padStart(3, "0") === userCode).map((office) => ({ value: userCode, label: `${userCode} - ${office.nama_kantor || "Cabang"}` }))
  if (userCode !== "000" && !areas.length) areas.push({ value: userCode, label: `${userCode} - Kantor saya` })
  const areaLabel = areas.find((item) => item.value === filters.area)?.label || filters.area
  const updateFilters = (next: Partial<RrFilters>) => { setLoading(true); setRows([]); setTotal(null); setMeta({}); setError(null); setFilters((current) => ({ ...current, ...next })) }
  const onSort = (column: string) => setSort((current) => ({ column, direction: current.column === column && current.direction === "asc" ? "desc" : "asc" }))
  const openDetail = (row: RrRow, status: "ALL" | "MIGRASI") => {
    const branchFilter = filters.area !== "000" && !filters.area.startsWith("KOR-") ? filters.area : null
    const rowIsBreakdown = isSingleOffice && row !== total
    const byAo = meta.breakdown_by === "AO"
    detailContext.current = {
      kode_kantor: branchFilter || (filters.area.startsWith("KOR-") || filters.area === "000" ? null : filters.area),
      korwil: filters.area.startsWith("KOR-") ? filters.area.slice(4) : null,
      kode_kankas: rowIsBreakdown && !byAo ? String(row.kode || "") : null,
      kode_ao: rowIsBreakdown && byAo ? String(row.kode || "") : null,
      tgl_tagih: "ALL", status,
    }
    setDetailLoading(true); setDetailError(null); setDetailRows([]); setDetail({ row, status }); setDetailPage(1); setDetailSearch(""); setDetailStatus("ALL"); setDetailFilters({ kankas: "", ao: "" })
    const branch = detailContext.current.kode_kantor
    if (branch) {
      const signal = new AbortController().signal
      void Promise.allSettled([
        requestRr<Array<{ kode_group1?: string; deskripsi_group1?: string }>>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kankas", kode_kantor: branch }), signal }),
        requestRr<Array<{ kode_group2?: string; nama_ao?: string }>>("kode", { method: "POST", body: JSON.stringify({ type: "kode_ao_kredit", kode_kantor: branch }), signal }),
      ]).then(([kas, ao]) => {
        if (kas.status === "fulfilled") setKankasOptions((kas.value.data || []).map((item) => ({ value: String(item.kode_group1 || ""), label: String(item.deskripsi_group1 || item.kode_group1 || "") })).filter((item) => item.value))
        if (ao.status === "fulfilled") setAoOptions((ao.value.data || []).map((item) => ({ value: String(item.kode_group2 || ""), label: String(item.nama_ao || item.kode_group2 || "") })).filter((item) => item.value))
      })
    }
  }

  const exportDetail = async () => {
    if (!detail || !detailContext.current) return
    const controller = new AbortController()
    setDetailLoading(true); setDetailError(null)
    const context = detailContext.current
    const pageSize = 500
    const payload: Record<string, string | number | boolean | null> = {
      type: "detail_rekap_rr", closing_date: filters.closingDate, harian_date: filters.actualDate,
      kode_kantor: context.kode_kantor, korwil: context.korwil,
      kode_kankas: detailFilters.kankas || context.kode_kankas, kode_ao: detailFilters.ao || context.kode_ao,
      tgl_tagih: context.tgl_tagih, status: context.status, hitung_berdasarkan: filters.nominalField,
      status_bayar: "all", status_tunggakan: "all", status_pembayaran: detailStatus,
      search: detailSearch.trim(), page: 1, limit: pageSize,
    }
    try {
      const first = await requestRr<DetailResponse>("rr", { method: "POST", body: JSON.stringify(payload), signal: controller.signal })
      const node = first.data
      const exportRows = Array.isArray(node?.data) ? [...node.data] : []
      const pageCount = Math.max(1, Number(node?.pagination?.total_pages || 1))
      for (let page = 2; page <= pageCount; page += 1) {
        const result = await requestRr<DetailResponse>("rr", { method: "POST", body: JSON.stringify({ ...payload, page, skip_count: true }), signal: controller.signal })
        const nextRows = Array.isArray(result.data?.data) ? result.data.data : []
        if (!nextRows.length) break
        exportRows.push(...nextRows)
      }
      if (exportRows.length < Number(node?.pagination?.total_records || exportRows.length)) throw new Error("Ekspor belum lengkap; silakan coba lagi.")
      detailExport(exportRows, `Repayment_Rate_Detail_${detail.status}_${filters.actualDate}`, context.kode_kantor || "")
    } catch (caught) {
      if (!controller.signal.aborted) setDetailError(caught instanceof Error ? caught.message : "Ekspor detail gagal.")
    } finally { if (!controller.signal.aborted) setDetailLoading(false) }
  }

  const visibleBreakdown = meta.breakdown_by === "AO" || meta.breakdown_by === "KANKAS" ? meta.breakdown_by : filters.breakdown
  const filterPanel = <RepaymentRateFilterPanel filters={{ ...filters, breakdown: visibleBreakdown }} areas={areas} disabled={userCode !== "000"} showBreakdown={isSingleOffice} onChange={updateFilters} />
  const labelCode = String(meta.label_kode || "KODE")
  const labelName = String(meta.label_nama || (userCode === "000" ? "NAMA KANTOR" : "NAMA KANKAS"))
  const visibleLoading = loading && access !== false

  return <AppShell title="Repayment Rate" description="Rekap repayment rate closing dan actual." activeHref={appRoutes.repaymentRate} legacyHref="/rekap_rr" navbarContent={filterPanel} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1600px] flex-col px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
      <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-4 py-2.5 sm:px-5">
          <div className="flex min-w-0 items-center gap-1.5"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Repayment Rate</h1><Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Informasi Repayment Rate" onClick={() => setInfoOpen(true)}><Info className="size-4" /></Button></div>
          <div className="flex min-w-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3"><span className="hidden max-w-[42rem] truncate lg:inline">{filters.nominalField === "saldo_bank" ? "Saldo bank" : "Baki debet"} | Closing {rrDateLabel(filters.closingDate)} | Actual {rrDateLabel(filters.actualDate)} | {areaLabel}</span><span className="hidden md:inline">{visibleLoading ? "Memuat data…" : `${rows.length.toLocaleString("id-ID")} baris`}</span><ReportExportMenu target={tableRef} filename={`Repayment_Rate_${filters.actualDate}`} disabled={visibleLoading || rows.length === 0} onExportExcel={() => exportRr(sortedRows, labelName, `Repayment_Rate_${filters.actualDate}`)} /></div>
          <p className="w-full truncate text-[0.68rem] text-muted-foreground lg:hidden">{filters.nominalField === "saldo_bank" ? "Saldo bank" : "Baki debet"} | {areaLabel}</p>
        </header>
        <div className="relative min-h-0 flex-1 overflow-hidden">
          {visibleLoading && <div className="absolute inset-0 z-[80] flex items-center justify-center gap-2 bg-background/75 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" /> Memuat Repayment Rate…</div>}
          {error && !loading ? <div className="flex h-full min-h-48 flex-col items-center justify-center gap-2 p-5 text-center"><p className="text-sm font-medium text-destructive">Data Repayment Rate belum dapat dimuat.</p><p className="max-w-xl text-xs text-muted-foreground">{error}</p><Button variant="outline" size="sm" onClick={() => updateFilters({})}>Coba lagi</Button></div>
            : access === false ? <div className="flex h-full min-h-48 items-center justify-center p-6 text-center text-sm text-muted-foreground">Akses menu Repayment Rate belum diberikan.</div>
              : <RepaymentRateTable rows={sortedRows} total={rows.length ? total : null} sort={sort} onSort={onSort} onDetail={openDetail} tableRef={tableRef} labelCode={labelCode} labelName={labelName} />}
        </div>
      </section>
    </div>
    <ReportInfoDialog open={infoOpen} title="Tentang Repayment Rate" description="Rasio saldo lancar dibanding total outstanding pada closing dan posisi actual." meta={`${filters.nominalField === "saldo_bank" ? "Saldo bank" : "Baki debet"} · ${areaLabel}`} onClose={() => setInfoOpen(false)}>
      <div className="space-y-3 text-sm"><p className="rounded-lg bg-muted p-3 text-muted-foreground">RR = nominal rekening dengan kolektibilitas L dan hari menunggak 0 dibagi total OS positif. Perubahan RR membandingkan persentase actual dengan closing.</p><div className="grid gap-2 sm:grid-cols-3"><div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">RR Closing</p><p className="mt-1 font-semibold tabular-nums">{Number(total?.m1_pct ?? 0).toLocaleString("id-ID", { maximumFractionDigits: 2 })}%</p></div><div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">RR Actual</p><p className="mt-1 font-semibold tabular-nums">{Number(total?.cur_pct ?? 0).toLocaleString("id-ID", { maximumFractionDigits: 2 })}%</p></div><div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">Perubahan</p><p className={`mt-1 font-semibold tabular-nums ${Number(total?.delta_pct ?? 0) < 0 ? "text-destructive" : Number(total?.delta_pct ?? 0) > 0 ? "text-signal-green-foreground" : ""}`}>{Number(total?.delta_pct ?? 0).toLocaleString("id-ID", { maximumFractionDigits: 2 })}%</p></div></div><p className="text-xs text-muted-foreground">Migrasi menunjukkan rekening yang lancar pada closing lalu menunggak pada actual; nilainya mengikuti saldo actual.</p></div>
    </ReportInfoDialog>
    {detail && <RepaymentRateDetailModal selection={detail} filters={filters} rows={detailRows} loading={detailLoading} error={detailError} page={detailPage} totalPages={detailPages} totalRecords={detailCount} search={detailSearch} status={detailStatus} kankas={detailFilters.kankas} ao={detailFilters.ao} kankasOptions={kankasOptions} aoOptions={aoOptions} onSearch={(value) => { setDetailLoading(true); setDetailRows([]); setDetailSearch(value); setDetailPage(1) }} onStatusChange={(value) => { setDetailLoading(true); setDetailRows([]); setDetailStatus(value); setDetailPage(1) }} onKankasChange={(value) => { setDetailLoading(true); setDetailRows([]); setDetailFilters({ kankas: value, ao: "" }); setDetailPage(1) }} onAoChange={(value) => { setDetailLoading(true); setDetailRows([]); setDetailFilters((current) => ({ ...current, ao: value })); setDetailPage(1) }} onClose={() => setDetail(null)} onPageChange={(page) => { setDetailLoading(true); setDetailRows([]); setDetailPage(page) }} onExport={() => void exportDetail()} />}
  </AppShell>
}
