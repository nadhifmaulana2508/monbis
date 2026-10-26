import { useEffect, useMemo, useRef, useState } from "react"
import { Info, LoaderCircle } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"
import type { SearchableOption } from "@/components/searchable-select"
import { dateLabel, exportRbb, officeCode, rbbAccess, requestRbb, sortRbbRows, type Office, type RbbFilters, type RbbPayload, type RbbRow, type RbbSort } from "./rbb-realisasi-kredit-data"
import { RbbRealisasiKreditFilterPanel } from "./rbb-realisasi-kredit-filter-panel"
import { RbbRealisasiKreditTable } from "./rbb-realisasi-kredit-table"

const korwilOptions: SearchableOption[] = [
  { value: "SEMARANG", label: "Korwil Semarang" }, { value: "SOLO", label: "Korwil Solo" },
  { value: "BANYUMAS", label: "Korwil Banyumas" }, { value: "PEKALONGAN", label: "Korwil Pekalongan" },
]
const today = new Date()
const localDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`

export function RbbRealisasiKreditPage() {
  const userCode = officeCode()
  const [filters, setFilters] = useState<RbbFilters>({ actualDate: localDate, area: userCode === "000" ? "000" : userCode, compareMode: "auto" })
  const [offices, setOffices] = useState<Office[]>([])
  const [defaultsReady, setDefaultsReady] = useState(false)
  const [access, setAccess] = useState<boolean | null>(null)
  const [rows, setRows] = useState<RbbRow[]>([])
  const [monthlyRows, setMonthlyRows] = useState<RbbRow[]>([])
  const [grandTotal, setGrandTotal] = useState<RbbRow | null>(null)
  const [meta, setMeta] = useState<Record<string, unknown>>({})
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [sort, setSort] = useState<RbbSort>({ column: "kode_kantor", direction: "asc" })
  const [infoOpen, setInfoOpen] = useState(false)
  const tableRef = useRef<HTMLTableElement>(null)

  useEffect(() => {
    const controller = new AbortController()
    void Promise.allSettled([
      requestRbb<Record<string, unknown>>("rbac", { method: "POST", body: JSON.stringify({ type: "me" }), signal: controller.signal }),
      requestRbb<{ last_created?: string }>("date", { signal: controller.signal }),
      requestRbb<Office[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kantor" }), signal: controller.signal }),
    ]).then(([permissionResult, dateResult, officeResult]) => {
      if (controller.signal.aborted) return
      if (permissionResult.status === "fulfilled") setAccess(rbbAccess(permissionResult.value.data || null))
      else if (import.meta.env.DEV) setAccess(true)
      else {
        setAccess(false)
        setError(permissionResult.reason instanceof Error ? permissionResult.reason.message : "Hak akses belum dapat diverifikasi.")
      }
      const loadedOffices = officeResult.status === "fulfilled" && Array.isArray(officeResult.value.data) ? officeResult.value.data : []
      setOffices(loadedOffices)
      setFilters((current) => ({ ...current, actualDate: dateResult.status === "fulfilled" ? dateResult.value.data?.last_created || current.actualDate : current.actualDate, area: userCode === "000" ? current.area : userCode }))
      setDefaultsReady(true)
    })
    return () => controller.abort()
  }, [userCode])

  useEffect(() => {
    if (!defaultsReady || access !== true) return undefined
    const controller = new AbortController()
    const body: Record<string, string> = { type: "realisasi_rbb_bulan_berjalan", harian_date: filters.actualDate, compare_mode: filters.compareMode }
    if (["SEMARANG", "SOLO", "BANYUMAS", "PEKALONGAN"].includes(filters.area)) body.korwil = filters.area
    else if (filters.area !== "000") body.kode_kantor = filters.area
    void requestRbb<RbbPayload>("rbb", { method: "POST", body: JSON.stringify(body), signal: controller.signal })
      .then((response) => {
        if (controller.signal.aborted) return
        const payload = response.data || {}
        const nextRows = Array.isArray(payload.data) ? payload.data : []
        const nextMonthly = Array.isArray(payload.monthly_breakdown) ? payload.monthly_breakdown : []
        const nextMeta = payload.meta || {}
        setRows(nextRows); setMonthlyRows(nextMonthly); setGrandTotal(payload.grand_total || null); setMeta(nextMeta)
        setSort(nextMonthly.length ? { column: "periode", direction: "desc" } : nextMeta.compare_mode === "history" ? { column: "selisih", direction: "asc" } : { column: "kode_kantor", direction: "asc" })
      })
      .catch((caught: unknown) => {
        if (!controller.signal.aborted) setError(caught instanceof Error ? caught.message : "Data RBB Realisasi Kredit belum dapat dimuat.")
      })
      .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [access, defaultsReady, filters])

  const monthly = monthlyRows.length > 0
  const history = meta.compare_mode === "history"
  const sourceRows = monthly ? monthlyRows : rows
  const sortedRows = useMemo(() => sortRbbRows(sourceRows, sort), [sourceRows, sort])
  const allowedOffices = userCode === "000" ? offices : offices.filter((office) => String(office.kode_kantor || "").padStart(3, "0") === userCode)
  if (userCode !== "000" && !allowedOffices.some((office) => String(office.kode_kantor || "").padStart(3, "0") === userCode)) allowedOffices.push({ kode_kantor: userCode, nama_kantor: String((window.getUser?.() as Record<string, unknown> | null)?.branch_name || "Cabang") })
  const areaOptions: SearchableOption[] = userCode === "000"
    ? [{ value: "000", label: "000 - Konsolidasi" }, ...korwilOptions, ...offices.filter((office) => office.kode_kantor && office.kode_kantor !== "000").map((office) => ({ value: String(office.kode_kantor).padStart(3, "0"), label: `${String(office.kode_kantor).padStart(3, "0")} - ${office.nama_kantor || "Tanpa nama"}` }))]
    : allowedOffices.map((office) => ({ value: String(office.kode_kantor).padStart(3, "0"), label: `${String(office.kode_kantor).padStart(3, "0")} - ${office.nama_kantor || "Cabang"}` }))
  const areaLabel = areaOptions.find((option) => option.value === filters.area)?.label || filters.area
  const updateFilters = (next: Partial<RbbFilters>) => {
    setRows([]); setMonthlyRows([]); setGrandTotal(null); setMeta({}); setLoading(true)
    setError(null)
    setFilters((current) => ({ ...current, ...next }))
  }
  const filterPanel = <RbbRealisasiKreditFilterPanel filters={filters} areas={areaOptions} disabled={userCode !== "000"} onChange={updateFilters} />
  const handleSort = (column: string) => setSort((current) => ({ column, direction: current.column === column && current.direction === "asc" ? "desc" : "asc" }))
  const exportRows = sortedRows

  return <AppShell title="RBB Realisasi Kredit" description="Perbandingan realisasi produksi kredit dengan target RBB." activeHref={appRoutes.rbbRealisasiKredit} legacyHref="/realisasi_rbb" navbarContent={filterPanel} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1600px] flex-col px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
      <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-4 py-2.5 sm:px-5">
          <div className="flex min-w-0 items-center gap-1.5"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">RBB Realisasi Kredit</h1><Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Informasi RBB Realisasi Kredit" onClick={() => setInfoOpen(true)}><Info className="size-4" /></Button></div>
          <div className="flex min-w-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3"><span className="hidden max-w-[42rem] truncate lg:inline">Actual {dateLabel(filters.actualDate)} | {areaLabel} | {filters.compareMode === "auto" ? "Auto" : filters.compareMode === "rbb" ? "RBB" : "History YoY"}</span><span className="hidden md:inline">{loading ? "Memuat data…" : `${sourceRows.length.toLocaleString("id-ID")} baris`}</span><ReportExportMenu target={tableRef} filename={`RBB_Realisasi_Kredit_${filters.actualDate}`} onExportExcel={() => exportRbb(exportRows, monthly, history, meta, `RBB_Realisasi_Kredit_${filters.actualDate}`)} disabled={loading || sourceRows.length === 0} /></div>
          <p className="w-full truncate text-[0.68rem] text-muted-foreground lg:hidden">Actual {dateLabel(filters.actualDate)} | {areaLabel}</p>
        </header>
        <div className="report-table-scroll relative min-h-0 flex-1 overflow-auto">
          {loading && <div className="absolute inset-0 z-20 flex items-center justify-center gap-2 bg-background/75 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" /> Memuat RBB Realisasi Kredit…</div>}
          {error ? <div className="flex min-h-48 flex-col items-center justify-center gap-2 px-4 text-center"><p className="text-sm font-medium text-destructive">Data laporan belum dapat dimuat.</p><p className="max-w-xl text-xs text-muted-foreground">{error}</p></div>
            : access === false ? <div className="flex min-h-48 items-center justify-center px-5 text-center text-sm text-muted-foreground">Akses menu RBB Realisasi Kredit belum diberikan.</div>
              : <RbbRealisasiKreditTable rows={sortedRows} total={grandTotal} monthly={monthly} history={history} hideName={/^\d{3}$/.test(filters.area) && filters.area !== "000"} meta={meta} sort={sort} onSort={handleSort} tableRef={tableRef} />}
        </div>
      </section>
    </div>
    <ReportInfoDialog open={infoOpen} title="Tentang RBB Realisasi Kredit" description="Rekap realisasi produksi kredit dibandingkan dengan target RBB." onClose={() => setInfoOpen(false)}>
      <p className="rounded-lg bg-muted p-3 text-sm text-muted-foreground">Nominal pada tabel ditampilkan penuh dalam rupiah. Mode Auto menggunakan data RBB yang tersedia dan beralih ke perbandingan History YoY sesuai respons endpoint lama.</p>
      <p className="mt-3 text-xs leading-relaxed text-muted-foreground">Breakdown bulanan ditampilkan ketika area yang dipilih adalah satu kantor dan endpoint mengembalikan data bulanan.</p>
    </ReportInfoDialog>
  </AppShell>
}
