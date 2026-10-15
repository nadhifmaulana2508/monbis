import { postKpi } from "@/lib/api-base"

import type { SettingKpiResponse } from "./setting-kpi-jabatan-types"

export function loadSettingKpi(signal?: AbortSignal) {
  return postKpi<SettingKpiResponse>({ type: "setting" }, signal)
}
