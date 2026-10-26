import { useEffect, useMemo } from "react"
import { ChevronDown, Download, FilePenLine } from "lucide-react"

import { DetailSearchToggle, ReportDetailFilters } from "@/components/report-detail-filters"
import { ReportDetailLayer } from "@/components/report-detail-layer"
import { ReportModalCloseButton } from "@/components/report-modal-close-button"
import { appRoutes } from "@/lib/routes"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Collapsible } from "@/components/ui/collapsible"
import type { PotensiDetailFilters, PotensiFilters, PotensiOffice, PotensiRow } from "./potensi-npl-data"
import { exportPotensiDetail, potensiCommitment, potensiDisplayStatus, potensiHasCommitment, potensiMoney, potensiNumber, potensiStatus } from "./potensi-npl-data"

type Props = { office: string; kankas?: string; label: string; filters: PotensiFilters; rows: PotensiRow[]; loading: boolean; error: string | null; detailFilters: PotensiDetailFilters; kankasOptions: PotensiOffice[]; aoOptions: Array<{ kode_group2?: string; nama_ao?: string }>; onFiltersChange: (next: Partial<PotensiDetailFilters>) => void; onClose: () => void; onRetry: () => void }
const dateLabel = (value: unknown) => { const raw = String(value ?? "").slice(0, 10); const match = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/); return match ? `${match[3]}/${match[2]}/${match[1]}` : raw || "-" }

export function PotensiNplDetailModal({ office, kankas, label, filters, rows, loading, error, detailFilters, kankasOptions, aoOptions, onFiltersChange, onClose, onRetry }: Props) {
  const visibleRows = useMemo(() => rows.filter((row) => {
    const kind = potensiStatus(row)
    const commitment = potensiHasCommitment(row)
    const query = detailFilters.search.trim().toLowerCase()
    const haystack = `${row.no_rekening ?? ""} ${row.nama_nasabah ?? ""} ${row.alamat ?? ""} ${row.nama_ao ?? ""} ${potensiDisplayStatus(row)} ${kind} ${potensiCommitment(row).komitmen} ${potensiCommitment(row).alasan}`.toLowerCase()
    return (detailFilters.status === "ALL" || kind === detailFilters.status) && (detailFilters.commitment === "ALL" || (detailFilters.commitment === "SUDAH" ? commitment : !commitment)) && (!query || haystack.includes(query))
  }), [rows, detailFilters])
  const totals = useMemo(() => visibleRows.reduce<{ closing: number; actual: number; arrears: number; savings: number; promise: number }>((sum, row) => {
    const commitment = potensiCommitment(row)
    sum.closing += Number(row.baki_debet_closing ?? 0); sum.actual += Number(row.baki_debet_harian ?? 0)
    sum.arrears += Number(row.tunggakan_pokok ?? 0) + Number(row.tunggakan_bunga ?? 0)
    sum.savings += Number(row.saldo_akhir ?? 0); sum.promise += commitment.nominal
    return sum
  }, { closing: 0, actual: 0, arrears: 0, savings: 0, promise: 0 }), [visibleRows])

  useEffect(() => {
    const overflow = document.body.style.overflow
    const keydown = (event: KeyboardEvent) => { if (event.key === "Escape") onClose() }
    document.body.style.overflow = "hidden"
    window.addEventListener("keydown", keydown)
    return () => { document.body.style.overflow = overflow; window.removeEventListener("keydown", keydown) }
  }, [onClose])

  const updateLegacy = () => {
    const payload = { source: "potensi_npl", kode_kantor: office === "000" ? "" : office, kode_kankas: detailFilters.kankas || kankas || "", kode_ao: detailFilters.ao, status_filter: detailFilters.status, commitment_filter: detailFilters.commitment, closing_date: filters.closingDate, harian_date: filters.actualDate }
    sessionStorage.setItem("flowpar_update", JSON.stringify(payload))
    sessionStorage.removeItem("potensinpl_update")
    window.location.assign(appRoutes.updateKomitmenFlow)
  }
  const statusClass = (row: PotensiRow) => potensiStatus(row) === "AMAN" ? "border-signal-green/30 bg-signal-green/10 text-signal-green-foreground" : potensiStatus(row) === "FLOW KOLEK" ? "border-destructive/30 bg-destructive/10 text-destructive" : potensiStatus(row) === "JATUH TEMPO" ? "border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-300" : "border-orange-500/30 bg-orange-500/10 text-orange-700 dark:text-orange-300"
  const filtersToolbar = <div className={`grid min-w-0 gap-2 ${office === "000" ? "grid-cols-2" : "grid-cols-4"}`}>
    <label className="grid min-w-0 gap-1 text-xs text-muted-foreground"><span className="truncate whitespace-nowrap">Status</span><select value={detailFilters.status} onChange={(event) => onFiltersChange({ status: event.target.value })} className="h-9 w-full min-w-0 truncate rounded-md border bg-background px-2 text-foreground"><option value="ALL">Semua Status Potensi</option><option value="AMAN">Aman / Lunas / Backflow</option><option value="JATUH TEMPO">Jatuh Tempo</option><option value="FLOW KOLEK">Flow Kolek</option><option value="MASIH POTENSI">Masih Potensi</option></select></label>
    <label className="grid min-w-0 gap-1 text-xs text-muted-foreground"><span className="truncate whitespace-nowrap">Komitmen</span><select value={detailFilters.commitment} onChange={(event) => onFiltersChange({ commitment: event.target.value })} className="h-9 w-full min-w-0 truncate rounded-md border bg-background px-2 text-foreground"><option value="ALL">Semua Komitmen</option><option value="SUDAH">Sudah Komitmen</option><option value="BELUM">Belum Komitmen</option></select></label>
    {office !== "000" && <label className="grid min-w-0 gap-1 text-xs text-muted-foreground"><span className="truncate whitespace-nowrap">Kankas</span><select value={detailFilters.kankas || kankas || ""} onChange={(event) => onFiltersChange({ kankas: event.target.value })} className="h-9 w-full min-w-0 truncate rounded-md border bg-background px-2 text-foreground"><option value="">Semua Kankas</option>{kankasOptions.map((option) => <option key={option.kode_group1} value={option.kode_group1}>{option.deskripsi_group1} ({option.kode_group1})</option>)}</select></label>}
    {office !== "000" && <label className="grid min-w-0 gap-1 text-xs text-muted-foreground"><span className="truncate whitespace-nowrap">AO</span><select value={detailFilters.ao} onChange={(event) => onFiltersChange({ ao: event.target.value })} className="h-9 w-full min-w-0 truncate rounded-md border bg-background px-2 text-foreground"><option value="">Semua AO</option>{aoOptions.map((option) => <option key={option.kode_group2} value={option.kode_group2}>{option.nama_ao || option.kode_group2}</option>)}</select></label>}
  </div>

  return <ReportDetailLayer className="flex items-end justify-center bg-foreground/35 p-0 sm:items-center sm:p-4">
    <button type="button" aria-label="Tutup detail Potensi NPL" className="absolute inset-0 cursor-default" onClick={onClose} />
    <section role="dialog" aria-modal="true" aria-labelledby="potensi-detail-title" className="relative flex h-[min(92vh,760px)] w-full max-w-6xl flex-col overflow-hidden rounded-t-xl border bg-background text-foreground shadow-xl sm:rounded-xl">
      <header className="flex shrink-0 flex-nowrap items-center justify-between gap-2 border-b px-3 py-2.5 sm:gap-3 sm:px-6">
        <div className="min-w-0 flex-1"><p className="text-xs text-muted-foreground">Closing {dateLabel(filters.closingDate)} · Actual {dateLabel(filters.actualDate)}</p><h2 id="potensi-detail-title" className="truncate text-base font-semibold tracking-tight">Detail {label} · {office}{kankas ? ` / Kankas ${kankas}` : ""}</h2><p className="mt-0.5 text-[0.68rem] text-muted-foreground">{visibleRows.length.toLocaleString("id-ID")}{detailFilters.search.trim() ? ` / ${rows.length.toLocaleString("id-ID")}` : ""} rekening</p></div>
        <div className="flex shrink-0 items-center gap-1.5 sm:gap-2">
          <DetailSearchToggle value={detailFilters.search} onChange={(search) => onFiltersChange({ search })} />
          <ReportDetailFilters panelClassName={office === "000" ? "" : "w-[min(40rem,calc(100vw-2rem))]"}>{filtersToolbar}</ReportDetailFilters>
          <Button type="button" variant="outline" size="sm" className="h-9 gap-1.5 px-2.5" onClick={updateLegacy} aria-label="Update komitmen" title="Update komitmen"><FilePenLine className="size-4" aria-hidden="true" /><span>Update</span></Button>
          <Button type="button" variant="signalGreen" size="icon-lg" className="size-9" disabled={!visibleRows.length} onClick={() => exportPotensiDetail(visibleRows, filters)} aria-label="Export detail ke Excel" title="Export Excel"><Download className="size-4" aria-hidden="true" /></Button>
          <ReportModalCloseButton label="Tutup detail" onClick={onClose} />
        </div>
      </header>
      <div className="report-table-scroll relative min-h-0 flex-1 overflow-auto p-3 sm:p-5">
        <Collapsible.Root defaultOpen={false} className="mb-3">
          <Collapsible.Trigger render={<Button type="button" variant="ghost" size="sm" className="group h-8 px-2 text-muted-foreground" />}>
            Ringkasan <ChevronDown className="size-4 transition-transform group-aria-expanded:rotate-180" aria-hidden="true" />
          </Collapsible.Trigger>
          <Collapsible.Panel className="mt-2 flex gap-2 overflow-x-auto pb-1">
            <Card size="sm" className="min-w-32 flex-1"><CardContent><p className="text-xs text-muted-foreground">Aman / Lunas</p><p className="mt-1 font-semibold tabular-nums">{visibleRows.filter((row) => potensiStatus(row) === "AMAN").length.toLocaleString("id-ID")}</p></CardContent></Card>
            <Card size="sm" className="min-w-32 flex-1"><CardContent><p className="text-xs text-muted-foreground">Sudah komitmen</p><p className="mt-1 font-semibold tabular-nums">{visibleRows.filter(potensiHasCommitment).length.toLocaleString("id-ID")}</p></CardContent></Card>
            <Card size="sm" className="min-w-32 flex-1"><CardContent><p className="text-xs text-muted-foreground">Belum komitmen</p><p className="mt-1 font-semibold tabular-nums">{visibleRows.filter((row) => !potensiHasCommitment(row)).length.toLocaleString("id-ID")}</p></CardContent></Card>
            <Card size="sm" className="min-w-32 flex-1"><CardContent><p className="text-xs text-muted-foreground">Nominal janji</p><p className="mt-1 truncate font-semibold tabular-nums">Rp {potensiNumber.format(Math.round(totals.promise))}</p></CardContent></Card>
          </Collapsible.Panel>
        </Collapsible.Root>
        {loading ? <div className="flex min-h-48 items-center justify-center text-sm text-muted-foreground">Mengambil detail debitur…</div> : error ? <div className="flex min-h-48 flex-col items-center justify-center gap-2 text-center"><p className="text-sm font-medium text-destructive">Detail belum dapat dimuat.</p><p className="max-w-lg text-xs text-muted-foreground">{error}</p><Button variant="outline" size="sm" onClick={onRetry}>Coba lagi</Button></div> : !visibleRows.length ? <div className="flex min-h-48 flex-col items-center justify-center text-center"><p className="text-sm font-medium">Data tidak ditemukan</p><p className="mt-1 text-xs text-muted-foreground">Coba ubah pencarian atau filter.</p></div> : <>
          <div className="hidden overflow-auto rounded-lg border md:block"><table className="min-w-[1500px] w-full border-separate border-spacing-0 text-xs"><thead className="sticky top-0 z-10 bg-muted text-left text-[0.68rem] font-semibold text-muted-foreground"><tr>{["No Rekening", "Debitur / Alamat / AO", "Status Potensi", "Kolek C → H", "Nominal Closing", "Nominal Actual", "Tunggakan P / B", "Saldo Tab", "JT", "DPD / P / B", "Angsuran P / B", "Status Komitmen", "Komitmen", "Tgl Janji", "Nominal Janji", "Alasan"].map((head) => <th key={head} className={`border-b border-r px-2.5 py-2 ${["Nominal Closing", "Nominal Actual", "Tunggakan P / B", "Saldo Tab", "Angsuran P / B", "Nominal Janji"].includes(head) ? "text-right" : ""}`}>{head}</th>)}</tr></thead>
            <tbody><tr className="sticky top-9 z-[8] bg-muted/90 font-semibold"><td className="border-b border-r px-2.5 py-2">TOTAL</td><td className="border-b border-r px-2.5 py-2">{visibleRows.length.toLocaleString("id-ID")} Debitur</td><td colSpan={2} className="border-b border-r"/><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(totals.closing)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(totals.actual)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(totals.arrears)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(totals.savings)}</td><td colSpan={6} className="border-b border-r"/><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(totals.promise)}</td><td className="border-b"/></tr>
              {visibleRows.map((row, index) => { const commitment = potensiCommitment(row); const address = String(row.alamat ?? "-"); return <tr key={`${String(row.no_rekening)}-${index}`} className="hover:bg-muted/30"><td className="border-b border-r px-2.5 py-2 font-mono text-muted-foreground">{String(row.no_rekening ?? "-")}</td><td className="w-40 max-w-40 border-b border-r px-2.5 py-2"><div className="truncate font-medium">{String(row.nama_nasabah ?? "-")}</div><div title={address} className="max-w-40 cursor-help truncate text-[0.68rem] text-muted-foreground">{address}</div><div className="truncate text-[0.68rem] text-primary">{String(row.nama_ao ?? "-")}</div></td><td className="border-b border-r px-2.5 py-2"><span className={`rounded-md border px-1.5 py-1 text-[0.65rem] ${statusClass(row)}`}>{potensiDisplayStatus(row)}</span></td><td className="border-b border-r px-2.5 py-2 text-center">{String(row.kolek_closing ?? "-")} → {String(row.kolek_harian ?? "-")}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(row.baki_debet_closing)}</td><td className="border-b border-r px-2.5 py-2 text-right font-medium tabular-nums">{potensiMoney(row.baki_debet_harian)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(Number(row.tunggakan_pokok ?? 0) + Number(row.tunggakan_bunga ?? 0))}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(row.saldo_akhir)}</td><td className="border-b border-r px-2.5 py-2 text-center">{dateLabel(row.jt_harian)}</td><td className="border-b border-r px-2.5 py-2 text-center tabular-nums">{potensiNumber.format(Number(row.hm_harian ?? 0))} / {potensiNumber.format(Number(row.hmp_harian ?? 0))} / {potensiNumber.format(Number(row.hmb_harian ?? 0))}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(Number(row.angsuran_pokok ?? 0) + Number(row.angsuran_bunga ?? 0))}</td><td className="border-b border-r px-2.5 py-2 text-center">{potensiHasCommitment(row) ? "Sudah" : "Belum"}</td><td className="max-w-40 truncate border-b border-r px-2.5 py-2">{commitment.komitmen || "-"}</td><td className="border-b border-r px-2.5 py-2 text-center">{dateLabel(commitment.tanggal)}</td><td className="border-b border-r px-2.5 py-2 text-right tabular-nums">{potensiMoney(commitment.nominal)}</td><td className="max-w-40 truncate border-b px-2.5 py-2" title={commitment.alasan}>{commitment.alasan || "-"}</td></tr> })}</tbody>
          </table></div>
          <div className="grid gap-2 md:hidden">{visibleRows.map((row, index) => { const commitment = potensiCommitment(row); const address = String(row.alamat ?? "-"); return <article key={`${String(row.no_rekening)}-${index}`} className="overflow-hidden rounded-lg border bg-card"><div className="flex items-start justify-between gap-2 border-b px-3 py-2"><div className="min-w-0"><p className="truncate text-sm font-semibold">{String(row.nama_nasabah ?? "-")}</p><p className="mt-0.5 truncate font-mono text-[0.68rem] text-muted-foreground">{String(row.no_rekening ?? "-")} · {String(row.nama_ao ?? "-")}</p><p title={address} className="cursor-help truncate text-[0.68rem] text-muted-foreground">{address}</p></div><span className={`shrink-0 rounded-md border px-1.5 py-1 text-[0.65rem] ${statusClass(row)}`}>{potensiDisplayStatus(row)}</span></div><div className="grid grid-cols-2 gap-px bg-border text-xs">{[["Nominal actual", potensiMoney(row.baki_debet_harian)], ["Tunggakan P / B", `${potensiMoney(row.tunggakan_pokok)} / ${potensiMoney(row.tunggakan_bunga)}`], ["Kolek C → H", `${String(row.kolek_closing ?? "-")} → ${String(row.kolek_harian ?? "-")}`], ["DPD P / B", `${potensiNumber.format(Number(row.hmp_harian ?? 0))} / ${potensiNumber.format(Number(row.hmb_harian ?? 0))}`], ["Jatuh tempo", dateLabel(row.jt_harian)], ["Angsuran P / B", `${potensiMoney(row.angsuran_pokok)} / ${potensiMoney(row.angsuran_bunga)}`]].map(([name, value]) => <div key={String(name)} className="min-w-0 bg-card px-2.5 py-2"><span className="block text-[0.65rem] text-muted-foreground">{name}</span><strong className="mt-0.5 block truncate tabular-nums">{value}</strong></div>)}</div><div className="space-y-1 px-3 py-2 text-[0.68rem]"><div className="flex justify-between gap-3"><span className="text-muted-foreground">Komitmen</span><b className="truncate">{commitment.komitmen || (potensiHasCommitment(row) ? "Sudah komitmen" : "Belum komitmen")}</b></div><div className="flex justify-between gap-3"><span className="text-muted-foreground">Nominal janji</span><b className="tabular-nums">{potensiMoney(commitment.nominal)}</b></div><div className="flex justify-between gap-3"><span className="text-muted-foreground">Tanggal janji</span><b>{dateLabel(commitment.tanggal)}</b></div><p className="line-clamp-2 text-muted-foreground">{commitment.alasan || "-"}</p></div></article> })}</div>
        </>}
      </div>
    </section>
  </ReportDetailLayer>
}
