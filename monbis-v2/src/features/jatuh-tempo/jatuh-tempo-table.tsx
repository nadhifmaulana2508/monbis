import { cn } from "@/lib/utils"

import { formatJtMoney, formatJtPercent } from "./jatuh-tempo-data"
import type { JtBreakdown, JtCategory, JtDetailSelection, JtReportRow } from "./jatuh-tempo-types"

type JatuhTempoTableProps = {
  rows: JtReportRow[]
  grandTotal: JtReportRow | null
  breakdown: JtBreakdown
  onDetail: (selection: JtDetailSelection) => void
}

const groupTone = {
  blue: "bg-primary/5 text-primary",
  green: "bg-signal-green/10 text-signal-green-foreground",
  sky: "bg-secondary text-secondary-foreground",
  amber: "bg-signal-yellow/15 text-signal-yellow-foreground",
}

function valueOf(row: JtReportRow, key: string) {
  return Number(row[key] ?? 0)
}

function rowLabel(row: JtReportRow, breakdown: JtBreakdown) {
  return breakdown === "CABANG" ? row.nama_kantor || "-" : row.group_label || row.nama_kantor || "-"
}

function makeSelection(row: JtReportRow, breakdown: JtBreakdown, category: JtCategory, isTotal = false): JtDetailSelection {
  return {
    kode: isTotal ? "" : String(row.kode_kantor || ""),
    label: isTotal ? "TOTAL" : rowLabel(row, breakdown),
    groupType: isTotal ? "TOTAL" : breakdown,
    groupCode: isTotal ? "" : breakdown === "CABANG" ? "" : String(row.group_code || ""),
    category,
    isTotal,
  }
}

function MetricCell({
  value,
  align = "right",
  tone,
  onClick,
  label,
}: {
  value: unknown
  align?: "left" | "center" | "right"
  tone?: string
  onClick?: () => void
  label?: string
}) {
  const className = cn(
    "block w-full px-2 py-3 tabular-nums transition-colors",
    align === "center" && "text-center",
    align === "left" && "text-left",
    align === "right" && "text-right",
    tone,
    onClick && "cursor-pointer hover:bg-foreground/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset",
  )
  const content = formatJtMoney(value)
  return onClick ? (
    <button type="button" className={className} onClick={onClick} aria-label={label || "Buka detail"}>
      {content}
    </button>
  ) : (
    <span className={className}>{content}</span>
  )
}

function TotalCell({ value, align = "right", tone }: { value: unknown; align?: "center" | "right"; tone?: string }) {
  return <td className={cn("border-b border-border/70 bg-muted px-2 py-3 font-semibold tabular-nums", align === "center" ? "text-center" : "text-right", tone)}>{formatJtMoney(value)}</td>
}

export function JatuhTempoTable({ rows, grandTotal, breakdown, onDetail }: JatuhTempoTableProps) {
  const total = grandTotal || rows.reduce<JtReportRow>((result, row) => {
    const keys = ["noa_potensi", "plafon_potensi", "noa_refinancing", "plafon_refinancing", "noa_lunas", "plafon_lunas", "sisa_belum_lunas", "noa_belum_lunas", "plafon_belum_lunas"]
    keys.forEach((key) => { result[key] = valueOf(result, key) + valueOf(row, key) })
    return result
  }, {})
  const percentage = valueOf(total, "plafon_potensi") > 0
    ? valueOf(total, "plafon_refinancing") / valueOf(total, "plafon_potensi") * 100
    : 0
  const identityLabel = breakdown === "AO" ? "Nama AO" : breakdown === "KANKAS" ? "Nama Kankas" : "Nama Kantor"
  const identityClass = "sticky z-20 border-r border-border/70 bg-background text-left"
  const totalIdentityClass = "sticky z-40 border-r border-border/70 bg-muted text-left"
  const nameLeft = "left-0 md:left-16"
  const codeClass = "w-16 min-w-16 text-center"
  const nameClass = "w-36 min-w-36 max-w-36 md:w-48 md:min-w-48 md:max-w-48"

  const renderCategoryCell = (row: JtReportRow, category: JtCategory, value: unknown, align: "center" | "right", tone: string, label: string) => (
    <MetricCell
      value={value}
      align={align}
      tone={tone}
      label={`Lihat detail ${label}`}
      onClick={() => onDetail(makeSelection(row, breakdown, category))}
    />
  )

  return (
    <table className="min-w-[1060px] w-full border-separate border-spacing-0 text-xs sm:text-sm">
      <colgroup>
        <col className="w-16" />
        <col className="w-36 md:w-48" />
        <col className="w-16" /><col className="w-32" />
        <col className="w-16" /><col className="w-32" />
        <col className="w-20" />
        <col className="w-16" /><col className="w-32" />
        <col className="w-36" /><col className="w-16" /><col className="w-32" />
      </colgroup>
      <thead className="font-semibold">
        <tr className="sticky top-0 z-30">
          <th rowSpan={2} className={cn(totalIdentityClass, codeClass, "hidden bg-muted px-2 py-2 text-center md:table-cell")}>Kode</th>
          <th rowSpan={2} className={cn(totalIdentityClass, nameClass, nameLeft, "bg-muted px-3 py-2")}>{identityLabel}</th>
          <th colSpan={2} className={cn("border-b border-r border-border/70 px-2 py-2 text-center", groupTone.blue)}>Potensi Kredit</th>
          <th colSpan={2} className={cn("border-b border-r border-border/70 px-2 py-2 text-center", groupTone.green)}>Refinancing</th>
          <th rowSpan={2} className="border-b border-r border-border/70 bg-muted px-2 py-2 text-center">%</th>
          <th colSpan={2} className={cn("border-b border-r border-border/70 px-2 py-2 text-center", groupTone.sky)}>Lunas (Prospek)</th>
          <th colSpan={3} className={cn("border-b border-border/70 px-2 py-2 text-center", groupTone.amber)}>Belum Lunas</th>
        </tr>
        <tr className="sticky top-9 z-30 text-[0.68rem] uppercase tracking-wide">
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-center", groupTone.blue)}>NOA</th>
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-right", groupTone.blue)}>Plafon</th>
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-center", groupTone.green)}>NOA</th>
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-right", groupTone.green)}>Plafon</th>
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-center", groupTone.sky)}>NOA</th>
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-right", groupTone.sky)}>Plafon</th>
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-right", groupTone.amber)}>Sisa saldo</th>
          <th className={cn("border-b border-r border-border/70 px-2 py-2 text-center", groupTone.amber)}>NOA</th>
          <th className={cn("border-b border-border/70 px-2 py-2 text-right", groupTone.amber)}>Plafon</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td className={cn(totalIdentityClass, codeClass, "hidden px-2 py-3 text-center text-muted-foreground md:table-cell")}>ALL</td>
          <td className={cn(totalIdentityClass, nameClass, nameLeft, "px-3 py-3 font-semibold text-foreground")}>TOTAL</td>
          <TotalCell value={total.noa_potensi} align="center" tone="text-primary" />
          <TotalCell value={total.plafon_potensi} tone="text-primary" />
          <TotalCell value={total.noa_refinancing} align="center" tone="text-signal-green-foreground" />
          <TotalCell value={total.plafon_refinancing} tone="text-signal-green-foreground" />
          <td className="border-b border-border/70 bg-muted px-2 py-3 text-center font-semibold text-signal-yellow-foreground tabular-nums">{formatJtPercent(percentage)}</td>
          <TotalCell value={total.noa_lunas} align="center" tone="text-secondary-foreground" />
          <TotalCell value={total.plafon_lunas} tone="text-secondary-foreground" />
          <TotalCell value={total.sisa_belum_lunas} tone="text-signal-yellow-foreground" />
          <TotalCell value={total.noa_belum_lunas} align="center" tone="text-signal-yellow-foreground" />
          <TotalCell value={total.plafon_belum_lunas} tone="text-signal-yellow-foreground" />
        </tr>
        {rows.map((row, index) => {
          const label = rowLabel(row, breakdown)
          return (
            <tr
              key={`${row.kode_kantor}-${row.group_code || "root"}-${index}`}
              className="group cursor-pointer border-b border-border/60 bg-background transition-colors hover:bg-muted/40"
              onClick={() => onDetail(makeSelection(row, breakdown, "POTENSI"))}
            >
              <td className={cn(identityClass, codeClass, "px-2 py-3 font-mono text-xs text-muted-foreground group-hover:bg-muted/40", "hidden md:table-cell")}>{breakdown === "CABANG" ? row.kode_kantor || "-" : row.group_code || "-"}</td>
              <td className={cn(identityClass, nameClass, "left-0 px-3 py-3 font-medium group-hover:bg-muted/40", "md:left-16")} title={label}>{label}</td>
              <td className="border-r border-border/60"><MetricCell value={row.noa_potensi} align="center" /></td>
              <td className="border-r border-border/60"><MetricCell value={row.plafon_potensi} /></td>
              <td className={cn("border-r border-border/60", groupTone.green)} onClick={(event) => event.stopPropagation()}>{renderCategoryCell(row, "REFINANCING", row.noa_refinancing, "center", groupTone.green, "Refinancing")}</td>
              <td className={cn("border-r border-border/60", groupTone.green)} onClick={(event) => event.stopPropagation()}>{renderCategoryCell(row, "REFINANCING", row.plafon_refinancing, "right", groupTone.green, "Refinancing")}</td>
              <td className="border-r border-border/60 px-2 py-3 text-center text-signal-yellow-foreground tabular-nums">{formatJtPercent(row.persentase)}</td>
              <td className={cn("border-r border-border/60", groupTone.sky)} onClick={(event) => event.stopPropagation()}>{renderCategoryCell(row, "LUNAS", row.noa_lunas, "center", groupTone.sky, "Lunas")}</td>
              <td className={cn("border-r border-border/60", groupTone.sky)} onClick={(event) => event.stopPropagation()}>{renderCategoryCell(row, "LUNAS", row.plafon_lunas, "right", groupTone.sky, "Lunas")}</td>
              <td className={cn("border-r border-border/60", groupTone.amber)} onClick={(event) => event.stopPropagation()}>{renderCategoryCell(row, "BELUM_LUNAS", row.sisa_belum_lunas, "right", groupTone.amber, "Belum Lunas")}</td>
              <td className={cn("border-r border-border/60", groupTone.amber)} onClick={(event) => event.stopPropagation()}>{renderCategoryCell(row, "BELUM_LUNAS", row.noa_belum_lunas, "center", groupTone.amber, "Belum Lunas")}</td>
              <td className={groupTone.amber} onClick={(event) => event.stopPropagation()}>{renderCategoryCell(row, "BELUM_LUNAS", row.plafon_belum_lunas, "right", groupTone.amber, "Belum Lunas")}</td>
            </tr>
          )
        })}
      </tbody>
    </table>
  )
}
