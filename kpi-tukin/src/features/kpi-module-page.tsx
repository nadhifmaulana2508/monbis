import { kpiRoutes, type KpiRoute } from "@/lib/routes"

import { NilaiKpiAoPage } from "./nilai-kpi-ao/nilai-kpi-ao-page"
import { RekapKpiAoPage } from "./rekap-kpi-ao/rekap-kpi-ao-page"
import { ReportCabangPage } from "./report-cabang/report-cabang-page"

export function KpiModulePage({ route }: { route: Exclude<KpiRoute, "/kpi/setting_kpi_jabatan"> }) {
  if (route === kpiRoutes.nilai) return <NilaiKpiAoPage />
  if (route === kpiRoutes.generate) return <NilaiKpiAoPage mode="bulk" />
  if (route === kpiRoutes.rekap) return <RekapKpiAoPage />
  return <ReportCabangPage />
}
