import type { RefObject } from "react"
import { ReportSortHeader } from "@/components/report-sort-header"
import type { PotensiFilters, PotensiRow, PotensiSort } from "./potensi-npl-data"
import { potensiCode, potensiMetric, potensiMoney, potensiName, potensiNumber } from "./potensi-npl-data"

type Props = { rows: PotensiRow[]; total: PotensiRow | null; sort: PotensiSort; filters: PotensiFilters; tableRef: RefObject<HTMLTableElement | null>; onSort: (key: string) => void; onDetail: (office: string, status: string, kankas?: string) => void }
const metrics = [
  { title: "Total Potensi NPL", noa: "total_noa", amount: "total_baki", tone: "bg-primary/[0.045]", status: "ALL" },
  { title: "Aman / Lunas", noa: "noa_aman", amount: "baki_aman", tone: "bg-signal-green/[0.045]", status: "AMAN" },
  { title: "Jatuh Tempo", noa: "noa_jt", amount: "baki_jt", tone: "bg-amber-500/[0.045]", status: "JATUH TEMPO" },
  { title: "Flow (KL/D/M)", noa: "noa_flow", amount: "baki_flow", tone: "bg-destructive/[0.04]", status: "FLOW KOLEK" },
  { title: "Masih Potensi", noa: "noa_potensi", amount: "baki_potensi", tone: "bg-orange-500/[0.045]", status: "MASIH POTENSI" },
]

export function PotensiNplTable({ rows, total, sort, filters, tableRef, onSort, onDetail }: Props) {
  const header = "sticky top-0 z-40 h-12 border-b border-r border-border bg-muted px-2 text-center text-xs font-semibold text-muted-foreground"
  const totalCell = "sticky top-12 z-30 border-b border-r border-border bg-muted px-2 py-1 font-semibold"
  const metricCell = (row: PotensiRow, metric: typeof metrics[number], isTotal: boolean, office: string, kankas?: string) => {
    const noa = potensiMetric(row, metric.noa)
    const amount = potensiMetric(row, metric.amount)
    const content = <>
      <span className="w-full whitespace-nowrap text-right font-medium tabular-nums">{potensiMoney(amount)}</span>
      {noa > 0 && <span className="w-full text-right text-[0.65rem] font-normal text-muted-foreground">NOA: {potensiNumber.format(noa)}</span>}
    </>
    const cellClass = `flex min-h-11 w-full min-w-0 flex-col justify-center gap-0.5 px-2 py-1 text-right text-xs text-foreground ${!isTotal && noa > 0 ? "cursor-pointer rounded-sm outline-none hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring" : ""}`
    return <td key={metric.title} className={`${isTotal ? `${totalCell} p-0` : "border-b border-r border-border/70 p-0"} ${!isTotal ? metric.tone : ""}`}>
      {noa > 0 ? <button type="button" className={cellClass} onClick={() => onDetail(office, metric.status, kankas)} aria-label={`Buka detail ${potensiNumber.format(noa)} ${metric.status}`}>
        {content}
      </button> : <div className={cellClass}>{content}</div>}
    </td>
  }
  const renderRow = (row: PotensiRow, isTotal: boolean, index: number) => {
    const code = isTotal ? (filters.area.startsWith("CAB-") ? filters.area.slice(4) : filters.area || "000") : potensiCode(row)
    const branch = isTotal ? (filters.area.startsWith("CAB-") ? filters.area.slice(4) : "000") : code.length > 3 ? code.slice(0, 3) : code
    const kankas = !isTotal && code.length > 3 ? code : undefined
    const name = isTotal ? "TOTAL" : potensiName(row)
    return <tr key={isTotal ? "potensi-total" : `${code}-${index}`} className={isTotal ? "report-total-row bg-muted" : "report-data-row group hover:bg-muted/25"}>
      <td className={`${isTotal ? `${totalCell} left-0 z-50` : "sticky left-0 z-20 border-b border-r border-border/70 bg-background px-2 py-2.5 font-mono text-[0.68rem] text-muted-foreground group-hover:bg-muted"} hidden w-[4.25rem] min-w-[4.25rem] text-center sm:table-cell`}>{isTotal ? "ALL" : code}</td>
      <td title={name} className={`${isTotal ? `${totalCell} left-0 z-50 sm:left-[4.25rem]` : "report-name-col sticky left-0 z-20 border-b border-r border-border/70 bg-background px-2 py-2.5 font-medium group-hover:bg-muted"} w-40 min-w-40 truncate text-left`}>{name}</td>
      {metrics.map((metric) => metricCell(row, metric, isTotal, branch, kankas))}
    </tr>
  }
  const totalSort = { column: sort.column === "total_noa" ? "potensi_noa" : sort.column, direction: sort.direction }
  return <div className="report-table-scroll h-full overflow-auto"><div className="report-main-table report-table-surface min-w-[800px] w-full text-xs">
    <table ref={tableRef} className="w-full table-fixed border-separate border-spacing-0 tabular-nums">
      <colgroup><col className="hidden w-[4.25rem] sm:table-column"/><col className="w-40"/>{metrics.map((metric) => <col key={metric.title} className="w-[8rem]"/>)}</colgroup>
      <thead className="report-table-head"><tr>
        <th scope="col" className={`${header} left-0 z-50 hidden w-[4.25rem] sm:table-cell`}>Kode</th>
        <th scope="col" className={`${header} report-name-col left-0 z-50 w-40 text-left sm:left-[4.25rem]`}>Nama Kantor</th>
        {metrics.map((metric) => metric.title === "Total Potensi NPL"
          ? <ReportSortHeader key={metric.title} label={metric.title} labelText={`${metric.title} nominal`} column="total_baki" sort={totalSort} onSort={() => onSort("total_baki")} alternateSort={{ column: "potensi_noa", label: "Urutkan Total Potensi NPL berdasarkan NOA", onSort: () => onSort("total_noa") }} className={`${header} ${metric.tone}`} buttonClassName="justify-center text-center" />
          : <th key={metric.title} scope="col" className={`${header} ${metric.tone}`}>{metric.title}</th>)}
      </tr></thead>
      <tbody>{total && renderRow(total, true, -1)}{rows.map((row, index) => renderRow(row, false, index))}{!rows.length && <tr><td colSpan={7} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada data pada filter ini.</td></tr>}</tbody>
    </table>
  </div></div>
}
