import { CalendarDays, Filter, Rows3 } from "lucide-react"
import type { LabaRugiArea } from "./laba-rugi-data"

export function LabaRugiFilterPanel({ date, area, level, areas, disabled, onChange }: { date: string; area: string; level: string; areas: LabaRugiArea[]; disabled: boolean; onChange: (next: { date?: string; area?: string; level?: string }) => void }) {
  const field = "h-8 min-w-0 rounded-lg border bg-background px-2 text-xs font-medium text-foreground outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
  return <div className="grid min-w-0 grid-cols-2 gap-2 sm:grid-cols-3">
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><CalendarDays className="size-3.5" aria-hidden="true" />Harian / Actual</span><input type="date" value={date} disabled={disabled} onChange={(event) => onChange({ date: event.target.value })} className={field} /></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><Filter className="size-3.5" aria-hidden="true" />Area / Cabang</span><select value={area} disabled={disabled} onChange={(event) => onChange({ area: event.target.value })} className={`${field} cursor-pointer`}>{areas.map((option) => <option key={option.value} value={option.value}>{option.label}</option>)}</select></label>
    <label className="grid min-w-0 gap-1 text-[0.68rem] font-medium text-muted-foreground"><span className="flex items-center gap-1.5"><Rows3 className="size-3.5" aria-hidden="true" />Level akun</span><select value={level} disabled={disabled} onChange={(event) => onChange({ level: event.target.value })} className={`${field} cursor-pointer`}><option value="3">3</option><option value="5">5</option><option value="7">7</option><option value="all">Semua</option></select></label>
  </div>
}
