import { CalendarDays, Filter, SlidersHorizontal } from "lucide-react"

import { SearchableSelect, type SearchableOption } from "@/components/searchable-select"
import type { RrFilters } from "./repayment-rate-data"

const field = "h-8 min-w-0 rounded-lg border bg-background px-2 text-xs font-medium text-foreground outline-none focus-visible:ring-2 focus-visible:ring-ring/50"

export function RepaymentRateFilterPanel({ filters, areas, disabled, showBreakdown, onChange }: { filters: RrFilters; areas: SearchableOption[]; disabled: boolean; showBreakdown: boolean; onChange: (next: Partial<RrFilters>) => void }) {
  return <div className={`grid min-w-0 grid-cols-2 gap-2 ${showBreakdown ? "lg:grid-cols-5" : "lg:grid-cols-4"}`}>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" />Closing (M-1)</span><input type="date" value={filters.closingDate} onChange={(event) => onChange({ closingDate: event.target.value })} className={field} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" />Harian / actual</span><input type="date" value={filters.actualDate} onChange={(event) => onChange({ actualDate: event.target.value })} className={field} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><Filter className="size-3.5" />Area / cabang</span><SearchableSelect value={filters.area} options={areas} placeholder="Pilih area" disabled={disabled} onChange={(area) => onChange({ area })} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><SlidersHorizontal className="size-3.5" />Tipe saldo</span><select value={filters.nominalField} onChange={(event) => onChange({ nominalField: event.target.value as RrFilters["nominalField"] })} className={`${field} cursor-pointer`}><option value="saldo_bank">Saldo bank</option><option value="baki_debet">Baki debet</option></select></label>
    {showBreakdown && <label className="col-span-2 grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground lg:col-span-1"><span>Breakdown</span><select value={filters.breakdown} onChange={(event) => onChange({ breakdown: event.target.value as RrFilters["breakdown"] })} className={`${field} cursor-pointer`}><option value="KANKAS">Per Kankas</option><option value="AO">Per AO Kredit</option></select></label>}
  </div>
}
