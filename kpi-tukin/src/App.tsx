import { useCallback, useEffect, useState } from "react"

import { KpiShell } from "@/components/app/kpi-shell"
import { KpiModulePage } from "@/features/kpi-module-page"
import { SettingKpiJabatanPage } from "@/features/setting-kpi-jabatan/setting-kpi-jabatan-page"
import { kpiRoutes, isKpiRoute } from "@/lib/routes"

function App() {
  const [pathname, setPathname] = useState(() => window.location.pathname)

  useEffect(() => {
    const onPopState = () => setPathname(window.location.pathname)
    window.addEventListener("popstate", onPopState)
    return () => window.removeEventListener("popstate", onPopState)
  }, [pathname])

  const route = isKpiRoute(pathname) ? pathname : kpiRoutes.setting
  const navigate = useCallback((href: string, replace = false) => {
    if (window.location.pathname === href) return
    window.history[replace ? "replaceState" : "pushState"]({}, "", href)
    setPathname(href)
    window.scrollTo({ top: 0, behavior: "instant" })
  }, [])

  useEffect(() => {
    if (pathname === "/" || pathname === "/kpi-tukin") navigate(kpiRoutes.setting, true)
  }, [navigate, pathname])

  const content = route === kpiRoutes.setting
    ? <SettingKpiJabatanPage />
    : <KpiModulePage route={route} />

  return <KpiShell activeHref={route} onNavigate={navigate}>{content}</KpiShell>
}

export default App
