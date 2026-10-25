import { useEffect } from "react"

import { JatuhTempoPage } from "@/features/jatuh-tempo/jatuh-tempo-page"
import { RealisasiGrowthPage } from "@/features/realisasi-growth/realisasi-growth-page"
import { appRoutes } from "@/lib/routes"

function App() {
  useEffect(() => {
    if (window.location.pathname === "/") {
      window.history.replaceState(null, "", appRoutes.realisasiGrowth)
    }
  }, [])

  return window.location.pathname === appRoutes.jatuhTempo
    ? <JatuhTempoPage />
    : <RealisasiGrowthPage />
}

export default App
