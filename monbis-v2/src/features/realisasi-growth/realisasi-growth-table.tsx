import { ArrowDown, ArrowUp } from "lucide-react"

import { cn } from "@/lib/utils"

import {
  formatCount,
  formatGrowth,
  formatNominal,
  rowNumber,
} from "./realisasi-growth-data"
import { tableColumns } from "./realisasi-growth-types"
import type { ReportRow, SortDirection, SortKey } from "./realisasi-growth-types"

function MetricCell({
  nominal,
  noa,
  onClick,
}: {
  nominal: unknown
  noa: unknown
  onClick?: () => void
}) {
  const hasNoa = noa !== undefined && Number(noa ?? 0) > 0
  const className = cn(
    "flex min-h-11 min-w-0 w-full flex-col justify-center gap-0.5 px-2 py-1 text-right text-xs text-foreground",
    onClick && "cursor-pointer rounded-md outline-none hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring",
  )
  const content = (
    <>
      <span className="whitespace-nowrap font-medium tabular-nums">{formatNominal(nominal)}</span>
      {hasNoa && (
        <span className="text-[0.65rem] font-normal text-muted-foreground">
          NOA: {formatCount(noa)}
        </span>
      )}
    </>
  )

  return onClick ? (
    <button type="button" className={className} onClick={onClick}>
      {content}
    </button>
  ) : (
    <div className={className}>{content}</div>
  )
}

function SortIcon({
  active,
  direction,
}: {
  active: boolean
  direction: SortDirection
}) {
  if (!active) return null
  return direction === "asc" ? (
    <ArrowUp className="size-3.5" aria-hidden="true" />
  ) : (
    <ArrowDown className="size-3.5" aria-hidden="true" />
  )
}

type RealisasiGrowthTableProps = {
  sortedRows: ReportRow[]
  grandTotal: ReportRow | null
  sortKey: SortKey
  sortDirection: SortDirection
  onSort: (key: SortKey) => void
  onDetail: (row: ReportRow, type: string) => void
}

export function RealisasiGrowthTable({
  sortedRows,
  grandTotal,
  sortKey,
  sortDirection,
  onSort,
  onDetail,
}: RealisasiGrowthTableProps) {
  const identityHeaderClass = "relative sticky top-0 z-40 border-b bg-muted shadow-[1px_0_0_0_var(--border),0_1px_0_0_var(--border)]"
  const identityCellClass = "relative sticky z-20 border-r border-border/70"

  return (
    <table className="report-main-table min-w-[1120px] w-full border-separate border-spacing-0 text-xs xl:min-w-0 xl:table-fixed">
      <colgroup>
        <col className="report-code-col w-16" />
        <col className="report-name-col w-32" />
        {tableColumns.slice(2).map((column) => <col key={column.key} className="w-28" />)}
      </colgroup>
      <thead className="text-xs text-muted-foreground">
        <tr>
          {tableColumns.map((column, index) => {
            const active = sortKey === column.key
            return (
              <th
                key={column.key}
                scope="col"
                aria-sort={active ? (sortDirection === "asc" ? "ascending" : "descending") : "none"}
                className={cn(
                  "relative sticky top-0 z-30 whitespace-nowrap border-b bg-muted px-2 py-2 font-semibold text-muted-foreground shadow-[0_1px_0_0_var(--border)]",
                  column.align,
                  index === 0 && cn("report-code-col report-sticky-code left-0 w-16 text-left", identityHeaderClass),
                  index === 1 && cn("report-name-col report-sticky-name left-16 w-32 min-w-32 text-left", identityHeaderClass),
                )}
              >
                <button type="button" className={cn("inline-flex min-h-8 w-full items-center gap-1 outline-none focus-visible:ring-2 focus-visible:ring-ring", index < 2 ? "justify-start" : "justify-end")} onClick={() => onSort(column.key)}>
                  {column.label}
                  <SortIcon active={active} direction={sortDirection} />
                </button>
              </th>
            )
          })}
        </tr>
      </thead>
      <tbody className="divide-y divide-border">
        {grandTotal && (
          <tr className="bg-muted font-semibold">
            <td className={cn("report-code-col report-sticky-code left-0 z-20 w-16 bg-muted px-2 py-3 text-left", identityCellClass)}>ALL</td>
            <td className={cn("report-name-col report-sticky-name left-16 z-20 w-32 min-w-32 bg-muted px-2 py-3 text-left", identityCellClass)}>TOTAL</td>
            <td className="border-b border-border px-3 py-1"><MetricCell nominal={grandTotal.nominal_closing} noa={undefined} /></td>
            <td className="border-b border-border px-3 py-1"><MetricCell nominal={grandTotal.total_realisasi} noa={grandTotal.noa_realisasi} onClick={() => onDetail(grandTotal, "Realisasi")} /></td>
            <td className="border-b border-border px-3 py-1"><MetricCell nominal={grandTotal.total_restruck} noa={grandTotal.noa_restruck} onClick={() => onDetail(grandTotal, "Restrukturisasi")} /></td>
            <td className="border-b border-border px-3 py-1"><MetricCell nominal={grandTotal.pelunasan} noa={grandTotal.noa_pelunasan} /></td>
            <td className="border-b border-border px-3 py-1"><MetricCell nominal={grandTotal.angsuran_murni} noa={grandTotal.noa_angsuran_murni} /></td>
            <td className="border-b border-border px-3 py-1"><MetricCell nominal={grandTotal.total_run_off} noa={grandTotal.noa_run_off} /></td>
            <td className="border-b border-border px-3 py-1"><MetricCell nominal={grandTotal.nominal_actual} noa={undefined} /></td>
            <td className={cn("border-b border-border px-3 py-3 text-right tabular-nums", rowNumber(grandTotal, "growth") < 0 ? "text-destructive" : "text-signal-green-foreground")}>{formatGrowth(grandTotal.growth)}</td>
          </tr>
        )}
        {sortedRows.map((row, index) => (
          <tr key={`${row.kode_kantor}-${index}`} className="group hover:bg-muted/25">
            <td className={cn("report-code-col report-sticky-code left-0 z-20 w-16 bg-background px-2 py-3 font-mono text-xs text-muted-foreground group-hover:bg-muted/60", identityCellClass)}>{row.kode_kantor || "-"}</td>
            <td className={cn("report-name-col report-sticky-name left-16 z-20 w-32 min-w-32 max-w-32 truncate bg-background px-2 py-3 font-medium group-hover:bg-muted/60", identityCellClass)} title={row.nama_kantor || "-"}>{row.nama_kantor || "-"}</td>
            <td className="border-b border-border/70 px-3 py-1 text-right"><MetricCell nominal={row.nominal_closing} noa={undefined} /></td>
            <td className="border-b border-border/70 px-3 py-1"><MetricCell nominal={row.total_realisasi} noa={row.noa_realisasi} onClick={rowNumber(row, "noa_realisasi") > 0 ? () => onDetail(row, "Realisasi") : undefined} /></td>
            <td className="border-b border-border/70 px-3 py-1"><MetricCell nominal={row.total_restruck} noa={row.noa_restruck} onClick={rowNumber(row, "noa_restruck") > 0 ? () => onDetail(row, "Restrukturisasi") : undefined} /></td>
            <td className="border-b border-border/70 px-3 py-1"><MetricCell nominal={row.pelunasan} noa={row.noa_pelunasan} /></td>
            <td className="border-b border-border/70 px-3 py-1"><MetricCell nominal={row.angsuran_murni} noa={row.noa_angsuran_murni} /></td>
            <td className="border-b border-border/70 px-3 py-1"><MetricCell nominal={row.total_run_off} noa={row.noa_run_off} /></td>
            <td className="border-b border-border/70 px-3 py-1"><MetricCell nominal={row.nominal_actual} noa={undefined} /></td>
            <td className={cn("border-b border-border/70 px-3 py-3 text-right tabular-nums", rowNumber(row, "growth") < 0 ? "text-destructive" : "text-signal-green-foreground")}>{formatGrowth(row.growth)}</td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}
