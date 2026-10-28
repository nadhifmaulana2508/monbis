import { useEffect, useMemo, useRef, useState } from "react"
import { CalendarDays, Check, ChevronDown, LoaderCircle, PencilLine, Search, TriangleAlert, X } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { AppToast } from "@/components/app/app-toast"
import { DetailSearchToggle } from "@/components/report-detail-filters"
import { ReportDetailLayer } from "@/components/report-detail-layer"
import { Button } from "@/components/ui/button"
import { Collapsible } from "@/components/ui/collapsible"
import { appRoutes } from "@/lib/routes"
import {
  normalizeUpdateFlowRows,
  requestUpdateFlow,
  updateFlowCommitmentOptions,
  updateFlowDate,
  updateFlowHasCommitment,
  updateFlowIsRisk,
  updateFlowMoney,
  updateFlowNumber,
  updateFlowPayload,
  type UpdateFlowRequest,
  type UpdateFlowRow,
} from "./update-komitmen-flow-data"

type Source = "flow_par" | "potensi_npl"
type CommitmentForm = { row: UpdateFlowRow; komitmen: string; tanggal: string; nominal: string; alasan: string }

function readStoredRequest(): UpdateFlowRequest | null {
  try {
    const stored = sessionStorage.getItem("flowpar_update")
    return stored ? JSON.parse(stored) as UpdateFlowRequest : null
  } catch { return null }
}

function commitmentSource(request: UpdateFlowRequest | null): Source {
  return request?.source === "potensi_npl" ? "potensi_npl" : "flow_par"
}

function rowIsLunasThisMonth(row: UpdateFlowRow, referenceDate?: string) {
  const due = row.tgl_jatuh_tempo ? new Date(row.tgl_jatuh_tempo) : null
  const reference = referenceDate ? new Date(referenceDate) : new Date()
  return Boolean(due && !Number.isNaN(due.getTime()) && due.getMonth() === reference.getMonth() && due.getFullYear() === reference.getFullYear())
}

function UpdateCommitmentDialog({ form, request, saving, error, onChange, onClose, onSave }: {
  form: CommitmentForm
  request: UpdateFlowRequest
  saving: boolean
  error: string | null
  onChange: (next: Partial<Omit<CommitmentForm, "row">>) => void
  onClose: () => void
  onSave: () => void
}) {
  const source = commitmentSource(request)
  const options = updateFlowCommitmentOptions(form.row, request.harian_date)
  const totalDue = form.row.tunggakan_pokok + form.row.tunggakan_bunga
  const dueNow = rowIsLunasThisMonth(form.row, request.harian_date)
  const previousOverflow = useRef("")
  const closeRef = useRef(onClose)
  const savingRef = useRef(saving)

  useEffect(() => { closeRef.current = onClose }, [onClose])
  useEffect(() => { savingRef.current = saving }, [saving])
  useEffect(() => {
    previousOverflow.current = document.body.style.overflow
    document.body.style.overflow = "hidden"
    const handleKey = (event: KeyboardEvent) => { if (event.key === "Escape" && !savingRef.current) closeRef.current() }
    window.addEventListener("keydown", handleKey)
    return () => { document.body.style.overflow = previousOverflow.current; window.removeEventListener("keydown", handleKey) }
  }, [])

  return <ReportDetailLayer className="flex items-end justify-center bg-foreground/35 p-0 sm:items-center sm:p-4">
    <button type="button" aria-label="Tutup form update komitmen" className="absolute inset-0 cursor-default" onClick={onClose} />
    <section role="dialog" aria-modal="true" aria-labelledby="update-commitment-title" className="relative flex max-h-[min(92dvh,740px)] w-full max-w-xl flex-col overflow-hidden rounded-t-xl border bg-background text-foreground shadow-xl sm:rounded-xl">
      <header className="flex shrink-0 items-start justify-between gap-3 border-b px-4 py-3 sm:px-5">
        <div className="min-w-0"><h2 id="update-commitment-title" className="text-base font-semibold">Update Komitmen</h2><p className="mt-0.5 text-xs text-muted-foreground">Catat target pembayaran agar proyeksi {source === "potensi_npl" ? "Potensi NPL" : "Flow PAR"} lebih terukur.</p></div>
        <Button type="button" variant="signalRed" size="icon-lg" className="size-9 shrink-0" aria-label="Tutup modal" onClick={onClose} disabled={saving}><X className="size-4" aria-hidden="true" /></Button>
      </header>
      <div className="min-h-0 space-y-4 overflow-y-auto p-4 sm:p-5">
        <section className="rounded-lg border bg-muted/20 p-3">
          <div className="flex items-start justify-between gap-3"><div className="min-w-0"><p className="text-[0.68rem] text-muted-foreground">No. rekening</p><p className="truncate font-mono text-xs font-medium">{form.row.no_rekening || "-"}</p></div><span className="shrink-0 rounded-md border px-2 py-1 text-[0.68rem]">{form.row.kolek_harian}</span></div>
          <div className="mt-2 flex items-start justify-between gap-3"><div className="min-w-0"><p className="truncate text-sm font-semibold">{form.row.nama_nasabah}</p><p className="line-clamp-2 text-xs text-muted-foreground" title={form.row.alamat}>{form.row.alamat}</p></div><strong className="shrink-0 text-right text-xs tabular-nums">Rp {updateFlowMoney(form.row.baki_debet)}</strong></div>
          <div className="mt-3 flex flex-wrap gap-x-4 gap-y-1 border-t pt-2 text-[0.68rem] text-muted-foreground"><span>Tunggakan pokok: <b className="text-foreground">Rp {updateFlowMoney(form.row.tunggakan_pokok)}</b></span><span>Tunggakan bunga: <b className="text-foreground">Rp {updateFlowMoney(form.row.tunggakan_bunga)}</b></span><span>DPD P/B: <b className="text-foreground">{updateFlowNumber.format(form.row.hari_menunggak_pokok)} / {updateFlowNumber.format(form.row.hari_menunggak_bunga)}</b></span></div>
        </section>
        <div className="grid gap-3 sm:grid-cols-2">
          <label className="grid gap-1.5 text-xs font-medium sm:col-span-2">Komitmen DPD
            <select value={form.komitmen} onChange={(event) => { onChange({ komitmen: event.target.value, ...(event.target.value === "O_Lunas" ? { nominal: String(totalDue) } : {}) }) }} className="h-10 w-full rounded-md border bg-background px-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring" disabled={saving}>
              {options.map(([value, label]) => <option key={value || "empty"} value={value}>{label}</option>)}
            </select>
            {dueNow && <span className="text-[0.68rem] font-normal text-muted-foreground">Jatuh tempo bulan ini: pilihan komitmen mengikuti aturan Flow PAR.</span>}
          </label>
          <label className="grid gap-1.5 text-xs font-medium">Tanggal bayar
            <span className="relative"><CalendarDays className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" aria-hidden="true" /><input type="date" value={form.tanggal} onChange={(event) => onChange({ tanggal: event.target.value })} className="h-10 w-full rounded-md border bg-background pl-9 pr-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring" disabled={saving} /></span>
          </label>
          <label className="grid gap-1.5 text-xs font-medium">Nominal janji
            <span className="relative"><span className="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground">Rp</span><input type="number" min="0" step="1" inputMode="numeric" value={form.nominal} onChange={(event) => onChange({ nominal: event.target.value })} placeholder="0" className="h-10 w-full rounded-md border bg-background pl-9 pr-3 text-sm tabular-nums outline-none focus-visible:ring-2 focus-visible:ring-ring" disabled={saving} /></span>
          </label>
          <label className="grid gap-1.5 text-xs font-medium sm:col-span-2">Alasan keterlambatan
            <textarea rows={3} value={form.alasan} onChange={(event) => onChange({ alasan: event.target.value })} placeholder="Tuliskan alasan keterlambatan atau rencana tindak lanjut..." className="min-h-20 resize-y rounded-md border bg-background px-3 py-2 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring" disabled={saving} />
          </label>
        </div>
        {error && <p role="alert" className="rounded-md border border-destructive/30 bg-destructive/5 px-3 py-2 text-xs text-destructive">{error}</p>}
      </div>
      <footer className="flex shrink-0 justify-end gap-2 border-t px-4 py-3 sm:px-5">
        <Button type="button" variant="outline" onClick={onClose} disabled={saving}>Batal</Button>
        <Button type="button" onClick={onSave} disabled={saving || !form.komitmen || !form.tanggal}>{saving ? <LoaderCircle className="size-4 animate-spin" aria-hidden="true" /> : <Check className="size-4" aria-hidden="true" />} {saving ? "Menyimpan..." : "Simpan Komitmen"}</Button>
      </footer>
    </section>
  </ReportDetailLayer>
}

export function UpdateKomitmenFlowPage() {
  const [request] = useState<UpdateFlowRequest | null>(readStoredRequest)
  const source = commitmentSource(request)
  const [access] = useState<boolean>(() => Boolean(request))
  const [rows, setRows] = useState<UpdateFlowRow[]>([])
  const [loading, setLoading] = useState(Boolean(request))
  const [error, setError] = useState<string | null>(() => request ? null : "Parameter halaman tidak ditemukan. Buka kembali halaman ini dari detail Flow PAR atau Potensi NPL.")
  const [search, setSearch] = useState("")
  const [commitmentFilter, setCommitmentFilter] = useState("ALL")
  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(() => window.innerWidth <= 767 ? 5 : 20)
  const [form, setForm] = useState<CommitmentForm | null>(null)
  const [saving, setSaving] = useState(false)
  const [saveError, setSaveError] = useState<string | null>(null)
  const [toast, setToast] = useState<{ message: string; variant: "success" | "error" } | null>(null)
  const [reloadKey, setReloadKey] = useState(0)
  const saveAbort = useRef<AbortController | null>(null)

  useEffect(() => {
    if (!request || access !== true) return undefined
    const controller = new AbortController()
    void requestUpdateFlow<Record<string, unknown>[] | { data?: Record<string, unknown>[] }>(source === "potensi_npl" ? "npl" : "flow_par", {
      method: "POST", body: JSON.stringify(updateFlowPayload(request, source)), signal: controller.signal,
    }).then((result) => {
      if (controller.signal.aborted) return
      const payload = result.data
      const data = Array.isArray(payload) ? payload : Array.isArray(payload?.data) ? payload.data : []
      setRows(normalizeUpdateFlowRows(data, source, request.status_filter || "ALL"))
    }).catch((reason: unknown) => {
      if (!controller.signal.aborted) setError(reason instanceof Error ? reason.message : "Gagal mengambil data kandidat komitmen.")
    }).finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [request, source, access, reloadKey])

  useEffect(() => () => saveAbort.current?.abort(), [])

  const filteredRows = useMemo(() => {
    const query = search.trim().toLowerCase()
    return rows.filter((row) => {
      const searchable = `${row.no_rekening} ${row.nama_nasabah} ${row.alamat} ${row.komitmen} ${row.alasan}`.toLowerCase()
      const hasCommitment = updateFlowHasCommitment(row)
      return (!query || searchable.includes(query)) && (commitmentFilter === "ALL" || (commitmentFilter === "SUDAH" ? hasCommitment : !hasCommitment))
    })
  }, [rows, search, commitmentFilter])
  const pageCount = Math.max(1, Math.ceil(filteredRows.length / pageSize))
  const safePage = Math.min(page, pageCount)
  const visibleRows = filteredRows.slice((safePage - 1) * pageSize, safePage * pageSize)
  const totals = useMemo(() => filteredRows.reduce((sum, row) => ({
    baki: sum.baki + row.baki_debet,
    pokok: sum.pokok + row.tunggakan_pokok,
    bunga: sum.bunga + row.tunggakan_bunga,
    nominal: sum.nominal + row.nominal,
  }), { baki: 0, pokok: 0, bunga: 0, nominal: 0 }), [filteredRows])
  const summary = useMemo(() => ({
    total: filteredRows.length,
    sudah: filteredRows.filter(updateFlowHasCommitment).length,
    nominal: totals.nominal,
  }), [filteredRows, totals.nominal])
  const heading = source === "potensi_npl" ? "Update Komitmen Potensi NPL" : "Update Komitmen Flow PAR"
  const scope = request ? [source === "potensi_npl" ? "Potensi NPL" : "Flow PAR", request.korwil ? `Korwil ${request.korwil}` : "", request.kode_kantor ? `Kantor ${request.kode_kantor}` : "Konsolidasi", request.kode_kankas ? `Kankas ${request.kode_kankas}` : "", request.kode_ao ? `AO ${request.kode_ao}` : "", request.klasifikasi_flow ? `Klasifikasi ${request.klasifikasi_flow}` : "", request.harian_date ? `Posisi ${updateFlowDate(request.harian_date)}` : ""].filter(Boolean).join(" · ") : ""

  const openForm = (row: UpdateFlowRow) => { setSaveError(null); setForm({ row, komitmen: row.komitmen, tanggal: row.tgl_pembayaran, nominal: row.nominal ? String(row.nominal) : "", alasan: row.alasan }) }
  const saveCommitment = async () => {
    if (!form || !request || saving) return
    if (!form.komitmen) { const message = "Pilih komitmen terlebih dahulu."; setSaveError(message); setToast({ message, variant: "error" }); return }
    if (!form.tanggal) { const message = "Tanggal pembayaran wajib diisi."; setSaveError(message); setToast({ message, variant: "error" }); return }
    if (Number(form.nominal || 0) < 0) { const message = "Nominal janji tidak valid."; setSaveError(message); setToast({ message, variant: "error" }); return }
    const controller = new AbortController()
    saveAbort.current = controller
    setSaving(true); setSaveError(null); setToast(null)
    try {
      const result = await requestUpdateFlow<unknown>("flow_par", {
        method: "POST",
        body: JSON.stringify({ type: "Update KL Baru", rekening: form.row.no_rekening, komitmen: form.komitmen, tgl_pembayaran: form.tanggal, nominal: form.nominal, alasan: form.alasan.trim() }),
        signal: controller.signal,
      })
      setForm(null)
      setToast({ message: result.message || "Komitmen berhasil disimpan.", variant: "success" })
      setLoading(true); setError(null); setRows([])
      setReloadKey((current) => current + 1)
    } catch (reason) {
      if (!controller.signal.aborted) {
        const message = reason instanceof Error ? reason.message : "Gagal menyimpan komitmen."
        setSaveError(message)
        setToast({ message, variant: "error" })
      }
    } finally { if (!controller.signal.aborted) setSaving(false) }
  }

  const navbarFilters = <div className="flex min-w-0 items-center gap-2">
    <label htmlFor="update-commitment-filter" className="shrink-0 text-xs font-medium text-muted-foreground">Status komitmen</label>
    <select id="update-commitment-filter" value={commitmentFilter} onChange={(event) => { setCommitmentFilter(event.target.value); setPage(1) }} aria-label="Filter komitmen" className="h-9 min-w-0 w-full max-w-64 rounded-md border bg-background px-2 text-xs sm:w-56">
      <option value="ALL">Semua Komitmen</option><option value="SUDAH">Sudah Komitmen</option><option value="BELUM">Belum Komitmen</option>
    </select>
  </div>

  const content = <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1800px] flex-col gap-3 px-3 py-3 sm:gap-4 sm:px-6 sm:py-5 lg:px-8">
    <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
      <header className="flex shrink-0 items-center justify-between gap-3 border-b px-3 py-3 sm:px-5">
        <div className="min-w-0"><div className="flex items-center gap-2"><h1 className="truncate text-sm font-semibold sm:text-base">{heading}</h1><span className="shrink-0 rounded-md bg-muted px-2 py-1 text-[0.68rem] text-muted-foreground">{updateFlowNumber.format(filteredRows.length)} data</span></div><p className="mt-1 truncate text-[0.68rem] text-muted-foreground">{scope}</p></div>
        <DetailSearchToggle value={search} onChange={(value) => { setSearch(value); setPage(1) }} placeholder="Cari nama / rekening..." ariaLabel="Cari nama atau rekening" />
      </header>
      <Collapsible.Root defaultOpen={false} className="shrink-0 border-b px-3 py-1.5 sm:px-4">
        <Collapsible.Trigger render={<Button type="button" variant="ghost" size="sm" className="group h-8 px-2 text-muted-foreground" />}>
          <span>Ringkasan</span><ChevronDown className="size-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" />
        </Collapsible.Trigger>
        <Collapsible.Panel className="grid grid-cols-2 gap-2 pb-2 sm:grid-cols-4">
        {[{ label: "Total debitur", value: updateFlowNumber.format(summary.total) }, { label: "Sudah komitmen", value: updateFlowNumber.format(summary.sudah) }, { label: "Belum komitmen", value: updateFlowNumber.format(summary.total - summary.sudah) }, { label: "Nominal janji", value: `Rp ${updateFlowMoney(summary.nominal)}` }].map((item) => <div key={item.label} className="min-w-0 rounded-md bg-muted/40 px-2.5 py-2"><p className="truncate text-[0.65rem] text-muted-foreground">{item.label}</p><p className="truncate text-xs font-semibold tabular-nums sm:text-sm">{item.value}</p></div>)}
        </Collapsible.Panel>
      </Collapsible.Root>
      <div className="report-table-scroll relative min-h-0 flex-1 overflow-auto">
        {loading ? <div className="flex min-h-48 items-center justify-center gap-2 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" />Memuat kandidat komitmen...</div>
          : error ? <div className="flex min-h-48 flex-col items-center justify-center gap-2 px-4 text-center"><TriangleAlert className="size-5 text-destructive" aria-hidden="true" /><p className="max-w-xl text-sm text-destructive">{error}</p>{access === true && request && <Button variant="outline" size="sm" onClick={() => setReloadKey((current) => current + 1)}>Coba lagi</Button>}</div>
            : access === false ? <div className="flex min-h-48 items-center justify-center px-4 text-center text-sm text-muted-foreground">Akses update komitmen tidak diberikan untuk sumber ini.</div>
              : filteredRows.length === 0 ? <div className="flex min-h-48 flex-col items-center justify-center gap-1 px-4 text-center"><Search className="size-5 text-muted-foreground" aria-hidden="true" /><p className="text-sm font-medium">Data tidak ditemukan</p><p className="text-xs text-muted-foreground">Coba ubah pencarian atau filter komitmen.</p></div>
                : <>
                  <div className="hidden md:block"><table className="update-commitment-table min-w-[1320px] w-full border-separate border-spacing-0 text-xs"><thead className="text-left text-[0.68rem] font-semibold text-muted-foreground"><tr>{["Debitur / Alamat", "Kolek", "Baki Debet", "Tungg. Pokok", "Tungg. Bunga", "DPD P", "DPD B", "Jatuh Tempo", "Komitmen", "Tgl Bayar", "Nominal Janji", "Alasan", "Aksi"].map((label, index) => <th key={label} className={`border-b border-r px-2.5 py-2 ${index >= 2 && index <= 4 || index === 10 ? "text-right" : index === 1 || (index >= 5 && index <= 7) || index === 9 || index === 12 ? "text-center" : ""}`}>{label}</th>)}</tr></thead>
                    <tbody><tr className="update-commitment-total font-semibold"><td className="border-b border-r px-2.5 py-2">TOTAL ({updateFlowNumber.format(filteredRows.length)})</td><td className="border-b border-r px-2.5 py-2 text-center">-</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(totals.baki)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(totals.pokok)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(totals.bunga)}</td><td colSpan={5} className="border-b border-r"/><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(totals.nominal)}</td><td colSpan={2} className="border-b"/></tr>
                      {visibleRows.map((row) => <tr key={row.no_rekening} className={updateFlowIsRisk(row, request?.harian_date) ? "bg-destructive/[0.035] hover:bg-destructive/[0.07]" : "hover:bg-muted/35"}><td className="max-w-64 border-b border-r px-2.5 py-2"><p className="truncate font-medium" title={row.nama_nasabah}>{row.nama_nasabah}</p><p className="truncate text-[0.68rem] text-muted-foreground" title={`${row.no_rekening} · ${row.alamat}`}>{row.no_rekening} · {row.alamat}</p></td><td className="border-b border-r px-2.5 py-2 text-center">{row.kolek_harian}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(row.baki_debet)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(row.tunggakan_pokok)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(row.tunggakan_bunga)}</td><td className={`border-b border-r px-2.5 py-2 text-center tabular-nums ${row.hari_menunggak_pokok >= 90 ? "font-semibold text-destructive" : ""}`}>{updateFlowNumber.format(row.hari_menunggak_pokok)}</td><td className={`border-b border-r px-2.5 py-2 text-center tabular-nums ${row.hari_menunggak_bunga >= 90 ? "font-semibold text-destructive" : ""}`}>{updateFlowNumber.format(row.hari_menunggak_bunga)}</td><td className="border-b border-r px-2.5 py-2 text-center">{updateFlowDate(row.tgl_jatuh_tempo)}</td><td className="border-b border-r px-2.5 py-2"><span className="rounded-md border px-1.5 py-1 text-[0.65rem]">{updateFlowHasCommitment(row) ? row.komitmen || "Sudah" : "Belum"}</span></td><td className="border-b border-r px-2.5 py-2 text-center">{updateFlowDate(row.tgl_pembayaran)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{updateFlowMoney(row.nominal)}</td><td className="max-w-48 border-b border-r px-2.5 py-2"><p className="max-w-48 truncate" title={row.alasan}>{row.alasan || "-"}</p></td><td className="border-b px-2 py-1.5 text-center"><Button type="button" variant="outline" size="icon-lg" className="size-9" aria-label={`Update komitmen ${row.nama_nasabah}`} title="Update komitmen" onClick={() => openForm(row)}><PencilLine className="size-4" aria-hidden="true" /></Button></td></tr>)}
                    </tbody></table></div>
                  <div className="grid gap-2 p-2 md:hidden">{visibleRows.map((row) => <article key={row.no_rekening} className={`overflow-hidden rounded-lg border bg-card ${updateFlowIsRisk(row, request?.harian_date) ? "border-destructive/30" : ""}`}><header className="flex items-start justify-between gap-2 border-b px-3 py-2"><div className="min-w-0"><p className="truncate text-sm font-semibold">{row.nama_nasabah}</p><p className="truncate font-mono text-[0.66rem] text-muted-foreground">{row.no_rekening} · {row.alamat}</p></div><Button type="button" variant="outline" size="icon-lg" className="size-9 shrink-0" aria-label={`Update komitmen ${row.nama_nasabah}`} title="Update komitmen" onClick={() => openForm(row)}><PencilLine className="size-4" aria-hidden="true" /></Button></header><div className="grid grid-cols-2 gap-px bg-border text-xs">{[["Kolek", row.kolek_harian], ["Baki debet", `Rp ${updateFlowMoney(row.baki_debet)}`], ["Tunggakan pokok", `Rp ${updateFlowMoney(row.tunggakan_pokok)}`], ["Tunggakan bunga", `Rp ${updateFlowMoney(row.tunggakan_bunga)}`], ["DPD pokok / bunga", `${updateFlowNumber.format(row.hari_menunggak_pokok)} / ${updateFlowNumber.format(row.hari_menunggak_bunga)}`], ["Jatuh tempo", updateFlowDate(row.tgl_jatuh_tempo)]].map(([label, value]) => <div key={label} className="min-w-0 bg-card px-2.5 py-1.5"><p className="text-[0.63rem] text-muted-foreground">{label}</p><p className="truncate font-medium tabular-nums">{value}</p></div>)}</div><footer className="grid grid-cols-2 gap-x-3 gap-y-1 px-3 py-2 text-[0.68rem]"><span className="text-muted-foreground">Komitmen</span><span className="truncate text-right font-medium">{updateFlowHasCommitment(row) ? row.komitmen || "Sudah" : "Belum"}</span><span className="text-muted-foreground">Tgl bayar</span><span className="text-right">{updateFlowDate(row.tgl_pembayaran)}</span><span className="text-muted-foreground">Nominal janji</span><span className="text-right font-medium tabular-nums">Rp {updateFlowMoney(row.nominal)}</span>{row.alasan && <><span className="text-muted-foreground">Alasan</span><span className="line-clamp-2 text-right" title={row.alasan}>{row.alasan}</span></>}</footer></article>)}</div>
                </>}
      </div>
      {!loading && !error && access === true && filteredRows.length > 0 && <footer className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-t px-3 py-2 text-xs sm:px-4"><span className="text-muted-foreground">Menampilkan {updateFlowNumber.format((safePage - 1) * pageSize + 1)}–{updateFlowNumber.format(Math.min(safePage * pageSize, filteredRows.length))} dari {updateFlowNumber.format(filteredRows.length)} data</span><div className="flex items-center gap-2"><label className="flex items-center gap-1.5 text-muted-foreground">Baris<select value={pageSize} onChange={(event) => { setPageSize(Number(event.target.value)); setPage(1) }} className="h-8 rounded-md border bg-background px-2 text-foreground" aria-label="Jumlah data per halaman">{[5, 10, 20, 50].map((size) => <option key={size} value={size}>{size}</option>)}</select></label><Button type="button" size="sm" variant="outline" disabled={safePage <= 1} onClick={() => setPage((current) => Math.max(1, current - 1))}>Sebelumnya</Button><span className="tabular-nums">{safePage}/{pageCount}</span><Button type="button" size="sm" variant="outline" disabled={safePage >= pageCount} onClick={() => setPage((current) => Math.min(pageCount, current + 1))}>Berikutnya</Button></div></footer>}
    </section>
    <AppToast open={Boolean(toast)} message={toast?.message || ""} variant={toast?.variant || "success"} onDismiss={() => setToast(null)} />
    {form && request && <UpdateCommitmentDialog form={form} request={request} saving={saving} error={saveError} onChange={(next) => setForm((current) => current ? { ...current, ...next } : current)} onClose={() => { if (!saving) { setForm(null); setSaveError(null) } }} onSave={() => void saveCommitment()} />}
  </div>

  return <AppShell title={heading} description="Perbarui komitmen pembayaran Flow PAR atau Potensi NPL." activeHref={appRoutes.updateKomitmenFlow} legacyHref="/update_flowpar" navbarContent={navbarFilters} showPageTitle={false}>{content}</AppShell>
}
