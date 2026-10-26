import type { RefObject } from "react"
import { ReportSortHeader } from "@/components/report-sort-header"
import { cn } from "@/lib/utils"

import { formatCount, formatGrowth, formatNominal, rowNumber } from "./realisasi-growth-data"
import { tableColumns } from "./realisasi-growth-types"
import type { ReportRow, SortDirection, SortKey } from "./realisasi-growth-types"

function MetricCell({ nominal, noa, onClick }: { nominal: unknown; noa: unknown; onClick?: () => void }) {
  const hasNoa = noa !== undefined && Number(noa ?? 0) > 0
  const className = cn(
    "flex min-h-11 min-w-0 w-full flex-col justify-center gap-0.5 px-2 py-1 text-right text-xs text-foreground",
    onClick && "cursor-pointer rounded-md outline-none hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring",
  )
  const content = (
    <>
      <span className="w-full whitespace-nowrap text-right font-medium tabular-nums">{formatNominal(nominal)}</span>
      {hasNoa && <span className="w-full text-right text-[0.65rem] font-normal text-muted-foreground">NOA: {formatCount(noa)}</span>}
    </>
  )
  return onClick ? <button type="button" className={className} onClick={onClick}>{content}</button> : <div className={className}>{content}</div>
}

type RealisasiGrowthTableProps = {
  sortedRows: ReportRow[]
  grandTotal: ReportRow | null
  sortKey: SortKey
  sortDirection: SortDirection
  onSort: (key: SortKey) => void
  onDetail: (row: ReportRow, type: string) => void
  tableRef: RefObject<HTMLTableElement | null>
}

const identityCellClass = "relative sticky border-r border-border/70"
const headerCellClass = "sticky top-0 z-[60] border-b bg-muted px-2 py-2 font-semibold text-muted-foreground shadow-[0_1px_0_0_var(--border)]"
const totalCellClass = "sticky top-12 z-50 border-b border-border bg-muted px-2 py-1 font-semibold"

export function RealisasiGrowthTable({ sortedRows, grandTotal, sortKey, sortDirection, onSort, onDetail, tableRef }: RealisasiGrowthTableProps) {
  return (
    <div className="report-main-table report-table-surface min-w-[1120px] w-full text-xs">
      <table ref={tableRef} className="w-full table-fixed border-separate border-spacing-0">
        <colgroup>
          <col className="report-code-col" style={{ width: "4rem" }} />
          <col className="report-name-col" style={{ width: "8rem" }} />
          {tableColumns.slice(2).map((column) => <col key={column.key} style={{ width: "7rem" }} />)}
        </colgroup>
        <thead className="report-table-head text-xs text-muted-foreground">
          <tr className="h-9">
            {tableColumns.map((column, index) => {
              return (
                <ReportSortHeader
                  key={column.key}
                  label={column.label}
                  labelText={column.label}
                  column={column.key}
                  sort={{ column: sortKey, direction: sortDirection }}
                  onSort={(key) => onSort(key as SortKey)}
                  className={cn(headerCellClass, "whitespace-nowrap text-center", index === 0 && "report-code-col left-0 z-[70]", index === 1 && "report-name-col left-16 z-[70]")}
                  buttonClassName="justify-center text-center"
                />
              )
            })}
          </tr>
        </thead>
        <tbody>
          {grandTotal && (
            <tr className="report-total-row bg-muted">
              <td className={cn("report-code-col left-0 z-[70] text-left", totalCellClass, identityCellClass)}>ALL</td>
              <td className={cn("report-name-col left-16 z-[70] min-w-0 text-left", totalCellClass, identityCellClass)}>TOTAL</td>
              <td className={cn(totalCellClass, "p-0")}><MetricCell nominal={grandTotal.nominal_closing} noa={undefined} /></td>
              <td className={cn(totalCellClass, "p-0")}><MetricCell nominal={grandTotal.total_realisasi} noa={grandTotal.noa_realisasi} onClick={() => onDetail(grandTotal, "Realisasi")} /></td>
              <td className={cn(totalCellClass, "p-0")}><MetricCell nominal={grandTotal.total_restruck} noa={grandTotal.noa_restruck} onClick={() => onDetail(grandTotal, "Restrukturisasi")} /></td>
              <td className={cn(totalCellClass, "p-0")}><MetricCell nominal={grandTotal.pelunasan} noa={grandTotal.noa_pelunasan} /></td>
              <td className={cn(totalCellClass, "p-0")}><MetricCell nominal={grandTotal.angsuran_murni} noa={grandTotal.noa_angsuran_murni} /></td>
              <td className={cn(totalCellClass, "p-0")}><MetricCell nominal={grandTotal.total_run_off} noa={grandTotal.noa_run_off} /></td>
              <td className={cn(totalCellClass, "p-0")}><MetricCell nominal={grandTotal.nominal_actual} noa={undefined} /></td>
              <td className={cn(totalCellClass, "px-2 text-right tabular-nums", rowNumber(grandTotal, "growth") < 0 ? "text-destructive" : "text-signal-green-foreground")}>{formatGrowth(grandTotal.growth)}</td>
            </tr>
          )}
          {sortedRows.map((row, index) => (
            <tr key={`${row.kode_kantor}-${index}`} className="report-data-row group hover:bg-muted/25">
              <td className={cn("report-code-col left-0 z-20 bg-background px-2 py-3 text-left font-mono text-xs text-muted-foreground group-hover:bg-muted/60", identityCellClass)}>{row.kode_kantor || "-"}</td>
              <td className={cn("report-name-col left-16 z-20 min-w-0 truncate bg-background px-2 py-3 text-left font-medium group-hover:bg-muted/60", identityCellClass)} title={row.nama_kantor || "-"}>{row.nama_kantor || "-"}</td>
              <td className="border-b border-border/70 p-0"><MetricCell nominal={row.nominal_closing} noa={undefined} /></td>
              <td className="border-b border-border/70 p-0"><MetricCell nominal={row.total_realisasi} noa={row.noa_realisasi} onClick={rowNumber(row, "noa_realisasi") > 0 ? () => onDetail(row, "Realisasi") : undefined} /></td>
              <td className="border-b border-border/70 p-0"><MetricCell nominal={row.total_restruck} noa={row.noa_restruck} onClick={rowNumber(row, "noa_restruck") > 0 ? () => onDetail(row, "Restrukturisasi") : undefined} /></td>
              <td className="border-b border-border/70 p-0"><MetricCell nominal={row.pelunasan} noa={row.noa_pelunasan} /></td>
              <td className="border-b border-border/70 p-0"><MetricCell nominal={row.angsuran_murni} noa={row.noa_angsuran_murni} /></td>
              <td className="border-b border-border/70 p-0"><MetricCell nominal={row.total_run_off} noa={row.noa_run_off} /></td>
              <td className="border-b border-border/70 p-0"><MetricCell nominal={row.nominal_actual} noa={undefined} /></td>
              <td className={cn("border-b border-border/70 px-2 py-1 text-right tabular-nums", rowNumber(row, "growth") < 0 ? "text-destructive" : "text-signal-green-foreground")}>{formatGrowth(row.growth)}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}
