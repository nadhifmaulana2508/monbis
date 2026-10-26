import { CalendarDays, Filter, SlidersHorizontal } from "lucide-react"

import { SearchableSelect, type SearchableOption } from "@/components/searchable-select"
import type { PotensiFilters } from "./potensi-npl-data"

export function PotensiNplFilterPanel({ filters, areas, disabled, onChange }: { filters: PotensiFilters; areas: SearchableOption[]; disabled: boolean; onChange: (next: Partial<PotensiFilters>) => void }) {
  const field = "h-8 min-w-0 rounded-lg border bg-background px-2 text-xs font-medium text-foreground outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
  return <div className="grid grid-cols-2 gap-2 lg:grid-cols-4">
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Closing</span><input type="date" value={filters.closingDate} onChange={(event) => onChange({ closingDate: event.target.value })} className={field} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Harian / actual</span><input type="date" value={filters.actualDate} onChange={(event) => onChange({ actualDate: event.target.value })} className={field} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><Filter className="size-3.5" aria-hidden="true" />Area / cabang</span><SearchableSelect value={filters.area} options={areas} placeholder="Pilih area" disabled={disabled} onChange={(area) => onChange({ area })} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><SlidersHorizontal className="size-3.5" aria-hidden="true" />Hitung berdasarkan</span><select value={filters.nominalField} onChange={(event) => onChange({ nominalField: event.target.value as PotensiFilters["nominalField"] })} className={`${field} cursor-pointer`}><option value="saldo_bank">Saldo Bank</option><option value="baki_debet">Baki Debet</option></select></label>
  </div>
}
