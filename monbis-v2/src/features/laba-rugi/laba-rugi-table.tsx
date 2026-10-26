import { useMemo, useState, type RefObject } from "react"
import { createPortal } from "react-dom"
import { ArrowLeftRight, ChevronDown, ChevronRight } from "lucide-react"
import { Button } from "@/components/ui/button"
import { emptyLabaRugiRows, labaRugiAmount, labaRugiCode, labaRugiNumber, type LabaRugiData, type LabaRugiRow } from "./laba-rugi-data"

type Side = "pendapatan" | "biaya"
const money = (value: number) => labaRugiNumber.format(value)
const isMajorAccount = (code: string) => code === "4" || code === "5"

function getVisible(rows: LabaRugiRow[], side: Side, level: string, query: string, overrides: Set<string>) {
  const codes = rows.map(labaRugiCode).filter(Boolean)
  const codeSet = new Set(codes)
  const parent = new Map<string, string>()
  const children = new Set<string>()
  codes.forEach((code) => {
    for (let length = code.length - 1; length > 0; length--) {
      const candidate = code.slice(0, length)
      if (codeSet.has(candidate)) { parent.set(code, candidate); children.add(candidate); break }
    }
  })
  const expanded = (code: string) => {
    const key = `${side}:${code}`
    if (overrides.has(`closed:${key}`)) return false
    if (overrides.has(`open:${key}`)) return true
    return level === "all" || [...codeSet].some((child) => child.startsWith(code) && child.length > code.length && child.length <= Number(level))
  }
  const visible = rows.filter((row) => {
    const code = labaRugiCode(row)
    if (query) return `${code} ${row.nama_perkiraan || ""}`.toLocaleLowerCase("id-ID").includes(query)
    if (level !== "all" && code.length > Number(level)) return false
    let ancestor = parent.get(code)
    while (ancestor) {
      if (!expanded(ancestor)) return false
      ancestor = parent.get(ancestor)
    }
    return true
  })
  return { visible, children }
}

function AccountName({ row, depth, expandable, open, onToggle }: { row: LabaRugiRow; depth: number; expandable: boolean; open: boolean; onToggle: () => void }) {
  const code = labaRugiCode(row)
  return <div className="flex min-w-0 items-center" style={{ paddingLeft: `${Math.min(7, depth) * 12}px` }}>
    {expandable ? <button type="button" className="mr-1 inline-flex size-5 shrink-0 items-center justify-center rounded text-muted-foreground hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" aria-label={`${open ? "Tutup" : "Buka"} akun ${code}`} aria-expanded={open} onClick={onToggle}>{open ? <ChevronDown className="size-3.5" aria-hidden="true" /> : <ChevronRight className="size-3.5" aria-hidden="true" />}</button> : <span className="mr-1 inline-block size-5 shrink-0" />}
    <span className="truncate" title={row.nama_perkiraan || code}>{row.nama_perkiraan || code}</span>
  </div>
}

export function LabaRugiTable({ data, level, query, targetRef }: { data: LabaRugiData; level: string; query: string; targetRef: RefObject<HTMLDivElement | null> }) {
  const [side, setSide] = useState<Side>("pendapatan")
  const [overrides, setOverrides] = useState<Set<string>>(new Set())
  const income = data.pendapatan || emptyLabaRugiRows
  const expenses = data.biaya || emptyLabaRugiRows
  const incomeRows = useMemo(() => getVisible(income, "pendapatan", level, query, overrides), [income, level, overrides, query])
  const expenseRows = useMemo(() => getVisible(expenses, "biaya", level, query, overrides), [expenses, level, overrides, query])
  const mobileRows = side === "pendapatan" ? incomeRows : expenseRows
  const toggle = (currentSide: Side, code: string, isOpen: boolean) => setOverrides((current) => {
    const next = new Set(current)
    const key = `${currentSide}:${code}`
    next.delete(`${isOpen ? "open" : "closed"}:${key}`)
    next.add(`${isOpen ? "closed" : "open"}:${key}`)
    return next
  })
  const totalIncome = Number(data.totals?.pendapatan || 0)
  const totalExpense = Number(data.totals?.biaya || 0)
  const gross = Number(data.totals?.laba_kotor ?? totalIncome - totalExpense)
  const tax = Number(data.totals?.pajak || 0)
  const net = Number(data.totals?.laba_bersih ?? gross - tax)
  const maxRows = Math.max(incomeRows.visible.length, expenseRows.visible.length)
  const empty = (colSpan: number) => <tr><td colSpan={colSpan} className="px-4 py-12 text-center text-sm text-muted-foreground">Tidak ada akun untuk tanggal, area, dan pencarian ini.</td></tr>

  return <>
    <div ref={targetRef} className="relative flex min-h-0 flex-1 flex-col overflow-hidden bg-background">
    <div className="min-h-0 flex-1 overflow-auto pb-[calc(4.5rem+env(safe-area-inset-bottom))] md:pb-16">
      <table className="hidden w-full min-w-[900px] border-separate border-spacing-0 text-sm md:table">
        <thead className="sticky top-0 z-20 bg-muted text-xs font-semibold text-muted-foreground"><tr><th colSpan={3} className="border-b border-r px-3 py-2 text-center">Pendapatan <span className="font-normal">(Revenues)</span></th><th colSpan={3} className="border-b px-3 py-2 text-center">Beban &amp; Biaya <span className="font-normal">(Expenses)</span></th></tr><tr>{["Kode", "Uraian Perkiraan", "Saldo", "Kode", "Uraian Perkiraan", "Saldo"].map((title, index) => <th key={`${title}-${index}`} className={`border-b border-r px-2 py-2 ${index % 3 === 2 ? "text-right" : "text-left"}`}>{title}</th>)}</tr></thead>
        <tbody>{maxRows === 0 ? empty(6) : Array.from({ length: maxRows }, (_, index) => {
          const left = incomeRows.visible[index]
          const right = expenseRows.visible[index]
          const account = (row: LabaRugiRow | undefined, currentSide: Side, children: Set<string>) => {
            if (!row) return <><td className="border-b border-r border-border/70 px-2 py-2" /><td className="border-b border-r border-border/70 px-2 py-2" /><td className="border-b border-r border-border/70 px-2 py-2" /></>
            const code = labaRugiCode(row)
            const isOpen = !overrides.has(`closed:${currentSide}:${code}`) && (overrides.has(`open:${currentSide}:${code}`) || level === "all" || [...(currentSide === "pendapatan" ? income : expenses)].some((child) => labaRugiCode(child).startsWith(code) && labaRugiCode(child).length > code.length && labaRugiCode(child).length <= Number(level)))
            const major = isMajorAccount(code)
            const cellClass = major ? "border-b border-r border-border/70 bg-muted font-semibold" : "border-b border-r border-border/70"
            return <><td className={`w-20 px-2 py-2 font-mono text-xs text-muted-foreground ${cellClass}`}>{code}</td><td className={`max-w-[28rem] px-2 py-2 ${cellClass}`}><AccountName row={row} depth={Number(row.depth || 0)} expandable={children.has(code)} open={isOpen} onToggle={() => toggle(currentSide, code, isOpen)} /></td><td className={`w-40 px-3 py-2 text-right tabular-nums ${cellClass}`}>{money(labaRugiAmount(row))}</td></>
          }
          return <tr key={index} className="group hover:bg-muted/30">{account(left, "pendapatan", incomeRows.children)}{account(right, "biaya", expenseRows.children)}</tr>
        })}</tbody>
      </table>
      <div className="md:hidden">
        <div className="sticky top-0 z-20 flex items-center justify-between border-b bg-muted px-3 py-2"><span className="text-xs font-semibold text-muted-foreground">{side === "pendapatan" ? "Pendapatan" : "Beban & Biaya"}</span><Button type="button" variant="outline" size="sm" className="h-8" onClick={() => setSide((current) => current === "pendapatan" ? "biaya" : "pendapatan")}><ArrowLeftRight data-icon="inline-start" aria-hidden="true" />Tampilkan {side === "pendapatan" ? "Biaya" : "Pendapatan"}</Button></div>
        <table className="w-full table-fixed border-separate border-spacing-0 text-xs tabular-nums"><colgroup><col className="w-[4.5rem]" /><col /><col className="w-32" /></colgroup><thead className="sticky top-12 z-10 bg-muted text-left text-[0.68rem] font-semibold text-muted-foreground"><tr><th className="border-b px-2 py-2">Kode</th><th className="border-b px-2 py-2">Uraian Perkiraan</th><th className="border-b px-2 py-2 text-right">Saldo</th></tr></thead>
          <tbody>{mobileRows.visible.map((row, index) => {
            const code = labaRugiCode(row)
            const isOpen = !overrides.has(`closed:${side}:${code}`) && (overrides.has(`open:${side}:${code}`) || level === "all" || [...(side === "pendapatan" ? income : expenses)].some((child) => labaRugiCode(child).startsWith(code) && labaRugiCode(child).length > code.length && labaRugiCode(child).length <= Number(level)))
            const major = isMajorAccount(code)
            const cellClass = major ? "bg-muted font-semibold" : ""
            return <tr key={`${side}-${code}-${index}`} className="border-b border-border/70 hover:bg-muted/30"><td className={`px-2 py-2 font-mono text-[0.68rem] text-muted-foreground ${cellClass}`}>{code}</td><td className={`max-w-[12rem] px-2 py-2 ${cellClass}`}><AccountName row={row} depth={Number(row.depth || 0)} expandable={mobileRows.children.has(code)} open={isOpen} onToggle={() => toggle(side, code, isOpen)} /></td><td className={`px-2 py-2 text-right ${cellClass}`}>{money(labaRugiAmount(row))}</td></tr>
          })}{!mobileRows.visible.length && empty(3)}</tbody>
        </table>
      </div>
    </div>
      <ProfitSummary gross={gross} tax={tax} net={net} className="absolute inset-x-0 bottom-0 z-40 hidden grid-cols-3 gap-1 border-t bg-background p-1.5 shadow-[0_-4px_12px_rgba(0,0,0,0.08)] md:grid md:gap-2 md:p-2" />
    </div>
    {createPortal(<ProfitSummary gross={gross} tax={tax} net={net} className="fixed inset-x-0 bottom-0 z-[9999] grid-cols-3 gap-1 border-t bg-background p-1.5 pb-[calc(env(safe-area-inset-bottom)+0.375rem)] shadow-[0_-4px_12px_rgba(0,0,0,0.16)] md:hidden" />, document.body)}
  </>
}

function ProfitSummary({ gross, tax, net, className = "" }: { gross: number; tax: number; net: number; className?: string }) {
  return <div className={`grid ${className}`}>
    <ProfitTotal label="Laba Rugi Kotor" value={gross} />
    <ProfitTotal label="Pajak" value={tax} />
    <ProfitTotal label="Laba Rugi Bersih" value={net} />
  </div>
}

function ProfitTotal({ label, value }: { label: string; value: number }) { return <div className="flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-md border bg-background px-1 py-1 text-center sm:flex-row sm:justify-between sm:gap-2 sm:px-3 sm:py-2"><span className="w-full truncate text-[0.52rem] leading-tight text-muted-foreground sm:text-xs">{label}</span><strong className="max-w-full truncate text-[0.58rem] leading-tight tabular-nums sm:shrink-0 sm:text-sm">{money(value)}</strong></div> }
