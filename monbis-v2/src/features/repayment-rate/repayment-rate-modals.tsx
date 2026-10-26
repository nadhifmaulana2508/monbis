import { Download, LoaderCircle } from "lucide-react"

import { DetailSearchToggle, ReportDetailFilters } from "@/components/report-detail-filters"
import { ReportDetailLayer } from "@/components/report-detail-layer"
import { ReportModalCloseButton } from "@/components/report-modal-close-button"
import { SearchableSelect } from "@/components/searchable-select"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { rrDateLabel, rrMoney, rrNumber, type RrFilters, type RrRow } from "./repayment-rate-data"

type Option = { value: string; label: string }

type Props = {
  selection: { row: RrRow; status: "ALL" | "MIGRASI" }
  filters: RrFilters
  rows: RrRow[]
  loading: boolean
  error: string | null
  page: number
  totalPages: number
  totalRecords: number
  search: string
  status: string
  kankas: string
  ao: string
  kankasOptions: Option[]
  aoOptions: Option[]
  onSearch: (value: string) => void
  onStatusChange: (value: string) => void
  onKankasChange: (value: string) => void
  onAoChange: (value: string) => void
  onClose: () => void
  onPageChange: (page: number) => void
  onExport: () => void
}

export function RepaymentRateDetailModal({ selection, filters, rows, loading, error, page, totalPages, totalRecords, search, status, kankas, ao, kankasOptions, aoOptions, onSearch, onStatusChange, onKankasChange, onAoChange, onClose, onPageChange, onExport }: Props) {
  const moneyKeys = ["trx_bulan_ini", "os_m1", "os_curr", "totung"] as const
  const identityKeys = ["no_rekening", "nama_nasabah", "alamat", "no_hp", "kankas", "nama_ao", "tgl_realisasi", "tgl_jatuh_tempo", "status_pembayaran"] as const

  return (
    <ReportDetailLayer className="flex items-end justify-center bg-foreground/35 p-0 sm:items-center sm:p-4">
      <button type="button" aria-label="Tutup detail" className="absolute inset-0 cursor-default" onClick={onClose} />
      <section role="dialog" aria-modal="true" aria-labelledby="rr-detail-title" className="relative flex h-[min(92vh,760px)] w-full max-w-6xl flex-col overflow-hidden rounded-t-xl border bg-background text-foreground shadow-xl sm:rounded-xl">
        <header className="flex shrink-0 flex-nowrap items-center justify-between gap-2 border-b px-3 py-2.5 sm:gap-3 sm:px-6">
          <div className="min-w-0 flex-1">
            <p className="text-xs text-muted-foreground">Closing {rrDateLabel(filters.closingDate)} · Actual {rrDateLabel(filters.actualDate)} · {totalRecords.toLocaleString("id-ID")} rekening</p>
            <h2 id="rr-detail-title" className="truncate text-base font-semibold tracking-tight">Detail {selection.status === "MIGRASI" ? "Migrasi" : "Actual"} · {String(selection.row.nama || "Grand total")}</h2>
          </div>
          <div className="flex shrink-0 items-center gap-1.5 sm:gap-2">
            <DetailSearchToggle value={search} onChange={onSearch} />
            <ReportDetailFilters>
              <div className="flex items-center gap-2">
                <div className="min-w-0 flex-1 sm:w-44 sm:flex-none"><SearchableSelect value={kankas} options={[{ value: "", label: "Semua Kankas" }, ...kankasOptions]} placeholder="Semua Kankas" onChange={onKankasChange} /></div>
                <div className="min-w-0 flex-1 sm:w-44 sm:flex-none"><SearchableSelect value={ao} options={[{ value: "", label: "Semua AO" }, ...aoOptions]} placeholder="Semua AO" onChange={onAoChange} /></div>
                <select value={status} onChange={(event) => onStatusChange(event.target.value)} className="h-9 min-w-0 flex-1 rounded-md border bg-background px-2 text-xs sm:flex-none">
                  <option value="ALL">Semua Status</option><option value="OTP">OTP</option><option value="TELAT">Telat</option><option value="TUNGGAKAN">Masih Tunggakan</option><option value="BELUM_JATUH_TEMPO">Belum Jatuh Tempo</option><option value="BELUM_BAYAR">Belum Bayar</option>
                </select>
              </div>
            </ReportDetailFilters>
            <Button type="button" variant="signalGreen" size="icon-lg" className="size-9" disabled={!rows.length || loading} onClick={onExport} aria-label="Export Excel" title="Export detail Excel"><Download className="size-4" aria-hidden="true" /></Button>
            <ReportModalCloseButton label="Tutup detail" onClick={onClose} />
          </div>
        </header>

        <div className="report-table-scroll relative min-h-0 flex-1 overflow-auto p-2 sm:p-4">
          {loading && <div className="absolute inset-0 z-20 flex items-center justify-center gap-2 bg-background/80 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" /> Mengambil detail...</div>}
          {error ? <div className="flex min-h-48 items-center justify-center text-sm text-destructive">{error}</div> : rows.length === 0 && !loading ? <div className="flex min-h-48 items-center justify-center text-sm text-muted-foreground">Tidak ada data detail untuk filter ini.</div> : <>
            <div className="hidden overflow-x-auto rounded-lg border sm:block">
              <table className="min-w-[1150px] w-full border-separate border-spacing-0 text-xs">
                <thead className="sticky top-0 z-10 bg-muted text-left font-semibold text-muted-foreground"><tr>{[...identityKeys.map((key) => [key, ({ no_rekening: "Rekening", nama_nasabah: "Nama Nasabah", alamat: "Alamat", no_hp: "No. HP", kankas: "Kankas", nama_ao: "AO", tgl_realisasi: "Realisasi", tgl_jatuh_tempo: "Jatuh Tempo", status_pembayaran: "Status Bayar" } as Record<string, string>)[key]] as const), ...moneyKeys.map((key) => [key, ({ trx_bulan_ini: "Transaksi", os_m1: "OS M-1", os_curr: "OS Actual", totung: "Tunggakan" } as Record<string, string>)[key]] as const), ["dpd_curr", "DPD"] as const].map(([key, label]) => <th key={key} className={`border-b border-r px-3 py-3 ${moneyKeys.includes(key as typeof moneyKeys[number]) || key === "dpd_curr" ? "text-right" : ""}`}>{label}</th>)}</tr></thead>
                <tbody>{rows.map((row, index) => <tr key={`${String(row.no_rekening)}-${index}`} className="hover:bg-muted/30">{identityKeys.map((key) => <td key={key} className="border-b border-r px-3 py-3">{String(row[key] ?? "-")}</td>)}{moneyKeys.map((key) => <td key={key} className="border-b border-r px-3 py-3 text-right tabular-nums">{rrMoney(row[key])}</td>)}<td className="border-b border-r px-3 py-3 text-right tabular-nums">{rrNumber.format(Number(row.dpd_curr || 0))}</td></tr>)}</tbody>
              </table>
            </div>
            <div className="grid gap-2 sm:hidden">
              {rows.map((row, index) => <Card key={`${String(row.no_rekening)}-${index}`} size="sm"><CardContent className="grid grid-cols-2 gap-x-3 gap-y-2 text-xs">
                <div className="col-span-2 min-w-0"><p className="truncate font-mono text-[0.68rem] text-muted-foreground">{String(row.no_rekening || "-")}</p><p className="truncate font-semibold">{String(row.nama_nasabah || "-")}</p></div>
                <div><p className="text-muted-foreground">Status bayar</p><p>{String(row.status_pembayaran || row.status_ket || "-")}</p></div><div className="text-right"><p className="text-muted-foreground">DPD</p><p className="tabular-nums">{rrNumber.format(Number(row.dpd_curr || 0))} hari</p></div>
                <div><p className="text-muted-foreground">OS M-1</p><p className="tabular-nums">{rrMoney(row.os_m1)}</p></div><div className="text-right"><p className="text-muted-foreground">OS Actual</p><p className="tabular-nums">{rrMoney(row.os_curr)}</p></div>
                <div><p className="text-muted-foreground">Angsuran bulan ini</p><p className="tabular-nums">{rrMoney(row.trx_bulan_ini)}</p></div><div className="text-right"><p className="text-muted-foreground">Tunggakan</p><p className="tabular-nums">{rrMoney(row.totung)}</p></div>
                <p className="col-span-2 truncate border-t pt-2 text-[0.68rem] text-muted-foreground">{String(row.kankas || "-")} · {String(row.nama_ao || "-")} · JT {String(row.tgl_jatuh_tempo || "-")}</p>
              </CardContent></Card>)}
            </div>
          </>}
        </div>

        <footer className="flex shrink-0 items-center justify-between border-t px-3 py-2 text-xs text-muted-foreground sm:px-6"><span>Halaman {page} dari {totalPages} · {totalRecords.toLocaleString("id-ID")} data</span><div className="flex gap-2"><Button type="button" variant="outline" size="sm" disabled={page <= 1 || loading} onClick={() => onPageChange(page - 1)}>Sebelumnya</Button><Button type="button" variant="outline" size="sm" disabled={page >= totalPages || loading} onClick={() => onPageChange(page + 1)}>Berikutnya</Button></div></footer>
      </section>
    </ReportDetailLayer>
  )
}
