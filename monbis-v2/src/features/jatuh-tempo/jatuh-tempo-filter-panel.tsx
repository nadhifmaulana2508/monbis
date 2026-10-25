import { CalendarDays, Filter, SlidersHorizontal } from "lucide-react"

import { cn } from "@/lib/utils"

import { previousMonthEnd } from "./jatuh-tempo-data"
import { jtMonths } from "./jatuh-tempo-types"
import type { JtAreaOption, JtFilters, JtNominalField } from "./jatuh-tempo-types"

type JatuhTempoFilterPanelProps = {
  filters: JtFilters
  areas: JtAreaOption[]
  areaDisabled?: boolean
  onChange: (next: Partial<JtFilters>) => void
}

const fieldClassName = "h-10 rounded-lg border bg-background px-3 text-sm font-medium text-foreground outline-none focus-visible:ring-3 focus-visible:ring-ring/50"

export function JatuhTempoFilterPanel({
  filters,
  areas,
  areaDisabled = false,
  onChange,
}: JatuhTempoFilterPanelProps) {
  return (
    <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Closing (M-1)</span>
        <input
          type="date"
          value={filters.closingDate}
          onChange={(event) => onChange({ closingDate: event.target.value })}
          className={fieldClassName}
        />
      </label>
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Harian / actual</span>
        <input
          type="date"
          value={filters.actualDate}
          onChange={(event) => onChange({
            actualDate: event.target.value,
            closingDate: previousMonthEnd(event.target.value),
          })}
          className={fieldClassName}
        />
      </label>
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span>Bulan jatuh tempo</span>
        <select value={filters.month} onChange={(event) => onChange({ month: event.target.value })} className={cn(fieldClassName, "cursor-pointer")}>
          {jtMonths.map(([value, label]) => <option key={value} value={value}>{label}</option>)}
        </select>
      </label>
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span>Tahun jatuh tempo</span>
        <input type="number" min={2000} max={2100} value={filters.year} onChange={(event) => onChange({ year: event.target.value })} className={fieldClassName} />
      </label>
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span>Basis nominal</span>
        <select value={filters.nominalField} onChange={(event) => onChange({ nominalField: event.target.value as JtNominalField })} className={cn(fieldClassName, "cursor-pointer")}>
          <option value="saldo_bank">Saldo bank</option>
          <option value="baki_debet">Baki debet</option>
        </select>
      </label>
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span className="flex items-center gap-1.5"><Filter className="size-3.5" aria-hidden="true" />Area / cabang / korwil</span>
        <select disabled={areaDisabled} value={filters.area} onChange={(event) => onChange({ area: event.target.value })} className={cn(fieldClassName, "cursor-pointer disabled:cursor-not-allowed disabled:opacity-60")}>
          {areas.map((area) => <option key={area.value} value={area.value}>{area.label}</option>)}
        </select>
      </label>
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span className="flex items-center gap-1.5"><SlidersHorizontal className="size-3.5" aria-hidden="true" />Kolektibilitas</span>
        <select value={filters.includeDp ? "L_DP" : "L"} onChange={(event) => onChange({ includeDp: event.target.value === "L_DP" })} className={cn(fieldClassName, "cursor-pointer")}>
          <option value="L">L saja</option>
          <option value="L_DP">L + DP</option>
        </select>
      </label>
    </div>
  )
}
