import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Info, LoaderCircle } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import type { SearchableOption } from "@/components/searchable-select"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"
import { RecoveryNplFilterPanel } from "./recovery-npl-filter-panel"
import { RecoveryNplDetailModal } from "./recovery-npl-modals"
import { recoveryAreas, recoveryCurrentUser, recoveryDetailPayload, recoveryMainPayload, recoveryName, recoveryNumber, recoveryPercent, recoveryRatio, recoverySortRows, recoveryUserCode, requestRecovery, exportRecovery, type RecoveryDetailType, type RecoveryFilters, type RecoveryOffice, type RecoveryRow, type RecoverySort } from "./recovery-npl-data"
import { RecoveryNplTable } from "./recovery-npl-table"

function localDate() {
  const date = new Date()
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`
}

type DetailSelection = { row: RecoveryRow; type: RecoveryDetailType }

export function RecoveryNplPage() {
  const userCode = recoveryUserCode(recoveryCurrentUser())
  const access = useMemo(() => true, [])
  const [filters, setFilters] = useState<RecoveryFilters>({ closingDate: localDate(), actualDate: localDate(), nominalField: "saldo_bank", area: userCode === "000" ? "ALL" : `CAB-${userCode}` })
  const [offices, setOffices] = useState<RecoveryOffice[]>([])
  const [defaultsReady, setDefaultsReady] = useState(false)
  const [rows, setRows] = useState<RecoveryRow[]>([])
  const [total, setTotal] = useState<RecoveryRow | null>(null)
  const [sort, setSort] = useState<RecoverySort>({ column: "kode_cabang", direction: "asc" })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [infoOpen, setInfoOpen] = useState(false)
  const [detail, setDetail] = useState<DetailSelection | null>(null)
  const [detailRows, setDetailRows] = useState<RecoveryRow[]>([])
  const [detailLoading, setDetailLoading] = useState(false)
  const [detailError, setDetailError] = useState<string | null>(null)
  const [detailStatus, setDetailStatus] = useState("all")
  const tableRef = useRef<HTMLTableElement>(null)
  const detailAbort = useRef<AbortController | null>(null)

  useEffect(() => {
    const controller = new AbortController()
    void Promise.allSettled([
      requestRecovery<{ last_created?: string; last_closing?: string }>("date", { signal: controller.signal }),
      requestRecovery<RecoveryOffice[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kantor" }), signal: controller.signal }),
    ]).then(([date, office]) => {
      if (controller.signal.aborted) return
      setOffices(office.status === "fulfilled" && Array.isArray(office.value.data) ? office.value.data : [])
      if (date.status === "fulfilled" && date.value.data?.last_created) {
        const actualDate = date.value.data.last_created
        setFilters((current) => ({ ...current, actualDate, closingDate: date.value.data?.last_closing || actualDate }))
      }
      setDefaultsReady(true)
    })
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!defaultsReady || !filters.closingDate || !filters.actualDate) return undefined
    const controller = new AbortController()
    void requestRecovery<RecoveryRow[]>("npl", { method: "POST", body: JSON.stringify(recoveryMainPayload(filters)), signal: controller.signal })
      .then((response) => {
        if (controller.signal.aborted) return
        const records = Array.isArray(response.data) ? response.data : []
        const totalRow = records.find((row) => String(row.kode_cabang ?? row.kode_unit ?? "").toUpperCase() === "TOTAL" || recoveryName(row).toUpperCase().includes("TOTAL")) || null
        const reportRows = records.filter((row) => row !== totalRow)
        setTotal(totalRow)
        setRows(recoverySortRows(reportRows, { column: "kode_cabang", direction: "asc" }))
        setSort({ column: "kode_cabang", direction: "asc" })
      })
      .catch((reason: unknown) => { if (!controller.signal.aborted) { setRows([]); setTotal(null); setError(reason instanceof Error ? reason.message : "Data Recovery NPL belum dapat dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setLoading(false) })
    return () => controller.abort()
  }, [defaultsReady, filters])

  useEffect(() => {
    if (!detail) return undefined
    const controller = new AbortController()
    detailAbort.current?.abort()
    detailAbort.current = controller
    const payload = recoveryDetailPayload(detail, filters, userCode, detailStatus)
    void requestRecovery<RecoveryRow[]>("npl", { method: "POST", body: JSON.stringify(payload), signal: controller.signal })
      .then((response) => { if (!controller.signal.aborted) setDetailRows(Array.isArray(response.data) ? response.data : []) })
      .catch((reason: unknown) => { if (!controller.signal.aborted) { setDetailRows([]); setDetailError(reason instanceof Error ? reason.message : "Detail Recovery NPL belum dapat dimuat.") } })
      .finally(() => { if (!controller.signal.aborted) setDetailLoading(false) })
    return () => controller.abort()
  }, [detail, detailStatus, filters, userCode])

  useEffect(() => () => detailAbort.current?.abort(), [])

  const areas = useMemo<SearchableOption[]>(() => recoveryAreas(offices, userCode), [offices, userCode])
  const areaLabel = areas.find((option) => option.value === filters.area)?.label || filters.area
  const sortedRows = useMemo(() => recoverySortRows(rows, sort), [rows, sort])
  const updateFilters = (next: Partial<RecoveryFilters>) => {
    setLoading(true)
    setRows([])
    setTotal(null)
    setDetail(null)
    setError(null)
    setSort({ column: "kode_cabang", direction: "asc" })
    setFilters((current) => ({ ...current, ...next }))
  }
  const onSort = (column: string) => setSort((current) => ({ column, direction: current.column === column ? current.direction === "asc" ? "desc" : "asc" : "desc" }))
  const openDetail = (row: RecoveryRow, type: RecoveryDetailType) => {
    detailAbort.current?.abort()
    setDetailStatus("all")
    setDetailRows([])
    setDetailError(null)
    setDetailLoading(true)
    setDetail({ row, type })
  }
  const closeDetail = useCallback(() => { detailAbort.current?.abort(); setDetail(null); setDetailRows([]); setDetailError(null) }, [])
  const filterPanel = <RecoveryNplFilterPanel filters={filters} areas={areas} disabled={access === false} onChange={updateFilters} />
  const contextLabel = `${filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"} | Closing ${filters.closingDate} | Actual ${filters.actualDate} | ${areaLabel}`
  const net = Number(total?.net_flow_recovery ?? 0)
  const totalRecovery = Number(total?.total_recovery ?? Number(total?.baki_debet_lunas ?? 0) + Number(total?.baki_debet_backflow ?? 0) + Number(total?.baki_debet_angsuran_npl ?? 0))
  const attention = [...rows].filter((row) => Number(row.net_flow_recovery ?? 0) > 0).sort((a, b) => Number(b.net_flow_recovery ?? 0) - Number(a.net_flow_recovery ?? 0)).slice(0, 3)
  const detailChangeStatus = (status: string) => { setDetailRows([]); setDetailLoading(true); setDetailError(null); setDetailStatus(status) }

  return <AppShell title="Recovery NPL" description="Rekap pemulihan dan flow kredit NPL." activeHref={appRoutes.recoveryNpl} legacyHref="/recovery_npl" navbarContent={filterPanel} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1600px] flex-col px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
      <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-4 py-2.5 sm:px-5">
          <div className="flex min-w-0 items-center gap-1.5"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Recovery NPL</h1><Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Informasi Recovery NPL" onClick={() => setInfoOpen(true)}><Info className="size-4" aria-hidden="true" /></Button></div>
          <div className="flex min-w-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3"><span className="hidden max-w-[42rem] truncate lg:inline">{contextLabel}</span><span className="hidden md:inline">{loading ? "Memuat data…" : `${recoveryNumber.format(rows.length)} area`}</span><ReportExportMenu target={tableRef} filename={`Rekap_Recovery_NPL_${filters.actualDate}`} disabled={loading || rows.length === 0} onExportExcel={() => exportRecovery(sortedRows, total, filters, sort)} /></div>
          <p className="w-full truncate text-[0.68rem] text-muted-foreground lg:hidden">{filters.nominalField === "baki_debet" ? "Baki Debet" : "Saldo Bank"} | {areaLabel}</p>
        </header>
        <div className="relative min-h-0 flex-1 overflow-hidden">
          {loading && <div className="absolute inset-0 z-[80] flex items-center justify-center gap-2 bg-background/75 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" /> Memuat Recovery NPL…</div>}
          {error && !loading ? <div className="flex h-full min-h-48 flex-col items-center justify-center gap-2 p-5 text-center"><p className="text-sm font-medium text-destructive">Data Recovery NPL belum dapat dimuat.</p><p className="max-w-xl text-xs text-muted-foreground">{error}</p><Button variant="outline" size="sm" onClick={() => updateFilters({})}>Coba lagi</Button></div>
            : access === false ? <div className="flex h-full min-h-48 items-center justify-center p-6 text-center text-sm text-muted-foreground">Akses menu Recovery NPL belum diberikan.</div>
              : <RecoveryNplTable rows={sortedRows} total={total} sort={sort} filters={filters} userCode={userCode} onSort={onSort} onDetail={openDetail} tableRef={tableRef} />}
        </div>
      </section>
    </div>

    <ReportInfoDialog open={infoOpen} title="Panduan & Fokus Recovery NPL" description="Komponen pemulihan dan perubahan posisi NPL sesuai filter aktif." meta={contextLabel} onClose={() => setInfoOpen(false)}>
      <div className="space-y-4 text-sm">
        <p className="rounded-lg bg-muted p-3 text-muted-foreground">Recovery adalah penurunan posisi NPL dari closing ke actual. Flow NPL menunjukkan rekening yang baru menjadi NPL pada posisi actual.</p>
        <div className="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
          <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">Total Recovery</p><p className="mt-1 font-semibold tabular-nums text-signal-green-foreground">Rp {recoveryNumber.format(Math.round(totalRecovery))}</p></div>
          <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">Flow NPL</p><p className="mt-1 font-semibold tabular-nums text-destructive">Rp {recoveryNumber.format(Math.round(Number(total?.baki_debet_flow_npl ?? 0)))}</p></div>
          <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">Backflow</p><p className="mt-1 font-semibold tabular-nums">{recoveryNumber.format(Number(total?.noa_backflow ?? 0))} NOA</p></div>
          <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">Kondisi Bersih</p><p className={`mt-1 font-semibold tabular-nums ${net > 0 ? "text-destructive" : "text-signal-green-foreground"}`}>{net > 0 ? "+" : ""}Rp {recoveryNumber.format(Math.round(net))}</p></div>
        </div>
        <p className="rounded-lg border bg-muted/30 p-3">Net = Flow NPL + kenaikan baki debet rekening yang tetap NPL - Total Recovery. Angka negatif berarti membaik, angka positif berarti memburuk. % Flow PAR = Flow NPL / NPL closing × 100, atau 0% jika NPL closing tidak ada.</p>
        <div className="grid gap-2 sm:grid-cols-2">
          <div className="rounded-lg border p-3"><h3 className="font-semibold">Komponen Recovery</h3><ul className="mt-2 space-y-1.5 text-xs text-muted-foreground"><li><b className="text-foreground">Lunas NPL:</b> rekening closing NPL yang tidak ada di actual.</li><li><b className="text-foreground">Backflow:</b> rekening NPL yang membaik menjadi L atau DP.</li><li><b className="text-foreground">Angsuran NPL:</b> selisih baki debet closing dan actual saat rekening tetap NPL.</li></ul></div>
          <div className="rounded-lg border p-3"><h3 className="font-semibold">Cabang yang perlu perhatian</h3>{attention.length ? <ul className="mt-2 space-y-2">{attention.map((row) => <li key={String(row.kode_cabang ?? row.kode_unit)} className="flex justify-between gap-3 text-xs"><span className="min-w-0 truncate">{recoveryName(row)}</span><b className="shrink-0 tabular-nums text-destructive">+Rp {recoveryNumber.format(Math.round(Number(row.net_flow_recovery ?? 0)))}</b></li>)}</ul> : <p className="mt-2 text-xs text-muted-foreground">Belum ada area dengan net pemburukan pada data aktif.</p>}</div>
        </div>
        <p className="text-xs text-muted-foreground">Nilai rasio saat ini pada total: {recoveryPercent.format(recoveryRatio(total || {}))}% Flow PAR.</p>
      </div>
    </ReportInfoDialog>

    {detail && <RecoveryNplDetailModal key={`${detail.type}-${String(detail.row.kode_cabang ?? detail.row.kode_unit ?? detail.row.nama_kantor)}`} selection={detail} filters={filters} rows={detailRows} loading={detailLoading} error={detailError} status={detailStatus} onStatusChange={detailChangeStatus} onClose={closeDetail} />}
  </AppShell>
}
