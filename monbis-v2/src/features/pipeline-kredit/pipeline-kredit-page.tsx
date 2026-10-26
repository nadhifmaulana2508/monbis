import { useEffect, useMemo, useRef, useState } from "react"
import { ArrowLeftRight, CalendarDays, Download, Info, LoaderCircle, SlidersHorizontal } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import { ReportModalCloseButton } from "@/components/report-modal-close-button"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { DetailSearchToggle, ReportDetailFilters } from "@/components/report-detail-filters"
import { ReportDetailLayer } from "@/components/report-detail-layer"
import { downloadDetailExcel } from "@/lib/download-detail-excel"
import { SearchableSelect } from "@/components/searchable-select"
import { appRoutes } from "@/lib/routes"
import { getUserOfficeCode } from "@/features/jatuh-tempo/jatuh-tempo-data"

import { buildPipelineDetailPayload, buildPipelinePayload, defaultPipelineFilters, downloadPipelineExcel, formatPipelineDate, formatPipelineMoney, pipelineNumberFormat, requestPipelineJson, requestPipelineOffices } from "./pipeline-kredit-data"
import { PipelineKreditTable } from "./pipeline-kredit-table"
import type { PipelineBreakdown, PipelineDetailPayload, PipelineDetailRow, PipelineFilters, PipelineRow, PipelineSelection } from "./pipeline-kredit-types"

const fieldClass = "h-8 rounded-lg border bg-background px-2 text-xs font-medium text-foreground outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
const areaOptions = [
  ["ALL", "Konsolidasi"], ["KOR-SEMARANG", "Korwil Semarang"], ["KOR-SOLO", "Korwil Solo"], ["KOR-BANYUMAS", "Korwil Banyumas"], ["KOR-PEKALONGAN", "Korwil Pekalongan"],
]

function PipelineFilterPanel({ filters, onChange, disabled, offices }: { filters: PipelineFilters; onChange: (next: Partial<PipelineFilters>) => void; disabled: boolean; offices: Array<{ kode_kantor?: string; nama_kantor?: string }> }) {
  const areaItems = [...areaOptions.map(([value, label]) => ({ value, label })), ...offices
    .filter((office) => office.kode_kantor && String(office.kode_kantor) !== "000")
    .sort((left, right) => String(left.kode_kantor).localeCompare(String(right.kode_kantor), "id-ID", { numeric: true }))
    .map((office) => {
      const code = String(office.kode_kantor).padStart(3, "0")
      return { value: `CAB-${code}`, label: `${code} - ${office.nama_kantor || "Tanpa nama"}` }
    })]
  return <div className="grid grid-cols-2 gap-2 lg:grid-cols-5">
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" />Actual</span><input type="date" value={filters.actualDate} onChange={(event) => onChange({ actualDate: event.target.value })} className={fieldClass} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span>Closing</span><input type="date" value={filters.closingDate} onChange={(event) => onChange({ closingDate: event.target.value })} className={fieldClass} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span>Tahun jatuh tempo</span><input type="number" min={2000} max={2100} value={filters.year} onChange={(event) => onChange({ year: event.target.value })} className={fieldClass} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><SlidersHorizontal className="size-3.5" />Area</span><SearchableSelect disabled={disabled} value={filters.area} onChange={(value) => onChange({ area: value })} placeholder="Pilih area" options={areaItems} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span>Kolektibilitas</span><select value={filters.includeDp ? "L_DP" : "L"} onChange={(event) => onChange({ includeDp: event.target.value === "L_DP" })} className={`${fieldClass} cursor-pointer`}><option value="L">L saja</option><option value="L_DP">L + DP</option></select></label>
  </div>
}

function PipelineDetailModal({ selection, filters, userCode, onClose }: { selection: PipelineSelection; filters: PipelineFilters; userCode: string; onClose: () => void }) {
  const [rows, setRows] = useState<PipelineDetailRow[]>([])
  const [payload, setPayload] = useState<PipelineDetailPayload | null>(null)
  const [status, setStatus] = useState(selection.status || "")
  const [page, setPage] = useState(1)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [search, setSearch] = useState("")
  useEffect(() => {
    let active = true
    setLoading(true); setError("")
    void requestPipelineJson<PipelineDetailPayload>({ method: "POST", body: JSON.stringify({ ...buildPipelineDetailPayload(filters, selection, userCode, status, page), kode_ao: selection.breakdown === "AO" ? selection.groupCode : null }) })
      .then((response) => { if (!active) return; setPayload(response.data || null); setRows(response.data?.data || []) })
      .catch((reason: unknown) => { if (active) setError(reason instanceof Error ? reason.message : "Detail belum dapat dimuat.") })
      .finally(() => { if (active) setLoading(false) })
    return () => { active = false }
  }, [filters, page, selection, status, userCode])
  const totalPages = Number(payload?.pagination?.total_pages || 1)
  const visibleRows = rows.filter((row) => [row.no_rekening, row.nama_nasabah, row.nama_produk, row.nama_ao, row.nama_kankas].join(" ").toLowerCase().includes(search.trim().toLowerCase()))
  return <ReportDetailLayer className="flex items-end justify-center bg-foreground/35 p-0 backdrop-blur-[2px] sm:items-center sm:p-4">
    <button type="button" aria-label="Tutup detail Pipeline Kredit" className="absolute inset-0 cursor-default" onClick={onClose} />
    <section className="relative flex h-[min(92vh,760px)] w-full flex-col overflow-hidden rounded-t-xl border bg-background shadow-2xl sm:rounded-xl" style={{ maxWidth: "72rem" }} role="dialog" aria-modal="true" aria-labelledby="pipeline-detail-title">
      <header className="flex shrink-0 items-center justify-between gap-3 border-b px-4 py-3 sm:px-5"><div className="min-w-0"><h2 id="pipeline-detail-title" className="truncate text-base font-semibold">Detail Pipeline Kredit</h2><p className="truncate text-xs text-muted-foreground">{selection.label} · Tahun JT {filters.year}</p></div><div className="flex items-center gap-2"><select value={status} onChange={(event) => { setPage(1); setStatus(event.target.value) }} className="h-9 max-w-40 rounded-md border bg-background px-2 text-xs"><option value="">Semua status</option><option value="sudah">Sudah ambil</option><option value="lunas">Lunas</option><option value="topup">Top up</option><option value="retensi">Retensi</option><option value="drop">Drop</option></select><ReportModalCloseButton label="Tutup detail" onClick={onClose} /></div></header>
      <style>{`[aria-labelledby="pipeline-detail-title"] header select,[aria-labelledby="pipeline-detail-title"] header > div:last-child > button:last-child{display:none}[aria-labelledby="pipeline-detail-title"] header > div:first-child{padding-right:10rem}`}</style>
      <div className="absolute right-3 top-2 z-30 flex shrink-0 items-center justify-end gap-1.5 bg-background sm:right-5 sm:gap-2"><DetailSearchToggle value={search} onChange={setSearch} /><ReportDetailFilters><div className="w-[min(14rem,calc(100vw-4rem))]"><SearchableSelect value={status} onChange={(value) => { setPage(1); setStatus(value) }} placeholder="Semua status" options={[{ value: "", label: "Semua status" }, { value: "sudah", label: "Sudah ambil" }, { value: "lunas", label: "Lunas" }, { value: "topup", label: "Top up" }, { value: "retensi", label: "Retensi" }, { value: "drop", label: "Drop" }]} /></div></ReportDetailFilters><Button type="button" variant="signalGreen" size="icon-lg" aria-label="Export detail Excel" title="Export detail Excel" disabled={visibleRows.length === 0} onClick={() => downloadDetailExcel(visibleRows, [["Rekening", "no_rekening"], ["Nama Nasabah", "nama_nasabah"], ["Produk", "nama_produk"], ["Nama AO", "nama_ao"], ["Kankas", "nama_kankas"], ["Plafon awal", "plafon_awal"], ["Tanggal JT", "tgl_jatuh_tempo"], ["Sisa OS", "os_actual"], ["Status", "status_ket"], ["Nominal baru", "plafon_baru"]], `Detail_Pipeline_${selection.kode || "ALL"}`)}><Download className="size-4" /></Button><ReportModalCloseButton label="Tutup detail" onClick={onClose} /></div>
      <div className="border-b bg-muted/50 px-4 py-2 text-xs text-muted-foreground">{payload?.pagination?.total_records ? `${pipelineNumberFormat.format(Number(payload.pagination.total_records))} data` : "Detail nasabah"}</div>
      <div className="relative min-h-0 flex-1 overflow-auto report-table-scroll">{loading && <div className="absolute inset-0 z-20 flex items-center justify-center gap-2 bg-background/80 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" /> Memuat detail...</div>}{error ? <div className="flex h-full items-center justify-center px-4 text-sm text-destructive">{error}</div> : visibleRows.length === 0 && !loading ? <div className="flex h-full items-center justify-center text-sm text-muted-foreground">Tidak ada detail pada filter ini.</div> : <><table className="hidden min-w-[1100px] w-full border-separate border-spacing-0 text-xs sm:table"><thead className="sticky top-0 z-30 bg-muted"><tr>{["Rekening", "Nama Nasabah", "Produk", "Nama AO", "Kankas", "Plafon awal", "Tanggal JT", "Sisa OS", "Status", "Nominal baru"].map((label) => <th key={label} className="border-b border-r border-border bg-muted px-3 py-3 text-left font-semibold">{label}</th>)}</tr></thead><tbody>{visibleRows.map((row, index) => <tr key={`${row.no_rekening}-${index}`} className="hover:bg-muted/40"><td className="border-b border-border/70 px-3 py-3 font-mono">{row.no_rekening || "-"}</td><td className="border-b border-border/70 px-3 py-3 font-medium">{row.nama_nasabah || "-"}</td><td className="border-b border-border/70 px-3 py-3">{row.nama_produk || "-"}</td><td className="border-b border-border/70 px-3 py-3">{row.nama_ao || "-"}</td><td className="border-b border-border/70 px-3 py-3">{row.nama_kankas || "-"}</td><td className="border-b border-border/70 px-3 py-3 text-right tabular-nums">{formatPipelineMoney(row.plafon_awal)}</td><td className="border-b border-border/70 px-3 py-3">{formatPipelineDate(row.tgl_jatuh_tempo || "")}</td><td className="border-b border-border/70 px-3 py-3 text-right tabular-nums">{formatPipelineMoney(row.os_actual)}</td><td className="border-b border-border/70 px-3 py-3">{row.status_ket || "-"}</td><td className="border-b border-border/70 px-3 py-3 text-right tabular-nums">{formatPipelineMoney(row.plafon_baru)}</td></tr>)}</tbody></table><div className="grid gap-2 p-3 sm:hidden">{visibleRows.map((row, index) => <Card key={`${row.no_rekening}-${index}`} size="sm"><CardContent className="grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs"><div className="col-span-2 min-w-0"><p className="font-mono text-[0.65rem] text-muted-foreground">{row.no_rekening || "-"}</p><p className="truncate font-semibold">{row.nama_nasabah || "-"}</p></div><div className="col-span-2 truncate text-muted-foreground">{row.nama_produk || "-"}</div><div><p className="text-muted-foreground">Tanggal JT</p><p>{formatPipelineDate(row.tgl_jatuh_tempo || "")}</p></div><div className="text-right"><p className="text-muted-foreground">Sisa OS</p><p className="tabular-nums">{formatPipelineMoney(row.os_actual)}</p></div><div><p className="text-muted-foreground">Plafon awal</p><p className="tabular-nums">{formatPipelineMoney(row.plafon_awal)}</p></div><div className="text-right"><p className="text-muted-foreground">Nominal baru</p><p className="tabular-nums">{formatPipelineMoney(row.plafon_baru)}</p></div><p className="col-span-2 truncate text-muted-foreground">{[row.nama_kankas, row.nama_ao, row.status_ket].filter(Boolean).join(" · ") || "-"}</p></CardContent></Card>)}</div></>}</div>
      <footer className="flex shrink-0 items-center justify-between border-t px-4 py-3 text-xs"><span>Halaman {page} dari {totalPages}</span><div className="flex gap-2"><Button type="button" size="sm" variant="outline" disabled={page <= 1 || loading} onClick={() => setPage((value) => value - 1)}>Sebelumnya</Button><Button type="button" size="sm" variant="outline" disabled={page >= totalPages || loading} onClick={() => setPage((value) => value + 1)}>Berikutnya</Button></div></footer>
    </section>
  </ReportDetailLayer>
}

export function PipelineKreditPage() {
  const userCode = getUserOfficeCode()
  const [filters, setFilters] = useState(() => ({ ...defaultPipelineFilters(), area: userCode === "000" ? "ALL" : `CAB-${userCode}` }))
  const [offices, setOffices] = useState<Array<{ kode_kantor?: string; nama_kantor?: string }>>([])
  const [rows, setRows] = useState<PipelineRow[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [selection, setSelection] = useState<PipelineSelection | null>(null)
  const [infoOpen, setInfoOpen] = useState(false)
  const branch = userCode !== "000" || filters.area.startsWith("CAB-")
  const [breakdown, setBreakdown] = useState<PipelineBreakdown>(branch ? "KANKAS" : "CABANG")
  const abortRef = useRef<AbortController | null>(null)
  const effectiveBreakdown = branch ? breakdown : "CABANG"
  const updateFilters = (next: Partial<PipelineFilters>) => {
    if (next.area !== undefined) setBreakdown(next.area.startsWith("CAB-") ? "KANKAS" : "CABANG")
    setFilters((current) => ({ ...current, ...next }))
  }
  useEffect(() => {
    const controller = new AbortController()
    void requestPipelineOffices(controller.signal).then(setOffices).catch(() => setOffices([]))
    return () => controller.abort()
  }, [])
  useEffect(() => {
    const abort = new AbortController(); abortRef.current?.abort(); abortRef.current = abort; setLoading(true); setError("")
    void requestPipelineJson<PipelineRow[]>({ method: "POST", body: JSON.stringify(buildPipelinePayload(filters, userCode, effectiveBreakdown)), signal: abort.signal }).then((response) => { if (!abort.signal.aborted) setRows((response.data || []).slice().sort((left, right) => Number(left.kode_cabang || 0) - Number(right.kode_cabang || 0))) }).catch((reason: unknown) => { if (!abort.signal.aborted) setError(reason instanceof Error ? reason.message : "Data pipeline belum dapat dimuat.") }).finally(() => { if (!abort.signal.aborted) setLoading(false) })
    return () => abort.abort()
  }, [filters, effectiveBreakdown, userCode])
  const summary = useMemo(() => {
    const officeCode = filters.area.startsWith("CAB-") ? filters.area.slice(4) : ""
    const officeName = offices.find((office) => String(office.kode_kantor).padStart(3, "0") === officeCode)?.nama_kantor
    const areaLabel = filters.area === "ALL" ? "Konsolidasi" : officeCode ? `${officeCode} - ${officeName || "Cabang"}` : areaOptions.find(([value]) => value === filters.area)?.[1] || "Area terpilih"
    return `${areaLabel} | Closing ${formatPipelineDate(filters.closingDate)} | Actual ${formatPipelineDate(filters.actualDate)}`
  }, [filters, offices])
  const title = effectiveBreakdown === "CABANG" ? "Nama Kantor" : effectiveBreakdown === "KANKAS" ? "Nama Kankas" : "Nama AO"
  return <AppShell title="Pipeline Kredit" description="Rekap dan detail pipeline kredit berdasarkan periode terpilih." activeHref={appRoutes.pipelineKredit} legacyHref="/recom_pipelane" navbarContent={<PipelineFilterPanel filters={filters} onChange={updateFilters} disabled={userCode !== "000"} offices={offices} />} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1600px] flex-col px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
      <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-4 py-2.5 sm:px-5">
          <div className="flex min-w-0 items-center gap-2"><div className="min-w-0"><div className="flex items-center gap-1.5"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Pipeline Kredit</h1><Button type="button" variant="ghost" size="icon-sm" className="rounded-full text-primary" aria-label="Informasi Pipeline Kredit" title="Informasi Pipeline Kredit" aria-expanded={infoOpen} onClick={() => setInfoOpen((value) => !value)}><Info className="size-4" /></Button></div></div></div>
          <div className="flex min-w-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3"><span className="hidden max-w-[38rem] truncate lg:inline">{summary}</span><span className="hidden md:inline">{loading ? "Mengambil data..." : `${pipelineNumberFormat.format(rows.length)} ${title.toLowerCase()}`}</span>{branch && <Button type="button" variant="outline" size="sm" className="min-h-9" onClick={() => setBreakdown((value) => value === "KANKAS" ? "AO" : "KANKAS")} aria-label="Ganti breakdown"><ArrowLeftRight /><span className="hidden sm:inline">{effectiveBreakdown === "KANKAS" ? "Kankas" : "AO"}</span></Button>}<ReportExportMenu target={() => document.querySelector<HTMLElement>(".density-page > section .report-table-scroll table")} filename={`Pipeline_Kredit_${filters.year}`} disabled={loading || rows.length === 0} onExportExcel={() => downloadPipelineExcel(rows, filters)} /></div>
        </header>
        <div className="relative min-h-0 flex-1 overflow-auto report-table-scroll">
          {loading && <div className="absolute inset-0 z-20 flex items-center justify-center gap-2 bg-background/80 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" /> Menyiapkan pipeline...</div>}
          {error ? <div className="flex min-h-56 flex-col items-center justify-center gap-3 text-center"><p className="text-sm font-medium text-destructive">Data pipeline belum dapat dimuat.</p><p className="max-w-lg text-xs text-muted-foreground">{error}</p><Button type="button" variant="outline" size="sm" onClick={() => setFilters((current) => ({ ...current }))}>Coba lagi</Button></div> : rows.length === 0 && !loading ? <div className="flex min-h-56 items-center justify-center text-sm text-muted-foreground">Tidak ada data pada filter ini.</div> : <PipelineKreditTable rows={rows} breakdown={effectiveBreakdown} onDetail={setSelection} />}
        </div>
      </section>
      <ReportInfoDialog open={infoOpen} title="Tentang Pipeline Kredit" description="Penjelasan kategori dan status pipeline." onClose={() => setInfoOpen(false)}>
        <p className="text-sm leading-relaxed text-muted-foreground">Rekap rekomendasi kredit berdasarkan nasabah yang jatuh tempo, status refinancing, lunas, top up, retensi, dan drop.</p>
      </ReportInfoDialog>
      {selection && <PipelineDetailModal selection={selection} filters={filters} userCode={userCode} onClose={() => setSelection(null)} />}
    </div>
  </AppShell>
}
