import { useEffect, useState, type ReactNode } from "react"
import type { LucideIcon } from "lucide-react"
import {
  BarChart3,
  CalendarClock,
  ChevronRight,
  Menu,
  Moon,
  PanelLeftClose,
  PanelLeftOpen,
  SlidersHorizontal,
  Sun,
  X,
} from "lucide-react"

import { Button, buttonVariants } from "@/components/ui/button"
import { cn } from "@/lib/utils"
import { appRoutes } from "@/lib/routes"

type NavItem = {
  label: string
  href?: string
  icon?: LucideIcon
  children?: NavItem[]
}

type NavGroup = {
  label: string
  items: NavItem[]
  hideLabel?: boolean
}

type AppShellProps = {
  children: ReactNode
  title: string
  description: string
  activeHref: string
  legacyHref?: string
  navbarContent?: ReactNode
  showPageTitle?: boolean
}

type ThemeMode = "system" | "light" | "dark"

function getStoredTheme(): ThemeMode {
  if (typeof window === "undefined") return "system"
  const value = window.localStorage.getItem("monbis-theme")
  return value === "light" || value === "dark" ? value : "system"
}

function themeLabel(theme: ThemeMode) {
  return theme === "system" ? "Sistem" : theme === "light" ? "Terang" : "Gelap"
}

const navGroups: NavGroup[] = [
  {
    label: "KPI Bisnis",
    hideLabel: true,
    items: [
      {
        label: "KPI Bisnis",
        icon: BarChart3,
        children: [
          { label: "Setting KPI Jabatan", href: "/pages/setting_kpi_jabatan.php" },
          { label: "Nilai KPI AO", href: "/pages/hitung_kpi_ao.php" },
          { label: "Generate KPI AO", href: "/pages/generate_kpi_ao.php" },
          { label: "Rekap KPI AO", href: "/pages/rekap_kpi_ao.php" },
          { label: "Report Cabang", href: "/pages/raport_cabang.php" },
        ],
      },
    ],
  },
  {
    label: "Laporan kredit",
    items: [
      {
        label: "Realisasi kredit",
        href: appRoutes.realisasiGrowth,
        icon: BarChart3,
      },
      {
        label: "Jatuh tempo kredit",
        href: appRoutes.jatuhTempo,
        icon: CalendarClock,
      },
    ],
  },
]

function Navigation({
  activeHref,
  compact,
  onNavigate,
}: {
  activeHref: string
  compact: boolean
  onNavigate?: () => void
}) {
  const [openItems, setOpenItems] = useState<string[]>(() => (
    navGroups.flatMap((group) => group.items)
      .filter((item) => item.label === "KPI Bisnis" || item.children?.some((child) => child.href === activeHref))
      .map((item) => item.label)
  ))

  const toggleItem = (label: string) => {
    setOpenItems((current) => current.includes(label)
      ? current.filter((item) => item !== label)
      : [...current, label])
  }

  return (
    <nav aria-label="Navigasi utama" className="space-y-5">
      {navGroups.map((group) => (
        <div key={group.label}>
          {!group.hideLabel && (
            <p
              className={cn(
                "mb-2 px-3 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-muted-foreground",
                compact && "sr-only",
              )}
            >
              {group.label}
            </p>
          )}
          <div className="space-y-1">
            {group.items.map((item) => {
              const Icon = item.icon
              const hasChildren = Boolean(item.children?.length)
              const isOpen = openItems.includes(item.label)
              const isActive = item.href === activeHref || item.children?.some((child) => child.href === activeHref)

              return (
                <div key={item.href || item.label}>
                  {hasChildren ? (
                    <button
                      type="button"
                      aria-expanded={compact ? undefined : isOpen}
                      aria-label={compact ? item.label : undefined}
                      title={compact ? item.label : undefined}
                      onClick={() => !compact && toggleItem(item.label)}
                      className={cn(
                        "group flex min-h-11 w-full items-center gap-3 rounded-lg px-3 text-left text-sm font-medium text-muted-foreground outline-none transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-3 focus-visible:ring-ring/50",
                        compact && "justify-center px-0",
                        isActive && "bg-muted text-foreground",
                      )}
                    >
                      {Icon && <Icon className="size-[1.05rem] shrink-0" aria-hidden="true" />}
                      <span className={cn("min-w-0 truncate", compact && "sr-only")}>{item.label}</span>
                      {!compact && <ChevronRight className={cn("ml-auto size-4 transition-transform", isOpen && "rotate-90")} aria-hidden="true" />}
                    </button>
                  ) : item.href ? (
                    <a
                      href={item.href}
                      aria-current={isActive ? "page" : undefined}
                      onClick={onNavigate}
                      className={cn(
                        "group flex min-h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium text-muted-foreground outline-none transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-3 focus-visible:ring-ring/50",
                        compact && "justify-center px-0",
                        isActive && "bg-muted text-foreground hover:bg-muted",
                      )}
                    >
                      {Icon && <Icon className="size-[1.05rem] shrink-0" aria-hidden="true" />}
                      <span className={cn("min-w-0 truncate", compact && "sr-only")}>{item.label}</span>
                      {!compact && isActive && <ChevronRight className="ml-auto size-4" aria-hidden="true" />}
                    </a>
                  ) : null}

                  {hasChildren && isOpen && !compact && (
                    <div className="ml-4 mt-1 space-y-1 border-l border-border pl-3">
                      {item.children?.map((child) => {
                        const childActive = child.href === activeHref
                        return child.href ? (
                          <a
                            key={child.href}
                            href={child.href}
                            aria-current={childActive ? "page" : undefined}
                            onClick={onNavigate}
                            className={cn(
                              "flex min-h-10 items-center rounded-lg px-3 text-sm font-medium text-muted-foreground outline-none transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-3 focus-visible:ring-ring/50",
                              childActive && "bg-muted text-foreground hover:bg-muted",
                            )}
                          >
                            <span className="truncate">{child.label}</span>
                          </a>
                        ) : null
                      })}
                    </div>
                  )}
                </div>
              )
            })}
          </div>
        </div>
      ))}
    </nav>
  )
}

function Brand({ compact }: { compact: boolean }) {
  return (
    <a
      href="/pages/dashboard.php"
      aria-label="MONBIS, buka dashboard"
      className={cn(
        "flex min-h-11 items-center gap-3 rounded-lg px-2 outline-none focus-visible:ring-3 focus-visible:ring-ring/50",
        compact && "justify-center px-0",
      )}
    >
      <span className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary text-sm font-bold text-primary-foreground">
        M
      </span>
      <span className={cn("min-w-0", compact && "sr-only")}>
        <span className="block truncate text-sm font-semibold tracking-tight text-foreground">
          MONBIS
        </span>
        <span className="block truncate text-xs text-muted-foreground">
          Laporan bisnis
        </span>
      </span>
    </a>
  )
}

function Sidebar({
  compact,
  mobile = false,
  activeHref,
  onClose,
}: {
  compact: boolean
  mobile?: boolean
  activeHref: string
  onClose?: () => void
}) {
  return (
    <aside
      aria-label="Navigasi MONBIS"
      className={cn(
        "flex h-full min-h-0 flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground",
        mobile ? "w-[min(86vw,320px)] shadow-xl" : compact ? "w-[76px]" : "w-64",
      )}
    >
      <div
        className={cn(
          "flex min-h-16 items-center border-b border-sidebar-border px-3",
          compact && !mobile ? "justify-center" : "justify-between",
        )}
      >
        <Brand compact={compact && !mobile} />
        {mobile && (
          <Button
            type="button"
            variant="ghost"
            size="icon"
            className="size-11"
            aria-label="Tutup menu navigasi"
            onClick={onClose}
          >
            <X aria-hidden="true" />
          </Button>
        )}
      </div>

      <div className="min-h-0 flex-1 overflow-y-auto px-3 py-5">
        <Navigation
          activeHref={activeHref}
          compact={compact && !mobile}
          onNavigate={onClose}
        />
      </div>

      {!compact && <div className="min-h-3 border-t border-sidebar-border" />}
    </aside>
  )
}

export function AppShell({
  children,
  title,
  description,
  activeHref,
  legacyHref,
  navbarContent,
  showPageTitle = true,
}: AppShellProps) {
  const [collapsed, setCollapsed] = useState(false)
  const [mobileOpen, setMobileOpen] = useState(false)
  const [navbarFilterOpen, setNavbarFilterOpen] = useState(false)
  const [theme, setTheme] = useState<ThemeMode>(getStoredTheme)
  const [systemDark, setSystemDark] = useState(() => (
    typeof window !== "undefined" && window.matchMedia("(prefers-color-scheme: dark)").matches
  ))

  useEffect(() => {
    const media = window.matchMedia("(prefers-color-scheme: dark)")
    const syncSystemTheme = () => setSystemDark(media.matches)
    syncSystemTheme()
    media.addEventListener("change", syncSystemTheme)
    return () => media.removeEventListener("change", syncSystemTheme)
  }, [])

  useEffect(() => {
    const isDark = theme === "dark" || (theme === "system" && systemDark)
    document.documentElement.classList.toggle("dark", isDark)
    document.documentElement.style.colorScheme = isDark ? "dark" : "light"
    window.localStorage.setItem("monbis-theme", theme)
  }, [systemDark, theme])

  useEffect(() => {
    if (!mobileOpen) {
      document.body.style.overflow = ""
      return undefined
    }

    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") {
        setMobileOpen(false)
      }
    }

    document.body.style.overflow = "hidden"
    window.addEventListener("keydown", onKeyDown)

    return () => {
      document.body.style.overflow = ""
      window.removeEventListener("keydown", onKeyDown)
    }
  }, [mobileOpen])

  return (
    <div className="flex h-screen h-dvh min-h-0 overflow-hidden bg-muted/35 text-foreground">
      <div className="flex h-full min-h-0 w-full">
        <div
          className={cn(
            "hidden h-full shrink-0 border-sidebar-border transition-[width] duration-150 lg:block",
            collapsed ? "w-[76px]" : "w-64",
          )}
        >
          <Sidebar compact={collapsed} activeHref={activeHref} />
        </div>

        {mobileOpen && (
          <div className="fixed inset-0 z-40 lg:hidden">
            <button
              type="button"
              aria-label="Tutup menu navigasi"
              className="absolute inset-0 cursor-default bg-foreground/35"
              onClick={() => setMobileOpen(false)}
            />
            <div className="relative h-full">
              <Sidebar
                compact={false}
                mobile
                activeHref={activeHref}
                onClose={() => setMobileOpen(false)}
              />
            </div>
          </div>
        )}

        <div className="flex h-full min-h-0 min-w-0 flex-1 flex-col">
          <header className="z-30 shrink-0 border-b border-border/80 bg-background/95 backdrop-blur-sm">
            <div className="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
              <div className="flex min-w-0 items-center gap-2">
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="size-11 lg:hidden"
                  aria-label="Buka menu navigasi"
                  aria-expanded={mobileOpen}
                  onClick={() => setMobileOpen(true)}
                >
                  <Menu aria-hidden="true" />
                </Button>
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="hidden size-11 lg:inline-flex"
                  aria-label={collapsed ? "Bentangkan sidebar" : "Ciutkan sidebar"}
                  aria-expanded={!collapsed}
                  onClick={() => setCollapsed((value) => !value)}
                >
                  {collapsed ? (
                    <PanelLeftOpen aria-hidden="true" />
                  ) : (
                    <PanelLeftClose aria-hidden="true" />
                  )}
                </Button>
                {showPageTitle && (
                  <div className="min-w-0">
                    <p className="hidden truncate text-xs font-medium text-muted-foreground sm:block">
                      MONBIS / Laporan kredit
                    </p>
                    <h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">
                      {title}
                    </h1>
                  </div>
                )}
              </div>

              <div className="flex shrink-0 items-center gap-2">
                <Button
                  type="button"
                  variant="outline"
                  size="icon"
                  className="size-11 sm:size-8"
                  aria-label={`Aktifkan tema ${theme === "dark" || (theme === "system" && systemDark) ? "terang" : "gelap"}`}
                  title={`Tema saat ini: ${themeLabel(theme)} · Klik untuk mengganti`}
                  onClick={() => setTheme((current) => current === "dark" ? "light" : "dark")}
                >
                  {theme === "dark" || (theme === "system" && systemDark) ? <Moon aria-hidden="true" /> : <Sun aria-hidden="true" />}
                </Button>
                {navbarContent && (
                  <Button
                    type="button"
                    variant={navbarFilterOpen ? "signalYellow" : "outline"}
                    size="sm"
                    className="min-h-11 sm:min-h-8"
                    aria-expanded={navbarFilterOpen}
                    aria-controls="navbar-filter-panel"
                    onClick={() => setNavbarFilterOpen((value) => !value)}
                  >
                    <SlidersHorizontal data-icon="inline-start" aria-hidden="true" />
                    Filter
                  </Button>
                )}
                <a
                  href={legacyHref || activeHref}
                  className={cn(buttonVariants({ variant: "signalGray", size: "sm" }), "hidden sm:inline-flex")}
                >
                  Buka halaman lama
                </a>
              </div>
            </div>
            {navbarContent && navbarFilterOpen && (
              <div id="navbar-filter-panel" className="border-t bg-muted/20 px-4 py-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-[1600px]">{navbarContent}</div>
              </div>
            )}
            {showPageTitle && (
              <p className="px-4 pb-3 text-xs text-muted-foreground sm:hidden">
                {description}
              </p>
            )}
          </header>

          <main className="min-h-0 min-w-0 flex-1 overflow-hidden">{children}</main>
        </div>
      </div>
    </div>
  )
}
