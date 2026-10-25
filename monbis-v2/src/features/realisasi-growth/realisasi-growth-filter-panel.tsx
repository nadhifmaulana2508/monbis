import { CalendarDays, Filter } from "lucide-react"

import { cn } from "@/lib/utils"

import { previousMonthEnd } from "./realisasi-growth-data"
import type { AreaOption, Filters, NominalField } from "./realisasi-growth-types"

type RealisasiGrowthFilterPanelProps = {
  filters: Filters
  areas: AreaOption[]
  onChange: (next: Partial<Filters>) => void
}

const fieldClassName = "h-10 rounded-lg border bg-background px-3 text-sm font-medium text-foreground outline-none focus-visible:ring-3 focus-visible:ring-ring/50"

export function RealisasiGrowthFilterPanel({
  filters,
  areas,
  onChange,
}: RealisasiGrowthFilterPanelProps) {
  return (
    <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
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
        <span className="flex items-center gap-1.5"><Filter className="size-3.5" aria-hidden="true" />Area / cabang</span>
        <select
          value={filters.area}
          onChange={(event) => onChange({ area: event.target.value })}
          className={cn(fieldClassName, "cursor-pointer")}
        >
          {areas.map((area) => <option key={area.value} value={area.value}>{area.label}</option>)}
        </select>
      </label>
      <label className="grid gap-1.5 text-xs font-medium text-muted-foreground">
        <span>Hitung berdasarkan</span>
        <select
          value={filters.nominalField}
          onChange={(event) => onChange({ nominalField: event.target.value as NominalField })}
          className={cn(fieldClassName, "cursor-pointer")}
        >
          <option value="saldo_bank">Saldo bank</option>
          <option value="baki_debet">Baki debet</option>
        </select>
      </label>
    </div>
  )
}
