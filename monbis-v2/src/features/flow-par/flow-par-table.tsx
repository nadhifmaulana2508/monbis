import type { RefObject } from "react"
import { ReportSortHeader } from "@/components/report-sort-header"
import type { FlowParDetailSelection, FlowParFilters, FlowParRow, FlowParSort } from "./flow-par-data"
import { flowParCategories, flowParCode, flowParName, flowParNumber, flowParValue } from "./flow-par-data"

type Props = { rows: FlowParRow[]; total: FlowParRow | null; sort: FlowParSort; filters: FlowParFilters; onSort: (column: string) => void; onDetail: (selection: FlowParDetailSelection) => void; desktopRef: RefObject<HTMLTableElement | null>; mobileRef: RefObject<HTMLTableElement | null> }

const totalCell = "sticky top-9 z-30 border-b border-r border-border bg-muted/95 px-2 py-1.5 font-semibold"

function metricDetail(kode: string, korwil: string, kind: string, label: string): FlowParDetailSelection { return { kode, korwil, klasifikasi: kind, label } }
function MetricCell({ row, noaKey, nominalKey, selection, onDetail, total = false, stacked = false }: { row: FlowParRow; noaKey: string; nominalKey: string; selection: FlowParDetailSelection; onDetail: (selection: FlowParDetailSelection) => void; total?: boolean; stacked?: boolean }) {
  const noa = flowParValue(row, noaKey)
  const value = <><span className="block tabular-nums">{flowParNumber.format(flowParValue(row, nominalKey))}</span><span className="mt-0.5 block text-[0.63rem] font-normal text-muted-foreground">{flowParNumber.format(noa)} NOA</span></>
  return <td className={`border-b border-r border-border px-1 py-1 text-right ${total ? `sticky ${stacked ? "top-12" : "top-9"} z-30 bg-muted/95 font-semibold` : ""}`}>
    {noa > 0 ? <button type="button" onClick={() => onDetail(selection)} aria-label={`Lihat ${selection.label}, ${flowParNumber.format(noa)} NOA`} className={`min-h-10 w-full rounded-sm px-1 text-right outline-none hover:bg-primary/10 focus-visible:ring-2 focus-visible:ring-ring ${stacked ? "text-xs" : ""}`}>{value}</button> : <span className="px-2 text-muted-foreground">-</span>}
  </td>
}

export function FlowParTable({ rows, total, sort, filters, onSort, onDetail, desktopRef, mobileRef }: Props) {
  const totalTarget = filters.area.startsWith("CAB-") ? filters.area.slice(4) : "000"
  const totalKorwil = filters.area.startsWith("KOR-") ? filters.area.slice(4) : ""
  const selectionFor = (row: FlowParRow, category: typeof flowParCategories[number] | null, isTotal = false) => {
    const rowCode = isTotal ? totalTarget : flowParCode(row)
    return metricDetail(rowCode, isTotal ? totalKorwil : "", category?.kind || "", category?.label || "Total Flow")
  }
  const sortClass = "sticky top-0 z-40 h-10 border-b border-r border-border bg-muted px-1 py-1 text-center text-[0.66rem] font-semibold text-muted-foreground"

  return <div className="report-table-scroll h-full overflow-auto">
    <div className="hidden min-w-[1050px] md:block"><table ref={desktopRef} className="w-full table-fixed border-separate border-spacing-0 text-xs tabular-nums">
      <colgroup><col style={{ width: 48 }} /><col style={{ width: 150 }} />{flowParCategories.map((item) => <col key={item.kind} style={{ width: 160 }} />)}<col style={{ width: 170 }} /></colgroup>
      <thead><tr className="h-10">
        <ReportSortHeader label="KODE" labelText="Kode" column="kode" sort={sort} onSort={onSort} className={`${sortClass} left-0 z-50`} buttonClassName="justify-center" />
        <ReportSortHeader label="NAMA KANTOR" labelText="Nama Kantor" column="nama" sort={sort} onSort={onSort} className={`${sortClass} left-12 z-50 text-left`} buttonClassName="justify-start text-left" />
        {flowParCategories.map((category) => <th key={category.kind} scope="col" className={`${sortClass} px-2`}>{category.label}</th>)}
        <ReportSortHeader label="TOTAL FLOW" labelText="Total Flow" column="baki" sort={sort} onSort={onSort} className={sortClass} buttonClassName="justify-center" />
      </tr></thead>
      <tbody>{total && <tr><td className={`${totalCell} left-0 z-50 text-center`}>ALL</td><td className={`${totalCell} left-12 z-50`}>TOTAL</td>{flowParCategories.map((category) => <FragmentCells key={category.kind} row={total} category={category} selection={selectionFor(total, category, true)} onDetail={onDetail} total />)}<MetricCell row={total} noaKey="noa_flow" nominalKey="baki_debet_flow" selection={selectionFor(total, null, true)} onDetail={onDetail} total /></tr>}
        {rows.map((row, index) => <tr key={`${flowParCode(row)}-${index}`} className="group hover:bg-muted/30"><td className="sticky left-0 z-20 border-b border-r border-border/70 bg-background px-1 py-2.5 text-center font-mono text-[0.68rem] text-muted-foreground group-hover:bg-muted/50">{flowParCode(row)}</td><td title={flowParName(row)} className="sticky left-12 z-20 truncate border-b border-r border-border/70 bg-background px-2 py-2.5 text-left font-medium group-hover:bg-muted/50">{flowParName(row)}</td>{flowParCategories.map((category) => <FragmentCells key={category.kind} row={row} category={category} selection={selectionFor(row, category)} onDetail={onDetail} />)}<MetricCell row={row} noaKey="noa_flow" nominalKey="baki_debet_flow" selection={selectionFor(row, null)} onDetail={onDetail} /></tr>)}
        {!rows.length && <tr><td colSpan={7} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada data Flow PAR pada filter ini.</td></tr>}
      </tbody>
    </table></div>
    <div className="min-w-[670px] md:hidden"><table ref={mobileRef} className="report-keep-first-col-mobile w-full table-fixed border-separate border-spacing-0 text-[0.68rem] tabular-nums">
      <colgroup><col style={{ width: 160 }} />{[1, 2, 3, 4].map((i) => <col key={i} style={{ width: 112 }} />)}<col style={{ width: 120 }} /></colgroup>
      <thead><tr className="sticky top-0 z-40 h-12 bg-muted text-center text-[0.62rem] font-semibold text-muted-foreground"><ReportSortHeader label="NAMA KANTOR" labelText="Nama Kantor" column="nama" sort={sort} onSort={onSort} className="flow-par-mobile-name report-sticky-name sticky left-0 z-50 border-b border-r bg-muted text-left" buttonClassName="justify-start text-left" />{flowParCategories.map((category) => <th key={category.kind} className="border-b border-r px-1 py-1">{category.label}</th>)}<ReportSortHeader label="TOTAL FLOW" labelText="Total Flow" column="baki" sort={sort} onSort={onSort} className="border-b bg-muted" buttonClassName="justify-center text-center" /></tr></thead>
      <tbody>{total && <tr><td className="flow-par-mobile-name report-sticky-name sticky left-0 top-12 z-50 border-b border-r bg-muted px-2 py-2 font-bold">TOTAL</td>{flowParCategories.map((category) => <MetricCell key={category.kind} row={total} noaKey={category.noa} nominalKey={category.nominal} selection={selectionFor(total, category, true)} onDetail={onDetail} total stacked />)}<MetricCell row={total} noaKey="noa_flow" nominalKey="baki_debet_flow" selection={selectionFor(total, null, true)} onDetail={onDetail} total stacked /></tr>}{rows.map((row, index) => <tr key={`${flowParCode(row)}-${index}`} className="group hover:bg-muted/30"><td title={flowParName(row)} className="flow-par-mobile-name report-sticky-name sticky left-0 z-20 w-40 min-w-40 truncate border-b border-r bg-background px-2 py-2.5 text-left font-semibold group-hover:bg-muted/50">{flowParName(row)}</td>{flowParCategories.map((category) => <MetricCell key={category.kind} row={row} noaKey={category.noa} nominalKey={category.nominal} selection={selectionFor(row, category)} onDetail={onDetail} stacked />)}<MetricCell row={row} noaKey="noa_flow" nominalKey="baki_debet_flow" selection={selectionFor(row, null)} onDetail={onDetail} stacked /> </tr>)}{!rows.length && <tr><td colSpan={6} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada data Flow PAR.</td></tr>}</tbody>
    </table></div>
  </div>
}

function FragmentCells({ row, category, selection, onDetail, total = false }: { row: FlowParRow; category: typeof flowParCategories[number]; selection: FlowParDetailSelection; onDetail: (selection: FlowParDetailSelection) => void; total?: boolean }) { return <MetricCell row={row} noaKey={category.noa} nominalKey={category.nominal} selection={selection} onDetail={onDetail} total={total} /> }
