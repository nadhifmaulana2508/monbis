import type { RefObject } from "react"
import { ReportSortHeader } from "@/components/report-sort-header"
import type { RecoveryDetailType, RecoveryFilters, RecoveryRow, RecoverySort } from "./recovery-npl-data"
import { recoveryCode, recoveryDetailHasAccess, recoveryMoney, recoveryName, recoveryNumber, recoveryPercent, recoveryRatio, recoveryMetric } from "./recovery-npl-data"

type Props = {
  rows: RecoveryRow[]
  total: RecoveryRow | null
  sort: RecoverySort
  filters: RecoveryFilters
  userCode: string
  onSort: (column: string) => void
  onDetail: (row: RecoveryRow, type: RecoveryDetailType) => void
  tableRef: RefObject<HTMLTableElement | null>
}

const codeWidth = "w-[4.25rem] min-w-[4.25rem]"
const nameWidth = "w-40 min-w-40"
const metricGroups: Array<{ label: string; noa: string; nominal: string; detailType: RecoveryDetailType | null }> = [
  { label: "Lunas NPL", noa: "noa_lunas", nominal: "baki_debet_lunas", detailType: "lunas" },
  { label: "Backflow", noa: "noa_backflow", nominal: "baki_debet_backflow", detailType: "backflow" },
  { label: "Angsuran NPL", noa: "noa_angsuran_npl", nominal: "baki_debet_angsuran_npl", detailType: "angsuran" },
  { label: "Total Recovery", noa: "total_noa_recovery", nominal: "total_recovery", detailType: "total_recovery" },
  { label: "Flow NPL", noa: "noa_flow_npl", nominal: "baki_debet_flow_npl", detailType: null },
]

function nominalValue(row: RecoveryRow, key: string) {
  if (key === "total_recovery") return Number(row.total_recovery ?? (recoveryMetric(row, "baki_debet_lunas") + recoveryMetric(row, "baki_debet_backflow") + recoveryMetric(row, "baki_debet_angsuran_npl")))
  return recoveryMetric(row, key)
}

function noaValue(row: RecoveryRow, group: typeof metricGroups[number]) {
  if (group.noa === "total_noa_recovery") return Number(row.total_noa_recovery ?? (recoveryMetric(row, "noa_lunas") + recoveryMetric(row, "noa_backflow") + recoveryMetric(row, "noa_angsuran_npl")))
  return recoveryMetric(row, group.noa)
}

export function RecoveryNplTable({ rows, total, sort, filters, userCode, onSort, onDetail, tableRef }: Props) {
  const stickyTotal = "sticky top-12 z-30 border-b border-r border-border bg-muted px-2 py-1 font-semibold tabular-nums"
  const headerCell = "sticky top-0 z-40 h-12 border-b border-r border-border bg-muted px-2 text-center text-xs font-semibold text-muted-foreground"

  const metricCell = (row: RecoveryRow, group: typeof metricGroups[number], isTotal: boolean) => {
    const nominal = nominalValue(row, group.nominal)
    const noa = noaValue(row, group)
    const allowed = group.detailType && nominal > 0 && recoveryDetailHasAccess(row, filters, userCode)
    const inner = <>
      <span className="w-full whitespace-nowrap text-right font-medium tabular-nums">{recoveryMoney(nominal)}</span>
      {noa > 0 && <span className="w-full text-right text-[0.65rem] font-normal text-muted-foreground">NOA: {recoveryNumber.format(noa)}</span>}
    </>
    const className = "flex min-h-11 w-full min-w-0 flex-col justify-center gap-0.5 px-2 py-1 text-right text-xs text-foreground"
    return <td key={group.label} className={`${isTotal ? `${stickyTotal} p-0` : "border-b border-r border-border/70 p-0"} ${!isTotal && group.label === "Total Recovery" ? "bg-primary/[0.03]" : ""}`}>
      {allowed
        ? <button type="button" className={`${className} cursor-pointer rounded-sm outline-none hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring`} onClick={() => onDetail(row, group.detailType!)} aria-label={`Buka detail ${group.label} ${recoveryName(row)}`}>{inner}</button>
        : <div className={className}>{inner}</div>}
    </td>
  }

  const renderRow = (row: RecoveryRow, isTotal: boolean, index: number) => <tr key={isTotal ? "recovery-total" : `${recoveryCode(row)}-${index}`} className={isTotal ? "report-total-row bg-muted" : "report-data-row group hover:bg-muted/25"}>
    <td className={`report-code-col ${isTotal ? `${stickyTotal} left-0 z-50` : "sticky left-0 z-20 border-b border-r border-border/70 bg-background px-2 py-2.5 font-mono text-[0.68rem] text-muted-foreground group-hover:bg-muted"} ${codeWidth} hidden text-left sm:table-cell`}>{isTotal ? "" : recoveryCode(row).padStart(filters.area.startsWith("CAB-") ? 6 : 3, "0")}</td>
    <td title={isTotal ? "TOTAL" : recoveryName(row)} className={`${isTotal ? `${stickyTotal} left-0 sm:left-[4.25rem] z-50 text-left` : "report-name-col sticky left-0 sm:left-[4.25rem] z-20 border-b border-r border-border/70 bg-background px-2 py-2.5 text-left font-medium group-hover:bg-muted"} ${nameWidth} truncate`}>{isTotal ? "TOTAL" : recoveryName(row)}</td>
    {metricGroups.map((group) => metricCell(row, group, isTotal))}
    <td className={`${isTotal ? stickyTotal : "border-b border-r border-border/70 px-2 py-2.5"} text-right`}>
      <span className={`inline-flex min-w-16 justify-end font-medium tabular-nums ${recoveryRatio(row) <= 0 ? "text-signal-green-foreground" : recoveryRatio(row) > 3 ? "text-destructive" : "text-amber-600 dark:text-amber-400"}`}>{recoveryPercent.format(recoveryRatio(row))}%</span>
    </td>
    {(() => {
      const value = recoveryMetric(row, "net_flow_recovery")
      return <td className={`${isTotal ? stickyTotal : "border-b border-r border-border/70 px-2 py-2.5"} text-right tabular-nums ${value > 0 ? "text-destructive" : value < 0 ? "text-signal-green-foreground" : "text-muted-foreground"}`}>{value > 0 ? "+" : ""}{recoveryMoney(value)}</td>
    })()}
  </tr>

  return <div className="report-table-scroll h-full overflow-auto">
    <div className="report-main-table report-table-surface min-w-[1200px] w-full text-xs">
    <table ref={tableRef} className="w-full table-fixed border-separate border-spacing-0 tabular-nums">
      <colgroup>
        <col className={`${codeWidth} hidden sm:table-column`} />
        <col className={nameWidth} />
        {metricGroups.map((group) => <col key={group.label} style={{ width: group.label === "Total Recovery" ? "144px" : "136px" }} />)}
        <col style={{ width: "104px" }} /><col style={{ width: "172px" }} />
      </colgroup>
      <thead><tr className="h-12">
        <th scope="col" className={`${headerCell} report-code-col left-0 z-50 ${codeWidth} hidden sm:table-cell`}>Kode</th>
        <th scope="col" className={`${headerCell} report-name-col left-0 sm:left-[4.25rem] z-50 ${nameWidth} text-left`}>Nama Kantor</th>
        {metricGroups.map((group) => group.label === "Total Recovery"
          ? <ReportSortHeader key={group.label} label={group.label} labelText={`${group.label} nominal`} column="total_recovery" sort={{ column: sort.column === "total_baki" ? "total_recovery" : sort.column === "total_noa" ? "total_recovery_noa" : sort.column, direction: sort.direction }} onSort={() => onSort("total_baki")} alternateSort={{ column: "total_recovery_noa", label: "Urutkan Total Recovery berdasarkan NOA", onSort: () => onSort("total_noa") }} className={`${headerCell} bg-primary/5`} buttonClassName="justify-center text-center" />
          : <th key={group.label} scope="col" className={`${headerCell} ${group.label === "Total Recovery" ? "bg-primary/5" : ""}`}>{group.label}</th>)}
        <th scope="col" className={`${headerCell} w-[104px]`}>% Flow PAR</th>
        <th scope="col" className={`${headerCell} w-[172px]`}>Nominal NPL (- / +)</th>
      </tr></thead>
      <tbody>{total && renderRow(total, true, -1)}{rows.map((row, index) => renderRow(row, false, index))}{!rows.length && <tr><td colSpan={9} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada data recovery untuk filter ini.</td></tr>}</tbody>
    </table>
    </div>
  </div>
}
