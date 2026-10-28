import { lazy, Suspense, useEffect, useMemo, useState } from "react"
import { LoaderCircle } from "lucide-react"

import { appRoutes } from "@/lib/routes"
import { clearMonbisSession, validateMonbisSession } from "@/lib/monbis-auth"
import { LoginPage } from "@/features/login/login-page"

const JatuhTempoPage = lazy(() => import("@/features/jatuh-tempo/jatuh-tempo-page").then((module) => ({ default: module.JatuhTempoPage })))
const PipelineKreditPage = lazy(() => import("@/features/pipeline-kredit/pipeline-kredit-page").then((module) => ({ default: module.PipelineKreditPage })))
const NplPage = lazy(() => import("@/features/npl/npl-page").then((module) => ({ default: module.NplPage })))
const RealisasiGrowthPage = lazy(() => import("@/features/realisasi-growth/realisasi-growth-page").then((module) => ({ default: module.RealisasiGrowthPage })))
const RbbRealisasiKreditPage = lazy(() => import("@/features/rbb-realisasi-kredit/rbb-realisasi-kredit-page").then((module) => ({ default: module.RbbRealisasiKreditPage })))
const RepaymentRatePage = lazy(() => import("@/features/repayment-rate/repayment-rate-page").then((module) => ({ default: module.RepaymentRatePage })))
const RecoveryNplPage = lazy(() => import("@/features/recovery-npl/recovery-npl-page").then((module) => ({ default: module.RecoveryNplPage })))
const PotensiNplPage = lazy(() => import("@/features/potensi-npl/potensi-npl-page").then((module) => ({ default: module.PotensiNplPage })))
const UpdateKomitmenFlowPage = lazy(() => import("@/features/update-komitmen-flow/update-komitmen-flow-page").then((module) => ({ default: module.UpdateKomitmenFlowPage })))
const FlowParPage = lazy(() => import("@/features/flow-par/flow-par-page").then((module) => ({ default: module.FlowParPage })))
const LabaRugiPage = lazy(() => import("@/features/laba-rugi/laba-rugi-page").then((module) => ({ default: module.LabaRugiPage })))

const reportRoutes = Object.values(appRoutes).filter((route) => route !== appRoutes.loginMonbisV2)
const routeTitles = new Map<string, string>([
  [appRoutes.jatuhTempo, "Jatuh Tempo Kredit"],
  [appRoutes.pipelineKredit, "Pipeline Kredit"],
  [appRoutes.npl, "NPL"],
  [appRoutes.rbbRealisasiKredit, "RBB Realisasi Kredit"],
  [appRoutes.repaymentRate, "Repayment Rate"],
  [appRoutes.recoveryNpl, "Recovery NPL"],
  [appRoutes.potensiNpl, "Potensi NPL"],
  [appRoutes.updateKomitmenFlow, "Update Komitmen Flow"],
  [appRoutes.flowPar, "Flow PAR"],
  [appRoutes.labaRugi, "Laba Rugi"],
  [appRoutes.realisasiGrowth, "Realisasi Kredit & Growth"],
])

function requestedRoute() {
  const requested = new URLSearchParams(window.location.search).get("next")
  if (!requested) return appRoutes.realisasiGrowth
  try {
    const candidate = new URL(requested, window.location.origin).pathname
    return reportRoutes.includes(candidate) ? candidate : appRoutes.realisasiGrowth
  } catch { return appRoutes.realisasiGrowth }
}

function loginHref(next: string) {
  const login = new URL(appRoutes.loginMonbisV2, window.location.origin)
  const target = `${window.location.origin}${next}`
  login.searchParams.set("next", target)
  return login.toString()
}

function SessionCheck() {
  return <main className="grid min-h-[100dvh] place-items-center bg-background px-4 text-sm text-muted-foreground"><div className="flex items-center gap-2"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" />Memverifikasi sesi MONBIS…</div></main>
}

function App() {
  const pathname = window.location.pathname
  const appRoot = import.meta.env.BASE_URL
  const normalizedRoot = appRoot.replace(/\/$/, "")
  const isAppRoot = pathname === "/" || pathname === appRoot || pathname === normalizedRoot
  const isLoginRoute = pathname === appRoutes.loginMonbisV2
  const isKnownRoute = reportRoutes.includes(pathname)
  const [session, setSession] = useState<"checking" | "signed-out" | "signed-in">("checking")
  const activePath = isAppRoot ? appRoutes.realisasiGrowth : pathname

  useEffect(() => {
    let active = true
    void validateMonbisSession().then((user) => { if (active) setSession(user ? "signed-in" : "signed-out") })
    return () => { active = false }
  }, [])

  useEffect(() => {
    const onExpired = () => {
      clearMonbisSession()
      setSession("signed-out")
    }
    window.addEventListener("monbis:session-expired", onExpired)
    return () => window.removeEventListener("monbis:session-expired", onExpired)
  }, [])

  useEffect(() => {
    if (session === "checking") return
    if (session === "signed-out") {
      if (isLoginRoute) return
      const next = isKnownRoute ? `${pathname}${window.location.search}` : appRoutes.realisasiGrowth
      window.location.replace(loginHref(next))
      return
    }
    if (session === "signed-in" && isLoginRoute) {
      window.location.replace(requestedRoute())
      return
    }
    if (session === "signed-in" && (isAppRoot || !isKnownRoute)) window.location.replace(appRoutes.realisasiGrowth)
  }, [isAppRoot, isKnownRoute, isLoginRoute, pathname, session])

  const page = useMemo(() => pathname === appRoutes.jatuhTempo ? <JatuhTempoPage />
    : pathname === appRoutes.pipelineKredit ? <PipelineKreditPage />
      : pathname === appRoutes.npl ? <NplPage />
        : pathname === appRoutes.rbbRealisasiKredit ? <RbbRealisasiKreditPage />
          : pathname === appRoutes.repaymentRate ? <RepaymentRatePage />
            : pathname === appRoutes.recoveryNpl ? <RecoveryNplPage />
              : pathname === appRoutes.potensiNpl ? <PotensiNplPage />
                : pathname === appRoutes.updateKomitmenFlow ? <UpdateKomitmenFlowPage />
                  : pathname === appRoutes.flowPar ? <FlowParPage />
                    : pathname === appRoutes.labaRugi ? <LabaRugiPage />
                      : <RealisasiGrowthPage />, [pathname])

  useEffect(() => {
    const routeTitle = routeTitles.get(activePath) || "MONBIS"
    document.title = `${isLoginRoute ? "Login" : routeTitle} | MONBIS`
    document.querySelector('meta[name="description"]')?.setAttribute("content", isLoginRoute ? "Login pegawai MONBIS." : `Laporan ${routeTitle} MONBIS.`)
  }, [activePath, isLoginRoute])

  if (session === "checking") return <SessionCheck />
  if (isLoginRoute && session === "signed-out") return <LoginPage onLogin={() => setSession("signed-in")} />
  if (session !== "signed-in" || isLoginRoute || isAppRoot || !isKnownRoute) return <SessionCheck />

  return <Suspense fallback={<SessionCheck />}>{page}</Suspense>
}

export default App
