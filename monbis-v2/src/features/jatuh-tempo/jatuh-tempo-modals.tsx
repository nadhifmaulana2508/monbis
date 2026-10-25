import { useMemo, useState } from "react"
import { Download, FileSearch, LoaderCircle, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"

import {
  formatJtDate,
  formatJtMoney,
  getJtCategoryLabel,
  getJtStatusTone,
  isJtClear,
  isJtDrop,
  jtNumberFormat,
} from "./jatuh-tempo-data"
import type {
  JtAoOption,
  JtDetailRow,
  JtDetailSelection,
  JtFilters,
  JtKankasOption,
} from "./jatuh-tempo-types"

export function JatuhTempoInfoPopover({
  open,
  onClose,
}: {
  open: boolean
  onClose: () => void
}) {
  if (!open) return null
  return (
    <aside role="dialog" aria-labelledby="jatuh-tempo-info-title" className="absolute left-0 top-full z-50 mt-2 w-[min(380px,calc(100vw-2rem))] rounded-xl border bg-background p-4 text-left shadow-xl">
      <div className="flex items-start justify-between gap-3">
        <div>
          <p id="jatuh-tempo-info-title" className="text-sm font-semibold">Tentang Jatuh Tempo Kredit</p>
          <p className="mt-1 text-xs leading-relaxed text-muted-foreground">Report ini membantu melihat kredit yang jatuh tempo dan peluang penyelesaian atau refinancing.</p>
        </div>
        <Button type="button" variant="ghost" size="icon-sm" aria-label="Tutup informasi" onClick={onClose}><X aria-hidden="true" /></Button>
      </div>
      <ul className="mt-3 grid gap-2 text-xs leading-relaxed text-muted-foreground">
        <li className="rounded-lg bg-muted/50 px-3 py-2"><b className="text-foreground">Potensi kredit:</b> seluruh daftar jatuh tempo sesuai filter.</li>
        <li className="rounded-lg bg-muted/50 px-3 py-2"><b className="text-foreground">Refinancing:</b> kredit yang memiliki realisasi baru.</li>
        <li className="rounded-lg bg-muted/50 px-3 py-2"><b className="text-foreground">Basis nominal:</b> saldo bank atau baki debet sesuai pilihan.</li>
      </ul>
    </aside>
  )
}

type JatuhTempoDetailModalProps = {
  selection: JtDetailSelection
  filters: JtFilters
  rows: JtDetailRow[]
  loading: boolean
  error: string | null
  page: number
  totalPages: number
  totalRecords: number
  kankasOptions: JtKankasOption[]
  aoOptions: JtAoOption[]
  selectedKankas: string
  selectedAo: string
  onKankasChange: (value: string) => void
  onAoChange: (value: string) => void
  onClose: () => void
  onPageChange: (page: number) => void
  onExport: () => void
}

export function JatuhTempoDetailModal({
  selection,
  filters,
  rows,
  loading,
  error,
  page,
  totalPages,
  totalRecords,
  kankasOptions,
  aoOptions,
  selectedKankas,
  selectedAo,
  onKankasChange,
  onAoChange,
  onClose,
  onPageChange,
  onExport,
}: JatuhTempoDetailModalProps) {
  const [search, setSearch] = useState("")

  const visibleRows = useMemo(() => {
    const keyword = search.trim().toLowerCase()
    if (!keyword) return rows
    return rows.filter((row) => [row.no_rekening_lama, row.nama_nasabah, row.nama_produk, row.nama_kankas, row.nama_ao].join(" ").toLowerCase().includes(keyword))
  }, [rows, search])

  const totals = useMemo(() => visibleRows.reduce((result, row) => {
    result.plafondLama += Number(row.plafond_lama || 0)
    result.plafondBaru += Number(row.plafond_baru || 0)
    if (!isJtClear(row)) result.sisa += Number(row.baki_debet_lama || 0)
    return result
  }, { plafondLama: 0, plafondBaru: 0, sisa: 0 }), [visibleRows])

  return (
    <div className="fixed inset-0 z-50 flex items-end justify-center bg-foreground/40 p-0 backdrop-blur-[2px] sm:items-center sm:p-4">
      <button type="button" aria-label="Tutup detail jatuh tempo" className="absolute inset-0 cursor-default" onClick={onClose} />
      <section role="dialog" aria-modal="true" aria-labelledby="jatuh-tempo-detail-title" className="relative flex h-[min(94vh,860px)] w-full max-w-[1500px] flex-col overflow-hidden rounded-t-xl border bg-background shadow-2xl sm:rounded-xl">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b px-4 py-3 sm:px-5">
          <div className="min-w-0">
            <p className="text-xs text-muted-foreground">{selection.label}</p>
            <h2 id="jatuh-tempo-detail-title" className="truncate text-base font-semibold tracking-tight sm:text-lg">
              {getJtCategoryLabel(selection.category)}
            </h2>
            <p className="mt-0.5 text-[0.7rem] text-muted-foreground">{filters.month}/{filters.year} · {filters.nominalField === "baki_debet" ? "Baki debet" : "Saldo bank"} · Kolek {filters.includeDp ? "L + DP" : "L"}</p>
          </div>
          <div className="flex w-full flex-wrap items-center gap-2 sm:w-auto">
            <div className="relative min-w-0 flex-1 sm:flex-none">
              <FileSearch className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" aria-hidden="true" />
              <input value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Cari rekening atau nama" aria-label="Cari detail jatuh tempo" className="h-10 w-full rounded-lg border bg-muted/25 pl-9 pr-3 text-sm outline-none focus-visible:ring-3 focus-visible:ring-ring/50 sm:w-56" />
            </div>
            <select value={selectedKankas} onChange={(event) => onKankasChange(event.target.value)} aria-label="Filter Kankas" className="h-10 min-w-0 flex-1 rounded-lg border bg-background px-2 text-xs font-medium outline-none focus-visible:ring-3 focus-visible:ring-ring/50 sm:w-40">
              <option value="">Semua Kankas</option>
              {kankasOptions.map((option) => <option key={option.kode_group1} value={option.kode_group1}>{option.deskripsi_group1 || option.kode_group1}</option>)}
            </select>
            <select value={selectedAo} onChange={(event) => onAoChange(event.target.value)} aria-label="Filter AO" className="h-10 min-w-0 flex-1 rounded-lg border bg-background px-2 text-xs font-medium outline-none focus-visible:ring-3 focus-visible:ring-ring/50 sm:w-40">
              <option value="">Semua AO</option>
              {aoOptions.map((option) => <option key={option.kode_group2} value={option.kode_group2}>{option.nama_ao || option.kode_group2}</option>)}
            </select>
            <Button type="button" variant="signalGreen" size="icon-lg" aria-label="Export detail Excel" title="Export detail Excel" onClick={onExport} disabled={loading || rows.length === 0}><Download aria-hidden="true" /></Button>
            <Button type="button" variant="ghost" size="icon-lg" aria-label="Tutup detail" onClick={onClose}><X aria-hidden="true" /></Button>
          </div>
        </header>

        <div className="relative min-h-0 flex-1 overflow-auto p-3 sm:p-5">
          {loading && (
            <div className="absolute inset-0 z-20 flex items-center justify-center gap-2 bg-background/80 text-sm text-muted-foreground backdrop-blur-[1px]">
              <LoaderCircle className="size-4 animate-spin" aria-hidden="true" /> Mengambil detail...
            </div>
          )}
          {error ? (
            <div className="flex min-h-48 flex-col items-center justify-center gap-3 text-center">
              <p className="text-sm font-medium text-destructive">Detail belum dapat dimuat.</p>
              <p className="max-w-lg text-xs text-muted-foreground">{error}</p>
              <Button type="button" variant="outline" size="sm" onClick={() => onPageChange(page)}>Coba lagi</Button>
            </div>
          ) : visibleRows.length === 0 && !loading ? (
            <div className="flex min-h-48 items-center justify-center text-sm text-muted-foreground">Tidak ada data detail.</div>
          ) : (
            <div className="overflow-x-auto rounded-lg border">
              <table className="min-w-[1180px] w-full border-separate border-spacing-0 text-xs">
                <thead className="sticky top-0 z-10 bg-muted text-left text-[0.68rem] font-semibold uppercase tracking-wide text-muted-foreground">
                  <tr>
                    <th className="px-3 py-3">Rekening</th>
                    <th className="px-3 py-3">Nama nasabah</th>
                    <th className="px-3 py-3">Produk</th>
                    <th className="px-3 py-3">Alamat</th>
                    <th className="px-3 py-3">No HP</th>
                    <th className="px-3 py-3">Kankas</th>
                    <th className="px-3 py-3">AO</th>
                    <th className="px-3 py-3 text-right">Plafon lama</th>
                    <th className="px-3 py-3">Tanggal JT</th>
                    <th className="px-3 py-3 text-right">Sisa {filters.nominalField === "baki_debet" ? "baki debet" : "saldo bank"}</th>
                    <th className="px-3 py-3">Status</th>
                    <th className="px-3 py-3 text-right">Plafon baru</th>
                    <th className="px-3 py-3 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  {visibleRows.map((row, index) => <JtDetailTableRow key={`${row.no_rekening_lama}-${index}`} row={row} filters={filters} />)}
                  {visibleRows.length > 0 && (
                    <tr className="bg-muted font-semibold">
                      <td colSpan={7} className="px-3 py-3 text-right">Total halaman</td>
                      <td className="px-3 py-3 text-right tabular-nums">{formatJtMoney(totals.plafondLama)}</td>
                      <td />
                      <td className="px-3 py-3 text-right tabular-nums">{formatJtMoney(totals.sisa)}</td>
                      <td />
                      <td className="px-3 py-3 text-right tabular-nums">{formatJtMoney(totals.plafondBaru)}</td>
                      <td />
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          )}
        </div>

        <footer className="flex shrink-0 flex-wrap items-center justify-between gap-3 border-t px-4 py-3 text-xs text-muted-foreground sm:px-5">
          <span>{jtNumberFormat.format(totalRecords)} data · halaman {page} dari {totalPages}</span>
          <div className="flex items-center gap-2">
            <Button type="button" variant="outline" size="sm" disabled={page <= 1 || loading} onClick={() => onPageChange(page - 1)}>Sebelumnya</Button>
            <Button type="button" variant="outline" size="sm" disabled={page >= totalPages || loading} onClick={() => onPageChange(page + 1)}>Berikutnya</Button>
          </div>
        </footer>
      </section>
    </div>
  )
}

function JtDetailTableRow({ row, filters }: { row: JtDetailRow; filters: JtFilters }) {
  const clear = isJtClear(row)
  const locked = clear || isJtDrop(row)
  const product = row.nama_produk || (row.kode_produk ? `Produk ${row.kode_produk}` : "-")
  const address = row.alamat || "-"
  const shortAddress = address.length > 28 ? `${address.slice(0, 28)}...` : address
  const daysLabel = getDaysLabel(row.tgl_jatuh_tempo, filters.actualDate, clear)

  return (
    <tr className="border-b border-border/60 align-top hover:bg-muted/30">
      <td className="px-3 py-3 font-mono text-[0.68rem] text-muted-foreground">{row.no_rekening_lama || "-"}</td>
      <td className="max-w-48 px-3 py-3 font-medium">{row.nama_nasabah || "-"}</td>
      <td className="max-w-44 px-3 py-3 font-medium text-primary">{product}</td>
      <td className="max-w-48 px-3 py-3 text-muted-foreground" title={address}>{shortAddress}</td>
      <td className="px-3 py-3 text-muted-foreground">{row.no_hp || "-"}</td>
      <td className="px-3 py-3 text-muted-foreground">{row.nama_kankas || row.kankas || "-"}</td>
      <td className="max-w-36 px-3 py-3 text-primary">{row.nama_ao || "-"}</td>
      <td className="px-3 py-3 text-right tabular-nums">{formatJtMoney(row.plafond_lama)}</td>
      <td className="px-3 py-3 text-muted-foreground">
        <span className="whitespace-nowrap">{formatJtDate(row.tgl_jatuh_tempo)}</span>
        {daysLabel && <span className="mt-1 block text-[0.65rem] font-medium">{daysLabel}</span>}
      </td>
      <td className="px-3 py-3 text-right font-semibold tabular-nums">{clear ? "-" : formatJtMoney(row.baki_debet_lama)}</td>
      <td className="px-3 py-3"><span className={cn("inline-flex rounded-md border px-2 py-1 text-[0.65rem] font-semibold", getJtStatusTone(row))}>{row.keterangan_status || "-"}</span></td>
      <td className="px-3 py-3 text-right tabular-nums text-signal-green-foreground">{Number(row.plafond_baru || 0) > 0 ? <><span className="block font-semibold">{formatJtMoney(row.plafond_baru)}</span><span className="text-[0.65rem] text-muted-foreground">{formatJtDate(row.tgl_realisasi_baru)}</span></> : "-"}</td>
      <td className="px-3 py-3 text-center">{locked ? <span className="text-[0.65rem] font-semibold text-muted-foreground">Locked</span> : <Button type="button" variant="outline" size="xs" disabled title="Aksi prospek belum tersedia">Prospek</Button>}</td>
    </tr>
  )
}

function getDaysLabel(jtDate: string | undefined, actualDate: string, clear: boolean) {
  if (!jtDate || clear) return ""
  const jt = new Date(`${jtDate.slice(0, 10)}T00:00:00`).getTime()
  const actual = new Date(`${actualDate}T00:00:00`).getTime()
  if (!Number.isFinite(jt) || !Number.isFinite(actual)) return ""
  const days = Math.ceil((jt - actual) / 86400000)
  if (days < 0) return <span className="text-destructive">Lewat {Math.abs(days)} hari</span>
  if (days === 0) return <span className="text-signal-yellow-foreground">Hari ini</span>
  return <span className="text-muted-foreground">Kurang {days} hari</span>
}
