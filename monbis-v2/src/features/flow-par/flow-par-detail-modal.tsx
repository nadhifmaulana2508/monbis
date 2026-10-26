import { useEffect, useMemo, useState } from "react"
import type { MouseEvent } from "react"
import { ChevronDown, ChevronUp, Download, LoaderCircle, PencilLine, X } from "lucide-react"
import { DetailSearchToggle, ReportDetailFilters } from "@/components/report-detail-filters"
import { ReportDetailLayer } from "@/components/report-detail-layer"
import { Button } from "@/components/ui/button"
import { exportFlowParDetail, flowParDate, flowParDetailPayload, flowParHasCommitment, flowParNumber, flowParValue, requestFlowPar, type FlowParDetailSelection, type FlowParFilters, type FlowParRow } from "./flow-par-data"

type Props = { selection: FlowParDetailSelection; filters: FlowParFilters; onClose: () => void }
type Kankas = { kode_group1?: string; deskripsi_group1?: string }
const fieldClass = "h-9 rounded-md border bg-background px-2 text-xs text-foreground"
const detailColumnWidths = [240, 66, 220, 120, 105, 105, 120, 100, 95, 58, 65, 65, 105, 105, 100, 120, 180, 120, 130]
const text = (value: unknown, fallback = "-") => String(value ?? "") || fallback
const money = (value: unknown) => flowParNumber.format(Number(value ?? 0))
const totalDue = (row: FlowParRow) => Number(row.total_tunggakan) > 0 ? Number(row.total_tunggakan) : flowParValue(row, "tunggakan_pokok") + flowParValue(row, "tunggakan_bunga")
const dailyCollectability = (row: FlowParRow) => String(row.kolektibilitas ?? row.kolek_harian ?? row.kolek ?? "-")
function dueLabel(value: unknown) {
  const text = String(value ?? "")
  if (!text) return "-"
  const due = new Date(text)
  if (Number.isNaN(due.getTime())) return "-"
  const today = new Date(); today.setHours(0, 0, 0, 0)
  due.setHours(0, 0, 0, 0)
  return due < today ? `${due.getFullYear()}-${String(due.getMonth() + 1).padStart(2, "0")}-${String(due.getDate()).padStart(2, "0")}` : String(due.getDate())
}
function daysOverdue(row: FlowParRow) { if (Number.isFinite(Number(row.hari_menunggak))) return Number(row.hari_menunggak); const due = new Date(String(row.tgl_jatuh_tempo ?? "")); return Number.isNaN(due.getTime()) ? 0 : Math.max(0, Math.floor((new Date().setHours(0, 0, 0, 0) - due.setHours(0, 0, 0, 0)) / 86400000)) }

export function FlowParDetailModal({ selection, filters, onClose }: Props) {
  const [rows, setRows] = useState<FlowParRow[]>([])
  const [kankas, setKankas] = useState<Kankas[]>([])
  const [kankasFilter, setKankasFilter] = useState(selection.kode.length > 3 ? selection.kode : "")
  const [commitment, setCommitment] = useState("all")
  const [search, setSearch] = useState("")
  const [summaryOpen, setSummaryOpen] = useState(false)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    const previous = document.body.style.overflow
    document.body.style.overflow = "hidden"
    const keydown = (event: KeyboardEvent) => { if (event.key === "Escape") onClose() }
    window.addEventListener("keydown", keydown)
    return () => { document.body.style.overflow = previous; window.removeEventListener("keydown", keydown) }
  }, [onClose])

  useEffect(() => {
    const controller = new AbortController()
    const office = selection.kode === "000" ? "" : selection.kode.slice(0, 3)
    if (!office) return () => controller.abort()
    void requestFlowPar<Kankas[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kankas", kode_kantor: office }), signal: controller.signal })
      .then((response) => { if (!controller.signal.aborted) setKankas(Array.isArray(response.data) ? response.data : []) })
      .catch(() => { if (!controller.signal.aborted) setKankas([]) })
    return () => controller.abort()
  }, [selection.kode])

  useEffect(() => {
    const controller = new AbortController()
    void requestFlowPar<FlowParRow[]>("flow_par", { method: "POST", body: JSON.stringify(flowParDetailPayload(selection, filters, kankasFilter)), signal: controller.signal })
      .then((response) => { if (!controller.signal.aborted) setRows(Array.isArray(response.data) ? response.data : []) })
      .catch((reason: unknown) => { if (!controller.signal.aborted) { setRows([]); setError(reason instanceof Error ? reason.message : "Detail Flow PAR gagal dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [selection, filters, kankasFilter])

  const filtered = useMemo(() => rows.filter((row) => {
    const hasCommitment = flowParHasCommitment(row)
    if (commitment === "committed" && !hasCommitment) return false
    if (commitment === "none" && hasCommitment) return false
    const query = search.trim().toLowerCase()
    return !query || [row.no_rekening, row.nama_nasabah, row.alamat, dailyCollectability(row), row.komitmen, row.tgl_pembayaran].some((value) => String(value ?? "").toLowerCase().includes(query))
  }), [rows, commitment, search])
  const sums = useMemo(() => filtered.reduce<{ nominal: number; pokok: number; bunga: number; tunggakan: number; saldo: number; angsuranPokok: number; angsuranBunga: number; promise: number; committed: number }>((sum, row) => ({
    nominal: sum.nominal + flowParValue(row, "baki_debet"), pokok: sum.pokok + flowParValue(row, "tunggakan_pokok"),
    bunga: sum.bunga + flowParValue(row, "tunggakan_bunga"), tunggakan: sum.tunggakan + totalDue(row), saldo: sum.saldo + flowParValue(row, "saldo_akhir"),
    angsuranPokok: sum.angsuranPokok + flowParValue(row, "angsuran_pokok"), angsuranBunga: sum.angsuranBunga + flowParValue(row, "angsuran_bunga"),
    promise: sum.promise + flowParValue(row, "nominal"), committed: sum.committed + Number(flowParHasCommitment(row)),
  }), { nominal: 0, pokok: 0, bunga: 0, tunggakan: 0, saldo: 0, angsuranPokok: 0, angsuranBunga: 0, promise: 0, committed: 0 }), [filtered])

  const goToUpdate = () => {
    sessionStorage.setItem("flowpar_update", JSON.stringify({ kode_kantor: selection.kode === "000" ? "" : selection.kode.slice(0, 3), kode_kankas: kankasFilter, korwil: selection.korwil, klasifikasi_flow: selection.klasifikasi, closing_date: filters.closingDate, harian_date: filters.actualDate }))
    window.location.assign("/update-komitmen-flow")
  }
  const exportDetail = () => exportFlowParDetail(filtered, filters)
  const changeKankas = (value: string) => { setLoading(true); setRows([]); setError(null); setKankasFilter(value) }
  const backdropMouseDown = (event: MouseEvent<HTMLDivElement>) => { if (event.target === event.currentTarget) onClose() }

  return <ReportDetailLayer className="flex items-end justify-center bg-foreground/35 p-0 sm:items-center sm:p-4">
    <div className="absolute inset-0" onMouseDown={backdropMouseDown} />
    <section role="dialog" aria-modal="true" aria-labelledby="flow-par-detail-title" className="relative flex max-h-[min(94dvh,920px)] w-full max-w-[1700px] flex-col overflow-hidden rounded-t-xl border bg-background text-foreground shadow-xl sm:rounded-xl" onMouseDown={(event) => event.stopPropagation()}>
      <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-3 py-2.5 sm:px-4"><div className="min-w-0"><h2 id="flow-par-detail-title" className="truncate text-sm font-semibold">Detail Debitur · {selection.label}</h2><p className="truncate text-[0.68rem] text-muted-foreground">Closing {flowParDate(filters.closingDate)} vs Actual {flowParDate(filters.actualDate)}{selection.klasifikasi ? ` · ${selection.label}` : ""}</p></div>
        <div className="flex shrink-0 items-center gap-1.5"><DetailSearchToggle value={search} onChange={setSearch} placeholder="Nama, rekening, alamat..." ariaLabel="Cari debitur" /><ReportDetailFilters><div className="grid gap-2">{selection.kode !== "000" && <label className="grid min-w-48 gap-1 text-xs text-muted-foreground">Kankas<select value={kankasFilter} onChange={(event) => changeKankas(event.target.value)} className={fieldClass}><option value="">Semua Kankas</option>{kankas.map((item) => <option key={item.kode_group1} value={item.kode_group1}>{item.kode_group1} - {item.deskripsi_group1 || "Kankas"}</option>)}</select></label>}<label className="grid min-w-48 gap-1 text-xs text-muted-foreground">Status Komitmen<select value={commitment} onChange={(event) => setCommitment(event.target.value)} className={fieldClass}><option value="all">Semua Status</option><option value="committed">Sudah Komitmen</option><option value="none">Belum Komitmen</option></select></label></div></ReportDetailFilters>
          <Button type="button" variant="signalGreen" size="icon-lg" className="size-9" aria-label="Export Excel detail" title="Export Excel detail" disabled={loading || filtered.length === 0} onClick={exportDetail}><Download className="size-4" aria-hidden="true" /></Button><Button type="button" variant="outline" size="icon-lg" className="size-9" aria-label="Update komitmen Flow PAR" title="Update komitmen Flow PAR" onClick={goToUpdate}><PencilLine className="size-4" aria-hidden="true" /></Button><Button type="button" variant="signalRed" size="icon-lg" className="size-9" aria-label="Tutup modal" onClick={onClose}><X className="size-4" aria-hidden="true" /></Button>
        </div>
      </header>
      <div className="shrink-0 border-b"><button type="button" aria-expanded={summaryOpen} aria-controls="flow-par-detail-summary" onClick={() => setSummaryOpen((open) => !open)} className="flex h-9 w-full items-center justify-between px-3 text-xs font-medium text-muted-foreground transition-colors hover:bg-muted/50 hover:text-foreground sm:px-4"><span>Ringkasan detail</span>{summaryOpen ? <ChevronUp className="size-4" aria-hidden="true" /> : <ChevronDown className="size-4" aria-hidden="true" />}</button><div id="flow-par-detail-summary" hidden={!summaryOpen} className="grid grid-cols-2 gap-1.5 px-2 pb-2 sm:grid-cols-4 sm:px-4"><Summary label="Kandidat Flow" value={`${flowParNumber.format(filtered.length)} Debitur`} /><Summary label="Sudah Komitmen" value={`${flowParNumber.format(sums.committed)} Debitur`} /><Summary label="Belum Komitmen" value={`${flowParNumber.format(filtered.length - sums.committed)} Debitur`} /><Summary label="Nominal Janji Bayar" value={`Rp ${flowParNumber.format(sums.promise)}`} /></div></div>
      <p className="shrink-0 border-b px-3 py-1.5 text-[0.65rem] text-muted-foreground sm:px-4">Gunakan tombol Update untuk mengisi komitmen. Ringkasan adalah proyeksi; hasil akhir mengikuti realisasi pembayaran dan posisi harian.</p>
      <div className="report-table-scroll min-h-0 flex-1 overflow-auto">{loading ? <div className="flex min-h-40 items-center justify-center gap-2 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" />Memuat detail debitur…</div> : error ? <p role="alert" className="p-8 text-center text-sm text-destructive">{error}</p> : <>
        <div className="hidden md:block"><table className="w-full min-w-[2219px] table-fixed border-separate border-spacing-0 text-xs tabular-nums"><colgroup>{detailColumnWidths.map((width, index) => <col key={index} style={{ width }} />)}</colgroup><thead><tr className="sticky top-0 z-40 h-11 bg-muted text-left text-[0.65rem] font-semibold leading-tight text-muted-foreground">{["Debitur / Rekening", "Kolek", "Alamat", filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank", "Tunggakan Pokok", "Tunggakan Bunga", "Total Tunggakan", "Saldo Tabungan", "Jatuh Tempo", "DPD", "DPD Pokok", "DPD Bunga", "Angsuran Pokok", "Angsuran Bunga", "Tgl Transaksi", "Status Komitmen", "Komitmen", "Tgl Janji Bayar", "Nominal Janji"].map((label, index) => <th key={label} className={`sticky top-0 border-b border-r bg-muted px-2 py-2 align-middle ${index === 0 ? "left-0 z-50" : "z-40"} ${[0, 2, 16].includes(index) ? "text-left" : "text-center"}`}>{label}</th>)}</tr></thead><tbody>
          <tr className="bg-muted/95 font-semibold"><td className="sticky left-0 top-11 z-50 whitespace-nowrap border-b border-r bg-muted px-2 py-2"><span className="block">TOTAL</span><span className="text-[0.65rem] font-normal text-muted-foreground">{flowParNumber.format(filtered.length)} Debitur</span></td><td className="sticky top-11 z-40 border-b border-r bg-muted px-2 py-2 text-center">-</td><td className="sticky top-11 z-40 border-b border-r bg-muted px-2 py-2"/><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-right">{money(sums.nominal)}</td><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-right">{money(sums.pokok)}</td><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-right">{money(sums.bunga)}</td><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-right">{money(sums.tunggakan)}</td><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-right">{money(sums.saldo)}</td><td colSpan={4} className="sticky top-11 z-40 border-b border-r bg-muted"/><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-right">{money(sums.angsuranPokok)}</td><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-right">{money(sums.angsuranBunga)}</td><td className="sticky top-11 z-40 border-b border-r bg-muted"/><td className="sticky top-11 z-40 whitespace-nowrap border-b border-r bg-muted px-2 py-2 text-center">{flowParNumber.format(sums.committed)} Komitmen</td><td className="sticky top-11 z-40 border-b border-r bg-muted"/><td className="sticky top-11 z-40 border-b border-r bg-muted"/><td className="sticky top-11 z-40 whitespace-nowrap border-b bg-muted px-2 py-2 text-right">{money(sums.promise)}</td></tr>
          {filtered.map((row, index) => <tr key={`${String(row.no_rekening)}-${index}`} className="group hover:bg-muted/30"><td className="sticky left-0 z-20 w-[240px] border-b border-r bg-background px-2 py-2 group-hover:bg-muted/50"><span className="block truncate font-medium" title={text(row.nama_nasabah)}>{text(row.nama_nasabah)}</span><span className="mt-0.5 block truncate font-mono text-[0.65rem] text-muted-foreground" title={text(row.no_rekening)}>{text(row.no_rekening)}</span></td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-center">{dailyCollectability(row)}</td><td title={text(row.alamat)} className="border-b border-r px-2 py-2"><span className="block truncate">{text(row.alamat)}</span></td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-right">{money(row.baki_debet)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-right">{money(row.tunggakan_pokok)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-right">{money(row.tunggakan_bunga)}</td><td className="whitespace-nowrap border-b border-r bg-destructive/5 px-2 py-2 text-right font-semibold">{money(totalDue(row))}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-right">{money(row.saldo_akhir)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-center">{dueLabel(row.tgl_jatuh_tempo)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-center">{money(daysOverdue(row))}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-center">{money(row.hari_menunggak_pokok)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-center">{money(row.hari_menunggak_bunga)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-right">{money(row.angsuran_pokok)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-right">{money(row.angsuran_bunga)}</td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-center">{flowParDate(row.tgl_trans)}</td><td className="border-b border-r px-2 py-2 text-center"><StatusBadge committed={flowParHasCommitment(row)} /></td><td title={text(row.komitmen)} className="border-b border-r px-2 py-2"><span className="block truncate">{text(row.komitmen)}</span></td><td className="whitespace-nowrap border-b border-r px-2 py-2 text-center">{flowParDate(row.tgl_pembayaran)}</td><td className="whitespace-nowrap border-b px-2 py-2 text-right">{money(row.nominal)}</td></tr>)}
          {!filtered.length && <tr><td colSpan={19} className="p-10 text-center text-sm text-muted-foreground">Data tidak ditemukan. Ubah pencarian atau filter komitmen.</td></tr>}
        </tbody></table></div>
        <div className="grid gap-2 p-2 md:hidden">{filtered.length ? filtered.map((row, index) => <article key={`${String(row.no_rekening)}-${index}`} className="rounded-lg border bg-card p-3"><div className="flex items-start justify-between gap-3"><div className="min-w-0"><p className="truncate font-mono text-[0.68rem] text-muted-foreground">{text(row.no_rekening)}</p><p className="truncate text-sm font-semibold">{text(row.nama_nasabah)}</p></div><div className="flex shrink-0 flex-col items-end gap-1"><StatusBadge committed={flowParHasCommitment(row)} /><span className="rounded border px-1.5 py-0.5 text-[0.65rem]">{dailyCollectability(row)}</span></div></div><p className="mt-1 line-clamp-2 text-xs text-muted-foreground" title={text(row.alamat)}>{text(row.alamat)}</p><div className="mt-2 grid grid-cols-2 gap-x-3 gap-y-2 border-t pt-2 text-xs">{[[filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank", `Rp ${money(row.baki_debet)}`], ["Tunggakan Pokok / Bunga", `${money(row.tunggakan_pokok)} / ${money(row.tunggakan_bunga)}`], ["Total Tunggakan", `Rp ${money(totalDue(row))}`], ["Saldo Tabungan", `Rp ${money(row.saldo_akhir)}`], ["DPD / TP / TB", `${money(row.hari_menunggak)} / ${money(row.hari_menunggak_pokok)} / ${money(row.hari_menunggak_bunga)}`], ["Jatuh Tempo", flowParDate(row.tgl_jatuh_tempo)], ["Angsuran Pokok / Bunga", `${money(row.angsuran_pokok)} / ${money(row.angsuran_bunga)}`], ["Tanggal Transaksi", flowParDate(row.tgl_trans)], ["Komitmen", text(row.komitmen)], ["Tanggal Janji", flowParDate(row.tgl_pembayaran)], ["Nominal Janji", `Rp ${money(row.nominal)}`]].map(([label, value]) => <div key={label} className="min-w-0"><p className="text-[0.62rem] text-muted-foreground">{label}</p><p className="truncate font-medium" title={value}>{value}</p></div>)}</div></article>) : <p className="p-8 text-center text-sm text-muted-foreground">Data tidak ditemukan. Ubah pencarian atau filter komitmen.</p>}</div>
      </>}</div>
    </section>
  </ReportDetailLayer>
}

function Summary({ label, value }: { label: string; value: string }) { return <div className="min-w-0 rounded-md bg-muted/40 px-2.5 py-1.5"><p className="truncate text-[0.63rem] text-muted-foreground">{label}</p><p className="truncate text-xs font-semibold tabular-nums">{value}</p></div> }
function StatusBadge({ committed }: { committed: boolean }) { return <span className={`rounded-md border px-1.5 py-0.5 text-[0.62rem] ${committed ? "border-signal-green/30 bg-signal-green/10 text-signal-green-foreground" : "border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-300"}`}>{committed ? "Sudah Komitmen" : "Belum Komitmen"}</span> }
