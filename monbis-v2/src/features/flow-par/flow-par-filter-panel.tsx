import { CalendarDays, Filter, SlidersHorizontal } from "lucide-react"

import { SearchableSelect, type SearchableOption } from "@/components/searchable-select"
import type { FlowParFilters } from "./flow-par-data"

export function FlowParFilterPanel({ filters, areas, disabled, onChange }: { filters: FlowParFilters; areas: SearchableOption[]; disabled: boolean; onChange: (next: Partial<FlowParFilters>) => void }) {
  const field = "h-8 min-w-0 rounded-lg border bg-background px-2 text-xs font-medium text-foreground outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
  return <div className="grid min-w-0 grid-cols-2 gap-2 xl:grid-cols-4">
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Closing (M-1)</span><input type="date" value={filters.closingDate} disabled={disabled} onChange={(event) => onChange({ closingDate: event.target.value })} className={field} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Actual (Harian)</span><input type="date" value={filters.actualDate} disabled={disabled} onChange={(event) => onChange({ actualDate: event.target.value })} className={field} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><SlidersHorizontal className="size-3.5" aria-hidden="true" />Basis Nominal</span><select value={filters.nominalField} disabled={disabled} onChange={(event) => onChange({ nominalField: event.target.value as FlowParFilters["nominalField"] })} className={`${field} cursor-pointer`}><option value="saldo_bank">Saldo Bank</option><option value="baki_debet">Baki Debet</option></select></label>
    <label className="col-span-2 grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground xl:col-span-1"><span className="flex items-center gap-1.5"><Filter className="size-3.5" aria-hidden="true" />Area / Cabang</span><SearchableSelect value={filters.area} options={areas} placeholder="Pilih area" disabled={disabled} onChange={(area) => onChange({ area })} /></label>
  </div>
}
