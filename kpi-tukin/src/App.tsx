import { useCallback, useEffect, useState } from "react"
import { LoaderCircle } from "lucide-react"

import { KpiShell } from "@/components/app/kpi-shell"
import { KpiModulePage } from "@/features/kpi-module-page"
import { SettingKpiJabatanPage } from "@/features/setting-kpi-jabatan/setting-kpi-jabatan-page"
import { KpiLoginPage } from "@/features/login/kpi-login-page"
import { kpiRoutes, isKpiRoute } from "@/lib/routes"
import { validateKpiSession } from "@/lib/auth"

function App() {
  const [pathname, setPathname] = useState(() => window.location.pathname)
  const [session, setSession] = useState<"checking" | "signed-out" | "signed-in">("checking")

  useEffect(() => {
    const controller = new AbortController()
    void validateKpiSession(controller.signal).then((user) => setSession(user ? "signed-in" : "signed-out"))
    return () => controller.abort()
  }, [])

  useEffect(() => {
    const onExpired = () => setSession("signed-out")
    window.addEventListener("monbis:session-expired", onExpired)
    return () => window.removeEventListener("monbis:session-expired", onExpired)
  }, [])

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

  if (session === "checking") {
    return <main className="grid min-h-[100dvh] place-items-center bg-background px-4 text-sm text-muted-foreground"><div className="flex items-center gap-2"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" />Memverifikasi sesi MONBIS…</div></main>
  }

  if (session === "signed-out") {
    return <KpiLoginPage onAuthenticated={() => setSession("signed-in")} />
  }

  const content = route === kpiRoutes.setting
    ? <SettingKpiJabatanPage />
    : <KpiModulePage route={route} />

  return <KpiShell activeHref={route} onNavigate={navigate}>{content}</KpiShell>
}

export default App
