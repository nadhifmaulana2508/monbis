import { useMemo } from "react"
import {
  ChevronLeft,
  ChevronRight,
  FileSearch,
  Info,
  LoaderCircle,
  X,
} from "lucide-react"

import { Button } from "@/components/ui/button"

import {
  formatDateLabel,
  formatGrowth,
  formatNominal,
  numberFormat,
  rowNumber,
} from "./realisasi-growth-data"
import type { DetailRow, Filters, ReportRow } from "./realisasi-growth-types"
import { cn } from "@/lib/utils"

export function SummaryModal({
  filters,
  grandTotal,
  rows,
  onClose,
}: {
  filters: Filters
  grandTotal: ReportRow | null
  rows: ReportRow[]
  onClose: () => void
}) {
  const topGrowth = useMemo(
    () => [...rows].sort((left, right) => rowNumber(right, "growth") - rowNumber(left, "growth")).slice(0, 3),
    [rows],
  )
  const topRealisasi = useMemo(
    () => [...rows].sort((left, right) => rowNumber(right, "total_realisasi") - rowNumber(left, "total_realisasi")).slice(0, 3),
    [rows],
  )
  const metrics = [
    { label: "Realisasi", value: formatNominal(grandTotal?.total_realisasi) },
    { label: "Run off", value: formatNominal(grandTotal?.total_run_off) },
    { label: "Saldo actual", value: formatNominal(grandTotal?.nominal_actual) },
    {
      label: "Growth",
      value: formatGrowth(grandTotal?.growth),
      valueClass: grandTotal && rowNumber(grandTotal, "growth") < 0 ? "text-destructive" : "text-signal-green-foreground",
    },
  ]

  return (
    <div className="fixed inset-0 z-50 grid place-items-center bg-foreground/35 p-4 backdrop-blur-[2px]">
      <button type="button" aria-label="Tutup ringkasan" className="absolute inset-0 cursor-default" onClick={onClose} />
      <section
        role="dialog"
        aria-modal="true"
        aria-labelledby="summary-title"
        aria-describedby="summary-description"
        className="relative flex max-h-[min(90vh,680px)] w-full max-w-2xl flex-col overflow-hidden rounded-xl border bg-background shadow-2xl"
      >
        <header className="flex items-start justify-between gap-3 border-b bg-muted/20 px-4 py-3 sm:px-6 sm:py-4">
          <div className="flex min-w-0 items-start gap-3">
            <span className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
              <Info className="size-4" aria-hidden="true" />
            </span>
            <div className="min-w-0">
              <h2 id="summary-title" className="truncate text-base font-semibold tracking-tight sm:text-lg">
                Ringkasan Realisasi Kredit &amp; Growth
              </h2>
              <p id="summary-description" className="mt-0.5 text-xs text-muted-foreground">
                Ringkasan performa dan tiga kantor dengan perubahan terbesar.
              </p>
              <p className="mt-1 text-xs text-muted-foreground">
                {filters.nominalField === "saldo_bank" ? "Saldo bank" : "Baki debet"} | Closing {formatDateLabel(filters.closingDate)} | Actual {formatDateLabel(filters.actualDate)}
              </p>
            </div>
          </div>
          <Button type="button" variant="ghost" size="icon" className="size-9 shrink-0" aria-label="Tutup ringkasan" onClick={onClose}>
            <X aria-hidden="true" />
          </Button>
        </header>

        <div className="min-h-0 overflow-hidden p-4 sm:p-6">
          <div className="mb-3 rounded-lg border bg-muted/50 px-3 py-2.5 text-sm">
            <span className="font-semibold">{numberFormat.format(rows.length)} kantor</span>
            <span className="text-muted-foreground"> | Ringkasan berdasarkan filter aktif.</span>
          </div>
          {grandTotal ? (
            <>
              <div className="grid grid-cols-2 gap-2 sm:gap-3">
                {metrics.map((metric) => (
                  <div key={metric.label} className="rounded-lg border px-3 py-2.5 sm:px-4 sm:py-3">
                    <p className="text-xs font-medium text-muted-foreground">{metric.label}</p>
                    <p className={cn("mt-0.5 truncate text-sm font-semibold tabular-nums sm:text-base", metric.valueClass)}>{metric.value}</p>
                  </div>
                ))}
              </div>
              <div className="mt-3 grid grid-cols-2 gap-2 sm:gap-3">
                <div className="rounded-lg border p-3 sm:p-4">
                  <h3 className="text-sm font-semibold">3 growth tertinggi</h3>
                  <ol className="mt-2 space-y-2 sm:mt-3 sm:space-y-3">
                    {topGrowth.map((row, index) => (
                      <li key={`${row.kode_kantor}-${index}`} className="flex items-start justify-between gap-3 text-sm">
                        <div className="min-w-0">
                          <p className="truncate font-medium">{row.nama_kantor || "Tanpa nama"}</p>
                          <p className="text-xs text-muted-foreground">{row.kode_kantor || "-"}</p>
                        </div>
                        <span className={cn("shrink-0 font-semibold tabular-nums", rowNumber(row, "growth") < 0 ? "text-destructive" : "text-signal-green-foreground")}>
                          {formatGrowth(row.growth)}
                        </span>
                      </li>
                    ))}
                  </ol>
                </div>
                <div className="rounded-lg border p-3 sm:p-4">
                  <h3 className="text-sm font-semibold">3 realisasi terbesar</h3>
                  <ol className="mt-2 space-y-2 sm:mt-3 sm:space-y-3">
                    {topRealisasi.map((row, index) => (
                      <li key={`${row.kode_kantor}-${index}`} className="flex items-start justify-between gap-3 text-sm">
                        <div className="min-w-0">
                          <p className="truncate font-medium">{row.nama_kantor || "Tanpa nama"}</p>
                          <p className="text-xs text-muted-foreground">{row.kode_kantor || "-"}</p>
                        </div>
                        <span className="shrink-0 font-semibold tabular-nums text-primary">{formatNominal(row.total_realisasi)}</span>
                      </li>
                    ))}
                  </ol>
                </div>
              </div>
            </>
          ) : (
            <p className="rounded-lg border border-dashed px-4 py-8 text-center text-sm text-muted-foreground">
              Data ringkasan belum tersedia.
            </p>
          )}
        </div>
      </section>
    </div>
  )
}

export function DetailModal({
  areaLabel,
  detailType,
  rows,
  loading,
  error,
  page,
  totalPages,
  totalRecords,
  search,
  onSearch,
  onClose,
  onPageChange,
}: {
  areaLabel: string
  detailType: string
  rows: DetailRow[]
  loading: boolean
  error: string | null
  page: number
  totalPages: number
  totalRecords: number
  search: string
  onSearch: (value: string) => void
  onClose: () => void
  onPageChange: (page: number) => void
}) {
  const visibleRows = useMemo(() => {
    const keyword = search.trim().toLowerCase()
    if (!keyword) return rows
    return rows.filter((row) =>
      [row.no_rekening, row.nama_nasabah, row.nama_ao, row.nama_kankas]
        .join(" ")
        .toLowerCase()
        .includes(keyword),
    )
  }, [rows, search])

  return (
    <div className="fixed inset-0 z-50 flex items-end justify-center bg-foreground/35 p-0 sm:items-center sm:p-4">
      <button type="button" aria-label="Tutup detail" className="absolute inset-0 cursor-default" onClick={onClose} />
      <section role="dialog" aria-modal="true" aria-labelledby="detail-title" className="relative flex h-[min(92vh,760px)] w-full max-w-6xl flex-col overflow-hidden rounded-t-xl border bg-background shadow-xl sm:rounded-xl">
        <header className="flex flex-wrap items-center justify-between gap-3 border-b px-4 py-3 sm:px-6">
          <div className="min-w-0">
            <p className="text-xs font-medium text-muted-foreground">{areaLabel}</p>
            <h2 id="detail-title" className="truncate text-base font-semibold tracking-tight">Detail {detailType}</h2>
          </div>
          <div className="flex items-center gap-2">
            <DetailSearch value={search} onChange={onSearch} className="hidden sm:block" />
            <Button type="button" variant="ghost" size="icon" className="size-11" aria-label="Tutup detail" onClick={onClose}>
              <X aria-hidden="true" />
            </Button>
          </div>
          <DetailSearch value={search} onChange={onSearch} className="w-full sm:hidden" />
        </header>

        <div className="min-h-0 flex-1 overflow-auto p-3 sm:p-5">
          {loading ? (
            <div className="flex h-full min-h-40 items-center justify-center gap-2 text-sm text-muted-foreground">
              <LoaderCircle className="size-4 animate-spin" aria-hidden="true" />
              Mengambil detail...
            </div>
          ) : error ? (
            <div className="flex h-full min-h-40 items-center justify-center text-sm text-destructive">{error}</div>
          ) : visibleRows.length === 0 ? (
            <div className="flex h-full min-h-40 items-center justify-center text-sm text-muted-foreground">Tidak ada data detail.</div>
          ) : (
            <div className="overflow-x-auto rounded-lg border">
              <table className="min-w-[760px] w-full border-collapse text-sm">
                <thead className="sticky top-0 bg-muted/95 text-left text-xs font-semibold text-muted-foreground">
                  <tr>
                    <th className="px-3 py-3">Rekening</th>
                    <th className="px-3 py-3">Nama nasabah</th>
                    <th className="hidden px-3 py-3 md:table-cell">Kankas</th>
                    <th className="hidden px-3 py-3 md:table-cell">AO</th>
                    <th className="px-3 py-3">Tgl realisasi</th>
                    <th className="px-3 py-3 text-right">Plafond</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {visibleRows.map((row, index) => (
                    <tr key={`${row.no_rekening}-${index}`} className="hover:bg-muted/30">
                      <td className="px-3 py-3 font-mono text-xs text-muted-foreground">{row.no_rekening || "-"}</td>
                      <td className="px-3 py-3 font-medium">{row.nama_nasabah || "-"}</td>
                      <td className="hidden px-3 py-3 text-muted-foreground md:table-cell">{row.nama_kankas || "-"}</td>
                      <td className="hidden px-3 py-3 text-muted-foreground md:table-cell">{row.nama_ao || "-"}</td>
                      <td className="px-3 py-3 text-muted-foreground">{row.tgl_realisasi || "-"}</td>
                      <td className="px-3 py-3 text-right tabular-nums">{formatNominal(row.plafond)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>

        <footer className="flex flex-wrap items-center justify-between gap-3 border-t px-4 py-3 text-xs text-muted-foreground sm:px-6">
          <span>{numberFormat.format(totalRecords)} data, halaman {page} dari {totalPages}</span>
          <div className="flex items-center gap-2">
            <Button type="button" variant="outline" size="sm" disabled={page <= 1 || loading} onClick={() => onPageChange(page - 1)}>
              <ChevronLeft data-icon="inline-start" aria-hidden="true" />
              Sebelumnya
            </Button>
            <Button type="button" variant="outline" size="sm" disabled={page >= totalPages || loading} onClick={() => onPageChange(page + 1)}>
              Berikutnya
              <ChevronRight data-icon="inline-end" aria-hidden="true" />
            </Button>
          </div>
        </footer>
      </section>
    </div>
  )
}

function DetailSearch({
  value,
  onChange,
  className,
}: {
  value: string
  onChange: (value: string) => void
  className?: string
}) {
  return (
    <div className={cn("relative", className)}>
      <FileSearch className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" aria-hidden="true" />
      <input
        value={value}
        onChange={(event) => onChange(event.target.value)}
        placeholder="Cari rekening atau nama"
        aria-label="Cari data detail"
        className="h-10 w-full rounded-lg border bg-muted/25 pl-9 pr-3 text-sm outline-none focus-visible:ring-3 focus-visible:ring-ring/50 sm:h-9 sm:w-56"
      />
    </div>
  )
}
