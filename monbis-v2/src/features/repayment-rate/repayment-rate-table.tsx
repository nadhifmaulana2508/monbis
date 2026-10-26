import type { RefObject } from "react"
import { GroupedReportTableHead } from "@/components/grouped-report-table-head"
import type { RrRow, RrSort } from "./repayment-rate-data"
import { rrMoney, rrNumber, rrPercent } from "./repayment-rate-data"

const groups = [
  { label: "Closing · M-1", columns: [{ key: "m1_lancar_os", label: "Nominal lancar" }, { key: "m1_all_noa", label: "NOA" }, { key: "m1_pct", label: "RR %" }] },
  { label: "Actual", columns: [{ key: "cur_lancar_os", label: "Nominal lancar" }, { key: "cur_all_noa", label: "NOA" }, { key: "cur_pct", label: "RR %" }] },
  { label: "Migrasi / perubahan RR", columns: [{ key: "delta_os_lancar", label: "Nominal migrasi" }, { key: "delta_noa", label: "NOA migrasi" }, { key: "delta_pct", label: "Perubahan %" }] },
]
const codeWidth = "w-16 min-w-16"
const nameWidth = "w-44 min-w-44"

export function RepaymentRateTable({ rows, total, sort, onSort, onDetail, tableRef, labelCode, labelName }: { rows: RrRow[]; total: RrRow | null; sort: RrSort; onSort: (key: string) => void; onDetail: (row: RrRow, status: "ALL" | "MIGRASI") => void; tableRef: RefObject<HTMLTableElement | null>; labelCode: string; labelName: string }) {
  const stickyTotal = "sticky top-[4.5rem] z-30 border-b border-r border-border bg-muted/95 px-2 py-1.5 font-semibold tabular-nums"
  const metricCell = (row: RrRow, key: string, kind: "money" | "count" | "percent", detailStatus?: "ALL" | "MIGRASI") => {
    const raw = Number(row[key] ?? 0)
    const value = kind === "money" ? rrMoney(raw) : kind === "count" ? rrNumber.format(raw) : `${rrPercent.format(raw)}%`
    const isDelta = key === "delta_os_lancar" || key === "delta_noa" || key === "delta_pct"
    const color = isDelta
      ? raw < 0
        ? key === "delta_pct" ? "text-destructive" : "text-signal-green-foreground"
        : raw > 0
          ? key === "delta_pct" ? "text-signal-green-foreground" : "text-destructive"
          : ""
      : ""
    return <td key={key} className={`border-b border-r border-border/70 px-2 py-1.5 text-right tabular-nums ${color}`}>
      {detailStatus ? <button type="button" onClick={() => onDetail(row, detailStatus)} className="min-h-8 w-full rounded-sm text-right font-medium outline-none hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring" aria-label={`Lihat detail ${kind} ${String(row.nama || "total")}`}>{value}</button> : value}
    </td>
  }

  return <div className="report-table-scroll h-full overflow-auto"><table ref={tableRef} className="w-full min-w-[1000px] table-fixed border-separate border-spacing-0 text-xs tabular-nums">
    <colgroup><col className={codeWidth} /><col className={nameWidth} />{groups.flatMap((group) => group.columns.map((column) => <col key={column.key} style={{ width: column.key.endsWith("pct") ? "88px" : column.key.includes("noa") ? "86px" : "136px" }} />))}</colgroup>
    <GroupedReportTableHead identityColumns={[{ key: "kode", label: labelCode, className: codeWidth }, { key: "nama", label: labelName, className: nameWidth }]} groups={groups} sort={sort} onSort={onSort} />
    <tbody>
      {total && <tr className="report-total-row bg-muted/80"><td className={`${stickyTotal} left-0 z-50 text-left`}>ALL</td><td className={`${stickyTotal} left-16 z-50 text-left`}>GRAND TOTAL</td>
        {metricCell(total, "m1_lancar_os", "money")}{metricCell(total, "m1_all_noa", "count")}{metricCell(total, "m1_pct", "percent")}
        {metricCell(total, "cur_lancar_os", "money", "ALL")}{metricCell(total, "cur_all_noa", "count", "ALL")}{metricCell(total, "cur_pct", "percent", "ALL")}
        {metricCell(total, "delta_os_lancar", "money", "MIGRASI")}{metricCell(total, "delta_noa", "count", "MIGRASI")}{metricCell(total, "delta_pct", "percent", "MIGRASI")}
      </tr>}
      {rows.map((row, index) => <tr key={`${String(row.kode)}-${index}`} className="group hover:bg-muted/25">
        <td className="sticky left-0 z-20 border-b border-r border-border/70 bg-background px-2 py-2.5 font-mono text-[0.68rem] text-muted-foreground group-hover:bg-muted/50">{String(row.kode ?? "-")}</td>
        <td title={String(row.nama ?? "-")} className="sticky left-16 z-20 max-w-44 truncate border-b border-r border-border/70 bg-background px-2 py-2.5 text-left font-medium group-hover:bg-muted/50">{String(row.nama ?? "-")}</td>
        {metricCell(row, "m1_lancar_os", "money")}{metricCell(row, "m1_all_noa", "count")}{metricCell(row, "m1_pct", "percent")}
        {metricCell(row, "cur_lancar_os", "money", "ALL")}{metricCell(row, "cur_all_noa", "count", "ALL")}{metricCell(row, "cur_pct", "percent", "ALL")}
        {metricCell(row, "delta_os_lancar", "money", "MIGRASI")}{metricCell(row, "delta_noa", "count", "MIGRASI")}{metricCell(row, "delta_pct", "percent", "MIGRASI")}
      </tr>)}
      {!rows.length && <tr><td colSpan={11} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada data pada filter ini.</td></tr>}
    </tbody>
  </table></div>
}
