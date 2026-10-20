import { createContext, useContext, useEffect, useMemo, useRef, useState, type ReactNode } from "react"
import type { LucideIcon } from "lucide-react"
import {
  Building2,
  Calculator,
  ChevronRight,
  Menu,
  Moon,
  PanelLeftClose,
  PanelLeftOpen,
  RefreshCw,
  Settings2,
  SlidersHorizontal,
  Sun,
  Table2,
  X,
  UserCircle,
} from "lucide-react"

import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"
import { kpiRoutes, legacyPageUrl } from "@/lib/routes"
import { loadCurrentUser, userInitials, type MonbisUser } from "@/lib/auth"

type NavItem = { label: string; href: string; icon: LucideIcon }

const navItems: NavItem[] = [
  { label: "Setting KPI Jabatan", href: kpiRoutes.setting, icon: Settings2 },
  { label: "Nilai KPI AO", href: kpiRoutes.nilai, icon: Calculator },
  { label: "Generate KPI AO", href: kpiRoutes.generate, icon: RefreshCw },
  { label: "Rekap KPI AO", href: kpiRoutes.rekap, icon: Table2 },
  { label: "Report Cabang", href: kpiRoutes.cabang, icon: Building2 },
]

type KpiShellProps = { activeHref: string; onNavigate: (href: string) => void; children: ReactNode }
type Theme = "light" | "dark"
type KpiToolbarContextValue = { setToolbar: (toolbar: ReactNode) => void }

const KpiToolbarContext = createContext<KpiToolbarContextValue | null>(null)

export function useKpiToolbar() {
  const context = useContext(KpiToolbarContext)
  if (!context) throw new Error("useKpiToolbar harus dipakai di dalam KpiShell")
  return context
}

function initialTheme(): Theme {
  if (typeof window === "undefined") return "light"
  return localStorage.getItem("monbis-kpi-theme") === "dark" ? "dark" : "light"
}

function Sidebar({ compact, mobile, activeHref, onClose, onNavigate }: {
  compact: boolean
  mobile?: boolean
  activeHref: string
  onClose?: () => void
  onNavigate: (href: string) => void
}) {
  return (
    <aside className={cn("flex h-full min-h-0 flex-col border-r border-[var(--line)] bg-[var(--surface)]", mobile ? "w-[min(88vw,320px)] shadow-xl" : compact ? "w-[76px]" : "w-64")}>
      <div className={cn("flex min-h-16 items-center border-b border-[var(--line-soft)] px-4", compact && !mobile ? "justify-center" : "justify-between")}>
        <a href={kpiRoutes.setting} onClick={(event) => { event.preventDefault(); onNavigate(kpiRoutes.setting); onClose?.() }} className={cn("flex min-w-0 items-center gap-3", compact && !mobile && "justify-center")} aria-label="KPI Tukin">
          <span className="grid size-9 shrink-0 place-items-center rounded-md bg-[var(--ink)] text-sm font-semibold text-[var(--surface)]">K</span>
          <span className={cn("min-w-0", compact && !mobile && "sr-only")}>
            <span className="block truncate text-sm font-semibold tracking-tight text-[var(--ink)]">KPI Tukin</span>
          </span>
        </a>
        {mobile && <Button variant="ghost" size="icon" aria-label="Tutup navigasi" onClick={onClose}><X size={17} /></Button>}
      </div>

      <nav aria-label="Navigasi KPI Tukin" className="min-h-0 flex-1 overflow-y-auto overscroll-contain px-3 py-6">
        <p className={cn("mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)]", compact && !mobile && "sr-only")}>KPI Tukin</p>
        <div className="space-y-1">
          {navItems.map(({ label, href, icon: Icon }) => {
            const active = href === activeHref
            return (
              <a
                key={href}
                href={href}
                aria-current={active ? "page" : undefined}
                onClick={(event) => {
                  if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return
                  event.preventDefault()
                  onNavigate(href)
                  onClose?.()
                }}
                title={compact && !mobile ? label : undefined}
                className={cn(
                  "flex min-h-10 items-center gap-3 rounded-md px-3 text-sm font-medium text-[var(--muted)] outline-none transition-colors hover:bg-[var(--surface-subtle)] hover:text-[var(--ink)] focus-visible:ring-2 focus-visible:ring-[var(--ring)]",
                  compact && !mobile && "justify-center px-0",
                  active && "bg-[var(--ink-soft)] text-[var(--ink)]",
                )}
              >
                <Icon size={16} strokeWidth={1.8} aria-hidden="true" />
                <span className={cn("truncate", compact && !mobile && "sr-only")}>{label}</span>
                {!compact && active && <ChevronRight className="ml-auto" size={15} aria-hidden="true" />}
              </a>
            )
          })}
        </div>
      </nav>
      {!compact && <div className="border-t border-[var(--line-soft)] px-4 py-4 text-[11px] leading-5 text-[var(--muted)]">Perhitungan KPI dan Tukin berasal dari layanan terpusat.</div>}
    </aside>
  )
}

export function KpiShell({ activeHref, onNavigate, children }: KpiShellProps) {
  const [collapsed, setCollapsed] = useState(false)
  const [mobileOpen, setMobileOpen] = useState(false)
  const [theme, setTheme] = useState<Theme>(initialTheme)
  const [toolbar, setToolbar] = useState<ReactNode>(null)
  const [filterOpen, setFilterOpen] = useState(false)
  const [profileOpen, setProfileOpen] = useState(false)
  const [user, setUser] = useState<MonbisUser | null>(null)
  const profileRef = useRef<HTMLDivElement>(null)
  const dark = theme === "dark"
  const toolbarContext = useMemo(() => ({ setToolbar }), [])

  useEffect(() => {
    document.documentElement.classList.toggle("dark", dark)
    document.documentElement.style.colorScheme = dark ? "dark" : "light"
    localStorage.setItem("monbis-kpi-theme", theme)
  }, [dark, theme])

  useEffect(() => {
    if (!mobileOpen) return undefined
    const onKeyDown = (event: KeyboardEvent) => event.key === "Escape" && setMobileOpen(false)
    const previousBodyOverflow = document.body.style.overflow
    document.body.style.overflow = "hidden"
    window.addEventListener("keydown", onKeyDown)
    return () => { document.body.style.overflow = previousBodyOverflow; window.removeEventListener("keydown", onKeyDown) }
  }, [mobileOpen])

  useEffect(() => {
    setFilterOpen(false)
    setProfileOpen(false)
  }, [activeHref])

  useEffect(() => {
    const controller = new AbortController()
    loadCurrentUser(controller.signal).then(setUser).catch(() => undefined)
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!profileOpen) return undefined
    const onPointerDown = (event: PointerEvent) => {
      if (!profileRef.current?.contains(event.target as Node)) setProfileOpen(false)
    }
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") setProfileOpen(false)
    }
    document.addEventListener("pointerdown", onPointerDown)
    window.addEventListener("keydown", onKeyDown)
    return () => {
      document.removeEventListener("pointerdown", onPointerDown)
      window.removeEventListener("keydown", onKeyDown)
    }
  }, [profileOpen])

  return (
    <KpiToolbarContext.Provider value={toolbarContext}>
    <div className="flex h-dvh min-h-0 overflow-hidden bg-[var(--page)] text-[var(--ink)]">
      <div className={cn("hidden h-full shrink-0 lg:block", collapsed ? "w-[76px]" : "w-64")}>
        <Sidebar compact={collapsed} activeHref={activeHref} onNavigate={onNavigate} />
      </div>
      {mobileOpen && <div className="fixed inset-0 z-50 h-dvh lg:hidden"><button className="absolute inset-0 cursor-default bg-black/35" aria-label="Tutup navigasi" onClick={() => setMobileOpen(false)} /><div className="relative h-full"><Sidebar mobile compact={false} activeHref={activeHref} onClose={() => setMobileOpen(false)} onNavigate={onNavigate} /></div></div>}

      <div className="flex h-full min-h-0 min-w-0 flex-1 flex-col">
        <header className="relative z-30 shrink-0 border-b border-[var(--line)] bg-[var(--page)]/95 backdrop-blur-sm">
          <div className="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
            <div className="flex min-w-0 items-center gap-2">
              <Button variant="ghost" size="icon" className="lg:hidden" aria-label="Buka navigasi" onClick={() => setMobileOpen(true)}><Menu size={17} /></Button>
              <Button variant="ghost" size="icon" className="hidden lg:inline-flex" aria-label={collapsed ? "Bentangkan sidebar" : "Ciutkan sidebar"} onClick={() => setCollapsed((value) => !value)}>{collapsed ? <PanelLeftOpen size={17} /> : <PanelLeftClose size={17} />}</Button>
              <div className="min-w-0">
                <p className="truncate text-xs text-[var(--muted)]">KPI Tukin</p>
                <h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">KPI Tukin</h1>
              </div>
            </div>
            <div className="relative flex items-center gap-2" ref={profileRef}>
              <Button variant="outline" size="icon" aria-label={dark ? "Aktifkan mode terang" : "Aktifkan mode gelap"} title={dark ? "Mode terang" : "Mode gelap"} onClick={() => setTheme(dark ? "light" : "dark")}>
                {dark ? <Sun size={16} /> : <Moon size={16} />}
              </Button>
              {toolbar && <Button variant="outline" size="sm" aria-expanded={filterOpen} aria-controls="kpi-filter-bar" onClick={() => setFilterOpen((value) => !value)} className={filterOpen ? "border-[#f5a623] bg-[#f5a623] text-[#111] hover:border-[#f5a623] hover:bg-[#f5a623]" : undefined}><SlidersHorizontal size={15} /> Filter</Button>}
              <button
                type="button"
                className={cn("flex min-h-9 max-w-[220px] items-center gap-2 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2.5 text-left outline-none transition-colors hover:bg-[var(--surface-subtle)] focus-visible:ring-2 focus-visible:ring-[var(--ring)]", profileOpen && "border-[var(--accent)]")}
                aria-expanded={profileOpen}
                aria-haspopup="menu"
                aria-label="Buka profil pengguna"
                onClick={() => setProfileOpen((value) => !value)}
              >
                <span className="grid size-7 shrink-0 place-items-center rounded-full bg-[var(--accent)] text-[10px] font-semibold text-white">{userInitials(user)}</span>
                <span className="hidden min-w-0 sm:block"><span className="block truncate text-xs font-medium text-[var(--ink)]">{user?.full_name || "Profil pengguna"}</span><span className="block truncate text-[10px] text-[var(--muted)]">{user?.job_position || "SSO aktif"}</span></span>
                <UserCircle size={16} className="shrink-0 text-[var(--muted)] sm:hidden" aria-hidden="true" />
              </button>
              {profileOpen && <ProfileMenu user={user} />}
            </div>
          </div>
          {toolbar && filterOpen && <div id="kpi-filter-bar" className="kpi-filter-bar border-t border-[var(--line-soft)] px-4 py-3 sm:px-6 lg:px-8">{toolbar}</div>}
        </header>
        <main className="kpi-scrollbar min-h-0 flex-1 overflow-x-hidden overflow-y-auto overscroll-contain">{children}</main>
      </div>
    </div>
    </KpiToolbarContext.Provider>
  )
}

function ProfileMenu({ user }: { user: MonbisUser | null }) {
  return (
    <div className="absolute right-0 top-[calc(100%+10px)] z-50 w-[min(90vw,320px)] overflow-hidden rounded-xl border border-[var(--line)] bg-[var(--surface)] shadow-xl" role="menu">
      <div className="border-b border-[var(--line-soft)] bg-[var(--surface-subtle)] px-4 py-3">
        <p className="text-[10px] font-semibold uppercase tracking-[0.14em] text-[var(--muted)]">Profil</p>
        <p className="mt-1 truncate text-sm font-semibold text-[var(--ink)]">{user?.full_name || "User belum terbaca"}</p>
        <p className="mt-0.5 truncate text-xs text-[var(--muted)]">{user?.employee_id || "Sesi SSO"}</p>
      </div>
      <dl className="grid gap-2 px-4 py-3 text-xs">
        <ProfileLine label="Jabatan" value={user?.job_position} />
        <ProfileLine label="Unit / kantor" value={[user?.unit_kerja, user?.branch_name].filter(Boolean).join(" · ")} />
        <ProfileLine label="Kode" value={user?.kode_kantor || user?.kode} />
      </dl>
      <div className="border-t border-[var(--line-soft)] p-2">
        <a href={legacyPageUrl("/pages/profile.php")} className="flex min-h-9 items-center rounded-md px-2.5 text-xs font-medium text-[var(--accent)] hover:bg-[var(--surface-subtle)]" role="menuitem">Buka profil</a>
      </div>
    </div>
  )
}

function ProfileLine({ label, value }: { label: string; value?: string }) {
  return <div className="grid grid-cols-[92px_1fr] gap-2"><dt className="text-[var(--muted)]">{label}</dt><dd className="truncate font-medium text-[var(--ink)]">{value || "-"}</dd></div>
}
