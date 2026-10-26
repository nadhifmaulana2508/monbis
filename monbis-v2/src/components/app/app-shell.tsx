import { useEffect, useLayoutEffect, useState, type ReactNode } from "react"
import type { LucideIcon } from "lucide-react"
import {
  BarChart3,
  ChevronRight,
  Landmark,
  Menu,
  Moon,
  PanelLeftClose,
  PanelLeftOpen,
  SlidersHorizontal,
  Sun,
  X,
  UserRound,
} from "lucide-react"

import { Button, buttonVariants } from "@/components/ui/button"
import { cn } from "@/lib/utils"
import { appRoutes } from "@/lib/routes"
import { ProfileSettingsModal } from "./profile-settings-modal"
import {
  defaultPreferences,
  getCurrentUser,
  loadAppearancePreferences,
  saveAppearancePreferences,
  type AppearancePreferences,
} from "@/lib/user-preferences"

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

function applyTheme(theme: ThemeMode, systemDark: boolean) {
  const isDark = theme === "dark" || (theme === "system" && systemDark)
  document.documentElement.classList.toggle("dark", isDark)
  document.documentElement.style.colorScheme = isDark ? "dark" : "light"
}

const navGroups: NavGroup[] = [
  {
    label: "Laporan kredit",
    hideLabel: true,
    items: [{
      label: "Laporan Kredit",
      icon: BarChart3,
      children: [
        {
          label: "Realisasi kredit",
          href: appRoutes.realisasiGrowth,
        },
        {
          label: "Jatuh tempo kredit",
          href: appRoutes.jatuhTempo,
        },
          {
            label: "Pipeline Kredit",
            href: appRoutes.pipelineKredit,
          },
        {
          label: "NPL",
          href: appRoutes.npl,
        },
        {
          label: "RBB Realisasi Kredit",
          href: appRoutes.rbbRealisasiKredit,
        },
        {
          label: "Repayment Rate",
          href: appRoutes.repaymentRate,
        },
        {
          label: "Recovery NPL",
          href: appRoutes.recoveryNpl,
        },
        {
          label: "Potensi NPL",
          href: appRoutes.potensiNpl,
        },
        {
          label: "Flow PAR",
          href: appRoutes.flowPar,
        },
      ],
    }],
  },
  {
    label: "Laporan keuangan",
    hideLabel: true,
    items: [{
      label: "Laporan Keuangan",
      icon: Landmark,
      children: [{ label: "Laba Rugi", href: appRoutes.labaRugi }],
    }],
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
  const [openItem, setOpenItem] = useState<string | null>(() => (
    navGroups.flatMap((group) => group.items)
      .filter((item) => item.label === "KPI Bisnis" || item.children?.some((child) => child.href === activeHref))
      .map((item) => item.label)[0] || null
  ))

  const toggleItem = (label: string) => {
    setOpenItem((current) => current === label ? null : label)
  }

  return (
    <nav aria-label="Navigasi utama" className="space-y-3 sm:space-y-5">
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
              const isOpen = openItem === item.label
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
                        "group flex min-h-10 w-full items-center gap-3 rounded-lg px-3 text-left text-sm font-medium text-muted-foreground outline-none transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-3 focus-visible:ring-ring/50 sm:min-h-11",
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
                        "group flex min-h-10 items-center gap-3 rounded-lg px-3 text-sm font-medium text-muted-foreground outline-none transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-3 focus-visible:ring-ring/50 sm:min-h-11",
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
                              "flex min-h-9 min-w-0 items-center rounded-lg px-3 py-1.5 text-sm font-medium text-muted-foreground outline-none transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-3 focus-visible:ring-ring/50 sm:min-h-10",
                              childActive && "bg-muted text-foreground hover:bg-muted",
                            )}
                          >
                            <span className="min-w-0 whitespace-normal leading-5">{child.label}</span>
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
        mobile ? "w-[min(90vw,320px)] shadow-xl" : compact ? "w-[76px]" : "w-64",
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

      <div className="min-h-0 flex-1 overscroll-contain overflow-y-auto px-2.5 py-4 sm:px-3 sm:py-5">
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
  const [profileOpen, setProfileOpen] = useState(false)
  const [theme, setTheme] = useState<ThemeMode>(getStoredTheme)
  const [currentUser] = useState(getCurrentUser)
  const [appearance, setAppearance] = useState<AppearancePreferences>(() => loadAppearancePreferences(getCurrentUser().key))
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

  useLayoutEffect(() => {
    applyTheme(theme, systemDark)
    window.localStorage.setItem("monbis-theme", theme)
  }, [systemDark, theme])

  useEffect(() => {
    const root = document.documentElement
    root.dataset.uiDensity = appearance.density
    root.dataset.uiFontScale = appearance.fontScale
    saveAppearancePreferences(currentUser.key, appearance)
  }, [appearance, currentUser.key])

  useEffect(() => {
    if (!profileOpen) return undefined
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") setProfileOpen(false)
    }
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [profileOpen])

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
    <div className="density-app flex h-[100dvh] min-h-0 overflow-hidden bg-muted/35 text-foreground">
      <div className="flex h-full min-h-0 w-full">
        <div
          className={cn(
            "relative z-40 hidden h-full shrink-0 border-sidebar-border transition-[width] duration-150 lg:block",
            collapsed ? "w-[76px]" : "w-64",
          )}
        >
          <Sidebar compact={collapsed} activeHref={activeHref} />
        </div>

        {mobileOpen && (
          <div className="fixed inset-0 z-[60] lg:hidden">
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
          <header id="monbis-app-navbar" className="relative z-50 shrink-0 border-b border-border/80 bg-background/95 backdrop-blur-sm">
            <div className="flex min-h-14 items-center justify-between gap-2 px-3 sm:min-h-16 sm:gap-3 sm:px-6 lg:px-8">
              <div className="flex min-w-0 items-center gap-2">
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="size-10 lg:hidden sm:size-11"
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
                  className="hidden size-10 lg:inline-flex sm:size-11"
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
                      {`MONBIS / ${activeHref === appRoutes.labaRugi ? "Laporan Keuangan" : "Laporan kredit"}`}
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
                  onClick={() => setTheme((current) => {
                    const next = current === "dark" ? "light" : "dark"
                    document.documentElement.classList.add("theme-switching")
                    applyTheme(next, systemDark)
                    window.requestAnimationFrame(() => {
                      window.requestAnimationFrame(() => document.documentElement.classList.remove("theme-switching"))
                    })
                    return next
                  })}
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
                <Button
                  type="button"
                  variant="outline"
                  size="icon"
                  className="size-11 sm:size-8"
                  aria-label={`Buka profil ${currentUser.name}`}
                  title={`Profil ${currentUser.name}`}
                  onClick={() => setProfileOpen(true)}
                >
                  <UserRound aria-hidden="true" />
                </Button>
                <a
                  href={legacyHref || activeHref}
                  className={cn(buttonVariants({ variant: "signalGray", size: "sm" }), "hidden sm:inline-flex")}
                >
                  Buka halaman lama
                </a>
              </div>
            </div>
            {navbarContent && navbarFilterOpen && (
              <div id="navbar-filter-panel" className="border-t bg-muted/20 px-4 py-2.5 sm:px-6 sm:py-3 lg:px-8">
                <div className="mx-auto max-w-[1600px]">{navbarContent}</div>
              </div>
            )}
            {showPageTitle && (
              <p className="px-4 pb-3 text-xs text-muted-foreground sm:hidden">
                {description}
              </p>
            )}
          </header>

          <main className="relative z-0 min-h-0 min-w-0 flex-1 overflow-hidden">{children}</main>
        </div>
      </div>
      <ProfileSettingsModal
        open={profileOpen}
        user={currentUser}
        preferences={appearance}
        onChange={setAppearance}
        onReset={() => setAppearance(defaultPreferences)}
        onClose={() => setProfileOpen(false)}
      />
    </div>
  )
}
