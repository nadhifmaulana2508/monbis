import { useState, type ReactNode } from "react"
import { ChevronDown, Search, SlidersHorizontal, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Collapsible } from "@/components/ui/collapsible"

export function ReportDetailFilters({ children, panelClassName = "" }: { children: ReactNode; panelClassName?: string }) {
  const [open, setOpen] = useState(false)
  return <Collapsible.Root open={open} onOpenChange={setOpen} className="relative inline-flex shrink-0">
    <Collapsible.Trigger render={<Button type="button" variant="outline" size="icon-sm" className="size-9 sm:h-9 sm:w-auto sm:px-2" aria-label="Filter detail" title="Filter detail" />}>
        <SlidersHorizontal className="size-3.5" /><span className="sr-only sm:not-sr-only">Filter</span><ChevronDown className={`hidden size-3.5 transition-transform sm:block ${open ? "rotate-180" : ""}`} />
      </Collapsible.Trigger>
    <Collapsible.Panel className={`absolute right-0 top-[calc(100%+0.4rem)] z-[120] w-[min(30rem,calc(100vw-2rem))] rounded-lg border bg-background p-3 shadow-lg ${panelClassName}`}>{children}</Collapsible.Panel>
  </Collapsible.Root>
}

export function DetailSearchToggle({ value, onChange, placeholder = "Cari detail...", ariaLabel = "Cari detail" }: { value: string; onChange: (value: string) => void; placeholder?: string; ariaLabel?: string }) {
  const [open, setOpen] = useState(false)
  return <div className="flex items-center gap-1">
    {open && <input autoFocus value={value} onChange={(event) => onChange(event.target.value)} onBlur={() => { if (!value) setOpen(false) }} onKeyDown={(event) => { if (event.key === "Escape") { onChange(""); setOpen(false) } }} placeholder={placeholder} aria-label={ariaLabel} className="h-8 w-36 rounded-md border bg-background px-2 text-xs outline-none focus-visible:ring-2 focus-visible:ring-ring sm:w-48" />}
    <Button type="button" variant="outline" size="icon-lg" className="size-9" aria-label={open ? "Tutup pencarian" : ariaLabel} onMouseDown={(event) => event.preventDefault()} onClick={() => { if (open) { onChange(""); setOpen(false) } else setOpen(true) }}>{open ? <X className="size-4" /> : <Search className="size-4" />}</Button>
  </div>
}
