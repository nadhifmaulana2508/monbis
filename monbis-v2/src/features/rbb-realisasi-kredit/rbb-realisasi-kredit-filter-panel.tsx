import { CalendarDays, Filter } from "lucide-react"

import { SearchableSelect, type SearchableOption } from "@/components/searchable-select"
import type { RbbFilters, RbbMode } from "./rbb-realisasi-kredit-data"

const inputClass = "h-8 rounded-lg border bg-background px-2 text-xs font-medium text-foreground outline-none focus-visible:ring-2 focus-visible:ring-ring/50"

export function RbbRealisasiKreditFilterPanel({ filters, areas, disabled, onChange }: { filters: RbbFilters; areas: SearchableOption[]; disabled: boolean; onChange: (next: Partial<RbbFilters>) => void }) {
  return <div className="grid min-w-0 grid-cols-2 gap-2 md:grid-cols-3">
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex min-w-0 items-center gap-1.5"><CalendarDays className="size-3.5 shrink-0" />Harian / actual</span><input type="date" value={filters.actualDate} onChange={(event) => onChange({ actualDate: event.target.value })} className={`${inputClass} w-full min-w-0`} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex min-w-0 items-center gap-1.5"><Filter className="size-3.5 shrink-0" />Area / cabang</span><SearchableSelect value={filters.area} options={areas} placeholder="Pilih area" disabled={disabled} onChange={(area) => onChange({ area })} /></label>
    <label className="col-span-2 grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground md:col-span-1"><span>Pembanding</span><select value={filters.compareMode} onChange={(event) => onChange({ compareMode: event.target.value as RbbMode })} className={`${inputClass} w-full min-w-0 cursor-pointer`}><option value="auto">Auto</option><option value="rbb">RBB</option><option value="history">History YoY</option></select></label>
  </div>
}
