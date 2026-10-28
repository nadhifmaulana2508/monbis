import { useEffect, useMemo, useRef, useState } from "react"
import { ArrowLeftRight, CalendarDays, Filter, Info, LoaderCircle, Minus, SlidersHorizontal, Triangle } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { GroupedReportTableHead } from "@/components/grouped-report-table-head"
import { ReportSortHeader } from "@/components/report-sort-header"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import { SearchableSelect, type SearchableOption } from "@/components/searchable-select"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"

import { exportNpl, formatNplMoney, formatNplPercent, nplCode, nplName, nplNumber, nplPayload, nplSortRows, requestNplJson, userOfficeCode, type NplFilters, type NplOffice, type NplRow, type NplSort, type NplView } from "./npl-data"

const korwilOptions: SearchableOption[] = [
  { value: "KOR-SEMARANG", label: "Korwil Semarang" },
  { value: "KOR-SOLO", label: "Korwil Solo" },
  { value: "KOR-BANYUMAS", label: "Korwil Banyumas" },
  { value: "KOR-PEKALONGAN", label: "Korwil Pekalongan" },
]

const kolekColumns = [
  ["bd_L", "Lancar"], ["bd_DP", "DPK"], ["bd_KL", "KL"], ["bd_D", "Diragukan"], ["bd_M", "Macet"],
  ["bd_npl", "NPL"], ["total_bd", "Portofolio"], ["persentase_npl", "% NPL"],
] as const
const compareColumns = [
  ["npl_closing", "Closing · Baki Debet"], ["npl_closing_persen", "Closing · %"],
  ["npl_harian", "Actual · Baki Debet"], ["npl_harian_persen", "Actual · %"],
  ["selisih_npl", "Growth · Baki Debet"], ["selisih_npl_persen", "Growth · %"], ["status", "Status"],
] as const
const compareGroups = [
  { label: "Closing", columns: [{ key: "npl_closing", label: "Baki Debet" }, { key: "npl_closing_persen", label: "%", className: "w-20" }] },
  { label: "Actual", columns: [{ key: "npl_harian", label: "Baki Debet" }, { key: "npl_harian_persen", label: "%", className: "w-20" }] },
  { label: "Growth", columns: [{ key: "selisih_npl", label: "Baki Debet" }, { key: "selisih_npl_persen", label: "%", className: "w-20" }] },
]

function initialFilters(userCode: string): NplFilters {
  const today = new Date()
  const actualDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`
  return { actualDate, closingDate: `${new Date(today.getFullYear(), today.getMonth(), 0).getFullYear()}-${String(new Date(today.getFullYear(), today.getMonth(), 0).getMonth() + 1).padStart(2, "0")}-${String(new Date(today.getFullYear(), today.getMonth(), 0).getDate()).padStart(2, "0")}`, nominalField: "baki_debet", area: userCode === "000" ? "ALL" : `CAB-${userCode}` }
}

function dateLabel(value: string) {
  const match = value.match(/^(\d{4})-(\d{2})-(\d{2})$/)
  return match ? `${match[3]}/${match[2]}/${match[1]}` : value
}

function NplFiltersBar({ filters, offices, disabled, showClosing, onChange }: { filters: NplFilters; offices: NplOffice[]; disabled: boolean; showClosing: boolean; onChange: (next: Partial<NplFilters>) => void }) {
  const options = useMemo(() => [
    { value: "ALL", label: "Konsolidasi" }, ...korwilOptions,
    ...offices.filter((office) => String(office.kode_kantor || "") !== "000").map((office) => {
      const code = String(office.kode_kantor || "").padStart(3, "0")
      return { value: `CAB-${code}`, label: `${code} - ${office.nama_kantor || "Cabang"}` }
    }),
  ], [offices])

  const fieldClass = "h-8 rounded-lg border bg-background px-2 text-xs font-medium text-foreground outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
  return <div className={`grid grid-cols-2 gap-2 ${showClosing ? "lg:grid-cols-4" : "lg:grid-cols-3"}`}>
    {showClosing && <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Closing (M-1)</span><input type="date" value={filters.closingDate} onChange={(event) => onChange({ closingDate: event.target.value })} className={fieldClass} /></label>}
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Harian / actual</span><input type="date" value={filters.actualDate} onChange={(event) => onChange({ actualDate: event.target.value })} className={fieldClass} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><Filter className="size-3.5" aria-hidden="true" />Area / cabang</span><SearchableSelect value={filters.area} options={options} placeholder="Pilih area" disabled={disabled} onChange={(area) => onChange({ area })} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><SlidersHorizontal className="size-3.5" aria-hidden="true" />Hitung berdasarkan</span><select value={filters.nominalField} onChange={(event) => onChange({ nominalField: event.target.value as NplFilters["nominalField"] })} className={`${fieldClass} cursor-pointer`}><option value="baki_debet">Baki debet</option><option value="saldo_bank">Saldo bank</option></select></label>
  </div>
}

function NplTable({ rows, total, view, sort, onSort, tableRef }: { rows: NplRow[]; total: NplRow | null; view: NplView; sort: NplSort; onSort: (column: string) => void; tableRef: React.RefObject<HTMLTableElement | null> }) {
  const columns = view === "kolek" ? kolekColumns : compareColumns
  const codeWidth = "w-[4rem] min-w-[4rem]"
  const nameWidth = "w-[9rem] min-w-[9rem]"
  const head = "sticky top-0 z-30 h-9 border-b border-border bg-muted px-2 text-center text-[0.68rem] font-semibold text-muted-foreground"
  const identity = "sticky border-r border-border/70"
  const totalCell = `sticky ${view === "npl" ? "top-[4.5rem]" : "top-9"} z-20 border-b border-border bg-muted px-2 py-2 font-semibold`
  const rowsWithTotal = [...(total ? [{ ...total, __total: true } as NplRow & { __total: boolean }] : []), ...rows]
  return <div className="report-table-scroll h-full overflow-auto">
    <table ref={tableRef} className="min-w-[900px] w-full border-separate border-spacing-0 text-xs">
      {view === "npl" && <colgroup><col className={codeWidth} /><col className={nameWidth} />{columns.map(([key]) => <col key={key} className={key === "status" ? "w-16" : key.includes("persen") ? "w-20" : "w-36"} />)}</colgroup>}
      {view === "npl" ? <GroupedReportTableHead identityColumns={[{ key: "kode_unit", label: "Kode", className: codeWidth }, { key: "nama_unit", label: "Nama Kantor", className: nameWidth }]} groups={compareGroups} trailingColumns={[{ key: "status", label: "Status", className: "w-16 min-w-16" }]} sort={sort} onSort={onSort} /> : <thead><tr className="h-9">
        <ReportSortHeader label="Kode" labelText="Kode" column="kode_unit" sort={sort} onSort={onSort} className={`${head} ${codeWidth} left-0 z-50`} buttonClassName="justify-center" />
        <ReportSortHeader label="Nama Kantor" labelText="Nama Kantor" column="nama_unit" sort={sort} onSort={onSort} className={`${head} ${nameWidth} left-16 z-50`} buttonClassName="justify-center" />
        {columns.map(([key, label]) => <ReportSortHeader key={key} label={label} labelText={label} column={key} sort={sort} onSort={onSort} className={head} buttonClassName="justify-center" />)}
      </tr></thead>}
      <tbody>{rowsWithTotal.map((row, index) => {
        const isTotal = "__total" in row
        const delta = Number(row.selisih_npl ?? 0)
        return <tr key={isTotal ? "total" : `${nplCode(row)}-${index}`} className={isTotal ? "bg-muted/80" : "group hover:bg-muted/25"}>
          <td className={`${identity} ${codeWidth} left-0 ${isTotal ? `${totalCell} z-40` : "z-10 bg-background px-2 py-3 font-mono text-[0.68rem] text-muted-foreground group-hover:bg-muted/60"}`}>{isTotal ? "ALL" : nplCode(row)}</td>
          <td title={isTotal ? "GRAND TOTAL" : nplName(row)} className={`${identity} ${nameWidth} left-16 min-w-0 truncate ${isTotal ? `${totalCell} z-40` : "z-10 bg-background px-2 py-3 font-medium group-hover:bg-muted/60"}`}>{isTotal ? "GRAND TOTAL" : nplName(row)}</td>
          {columns.map(([key]) => {
            if (key === "status") return <td key={key} className={`${isTotal ? totalCell : "border-b border-border/70 px-2 py-3"} text-center ${delta > 0 ? "text-destructive" : delta < 0 ? "text-signal-green-foreground" : "text-muted-foreground"}`} title={delta > 0 ? "NPL naik" : delta < 0 ? "NPL turun" : "NPL tetap"} aria-label={delta > 0 ? "NPL naik" : delta < 0 ? "NPL turun" : "NPL tetap"}>{delta > 0 ? <Triangle className="mx-auto size-3.5 fill-current" aria-hidden="true" /> : delta < 0 ? <Triangle className="mx-auto size-3.5 rotate-180 fill-current" aria-hidden="true" /> : <Minus className="mx-auto size-3.5" aria-hidden="true" />}</td>
            if (key === "persentase_npl" || key.endsWith("_persen")) return <td key={key} className={`${isTotal ? totalCell : "border-b border-border/70 px-2 py-3"} text-right tabular-nums`}>{formatNplPercent(row[key])}</td>
            const noaKey = ({ bd_L: "noa_L", bd_DP: "noa_DP", bd_KL: "noa_KL", bd_D: "noa_D", bd_M: "noa_M", bd_npl: "noa_npl", total_bd: "total_noa" } as Record<string, string>)[key]
            const money = key === "selisih_npl" ? Number(row[key] ?? 0) : Number(row[key] ?? 0)
            const sign = key === "selisih_npl" ? money > 0 ? "+" : money < 0 ? "(" : "" : ""
            const formatted = key === "selisih_npl" && money < 0 ? `(${formatNplMoney(Math.abs(money))})` : `${sign}${formatNplMoney(money)}`
            const tone = key === "selisih_npl" ? money > 0 ? "text-destructive" : money < 0 ? "text-signal-green-foreground" : "" : ""
            return <td key={key} className={`${isTotal ? totalCell : "border-b border-border/70 px-2 py-1"} text-right tabular-nums`}><div className={`flex min-h-9 flex-col justify-center text-right ${tone}`}><span className="whitespace-nowrap">{formatted}</span>{noaKey && Number(row[noaKey] ?? 0) > 0 && <span className="text-[0.65rem] font-normal text-muted-foreground">NOA: {nplNumber.format(Number(row[noaKey]))}</span>}</div></td>
          })}
        </tr>
      })}{!rows.length && <tr><td colSpan={columns.length + 2} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada data pada filter ini.</td></tr>}</tbody>
    </table>
  </div>
}

export function NplPage() {
  const userCode = userOfficeCode()
  const access = useMemo(() => true, [])
  const [filters, setFilters] = useState(() => initialFilters(userCode))
  const [view, setView] = useState<NplView>("kolek")
  const [offices, setOffices] = useState<NplOffice[]>([])
  const [rows, setRows] = useState<NplRow[]>([])
  const [total, setTotal] = useState<NplRow | null>(null)
  const [sort, setSort] = useState<NplSort>({ column: "kode_unit", direction: "asc" })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [infoOpen, setInfoOpen] = useState(false)
  const tableRef = useRef<HTMLTableElement>(null)

  useEffect(() => {
    let active = true
    const abort = new AbortController()
    const load = async () => {
      if (!active) return
      try {
        const [dateResult, officeResult] = await Promise.all([
          requestNplJson<{ last_created?: string; last_closing?: string }>("date", { signal: abort.signal }),
          requestNplJson<NplOffice[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kantor" }), signal: abort.signal }),
        ])
        if (!active) return
        setOffices(Array.isArray(officeResult.data) ? officeResult.data : [])
        if (dateResult.data?.last_created) setFilters((current) => ({ ...current, actualDate: dateResult.data?.last_created || current.actualDate, closingDate: dateResult.data?.last_closing || current.closingDate }))
      } catch (reason) {
        if (active && !(reason instanceof Error && reason.name === "AbortError")) setError(reason instanceof Error ? reason.message : "Default NPL belum dapat dimuat.")
      } finally { if (active) setLoading(false) }
    }
    void load()
    return () => { active = false; abort.abort() }
  }, [])

  useEffect(() => {
    if (!filters.actualDate || (view === "npl" && !filters.closingDate)) return undefined
    const controller = new AbortController()
    const timer = window.setTimeout(() => {
      setLoading(true); setError(null)
      void requestNplJson<{ data?: NplRow[]; grand_total?: NplRow }>(view === "kolek" ? "kredit" : "npl", { method: "POST", body: JSON.stringify(nplPayload(view, filters)), signal: controller.signal })
        .then((response) => { if (!controller.signal.aborted) { setRows(Array.isArray(response.data?.data) ? response.data.data : []); setTotal(response.data?.grand_total || null) } })
        .catch((reason: unknown) => { if (!controller.signal.aborted) { setRows([]); setTotal(null); setError(reason instanceof Error ? reason.message : "Data NPL belum dapat dimuat.") } })
        .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    }, 180)
    return () => { window.clearTimeout(timer); controller.abort() }
  }, [filters, view])

  const allowedOffices = userCode === "000" ? offices : offices.filter((office) => String(office.kode_kantor || "").padStart(3, "0") === userCode)
  if (userCode !== "000" && allowedOffices.length === 0) allowedOffices.push({ kode_kantor: userCode, nama_kantor: "Cabang" })
  const sortedRows = useMemo(() => nplSortRows(rows, sort), [rows, sort])
  const filterBar = <NplFiltersBar filters={filters} offices={allowedOffices} disabled={userCode !== "000"} showClosing={view === "npl"} onChange={(next) => setFilters((current) => ({ ...current, ...next }))} />
  const areaLabel = filters.area === "ALL" ? "Konsolidasi" : korwilOptions.find((option) => option.value === filters.area)?.label || allowedOffices.find((office) => `CAB-${String(office.kode_kantor || "").padStart(3, "0")}` === filters.area)?.nama_kantor || filters.area
  const summaryText = `${filters.nominalField === "baki_debet" ? "Baki debet" : "Saldo bank"}${view === "npl" ? ` | Closing ${dateLabel(filters.closingDate)}` : ""} | Actual ${dateLabel(filters.actualDate)} | ${areaLabel}`
  const onSort = (column: string) => setSort((current) => ({ column, direction: current.column === column && current.direction === "asc" ? "desc" : "asc" }))
  const changeView = (next: NplView) => { setView(next); setSort({ column: "kode_unit", direction: "asc" }) }

  return <AppShell title="NPL" description="Monitoring kolektibilitas dan perubahan NPL." activeHref={appRoutes.npl} legacyHref="/pages/report_npl.php" navbarContent={filterBar} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1600px] flex-col px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
      <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-4 py-2.5 sm:px-5">
          <div className="flex min-w-0 items-center gap-1.5"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Monitoring NPL</h1><Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Informasi NPL" onClick={() => setInfoOpen(true)}><Info className="size-4" /></Button></div>
          <div className="flex min-w-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3"><span className="hidden max-w-[42rem] truncate lg:inline">{summaryText}</span><span className="hidden md:inline">{loading ? "Memuat data…" : `${nplNumber.format(rows.length)} kantor`}</span><Button type="button" variant="outline" size="icon" className="size-10 sm:size-8" onClick={() => changeView(view === "kolek" ? "npl" : "kolek")} aria-label={`Ganti jenis laporan, saat ini ${view === "kolek" ? "Kolektibilitas" : "Perbandingan NPL"}`} title={`Ganti ke ${view === "kolek" ? "Perbandingan NPL" : "Kolektibilitas"}`}><ArrowLeftRight aria-hidden="true" /></Button><ReportExportMenu target={tableRef} filename={`${view === "kolek" ? "Kolektibilitas" : "Perbandingan_NPL"}_${filters.actualDate || "actual"}`} disabled={loading || rows.length === 0} onExportExcel={() => exportNpl(nplSortRows(rows, sort), view, filters.actualDate)} /></div>
          <p className="w-full truncate text-[0.68rem] text-muted-foreground lg:hidden">{summaryText}</p>
        </header>
        <div className="relative min-h-0 flex-1 overflow-hidden">{loading && <div className="absolute inset-0 z-[80] flex items-center justify-center gap-2 bg-background/70 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" /> Memuat laporan NPL…</div>}{error ? <div className="flex h-full min-h-48 flex-col items-center justify-center gap-2 p-5 text-center"><p className="text-sm font-medium text-destructive">Data NPL belum dapat dimuat.</p><p className="max-w-xl text-xs text-muted-foreground">{error}</p><Button variant="outline" size="sm" onClick={() => setFilters((current) => ({ ...current }))}>Coba lagi</Button></div> : access === false ? <div className="flex h-full min-h-48 items-center justify-center p-6 text-center"><div><p className="text-sm font-semibold">Akses khusus Divisi Operasional</p><p className="mt-1 text-xs text-muted-foreground">Halaman NPL ini berada di area review komponen report.</p></div></div> : <NplTable rows={sortedRows} total={total} view={view} sort={sort} onSort={onSort} tableRef={tableRef} />}</div>
      </section>
    </div>
    <ReportInfoDialog open={infoOpen} title="Ringkasan Kondisi NPL" description="Sorotan posisi saat ini dan prioritas tindak lanjut." onClose={() => setInfoOpen(false)}>
      <div className="space-y-3 text-sm">
        <p className="rounded-lg bg-muted p-3 text-muted-foreground">{view === "kolek" ? `Komposisi kolektibilitas ${areaLabel}: NPL saat ini ${formatNplPercent(total?.persentase_npl)}.` : `Perbandingan NPL ${areaLabel}: NPL actual ${formatNplPercent(total?.npl_harian_persen)}.`}</p>
        <div className="grid gap-2 sm:grid-cols-3">
          <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">% NPL Saat Ini</p><p className="mt-1 font-semibold tabular-nums">{formatNplPercent(view === "kolek" ? total?.persentase_npl : total?.npl_harian_persen)}</p></div>
          <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">Nominal NPL</p><p className="mt-1 font-semibold tabular-nums">Rp {formatNplMoney(view === "kolek" ? total?.bd_npl : total?.npl_harian)}</p></div>
          <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">{view === "kolek" ? "NOA NPL" : "Kenaikan NPL"}</p><p className="mt-1 font-semibold tabular-nums">{view === "kolek" ? `${nplNumber.format(Number(total?.noa_npl ?? 0))} NOA` : `Rp ${formatNplMoney(total?.selisih_npl)}`}</p></div>
        </div>
        <div className="space-y-2"><h3 className="text-sm font-semibold">{view === "kolek" ? "Cara Membaca Kolektibilitas" : "Cara Membaca Perbandingan NPL"}</h3><p className="text-xs leading-relaxed text-muted-foreground">{view === "kolek" ? "Pantau DPK dan KL sebelum menjadi NPL. Kelompok diragukan dan macet menjadi sumber utama nominal NPL; bandingkan total NPL dengan portofolio untuk menemukan rasio tinggi." : "Selisih positif berarti NPL naik; selisih negatif berarti NPL turun. Pastikan perubahan berasal dari recovery, pelunasan, atau perubahan kolektibilitas yang valid."}</p></div>
        <p className="text-xs text-muted-foreground">Keputusan tindak lanjut tetap mengikuti detail rekening, kondisi debitur, dan ketentuan internal.</p>
      </div>
    </ReportInfoDialog>
  </AppShell>
}
