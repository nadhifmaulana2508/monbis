import type { RefObject } from "react"
import { ReportSortHeader } from "@/components/report-sort-header"
import { useReportSort } from "@/lib/use-report-sort"
import { cn } from "@/lib/utils"

import { formatJtCount, formatJtMoney, formatJtPercent } from "./jatuh-tempo-data"
import type { JtBreakdown, JtCategory, JtDetailSelection, JtReportRow } from "./jatuh-tempo-types"

type Props = { rows: JtReportRow[]; grandTotal: JtReportRow | null; breakdown: JtBreakdown; onDetail: (selection: JtDetailSelection) => void; tableRef: RefObject<HTMLTableElement | null> }
const keys = ["noa_potensi", "plafon_potensi", "noa_refinancing", "plafon_refinancing", "noa_lunas", "plafon_lunas", "sisa_belum_lunas", "noa_belum_lunas", "plafon_belum_lunas"]
const columns = [
  { key: "code", label: "Kode" }, { key: "name", label: "Nama Kantor" },
  { key: "plafon_potensi", label: "Potensi Kredit" }, { key: "plafon_refinancing", label: "Refinancing" },
  { key: "persentase", label: "%" }, { key: "plafon_lunas", label: "Lunas (Prospek)" },
  { key: "sisa_belum_lunas", label: "Belum Lunas · Sisa saldo" }, { key: "plafon_belum_lunas", label: "Belum Lunas · Nominal" },
]

function valueOf(row: JtReportRow, key: string) { return Number(row[key] ?? 0) }
function rowLabel(row: JtReportRow, breakdown: JtBreakdown) { return breakdown === "CABANG" ? row.nama_kantor || "-" : row.group_label || row.nama_kantor || "-" }
function makeSelection(row: JtReportRow, breakdown: JtBreakdown, category: JtCategory): JtDetailSelection { return { kode: String(row.kode_kantor || ""), label: rowLabel(row, breakdown), groupType: breakdown, groupCode: breakdown === "CABANG" ? "" : String(row.group_code || ""), category } }

function MetricCell({ value, noa, onClick, label }: { value: unknown; noa: unknown; onClick?: () => void; label: string }) {
  const content = <><span className="block text-right tabular-nums">{formatJtMoney(value)}</span><span className="mt-0.5 block text-right text-[0.63rem] font-normal text-muted-foreground">NOA: {formatJtCount(noa)}</span></>
  return onClick ? <button type="button" className="w-full px-2 py-2.5 text-right transition-colors hover:bg-foreground/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset" onClick={onClick} aria-label={label}>{content}</button> : <div className="px-2 py-2.5">{content}</div>
}

function TotalCell({ value, noa }: { value: unknown; noa: unknown }) { return <td className="sticky top-12 z-50 border-b border-border bg-muted p-0 font-semibold"><MetricCell value={value} noa={noa} label="" /></td> }

export function JatuhTempoTable({ rows, grandTotal, breakdown, onDetail, tableRef }: Props) {
  const total = grandTotal || rows.reduce<JtReportRow>((result, row) => { keys.forEach((key) => { result[key] = valueOf(result, key) + valueOf(row, key) }); return result }, {})
  const percentage = valueOf(total, "plafon_potensi") > 0 ? valueOf(total, "plafon_refinancing") / valueOf(total, "plafon_potensi") * 100 : 0
  const identity = breakdown === "AO" ? "Nama AO" : breakdown === "KANKAS" ? "Nama Kankas" : "Nama Kantor"
  const selection = (row: JtReportRow, category: JtCategory) => () => onDetail(makeSelection(row, breakdown, category))
  const { sortedRows, sortKey, direction, toggleSort } = useReportSort(rows, (row, key) => {
    if (key === "code") return row.kode_kantor || row.group_code
    if (key === "name") return row.nama_kantor || row.group_label
    if (key === "persentase") return valueOf(row, "plafon_potensi") ? valueOf(row, "plafon_refinancing") / valueOf(row, "plafon_potensi") * 100 : 0
    return row[key]
  }, "code")
  const widths = ["52px", "140px", "112px", "112px", "56px", "112px", "112px", "112px"]

  return <div className="min-w-[808px] text-xs sm:text-sm"><table ref={tableRef} className="w-full table-fixed border-separate border-spacing-0 font-normal"><colgroup>{widths.map((width, index) => <col key={index} className={index === 0 ? "report-code-col" : index === 1 ? "report-name-col" : undefined} style={{ width }} />)}</colgroup><thead className="report-table-head"><tr className="h-12 text-center text-[0.68rem] font-semibold tracking-tight">{columns.map((column, index) => <ReportSortHeader key={column.key} label={index === 1 ? identity : column.label} labelText={column.label} column={column.key} sort={{ column: sortKey, direction }} onSort={toggleSort} className={cn("sticky top-0 z-40 border-b border-r border-border bg-muted px-2 py-1.5 text-center", index < 2 && "text-left", index === 0 && "report-code-col left-0 z-70", index === 1 && "report-name-col left-[52px] z-70")} buttonClassName="justify-center text-center" />)}</tr></thead><tbody>
    <tr className="report-total-row"><td className="report-code-col sticky left-0 top-12 z-70 border-b border-r border-border bg-muted px-1 py-2 text-center text-muted-foreground">ALL</td><td className="report-name-col sticky left-[52px] top-12 z-70 border-b border-r border-border bg-muted px-2 py-2 text-left font-semibold">TOTAL</td><TotalCell value={total.plafon_potensi} noa={total.noa_potensi} /><TotalCell value={total.plafon_refinancing} noa={total.noa_refinancing} /><td className="sticky top-12 z-50 border-b border-r border-border bg-muted px-2 py-2 text-right font-semibold tabular-nums">{formatJtPercent(percentage)}</td><TotalCell value={total.plafon_lunas} noa={total.noa_lunas} /><TotalCell value={total.sisa_belum_lunas} noa={total.noa_belum_lunas} /><TotalCell value={total.plafon_belum_lunas} noa={total.noa_belum_lunas} /></tr>
    {sortedRows.map((row, index) => { const label = rowLabel(row, breakdown); const percent = valueOf(row, "plafon_potensi") > 0 ? valueOf(row, "plafon_refinancing") / valueOf(row, "plafon_potensi") * 100 : 0; return <tr key={`${row.kode_kantor}-${row.group_code || "root"}-${index}`} className="report-data-row group hover:bg-muted/40"><td className="report-code-col sticky left-0 z-20 border-b border-r border-border/70 bg-background px-1 py-2.5 text-center font-mono text-[0.68rem] text-muted-foreground">{row.kode_kantor || row.group_code || "-"}</td><td className="report-name-col sticky left-[52px] z-20 border-b border-r border-border/70 bg-background px-2 py-2.5 text-left" title={label}>{label}</td><td className="border-b border-border/70 p-0"><MetricCell value={row.plafon_potensi} noa={row.noa_potensi} onClick={selection(row, "POTENSI")} label="Lihat detail Potensi Kredit" /></td><td className="border-b border-border/70 p-0"><MetricCell value={row.plafon_refinancing} noa={row.noa_refinancing} onClick={selection(row, "REFINANCING")} label="Lihat detail Refinancing" /></td><td className="border-b border-border/70 px-1 py-2.5 text-right tabular-nums">{formatJtPercent(percent)}</td><td className="border-b border-border/70 p-0"><MetricCell value={row.plafon_lunas} noa={row.noa_lunas} onClick={selection(row, "LUNAS")} label="Lihat detail Lunas" /></td><td className="border-b border-border/70 p-0"><MetricCell value={row.sisa_belum_lunas} noa={row.noa_belum_lunas} onClick={selection(row, "BELUM_LUNAS")} label="Lihat detail sisa saldo" /></td><td className="border-b border-border/70 p-0"><MetricCell value={row.plafon_belum_lunas} noa={row.noa_belum_lunas} onClick={selection(row, "BELUM_LUNAS")} label="Lihat detail Belum Lunas" /></td></tr> })}
  </tbody></table></div>
}
