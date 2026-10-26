import { ReportSortHeader } from "@/components/report-sort-header"

type SortState = { column: string; direction: "asc" | "desc" }

type HeaderColumn = { key: string; label: string; className?: string }
type HeaderGroup = { label: string; columns: HeaderColumn[]; className?: string }

type GroupedReportTableHeadProps = {
  identityColumns: HeaderColumn[]
  groups: HeaderGroup[]
  trailingColumns?: HeaderColumn[]
  sort: SortState
  onSort: (column: string) => void
}

export function GroupedReportTableHead({ identityColumns, groups, trailingColumns = [], sort, onSort }: GroupedReportTableHeadProps) {
  const firstRow = "sticky top-0 z-30 h-9 border-b border-border bg-muted px-2 text-center text-[0.68rem] font-semibold text-muted-foreground"
  const secondRow = "sticky top-9 z-30 h-9 border-b border-border bg-muted px-2 text-center text-[0.68rem] font-semibold text-muted-foreground"
  return <thead>
    <tr className="h-9">
      {identityColumns.map((column, index) => <ReportSortHeader key={column.key} label={column.label} labelText={column.label} column={column.key} sort={sort} onSort={onSort} rowSpan={2} className={`${firstRow} ${index === 0 ? "left-0 z-50" : "left-16 z-50"} ${column.className || ""}`} buttonClassName="justify-center text-center" />)}
      {groups.map((group) => <th key={group.label} scope="colgroup" colSpan={group.columns.length} className={`${firstRow} ${group.className || "border-r border-border/70"}`}>{group.label}</th>)}
      {trailingColumns.map((column) => <ReportSortHeader key={column.key} label={column.label} labelText={column.label} column={column.key} sort={sort} onSort={onSort} rowSpan={2} className={`${firstRow} ${column.className || ""}`} buttonClassName="justify-center text-center" />)}
    </tr>
    <tr className="h-9">
      {groups.flatMap((group) => group.columns.map((column) => <ReportSortHeader key={column.key} label={column.label} labelText={column.label} column={column.key} sort={sort} onSort={onSort} className={`${secondRow} ${column.className || ""}`} buttonClassName="justify-center text-center" />))}
    </tr>
  </thead>
}
