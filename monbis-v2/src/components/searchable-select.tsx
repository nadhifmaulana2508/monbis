import { useMemo, useState } from "react"
import { Check, ChevronDown, Search } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Popover } from "@/components/ui/popover"
import { cn } from "@/lib/utils"

export type SearchableOption = { value: string; label: string }

export function SearchableSelect({ value, options, placeholder, onChange, disabled = false, className }: { value: string; options: SearchableOption[]; placeholder: string; onChange: (value: string) => void; disabled?: boolean; className?: string }) {
  const [open, setOpen] = useState(false)
  const [query, setQuery] = useState("")
  const selected = options.find((option) => option.value === value)
  const filtered = useMemo(() => {
    const keyword = query.trim().toLocaleLowerCase("id")
    return keyword ? options.filter((option) => option.label.toLocaleLowerCase("id").includes(keyword)) : options
  }, [options, query])

  return <Popover.Root open={open} onOpenChange={(next) => { setOpen(next); if (!next) setQuery("") }}>
    <Popover.Trigger render={<Button type="button" variant="outline" disabled={disabled} aria-label={placeholder} className={cn("h-8 min-w-0 w-full justify-between gap-2 px-2 text-left text-xs font-medium", className)} />}>
      <span className="truncate">{selected?.label || placeholder}</span><ChevronDown className="size-3.5 shrink-0 text-muted-foreground" aria-hidden="true" />
    </Popover.Trigger>
    <Popover.Portal>
      <Popover.Positioner side="bottom" align="start" sideOffset={4} className="z-[2147483647] outline-none">
        <Popover.Popup className="w-[var(--anchor-width)] min-w-48 max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border bg-popover text-popover-foreground shadow-lg">
          <div className="relative border-b p-2"><Search className="pointer-events-none absolute left-4 top-1/2 size-3.5 -translate-y-1/2 text-muted-foreground" /><input autoFocus value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Cari pilihan..." aria-label="Cari pilihan" className="h-8 w-full rounded-md border bg-background pl-8 pr-2 text-xs outline-none focus-visible:ring-2 focus-visible:ring-ring" /></div>
          <div className="max-h-56 overflow-y-auto p-1" role="listbox">{filtered.length ? filtered.map((option) => <button key={option.value} type="button" role="option" aria-selected={option.value === value} onClick={() => { onChange(option.value); setOpen(false); setQuery("") }} className="flex min-h-8 w-full items-center justify-between gap-2 rounded-md px-2 py-1.5 text-left text-xs hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"><span className="truncate">{option.label}</span>{option.value === value && <Check className="size-3.5 shrink-0" aria-hidden="true" />}</button>) : <p className="px-2 py-3 text-center text-xs text-muted-foreground">Pilihan tidak ditemukan.</p>}</div>
        </Popover.Popup>
      </Popover.Positioner>
    </Popover.Portal>
  </Popover.Root>
}
