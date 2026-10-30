import { useEffect, useState, type ReactNode } from "react"
import { BarChart3, Building2, Menu, PanelLeftClose, PanelLeftOpen, Table2, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"
import type { RbbTab } from "@/lib/rbb-types"

type RbbShellProps = {
  activeTab: RbbTab
  onTabChange: (tab: RbbTab) => void
  children: ReactNode
}

const navItems: Array<{ tab: RbbTab; label: string; description: string; icon: typeof BarChart3 }> = [
  { tab: "projection", label: "Proyeksi RBB", description: "Ringkasan proyeksi", icon: BarChart3 },
  { tab: "detail", label: "Input Detail", description: "Input COA manual", icon: Table2 },
  { tab: "aba", label: "Input ABA", description: "Penempatan dan CKPN", icon: Building2 },
]

function Sidebar({ compact, mobile, activeTab, onClose, onTabChange }: {
  compact: boolean
  mobile?: boolean
  activeTab: RbbTab
  onClose?: () => void
  onTabChange: (tab: RbbTab) => void
}) {
  return (
    <aside className={cn("flex h-full min-h-0 flex-col border-r border-[var(--line)] bg-[var(--surface)]", mobile ? "w-[min(88vw,320px)] shadow-xl" : compact ? "w-[76px]" : "w-64")}>
      <div className={cn("flex min-h-16 items-center border-b border-[var(--line-soft)] px-4", compact && !mobile ? "justify-center" : "justify-between")}>
        <div className={cn("flex min-w-0 items-center gap-3", compact && !mobile && "justify-center")}>
          <span className="grid size-9 shrink-0 place-items-center rounded-md bg-[var(--ink)] text-sm font-semibold text-[var(--surface)]">M</span>
          <span className={cn("min-w-0", compact && !mobile && "sr-only")}>
            <span className="block truncate text-sm font-semibold tracking-tight text-[var(--ink)]">MONBIS</span>
            <span className="block truncate text-xs text-[var(--muted)]">RBB Bisnis</span>
          </span>
        </div>
        {mobile && <Button variant="ghost" size="icon" aria-label="Tutup navigasi" onClick={onClose}><X size={17} /></Button>}
      </div>

      <nav aria-label="Navigasi RBB Bisnis" className="min-h-0 flex-1 overflow-y-auto overscroll-contain px-3 py-6">
        <p className={cn("mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)]", compact && !mobile && "sr-only")}>RBB Bisnis</p>
        <div className="space-y-1">
          {navItems.map(({ tab, label, description, icon: Icon }) => {
            const active = tab === activeTab
            return (
              <button
                key={tab}
                type="button"
                title={compact && !mobile ? label : undefined}
                aria-current={active ? "page" : undefined}
                onClick={() => { onTabChange(tab); onClose?.() }}
                className={cn(
                  "flex min-h-11 w-full items-center gap-3 rounded-md px-3 text-left text-sm font-medium text-[var(--muted)] outline-none transition-colors hover:bg-[var(--surface-subtle)] hover:text-[var(--ink)] focus-visible:ring-2 focus-visible:ring-[var(--ring)]",
                  compact && !mobile && "justify-center px-0",
                  active && "bg-[var(--ink-soft)] text-[var(--ink)]",
                )}
              >
                <Icon size={17} strokeWidth={1.8} aria-hidden="true" />
                <span className={cn("min-w-0 truncate", compact && !mobile && "sr-only")}>
                  <span className="block truncate">{label}</span>
                  {!compact && <span className="mt-0.5 block truncate text-[11px] font-normal text-[var(--muted)]">{description}</span>}
                </span>
              </button>
            )
          })}
        </div>
      </nav>
      {!compact && <div className="border-t border-[var(--line-soft)] px-4 py-4 text-[11px] leading-5 text-[var(--muted)]">Data RBB diambil langsung dari API MONBIS.</div>}
    </aside>
  )
}

export function RbbShell({ activeTab, onTabChange, children }: RbbShellProps) {
  const [collapsed, setCollapsed] = useState(false)
  const [mobileOpen, setMobileOpen] = useState(false)

  useEffect(() => {
    if (!mobileOpen) return undefined
    const onKeyDown = (event: KeyboardEvent) => event.key === "Escape" && setMobileOpen(false)
    const previousBodyOverflow = document.body.style.overflow
    document.body.style.overflow = "hidden"
    window.addEventListener("keydown", onKeyDown)
    return () => { document.body.style.overflow = previousBodyOverflow; window.removeEventListener("keydown", onKeyDown) }
  }, [mobileOpen])

  return (
    <div className="flex h-dvh min-h-0 overflow-hidden bg-[var(--page)] text-[var(--ink)]">
      <div className={cn("hidden h-full shrink-0 lg:block", collapsed ? "w-[76px]" : "w-64")}>
        <Sidebar compact={collapsed} activeTab={activeTab} onTabChange={onTabChange} />
      </div>
      {mobileOpen && <div className="fixed inset-0 z-50 h-dvh lg:hidden"><button className="absolute inset-0 cursor-default bg-black/35" aria-label="Tutup navigasi" onClick={() => setMobileOpen(false)} /><div className="relative h-full"><Sidebar mobile compact={false} activeTab={activeTab} onClose={() => setMobileOpen(false)} onTabChange={onTabChange} /></div></div>}

      <div className="flex h-full min-h-0 min-w-0 flex-1 flex-col">
        <header className="relative z-30 shrink-0 border-b border-[var(--line)] bg-[var(--page)]/95 backdrop-blur-sm">
          <div className="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
            <div className="flex min-w-0 items-center gap-2">
              <Button variant="ghost" size="icon" className="lg:hidden" aria-label="Buka navigasi" onClick={() => setMobileOpen(true)}><Menu size={17} /></Button>
              <Button variant="ghost" size="icon" className="hidden lg:inline-flex" aria-label={collapsed ? "Bentangkan sidebar" : "Ciutkan sidebar"} onClick={() => setCollapsed((value) => !value)}>{collapsed ? <PanelLeftOpen size={17} /> : <PanelLeftClose size={17} />}</Button>
              <div className="min-w-0">
                <p className="truncate text-xs text-[var(--muted)]">MONBIS / RBB Bisnis</p>
                <h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">{navItems.find((item) => item.tab === activeTab)?.label}</h1>
              </div>
            </div>
            <span className="hidden rounded-full border border-[var(--line)] bg-[var(--surface)] px-2.5 py-1 text-[11px] font-medium text-[var(--muted)] sm:inline-flex">React · API</span>
          </div>
        </header>
        <main className="min-h-0 flex-1 overflow-x-hidden overflow-y-auto overscroll-contain">{children}</main>
      </div>
    </div>
  )
}
