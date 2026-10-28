const appBase = typeof window !== "undefined" && /^\/report-dpk\/kpi(?:\/|$)/.test(window.location.pathname)
  ? "/report-dpk/kpi"
  : "/kpi"

export const kpiRoutes = {
  setting: `${appBase}/setting_kpi_jabatan`,
  nilai: `${appBase}/nilai_kpi_ao`,
  generate: `${appBase}/generate_kpi_ao`,
  rekap: `${appBase}/rekap_kpi_ao`,
  cabang: `${appBase}/report_cabang`,
} as const

export type KpiRoute = typeof kpiRoutes[keyof typeof kpiRoutes]

export function isKpiRoute(pathname: string): pathname is KpiRoute {
  return Object.values(kpiRoutes).includes(pathname as KpiRoute)
}

export function legacyPageUrl(pathname: string) {
  if (!import.meta.env.DEV) return pathname
  return `http://localhost${pathname.startsWith("/report-dpk/") ? pathname : `/report-dpk${pathname}`}`
}
