import { useMemo, useState } from "react"

export type ReportSortDirection = "asc" | "desc"

function compareValues(left: unknown, right: unknown) {
  const leftText = String(left ?? "").trim()
  const rightText = String(right ?? "").trim()
  const leftNumber = Number(leftText)
  const rightNumber = Number(rightText)
  const areNumbers = leftText !== "" && rightText !== "" && Number.isFinite(leftNumber) && Number.isFinite(rightNumber)

  return areNumbers
    ? leftNumber - rightNumber
    : leftText.localeCompare(rightText, "id", { numeric: true, sensitivity: "base" })
}

export function useReportSort<T>(rows: T[], getValue: (row: T, key: string) => unknown, initialKey: string) {
  const [sortKey, setSortKey] = useState(initialKey)
  const [direction, setDirection] = useState<ReportSortDirection>("asc")
  const sortedRows = useMemo(() => rows
    .map((row, index) => ({ row, index }))
    .sort((left, right) => {
      const result = compareValues(getValue(left.row, sortKey), getValue(right.row, sortKey))
      return (result === 0 ? left.index - right.index : result) * (direction === "asc" ? 1 : -1)
    })
    .map(({ row }) => row), [direction, getValue, rows, sortKey])

  const toggleSort = (key: string) => {
    if (key === sortKey) setDirection((current) => current === "asc" ? "desc" : "asc")
    else {
      setSortKey(key)
      setDirection("asc")
    }
  }

  return { sortedRows, sortKey, direction, toggleSort }
}
