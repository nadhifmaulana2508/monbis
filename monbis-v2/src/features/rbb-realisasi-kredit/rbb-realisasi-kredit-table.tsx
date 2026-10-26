import type { RefObject } from "react"
import { ReportSortHeader } from "@/components/report-sort-header"
import type { RbbRow, RbbSort } from "./rbb-realisasi-kredit-data"
import { monthLabel, nominal, percent, signedNominal } from "./rbb-realisasi-kredit-data"

type Column = { key: string; label: string; kind?: "money" | "percent" | "signed" | "month" }
type Props = { rows: RbbRow[]; total: RbbRow | null; monthly: boolean; history: boolean; hideName: boolean; meta: Record<string, unknown>; sort: RbbSort; onSort: (key: string) => void; tableRef: RefObject<HTMLTableElement | null> }

export function RbbRealisasiKreditTable({ rows, total, monthly, history, hideName, meta, sort, onSort, tableRef }: Props) {
  const year = String(meta.tahun || "Tahun Ini")
  const previousYear = String(meta.tahun_pembanding || "Tahun Lalu")
  const identity: Column[] = monthly ? [{ key: "periode", label: "Bulan", kind: "month" }] : [{ key: "kode_kantor", label: "Kode" }]
  const office: Column[] = hideName ? [] : [{ key: "nama_kantor", label: monthly ? "Kantor" : "Nama Kantor" }]
  const dataColumns: Column[] = history ? [
    { key: "realisasi_bulan_ini", label: `Realisasi ${year}`, kind: "money" }, { key: "run_off", label: "Run Off", kind: "money" },
    { key: "growth", label: "Growth", kind: "signed" }, { key: "growth_persen", label: "% Growth", kind: "percent" },
    { key: "realisasi_tahun_lalu", label: `Realisasi ${previousYear}`, kind: "money" }, { key: "selisih", label: "Selisih YoY", kind: "signed" }, { key: "yoy_persen", label: "% YoY", kind: "percent" },
  ] : monthly ? [
    { key: "nilai_rbb", label: "Nilai RBB", kind: "money" }, { key: "realisasi_bulan_ini", label: "Realisasi", kind: "money" },
    { key: "persentase_rbb_bulan_ini", label: "% RBB", kind: "percent" }, { key: "selisih", label: "Selisih", kind: "signed" }, { key: "kekurangan", label: "Kekurangan", kind: "money" },
  ] : [
    { key: "nilai_rbb", label: "Nilai RBB", kind: "money" }, { key: "realisasi_bulan_ini", label: "Realisasi Bulan Ini", kind: "money" },
    { key: "persentase_rbb_bulan_ini", label: "% RBB", kind: "percent" }, { key: "kekurangan_sd_bulan_lalu", label: "Kekurangan s/d Bulan Lalu", kind: "money" },
    { key: "total_beban_target", label: "Total Beban Target", kind: "money" }, { key: "persentase_rbb_plus_kekurangan", label: "% Beban", kind: "percent" },
  ]
  const columns = [...identity, ...office, ...dataColumns]
  const monthlyTotals = monthly ? rows.reduce<RbbRow>((sum, row) => {
    for (const key of ["nilai_rbb", "realisasi_bulan_ini", "selisih", "kekurangan", "run_off", "growth", "realisasi_tahun_lalu"]) sum[key] = Number(sum[key] ?? 0) + Number(row[key] ?? 0)
    return sum
  }, {}) : null
  const totalRow = !monthly && !history && hideName ? null : monthly && !history ? {
    ...monthlyTotals,
    persentase_rbb_bulan_ini: Number(monthlyTotals?.nilai_rbb) ? Number(monthlyTotals?.realisasi_bulan_ini) / Number(monthlyTotals?.nilai_rbb) * 100 : 0,
  } : monthly && history ? {
    ...monthlyTotals,
    growth_persen: Number(monthlyTotals?.run_off) ? Number(monthlyTotals?.growth) / Number(monthlyTotals?.run_off) * 100 : 0,
    selisih: Number(monthlyTotals?.realisasi_bulan_ini) - Number(monthlyTotals?.realisasi_tahun_lalu),
    yoy_persen: Number(monthlyTotals?.realisasi_tahun_lalu) ? (Number(monthlyTotals?.realisasi_bulan_ini) - Number(monthlyTotals?.realisasi_tahun_lalu)) / Number(monthlyTotals?.realisasi_tahun_lalu) * 100 : 0,
  } : total

  const cellValue = (row: RbbRow, column: Column) => {
    const value = row[column.key]
    if (column.kind === "month") return monthLabel(value)
    if (column.kind === "percent") return percent(value)
    if (column.kind === "signed") return signedNominal(value)
    if (column.kind === "money") return nominal(value)
    if (column.key === "kode_kantor") return String(value ?? "")
    return String(value ?? "-")
  }

  return <div className="min-w-[900px] text-xs sm:min-w-[1040px]"><table ref={tableRef} className="w-full table-fixed border-separate border-spacing-0">
    <colgroup>{columns.map((column) => <col key={column.key} style={{ width: column.key === "kode_kantor" ? "56px" : column.key === "periode" ? "108px" : column.key === "nama_kantor" ? "180px" : column.kind === "percent" ? "92px" : undefined }} />)}</colgroup>
    <thead><tr>{columns.map((column) => <ReportSortHeader key={column.key} label={column.label} labelText={column.label} column={column.key} sort={sort} onSort={onSort} className={`sticky top-0 z-40 h-10 border-b border-r border-border bg-muted px-2 text-center text-[0.68rem] font-semibold leading-tight text-muted-foreground ${column.key === "kode_kantor" || column.key === "periode" ? "left-0 z-60" : column.key === "nama_kantor" ? `${monthly ? "left-[108px]" : "left-[56px]"} z-60` : ""}`} buttonClassName="justify-center text-center" />)}</tr>
      {totalRow && <tr>{columns.map((column) => <th key={column.key} className={`sticky top-10 z-30 border-b border-r border-border bg-muted/70 px-2 py-2 text-right font-semibold tabular-nums text-foreground ${columns.indexOf(column) < (hideName ? 1 : 2) ? "text-left" : ""} ${column.key === "kode_kantor" || column.key === "periode" ? "left-0 z-60" : column.key === "nama_kantor" ? `${monthly ? "left-[108px]" : "left-[56px]"} z-60` : ""}`}>
        {column.key === "periode" ? "TOTAL" : column.key === "kode_kantor" ? (hideName && history ? "TOTAL" : "") : column.key === "nama_kantor" ? "TOTAL" : cellValue(totalRow, column)}
      </th>)}</tr>}
    </thead>
    <tbody>{rows.map((row, rowIndex) => <tr key={`${row.periode || row.kode_kantor || "row"}-${rowIndex}`} className="group hover:bg-muted/30">
      {columns.map((column) => <td key={column.key} className={`border-b border-r border-border/70 px-2 py-2.5 tabular-nums ${column.kind ? "text-right" : "text-left"} ${column.key === "kode_kantor" || column.key === "periode" ? "sticky left-0 z-10 bg-background font-mono text-[0.68rem] text-muted-foreground group-hover:bg-muted/50" : column.key === "nama_kantor" ? `sticky ${monthly ? "left-[108px]" : "left-[56px]"} z-10 max-w-48 truncate bg-background font-medium text-foreground group-hover:bg-muted/50` : ""} ${column.kind === "signed" && Number(row[column.key] ?? 0) > 0 ? "text-signal-green-foreground" : column.kind === "signed" && Number(row[column.key] ?? 0) < 0 ? "text-destructive" : ""}`} title={column.key === "nama_kantor" ? String(row[column.key] ?? "") : undefined}>{cellValue(row, column)}</td>)}
    </tr>)}{!rows.length && <tr><td colSpan={columns.length} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada data pada filter ini.</td></tr>}</tbody>
  </table></div>
}
