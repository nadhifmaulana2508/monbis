import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Info, LoaderCircle } from "lucide-react"
import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import type { SearchableOption } from "@/components/searchable-select"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"
import { FlowParDetailModal } from "./flow-par-detail-modal"
import { FlowParFilterPanel } from "./flow-par-filter-panel"
import { exportFlowPar, flowParAccess, flowParAreas, flowParCurrentUser, flowParPayload, flowParSortRows, flowParUserCode, requestFlowPar, type FlowParDetailSelection, type FlowParFilters, type FlowParOffice, type FlowParRow, type FlowParSort } from "./flow-par-data"
import { FlowParTable } from "./flow-par-table"

function localDate() { const d = new Date(); return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}` }

export function FlowParPage() {
  const userCode = flowParUserCode(flowParCurrentUser())
  const today = localDate()
  const [filters, setFilters] = useState<FlowParFilters>({ closingDate: today, actualDate: today, nominalField: "saldo_bank", area: userCode === "000" ? "ALL" : `CAB-${userCode}` })
  const [offices, setOffices] = useState<FlowParOffice[]>([])
  const [defaultsReady, setDefaultsReady] = useState(false)
  const [access, setAccess] = useState<boolean | null>(null)
  const [rows, setRows] = useState<FlowParRow[]>([])
  const [total, setTotal] = useState<FlowParRow | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [sort, setSort] = useState<FlowParSort>({ column: "", direction: "asc" })
  const [infoOpen, setInfoOpen] = useState(false)
  const [detail, setDetail] = useState<FlowParDetailSelection | null>(null)
  const desktopRef = useRef<HTMLTableElement>(null)
  const mobileRef = useRef<HTMLTableElement>(null)

  useEffect(() => {
    const controller = new AbortController()
    void Promise.allSettled([
      requestFlowPar<Record<string, unknown>>("rbac", { method: "POST", body: JSON.stringify({ type: "me" }), signal: controller.signal }),
      requestFlowPar<{ last_created?: string; last_closing?: string }>("date", { signal: controller.signal }),
      requestFlowPar<FlowParOffice[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kantor" }), signal: controller.signal }),
    ]).then(([permission, dates, office]) => {
      if (controller.signal.aborted) return
      if (permission.status === "fulfilled") {
        const allowed = flowParAccess(permission.value.data || null)
        setAccess(allowed)
        if (!allowed) setLoading(false)
      } else if (import.meta.env.DEV) setAccess(true)
      else { setAccess(false); setLoading(false); setError(permission.reason instanceof Error ? permission.reason.message : "Hak akses Flow PAR belum dapat diverifikasi.") }
      setOffices(office.status === "fulfilled" && Array.isArray(office.value.data) ? office.value.data : [])
      if (dates.status === "fulfilled" && dates.value.data?.last_created) {
        const actualDate = dates.value.data.last_created
        setFilters((current) => ({ ...current, actualDate, closingDate: dates.value.data?.last_closing || actualDate }))
      } else { const fallbackDate = localDate(); setFilters((current) => ({ ...current, actualDate: fallbackDate, closingDate: fallbackDate })) }
      setDefaultsReady(true)
    })
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!defaultsReady || access !== true || !filters.closingDate || !filters.actualDate) return undefined
    const controller = new AbortController()
    void requestFlowPar<{ data?: FlowParRow[]; grand_total?: FlowParRow }>("flow_par", { method: "POST", body: JSON.stringify(flowParPayload(filters)), signal: controller.signal })
      .then((result) => { if (!controller.signal.aborted) { setRows(Array.isArray(result.data?.data) ? result.data.data : []); setTotal(result.data?.grand_total || null); setError(null) } })
      .catch((reason: unknown) => { if (!controller.signal.aborted) { setRows([]); setTotal(null); setError(reason instanceof Error ? reason.message : "Data Flow PAR gagal dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [access, defaultsReady, filters])

  const areas = useMemo<SearchableOption[]>(() => flowParAreas(offices, userCode), [offices, userCode])
  const areaLabel = areas.find((item) => item.value === filters.area)?.label || filters.area
  const sortedRows = useMemo(() => flowParSortRows(rows, sort), [rows, sort])
  const updateFilters = (next: Partial<FlowParFilters>) => { setDetail(null); setLoading(true); setRows([]); setTotal(null); setError(null); setSort({ column: "", direction: "asc" }); setFilters((current) => ({ ...current, ...next })) }
  const onSort = (column: string) => setSort((current) => ({ column, direction: current.column === column ? current.direction === "asc" ? "desc" : "asc" : "asc" }))
  const closeDetail = useCallback(() => setDetail(null), [])
  const navbarFilters = <FlowParFilterPanel filters={filters} areas={areas} disabled={access === false || userCode !== "000"} onChange={updateFilters} />
  const context = `${filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"} | Closing ${filters.closingDate} | Actual ${filters.actualDate} | ${areaLabel}`

  return <AppShell title="Flow PAR" description="Rekap debitur yang berpindah ke kolektibilitas KL." activeHref={appRoutes.flowPar} legacyHref="/flow_par" navbarContent={navbarFilters} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1700px] flex-col px-3 py-3 sm:px-6 sm:py-5 lg:px-8"><section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
      <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-3 py-2.5 sm:px-5"><div className="flex min-w-0 items-center gap-1"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Rekap Flow PAR</h1><Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Informasi Flow PAR" onClick={() => setInfoOpen(true)}><Info className="size-4" aria-hidden="true" /></Button></div><div className="flex min-w-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3"><span className="hidden max-w-[42rem] truncate lg:inline">{context}</span><span className="hidden md:inline">{loading ? "Memuat data…" : `${sortedRows.length.toLocaleString("id-ID")} area`}</span><ReportExportMenu target={() => window.innerWidth < 768 ? mobileRef.current : desktopRef.current} filename={`Rekap_Flow_PAR_${filters.actualDate}`} disabled={loading || sortedRows.length === 0} onExportExcel={() => exportFlowPar(sortedRows, total, filters, sort)} /></div><p className="w-full truncate text-[0.68rem] text-muted-foreground lg:hidden">{filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"} · {areaLabel}</p></header>
      <div className="relative min-h-0 flex-1 overflow-hidden">{loading && <div className="absolute inset-0 z-[80] flex items-center justify-center gap-2 bg-background/75 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" />Memuat Flow PAR…</div>}{error && !loading ? <div className="flex h-full min-h-48 flex-col items-center justify-center gap-2 p-5 text-center"><p className="text-sm font-medium text-destructive">Data Flow PAR gagal dimuat.</p><p className="max-w-xl text-xs text-muted-foreground">{error}</p><Button variant="outline" size="sm" onClick={() => updateFilters({})}>Coba lagi</Button></div> : access === false ? <div className="flex h-full min-h-48 items-center justify-center p-6 text-center text-sm text-muted-foreground">Akses menu Flow PAR belum diberikan.</div> : <FlowParTable rows={sortedRows} total={total} sort={sort} filters={filters} onSort={onSort} onDetail={setDetail} desktopRef={desktopRef} mobileRef={mobileRef} />}</div>
    </section></div>
    <ReportInfoDialog open={infoOpen} title="Panduan Tindak Lanjut Flow PAR" description="Acuan penanganan rekening yang memburuk ke kolektibilitas KL." meta={context} onClose={() => setInfoOpen(false)}><div className="space-y-3 text-sm"><p className="rounded-lg bg-muted p-3 text-muted-foreground">Prioritaskan tindak lanjut sebelum posisi harian/closing. Klik angka pada tabel untuk melihat rekening yang perlu ditangani berdasarkan penyebab flow.</p><div className="grid gap-2 sm:grid-cols-2">{[{ label: "Jatuh Tempo / Lainnya", text: "Termasuk jatuh tempo tagihan, kondisi lainnya, atau one obligor." }, { label: "DPD Pokok > 90 Hari", text: "Minimal membayar satu kali angsuran pokok; bila memungkinkan, lakukan satu kali angsuran penuh pokok dan bunga." }, { label: "DPD Bunga > 90 Hari", text: "Selesaikan tunggakan pokok dan minimal satu kali angsuran bunga." }, { label: "DPD Pokok + Bunga > 90 Hari", text: "Prioritaskan pembayaran angsuran pokok dan bunga sesuai data tunggakan." }].map((item) => <section key={item.label} className="rounded-lg border p-3"><h3 className="font-semibold">{item.label}</h3><p className="mt-1 text-xs text-muted-foreground">{item.text}</p></section>)}</div><p className="rounded-lg border bg-muted/30 p-3 text-xs text-muted-foreground">Nominal akhir mengikuti tunggakan, jadwal angsuran, kondisi rekening, dan ketentuan internal. Pastikan pembayaran tercermin pada posisi harian berikutnya.</p></div></ReportInfoDialog>
    {detail && <FlowParDetailModal key={`${detail.kode}-${detail.korwil}-${detail.klasifikasi}-${filters.closingDate}-${filters.actualDate}-${filters.nominalField}`} selection={detail} filters={filters} onClose={closeDetail} />}
  </AppShell>
}
