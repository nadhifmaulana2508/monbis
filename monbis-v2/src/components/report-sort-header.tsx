import type { ReactNode } from "react"
import { ArrowDown, ArrowUp, Hash } from "lucide-react"

import { Button } from "@/components/ui/button"

type Props = {
  label: ReactNode
  labelText: string
  column: string
  sort: { column: string; direction: "asc" | "desc" }
  onSort: (column: string) => void
  className?: string
  buttonClassName?: string
  scope?: "col" | "row"
  rowSpan?: number
  alternateSort?: { column: string; label: string; onSort: () => void }
}

export function ReportSortHeader({ label, labelText, column, sort, onSort, className = "", buttonClassName = "", scope = "col", rowSpan, alternateSort }: Props) {
  const active = sort.column === column
  const Icon = sort.direction === "asc" ? ArrowUp : ArrowDown
  const alternateActive = alternateSort ? sort.column === alternateSort.column : false

  return (
    <th
      scope={scope}
      rowSpan={rowSpan}
      aria-sort={active || alternateActive ? (sort.direction === "asc" ? "ascending" : "descending") : "none"}
      className={className}
    >
      <div className="flex w-full min-w-0 items-center justify-center gap-0.5">
        <Button
          type="button"
          variant="ghost"
          size="sm"
          onClick={() => onSort(column)}
          aria-label={`Urutkan ${labelText}`}
          className={`min-h-8 h-auto min-w-0 flex-1 gap-1 rounded-sm px-1 py-1 text-inherit hover:bg-foreground/5 hover:text-inherit ${buttonClassName}`}
        >
          {label}
          {active && <Icon className="size-3 shrink-0" aria-hidden="true" />}
        </Button>
        {alternateSort && <Button type="button" variant="ghost" size="icon-xs" onClick={alternateSort.onSort} aria-label={alternateSort.label} title={alternateSort.label} className="size-6 shrink-0 text-muted-foreground hover:text-foreground">
          {alternateActive ? <Icon className="size-3" aria-hidden="true" /> : <Hash className="size-3" aria-hidden="true" />}
        </Button>}
      </div>
    </th>
  )
}
