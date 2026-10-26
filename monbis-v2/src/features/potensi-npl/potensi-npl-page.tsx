import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Info, LoaderCircle, RefreshCw } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import type { SearchableOption } from "@/components/searchable-select"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"
import { PotensiNplFilterPanel } from "./potensi-npl-filter-panel"
import { PotensiNplDetailModal } from "./potensi-npl-detail-modal"
import { exportPotensi, potensiAccess, potensiAreas, potensiMainPayload, potensiName, potensiNumber, potensiSortRows, potensiUser, potensiUserCode, requestPotensi, type PotensiDetailFilters, type PotensiFilters, type PotensiOffice, type PotensiRow, type PotensiSort } from "./potensi-npl-data"
import { PotensiNplTable } from "./potensi-npl-table"

type Selection = { office: string; kankas?: string; label: string; status: string }
const today = () => { const date = new Date(); return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}` }
const emptyDetailFilters = (status = "ALL", kankas = ""): PotensiDetailFilters => ({ status, commitment: "ALL", search: "", kankas, ao: "" })

export function PotensiNplPage() {
  const userCode = potensiUserCode(potensiUser())
  const [filters, setFilters] = useState<PotensiFilters>({ closingDate: "", actualDate: "", nominalField: "saldo_bank", area: userCode === "000" ? "" : userCode })
  const [offices, setOffices] = useState<PotensiOffice[]>([])
  const [defaultsReady, setDefaultsReady] = useState(false)
  const [access, setAccess] = useState<boolean | null>(null)
  const [rows, setRows] = useState<PotensiRow[]>([])
  const [total, setTotal] = useState<PotensiRow | null>(null)
  const [sort, setSort] = useState<PotensiSort>({ column: "kode_cabang", direction: "asc" })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [infoOpen, setInfoOpen] = useState(false)
  const [selection, setSelection] = useState<Selection | null>(null)
  const [detailRows, setDetailRows] = useState<PotensiRow[]>([])
  const [detailLoading, setDetailLoading] = useState(false)
  const [detailError, setDetailError] = useState<string | null>(null)
  const [detailFilters, setDetailFilters] = useState<PotensiDetailFilters>(emptyDetailFilters())
  const [kankasOptions, setKankasOptions] = useState<PotensiOffice[]>([])
  const [aoOptions, setAoOptions] = useState<Array<{ kode_group2?: string; nama_ao?: string }>>([])
  const [detailReload, setDetailReload] = useState(0)
  const tableRef = useRef<HTMLTableElement>(null)
  const detailAbort = useRef<AbortController | null>(null)
  const areas = useMemo<SearchableOption[]>(() => potensiAreas(offices, userCode), [offices, userCode])
  const sortedRows = useMemo(() => potensiSortRows(rows, sort), [rows, sort])
  const areaLabel = areas.find((option) => option.value === filters.area)?.label || filters.area || "Konsolidasi (Semua)"

  useEffect(() => {
    const controller = new AbortController()
    void Promise.allSettled([
      requestPotensi<Record<string, unknown>>("rbac", { method: "POST", body: JSON.stringify({ type: "me" }), signal: controller.signal }),
      requestPotensi<{ last_created?: string; last_closing?: string }>("date", { signal: controller.signal }),
      requestPotensi<PotensiOffice[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kantor" }), signal: controller.signal }),
    ]).then(([permission, date, office]) => {
      if (controller.signal.aborted) return
      if (permission.status === "fulfilled") { const permitted = potensiAccess(permission.value.data || null); setAccess(permitted); if (!permitted) setLoading(false) }
      else if (import.meta.env.DEV) setAccess(true)
      else { setAccess(false); setLoading(false); setError(permission.reason instanceof Error ? permission.reason.message : "Hak akses Potensi NPL belum dapat diverifikasi.") }
      setOffices(office.status === "fulfilled" && Array.isArray(office.value.data) ? office.value.data : [])
      if (date.status === "fulfilled") {
        const actualDate = date.value.data?.last_created || today()
        setFilters((current) => ({ ...current, actualDate, closingDate: date.value.data?.last_closing || current.closingDate || actualDate }))
      } else setFilters((current) => ({ ...current, actualDate: current.actualDate || today(), closingDate: current.closingDate || today() }))
      setDefaultsReady(true)
    })
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!defaultsReady || access !== true || !filters.actualDate || !filters.closingDate) return undefined
    const controller = new AbortController()
    void requestPotensi<{ data?: PotensiRow[]; grand_total?: PotensiRow }>("npl", { method: "POST", body: JSON.stringify(potensiMainPayload(filters)), signal: controller.signal })
      .then((response) => {
        if (controller.signal.aborted) return
        const nextRows = Array.isArray(response.data?.data) ? response.data.data : []
        setRows(nextRows); setTotal(response.data?.grand_total || null)
        setSort({ column: "kode_cabang", direction: "asc" })
      })
      .catch((reason: unknown) => { if (!controller.signal.aborted) { setRows([]); setTotal(null); setError(reason instanceof Error ? reason.message : "Data Potensi NPL belum dapat dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [access, defaultsReady, filters])

  useEffect(() => {
    if (!selection || access !== true) return undefined
    const controller = new AbortController()
    detailAbort.current?.abort(); detailAbort.current = controller
    const payload = {
      type: "Debitur Potensi NPL", kode_kantor: selection.office === "000" ? "" : selection.office,
      korwil: filters.area.startsWith("KOR-") ? filters.area.slice(4) : "", kode_kankas: detailFilters.kankas, kode_ao: detailFilters.ao,
      closing_date: filters.closingDate, harian_date: filters.actualDate, hitung_berdasarkan: filters.nominalField,
      status_filter: "ALL", include_lunas: 1, include_aman_backflow: 1,
    }
    void requestPotensi<PotensiRow[]>("npl", { method: "POST", body: JSON.stringify(payload), signal: controller.signal })
      .then((response) => { if (!controller.signal.aborted) setDetailRows(Array.isArray(response.data) ? response.data : []) })
      .catch((reason: unknown) => { if (!controller.signal.aborted) { setDetailRows([]); setDetailError(reason instanceof Error ? reason.message : "Detail Potensi NPL belum dapat dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setDetailLoading(false) })
    return () => controller.abort()
  }, [selection, filters, detailFilters.kankas, detailFilters.ao, detailReload, access])

  const selectedOffice = selection?.office
  useEffect(() => {
    if (!selectedOffice || selectedOffice === "000") return undefined
    const controller = new AbortController()
    void Promise.all([
      requestPotensi<PotensiOffice[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kankas", kode_kantor: selectedOffice }), signal: controller.signal }),
      requestPotensi<Array<{ kode_group2?: string; nama_ao?: string }>>("kode", { method: "POST", body: JSON.stringify({ type: "kode_ao_kredit", kode_kantor: selectedOffice }), signal: controller.signal }),
    ]).then(([kankas, ao]) => {
      if (controller.signal.aborted) return
      setKankasOptions(Array.isArray(kankas.data) ? kankas.data : [])
      setAoOptions(Array.isArray(ao.data) ? ao.data : [])
    }).catch(() => { if (!controller.signal.aborted) { setKankasOptions([]); setAoOptions([]) } })
    return () => controller.abort()
  }, [selectedOffice])

  useEffect(() => () => detailAbort.current?.abort(), [])

  const updateFilters = (next: Partial<PotensiFilters>) => {
    setLoading(true); setRows([]); setTotal(null); setError(null); setSelection(null)
    setSort({ column: "kode_cabang", direction: "asc" })
    setFilters((current) => ({ ...current, ...next }))
  }
  const onSort = (column: string) => setSort((current) => ({ column, direction: current.column === column && current.direction === "asc" ? "desc" : "asc" }))
  const closeDetail = useCallback(() => { detailAbort.current?.abort(); setSelection(null); setDetailRows([]); setDetailError(null) }, [])
  const openDetail = (office: string, status: string, kankas?: string) => {
    const targetOffice = office || "000"
    if (userCode !== "000" && targetOffice.slice(0, 3) !== userCode) return
    setKankasOptions([]); setAoOptions([]); setDetailFilters(emptyDetailFilters(status, kankas || "")); setDetailRows([]); setDetailError(null); setDetailLoading(true)
    setSelection({ office: targetOffice.slice(0, 3), ...(kankas ? { kankas } : {}), label: targetOffice === "000" ? areaLabel : offices.find((item) => String(item.kode_kantor).padStart(3, "0") === targetOffice.slice(0, 3))?.nama_kantor || potensiName({ nama_cabang: targetOffice }), status })
  }
  const filterPanel = <PotensiNplFilterPanel filters={filters} areas={areas} disabled={access === false || userCode !== "000"} onChange={updateFilters} />
  const context = `${filters.nominalField === "saldo_bank" ? "Saldo Bank" : "Baki Debet"} | Closing ${filters.closingDate || "-"} | Actual ${filters.actualDate || "-"} | ${areaLabel}`

  return <AppShell title="Potensi NPL" description="Monitoring kandidat NPL, status penyelamatan, dan komitmen pembayaran cabang." activeHref={appRoutes.potensiNpl} legacyHref="/potensi_npl" navbarContent={filterPanel} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1600px] flex-col px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
      <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-4 py-2.5 sm:px-5">
          <div className="flex min-w-0 items-center gap-1.5"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Potensi NPL</h1><Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Informasi Potensi NPL" onClick={() => setInfoOpen(true)}><Info className="size-4" aria-hidden="true" /></Button></div>
          <div className="flex min-w-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3"><span className="hidden max-w-[42rem] truncate lg:inline">{context}</span><span className="hidden md:inline">{loading ? "Memuat data…" : `${potensiNumber.format(rows.length)} kantor`}</span><ReportExportMenu target={tableRef} filename={`Rekap_Potensi_NPL_${filters.actualDate || "actual"}`} disabled={loading || rows.length === 0} onExportExcel={() => exportPotensi(sortedRows, total, filters, sort)} /></div>
          <p className="w-full truncate text-[0.68rem] text-muted-foreground lg:hidden">{filters.nominalField === "saldo_bank" ? "Saldo Bank" : "Baki Debet"} | {areaLabel}</p>
        </header>
        <div className="relative min-h-0 flex-1 overflow-hidden">
          {loading && <div className="absolute inset-0 z-[80] flex items-center justify-center gap-2 bg-background/75 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true"/>Memuat Potensi NPL…</div>}
          {error && !loading ? <div className="flex h-full min-h-48 flex-col items-center justify-center gap-2 p-5 text-center"><p className="text-sm font-medium text-destructive">Data Potensi NPL belum dapat dimuat.</p><p className="max-w-xl text-xs text-muted-foreground">{error}</p><Button variant="outline" size="sm" onClick={() => updateFilters({})}><RefreshCw className="size-3.5"/>Coba lagi</Button></div>
            : access === false ? <div className="flex h-full min-h-48 items-center justify-center p-6 text-center text-sm text-muted-foreground">Akses menu Potensi NPL belum diberikan.</div>
              : <PotensiNplTable rows={sortedRows} total={total} sort={sort} filters={filters} tableRef={tableRef} onSort={onSort} onDetail={openDetail}/ >}
        </div>
      </section>
    </div>
    <ReportInfoDialog open={infoOpen} title="Panduan Potensi NPL" description="Kamus status kandidat dan komitmen penyelesaian." meta={context} onClose={() => setInfoOpen(false)}>
      <div className="space-y-3 text-sm"><div className="grid gap-2 sm:grid-cols-2">{[
        ["Total Potensi NPL", "Seluruh rekening kandidat yang dipantau dari posisi closing sampai posisi actual."],
        ["Aman / Lunas", "Rekening yang tidak menjadi NPL, termasuk tetap aman, membaik/backflow, atau sudah lunas."],
        ["Jatuh Tempo", "Rekening yang jatuh tempo dan perlu diselesaikan agar tidak masuk flow."],
        ["Flow (KL/D/M)", "Rekening yang pada posisi actual sudah masuk kolektibilitas Kurang Lancar, Diragukan, atau Macet."],
        ["Masih Potensi", "Belum tercatat sebagai flow, tetapi masih berisiko berdasarkan DPD, tunggakan, atau jatuh tempo."],
        ["Komitmen Cabang", "Catatan rencana penyelesaian, tanggal janji, nominal, dan alasan untuk pemantauan rekening."],
      ].map(([title, description]) => <article key={title} className="rounded-lg border p-3"><h3 className="text-sm font-semibold">{title}</h3><p className="mt-1 text-xs leading-relaxed text-muted-foreground">{description}</p></article>)}</div><p className="rounded-lg bg-muted p-3 text-xs text-muted-foreground">Klik angka NOA untuk melihat daftar detail. Filter status dan komitmen berlaku pada detail; filter Kankas/AO mengubah permintaan data.</p></div>
    </ReportInfoDialog>
    {selection && <PotensiNplDetailModal key={`${selection.office}-${selection.kankas || "all"}`} office={selection.office} kankas={selection.kankas} label={selection.label} filters={filters} rows={detailRows} loading={detailLoading} error={detailError} detailFilters={detailFilters} kankasOptions={kankasOptions} aoOptions={aoOptions} onFiltersChange={(next) => { if ("kankas" in next || "ao" in next) { setDetailLoading(true); setDetailRows([]); setDetailError(null) } setDetailFilters((current) => ({ ...current, ...next })) }} onClose={closeDetail} onRetry={() => { setDetailLoading(true); setDetailReload((value) => value + 1) }}/ >}
  </AppShell>
}
