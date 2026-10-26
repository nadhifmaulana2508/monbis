import { useEffect, useMemo, useState } from "react"
import { DetailSearchToggle, ReportDetailFilters } from "@/components/report-detail-filters"
import { ReportDetailLayer } from "@/components/report-detail-layer"
import { ReportModalCloseButton } from "@/components/report-modal-close-button"
import { Card, CardContent } from "@/components/ui/card"
import type { RecoveryDetailType, RecoveryFilters, RecoveryRow } from "./recovery-npl-data"
import { recoveryDetailLabel, recoveryMoney, recoveryName, recoveryNumber, recoveryMetric } from "./recovery-npl-data"

type Props = {
  selection: { row: RecoveryRow; type: RecoveryDetailType }
  filters: RecoveryFilters
  rows: RecoveryRow[]
  loading: boolean
  error: string | null
  status: string
  onStatusChange: (status: string) => void
  onClose: () => void
}

function dateLabel(value: unknown) {
  const raw = String(value ?? "").slice(0, 10)
  const match = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/)
  return match ? `${match[3]}/${match[2]}/${match[1]}` : raw || "-"
}

function statusTone(value: unknown) {
  const status = String(value ?? "").toLowerCase()
  return status.includes("sudah") ? "border-signal-green/30 bg-signal-green/10 text-signal-green-foreground" : status.includes("potensi") || status.includes("belum") ? "border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-300" : "border-border bg-muted text-muted-foreground"
}

export function RecoveryNplDetailModal({ selection, filters, rows, loading, error, status, onStatusChange, onClose }: Props) {
  const [search, setSearch] = useState("")
  const visibleRows = useMemo(() => {
    const query = search.trim().toLowerCase()
    return query ? rows.filter((row) => `${row.no_rekening ?? ""} ${row.nama_nasabah ?? ""} ${row.jenis_recovery ?? ""}`.toLowerCase().includes(query)) : rows
  }, [rows, search])
  const totalBaki = visibleRows.reduce((sum, row) => sum + recoveryMetric(row, "baki_debet"), 0)
  const totalRecovery = visibleRows.reduce((sum, row) => sum + recoveryMetric(row, "recovery_nominal"), 0)
  const totalAngsuran = visibleRows.reduce((sum, row) => sum + recoveryMetric(row, "angsuran_pokok") + recoveryMetric(row, "angsuran_bunga"), 0)
  const monitored = selection.type === "backflow" ? visibleRows.filter((row) => String(row.tgl_jatuh_tempo ?? "").slice(0, 10) > filters.actualDate) : []
  const monitoredBaki = monitored.reduce((sum, row) => sum + recoveryMetric(row, "baki_debet"), 0)
  const nominalLabel = filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"

  useEffect(() => {
    const overflow = document.body.style.overflow
    const onKeyDown = (event: KeyboardEvent) => { if (event.key === "Escape") onClose() }
    document.body.style.overflow = "hidden"
    window.addEventListener("keydown", onKeyDown)
    return () => { document.body.style.overflow = overflow; window.removeEventListener("keydown", onKeyDown) }
  }, [onClose])

  return <ReportDetailLayer className="flex items-end justify-center bg-foreground/35 p-0 sm:items-center sm:p-4">
    <button type="button" aria-label="Tutup detail Recovery NPL" className="absolute inset-0 cursor-default" onClick={onClose} />
    <section role="dialog" aria-modal="true" aria-labelledby="recovery-detail-title" className="relative flex h-[min(92vh,760px)] w-full max-w-6xl flex-col overflow-hidden rounded-t-xl border bg-background text-foreground shadow-xl sm:rounded-xl">
      <header className="flex shrink-0 flex-nowrap items-center justify-between gap-2 border-b px-3 py-2.5 sm:gap-3 sm:px-6">
        <div className="min-w-0 flex-1"><p className="text-xs text-muted-foreground">Posisi {dateLabel(filters.closingDate)} dibanding {dateLabel(filters.actualDate)}</p><h2 id="recovery-detail-title" className="truncate text-base font-semibold tracking-tight">{recoveryDetailLabel(selection.type)} · {recoveryName(selection.row)}</h2><p className="text-[0.68rem] text-muted-foreground">{visibleRows.length.toLocaleString("id-ID")}{search.trim() ? ` / ${rows.length.toLocaleString("id-ID")}` : ""} rekening</p></div>
        <div className="flex shrink-0 items-center gap-1.5 sm:gap-2">
          <DetailSearchToggle value={search} onChange={setSearch} />
          {selection.type === "backflow" && <ReportDetailFilters><select value={status} onChange={(event) => onStatusChange(event.target.value)} className="h-9 w-full rounded-md border bg-background px-2 text-xs sm:w-44"><option value="all">Semua Backflow</option><option value="sudah">Sudah Angsuran</option><option value="belum">Potensi Flow</option></select></ReportDetailFilters>}
          <ReportModalCloseButton label="Tutup detail" onClick={onClose} />
        </div>
      </header>

      <div className="min-h-0 flex-1 overflow-auto p-3 sm:p-5">
        <div className="mb-3 grid grid-cols-2 gap-2 lg:grid-cols-4">
          <Card size="sm"><CardContent><p className="text-xs text-muted-foreground">Jumlah Debitur</p><p className="mt-1 font-semibold tabular-nums">{visibleRows.length.toLocaleString("id-ID")}{search.trim() ? ` / ${rows.length.toLocaleString("id-ID")}` : ""}</p></CardContent></Card>
          <Card size="sm"><CardContent><p className="text-xs text-muted-foreground">Total {nominalLabel}</p><p className="mt-1 font-semibold tabular-nums">Rp {recoveryNumber.format(Math.round(totalBaki))}</p></CardContent></Card>
          <Card size="sm"><CardContent><p className="text-xs text-muted-foreground">Total Recovery</p><p className="mt-1 font-semibold tabular-nums text-signal-green-foreground">Rp {recoveryNumber.format(Math.round(totalRecovery))}</p></CardContent></Card>
          <Card size="sm"><CardContent><p className="text-xs text-muted-foreground">Pokok + Bunga</p><p className="mt-1 font-semibold tabular-nums">Rp {recoveryNumber.format(Math.round(totalAngsuran))}</p></CardContent></Card>
        </div>
        {monitored.length > 0 && <div role="note" className="mb-3 rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-xs text-amber-800 dark:text-amber-200">{monitored.length.toLocaleString("id-ID")} debitur backflow masih dalam masa pantau, Rp {recoveryNumber.format(Math.round(monitoredBaki))}. Tanggal jatuh tempo angsuran berikutnya masih setelah posisi harian.</div>}
        {loading ? <div className="flex min-h-48 items-center justify-center text-sm text-muted-foreground">Mengambil detail debitur...</div> : error ? <div className="flex min-h-48 flex-col items-center justify-center gap-2 text-center"><p className="text-sm font-medium text-destructive">Detail belum dapat dimuat.</p><p className="max-w-lg text-xs text-muted-foreground">{error}</p></div> : visibleRows.length === 0 ? <div className="flex min-h-48 flex-col items-center justify-center text-center"><p className="text-sm font-medium">Data tidak ditemukan</p><p className="mt-1 text-xs text-muted-foreground">Coba ubah kata pencarian atau filter status.</p></div> : <>
          <div className="hidden overflow-x-auto rounded-lg border md:block">
            <table className="min-w-[1180px] w-full border-separate border-spacing-0 text-xs">
              <thead className="sticky top-0 z-10 bg-muted text-left text-[0.68rem] font-semibold text-muted-foreground"><tr>{["Jenis", "No. Rekening", "Nama Nasabah", nominalLabel, "Recovery", "Kol", "Upd", "Tgl Angsuran", "Status", "Tgl Bayar", "Pokok", "Bunga"].map((label) => <th key={label} className={`border-b border-r px-3 py-2.5 ${[nominalLabel, "Recovery", "Pokok", "Bunga"].includes(label) ? "text-right" : ""}`}>{label}</th>)}</tr></thead>
              <tbody>{visibleRows.map((row, index) => <tr key={`${String(row.no_rekening)}-${index}`} className="hover:bg-muted/30">
                <td className="border-b border-r px-3 py-2.5">{String(row.jenis_recovery || recoveryDetailLabel(selection.type))}</td><td className="border-b border-r px-3 py-2.5 font-mono text-muted-foreground">{String(row.no_rekening || "-")}</td><td title={String(row.nama_nasabah || "-")} className="max-w-48 truncate border-b border-r px-3 py-2.5 font-medium">{String(row.nama_nasabah || "-")}</td>
                <td className="border-b border-r px-3 py-2.5 text-right tabular-nums">{recoveryMoney(row.baki_debet)}</td><td className="border-b border-r px-3 py-2.5 text-right font-medium tabular-nums">{recoveryMoney(row.recovery_nominal)}</td><td className="border-b border-r px-3 py-2.5 text-center">{String(row.kolek || "-")}</td><td className="border-b border-r px-3 py-2.5 text-center">{String(row.kolek_update || "-")}</td><td className="border-b border-r px-3 py-2.5 text-center">{dateLabel(row.tgl_jatuh_tempo)}</td><td className="border-b border-r px-3 py-2.5 text-center"><span className={`inline-flex rounded-md border px-1.5 py-1 text-[0.65rem] ${statusTone(row.jt_status)}`}>{String(row.jt_status || "-")}</span></td><td className="border-b border-r px-3 py-2.5 text-center">{dateLabel(row.tgl_trans)}</td><td className="border-b border-r px-3 py-2.5 text-right tabular-nums">{recoveryMoney(row.angsuran_pokok)}</td><td className="border-b border-r px-3 py-2.5 text-right tabular-nums">{recoveryMoney(row.angsuran_bunga)}</td>
              </tr>)}</tbody>
            </table>
          </div>
          <div className="grid gap-2 md:hidden">{visibleRows.map((row, index) => <Card key={`${String(row.no_rekening)}-${index}`} size="sm"><CardContent className="grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
            <div className="col-span-2 min-w-0"><p className="truncate font-mono text-[0.68rem] text-muted-foreground">{String(row.no_rekening || "-")}</p><p className="truncate font-semibold">{String(row.nama_nasabah || "-")}</p></div>
            <div><p className="text-muted-foreground">Recovery</p><p className="font-medium tabular-nums">Rp {recoveryMoney(row.recovery_nominal)}</p></div><div className="text-right"><p className="text-muted-foreground">{nominalLabel}</p><p className="tabular-nums">Rp {recoveryMoney(row.baki_debet)}</p></div>
            <div><p className="text-muted-foreground">Pokok</p><p className="tabular-nums">Rp {recoveryMoney(row.angsuran_pokok)}</p></div><div className="text-right"><p className="text-muted-foreground">Bunga</p><p className="tabular-nums">Rp {recoveryMoney(row.angsuran_bunga)}</p></div>
            <div><p className="text-muted-foreground">Status</p><span className={`mt-0.5 inline-flex rounded-md border px-1.5 py-1 text-[0.65rem] ${statusTone(row.jt_status)}`}>{String(row.jt_status || "-")}</span></div><div className="text-right"><p className="text-muted-foreground">Kolektibilitas</p><p>{String(row.kolek || "-")} → {String(row.kolek_update || "-")}</p></div>
            <p className="col-span-2 truncate border-t pt-2 text-[0.68rem] text-muted-foreground">Angsuran {dateLabel(row.tgl_jatuh_tempo)} · Bayar {dateLabel(row.tgl_trans)}</p>
          </CardContent></Card>)}</div>
        </>}
      </div>
    </section>
  </ReportDetailLayer>
}
