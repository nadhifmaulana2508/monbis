const routeBase = import.meta.env.BASE_URL.replace(/\/+$/, "")
const route = (path: string) => `${routeBase}${path}`

export const appRoutes = {
  loginMonbisV2: route("/login-monbis-v2"),
  realisasiGrowth: route("/realisasi-kredit-growth"),
  jatuhTempo: route("/jatuh-tempo"),
  pipelineKredit: route("/pipeline-kredit"),
  npl: route("/npl"),
  rbbRealisasiKredit: route("/rbb-realisasi-kredit"),
  repaymentRate: route("/repayment-rate"),
  recoveryNpl: route("/recovery-npl"),
  potensiNpl: route("/potensi-npl"),
  updateKomitmenFlow: route("/update-komitmen-flow"),
  flowPar: route("/flow-par"),
  labaRugi: route("/laba-rugi"),
} as const
