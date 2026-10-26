import { lazy, Suspense, useEffect } from "react"
import { LoaderCircle } from "lucide-react"

import { appRoutes } from "@/lib/routes"

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

function App() {
  const pathname = window.location.pathname
  const activePath = pathname === "/" ? appRoutes.realisasiGrowth : pathname

  useEffect(() => {
    if (pathname === "/") {
      window.history.replaceState(null, "", appRoutes.realisasiGrowth)
    }
    const routeTitle = new Map<string, string>([
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
    document.title = `${routeTitle.get(activePath) || "MONBIS"} | MONBIS`
    document.querySelector('meta[name="description"]')?.setAttribute("content", `Laporan ${routeTitle.get(activePath) || "bisnis"} MONBIS.`)
  }, [activePath, pathname])

  const page = pathname === appRoutes.jatuhTempo ? <JatuhTempoPage />
    : pathname === appRoutes.pipelineKredit ? <PipelineKreditPage />
      : pathname === appRoutes.npl ? <NplPage />
        : pathname === appRoutes.rbbRealisasiKredit ? <RbbRealisasiKreditPage />
          : pathname === appRoutes.repaymentRate ? <RepaymentRatePage />
            : pathname === appRoutes.recoveryNpl ? <RecoveryNplPage />
              : pathname === appRoutes.potensiNpl ? <PotensiNplPage />
                : pathname === appRoutes.updateKomitmenFlow ? <UpdateKomitmenFlowPage />
                : pathname === appRoutes.flowPar ? <FlowParPage />
                  : pathname === appRoutes.labaRugi ? <LabaRugiPage />
              : <RealisasiGrowthPage />

  return <Suspense fallback={<div className="flex h-[100dvh] items-center justify-center gap-2 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" />Memuat laporan...</div>}>{page}</Suspense>
}

export default App
