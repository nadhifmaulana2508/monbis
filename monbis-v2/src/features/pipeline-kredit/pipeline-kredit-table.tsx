import { ReportSortHeader } from "@/components/report-sort-header"
import { useReportSort } from "@/lib/use-report-sort"

import { formatPipelineCount, formatPipelineMoney } from "./pipeline-kredit-data"
import type { PipelineBreakdown, PipelineRow, PipelineSelection } from "./pipeline-kredit-types"

type Props = { rows: PipelineRow[]; breakdown: PipelineBreakdown; onDetail: (selection: PipelineSelection) => void }
const keys = ["noa_target", "plafon_closing", "noa_sudah", "nominal_sudah", "noa_lunas", "nominal_lunas", "noa_topup", "os_topup", "noa_retensi", "os_retensi", "noa_drop", "os_drop"]
const columnWidths = ["48px", "118px", "118px", "118px", "50px", "118px", "118px", "118px", "118px"]

function nameOf(row: PipelineRow, breakdown: PipelineBreakdown) { return breakdown === "CABANG" ? row.nama_kantor || row.kode_cabang || "-" : row.group_label || row.group_code || "-" }
function codeOf(row: PipelineRow, breakdown: PipelineBreakdown) { return breakdown === "CABANG" ? row.kode_cabang || "-" : row.group_code || "-" }
function num(row: PipelineRow, key: string) { return Number(row[key] ?? 0) }

function MetricCell({ value, noa, onClick, label }: { value: unknown; noa: unknown; onClick?: () => void; label: string }) {
  const content = <><span className="block text-right tabular-nums">{formatPipelineMoney(value)}</span><span className="mt-0.5 block text-right text-[0.63rem] font-normal text-muted-foreground">NOA: {formatPipelineCount(noa)}</span></>
  return onClick ? <button type="button" className="w-full px-2 py-2.5 text-right transition-colors hover:bg-primary/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset" onClick={onClick} aria-label={label}>{content}</button> : <div className="px-2 py-2.5">{content}</div>
}

function TotalCell({ value, noa }: { value: unknown; noa: unknown }) {
  return <td className="sticky top-9 z-40 border-b border-border bg-muted px-2 py-2 font-semibold"><span className="block text-right tabular-nums">{formatPipelineMoney(value)}</span><span className="mt-0.5 block text-right text-[0.63rem] font-normal text-muted-foreground">NOA: {formatPipelineCount(noa)}</span></td>
}

export function PipelineKreditTable({ rows, breakdown, onDetail }: Props) {
  const total = rows.reduce<PipelineRow>((result, row) => { keys.forEach((key) => { result[key] = num(result, key) + num(row, key) }); return result }, {})
  const identity = breakdown === "CABANG" ? "Nama Kantor" : breakdown === "KANKAS" ? "Nama Kankas" : "Nama AO"
  const selection = (row: PipelineRow, status?: string): PipelineSelection => ({ kode: String(row.kode_cabang || ""), label: nameOf(row, breakdown), breakdown, groupCode: breakdown === "CABANG" ? "" : String(row.group_code || ""), status })
  const detail = (row: PipelineRow, status?: string) => () => onDetail(selection(row, status))
  const percent = num(total, "plafon_closing") ? num(total, "nominal_sudah") / num(total, "plafon_closing") * 100 : 0
  const sortKeys = ["code", "name", "plafon_closing", "nominal_sudah", "percent", "nominal_lunas", "os_topup", "os_retensi", "os_drop"]
  const labels = ["Kode", identity, "Target JT", "Refinancing / Top Up", "%", "Lunas", "Top Up ≤ 50%", "Retensi > 50%", "Drop"]
  const { sortedRows, sortKey, direction, toggleSort } = useReportSort(rows, (row, key) => key === "code" ? codeOf(row, breakdown) : key === "name" ? nameOf(row, breakdown) : key === "percent" ? (num(row, "plafon_closing") ? num(row, "nominal_sudah") / num(row, "plafon_closing") * 100 : 0) : row[key], "code")

  return <div className="min-w-[906px] text-[0.7rem] sm:text-xs"><table className="w-full table-fixed border-separate border-spacing-0 font-normal"><colgroup>{columnWidths.map((width, index) => <col key={index} style={{ width }} />)}</colgroup><thead><tr className="h-9 text-center text-[0.68rem] font-normal text-muted-foreground">{labels.map((label, index) => <ReportSortHeader key={label} label={label} labelText={label} column={sortKeys[index]} sort={{ column: sortKey, direction }} onSort={toggleSort} className={`sticky top-0 h-9 border-b border-r border-border bg-muted px-2 py-1.5 text-center ${index === 0 ? "left-0 z-50" : index === 1 ? "left-12 z-50" : "z-40"}`} buttonClassName="justify-center text-center" />)}</tr></thead><tbody>
    <tr><td className="sticky left-0 top-9 z-50 border-b border-r border-border bg-muted px-1 py-2 text-center text-muted-foreground">ALL</td><td className="sticky left-12 top-9 z-50 border-b border-r border-border bg-muted px-2 py-2 font-semibold">TOTAL</td><TotalCell value={total.plafon_closing} noa={total.noa_target} /><TotalCell value={total.nominal_sudah} noa={total.noa_sudah} /><td className="sticky top-9 z-40 border-b border-r border-border bg-muted px-2 py-2 text-right font-semibold tabular-nums">{percent.toFixed(2).replace(".", ",")} %</td><TotalCell value={total.nominal_lunas} noa={total.noa_lunas} /><TotalCell value={total.os_topup} noa={total.noa_topup} /><TotalCell value={total.os_retensi} noa={total.noa_retensi} /><TotalCell value={total.os_drop} noa={total.noa_drop} /></tr>
    {sortedRows.map((row, index) => { const rowPercent = num(row, "plafon_closing") ? num(row, "nominal_sudah") / num(row, "plafon_closing") * 100 : 0; return <tr key={`${codeOf(row, breakdown)}-${index}`} className="group hover:bg-muted/40"><td className="sticky left-0 z-20 border-b border-border/70 bg-background px-1 py-2.5 text-center font-mono text-[0.68rem] text-muted-foreground group-hover:bg-muted/40">{codeOf(row, breakdown)}</td><td className="sticky left-12 z-20 border-b border-r border-border/70 bg-background px-2 py-2.5 group-hover:bg-muted/40" title={nameOf(row, breakdown)}>{nameOf(row, breakdown)}</td><td className="border-b border-border/70"><MetricCell value={row.plafon_closing} noa={row.noa_target} label="Lihat detail target" /></td><td className="border-b border-border/70"><MetricCell value={row.nominal_sudah} noa={row.noa_sudah} onClick={detail(row, "sudah")} label="Lihat detail refinancing" /></td><td className="border-b border-border/70 px-1 py-2.5 text-right tabular-nums">{rowPercent.toFixed(2).replace(".", ",")}%</td><td className="border-b border-border/70"><MetricCell value={row.nominal_lunas} noa={row.noa_lunas} onClick={detail(row, "lunas")} label="Lihat detail lunas" /></td><td className="border-b border-border/70"><MetricCell value={row.os_topup} noa={row.noa_topup} onClick={detail(row, "topup")} label="Lihat detail top up" /></td><td className="border-b border-border/70"><MetricCell value={row.os_retensi} noa={row.noa_retensi} onClick={detail(row, "retensi")} label="Lihat detail retensi" /></td><td className="border-b border-border/70"><MetricCell value={row.os_drop} noa={row.noa_drop} onClick={detail(row, "drop")} label="Lihat detail drop" /></td></tr> })}
  </tbody></table></div>
}
