export const kpiRoutes = {
  setting: "/kpi/setting_kpi_jabatan",
  nilai: "/kpi/nilai_kpi_ao",
  generate: "/kpi/generate_kpi_ao",
  rekap: "/kpi/rekap_kpi_ao",
  cabang: "/kpi/report_cabang",
} as const

export type KpiRoute = typeof kpiRoutes[keyof typeof kpiRoutes]

export function isKpiRoute(pathname: string): pathname is KpiRoute {
  return Object.values(kpiRoutes).includes(pathname as KpiRoute)
}

export function legacyPageUrl(pathname: string) {
  return import.meta.env.DEV ? `http://localhost/report-dpk${pathname}` : pathname
}
