import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Info, LoaderCircle } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { Button } from "@/components/ui/button"
import { downloadReportExcel } from "@/lib/download-report-excel"
import { appRoutes } from "@/lib/routes"

import { DetailModal, SummaryModal } from "./realisasi-growth-modals"
import { requestJson, defaultFilters, formatDateLabel, previousMonthEnd, rowNumber, sortRows } from "./realisasi-growth-data"
import { RealisasiGrowthFilterPanel } from "./realisasi-growth-filter-panel"
import { RealisasiGrowthTable } from "./realisasi-growth-table"
import { areaDefaults } from "./realisasi-growth-types"
import type {
  AreaOption,
  DetailPayload,
  DetailRow,
  DetailSelection,
  Filters,
  ReportPayload,
  ReportRow,
  SortDirection,
  SortKey,
} from "./realisasi-growth-types"

export function RealisasiGrowthPage() {
  const [filters, setFilters] = useState<Filters>(defaultFilters)
  const [areas, setAreas] = useState<AreaOption[]>(areaDefaults)
  const [defaultsReady, setDefaultsReady] = useState(false)
  const [reloadKey, setReloadKey] = useState(0)
  const [rows, setRows] = useState<ReportRow[]>([])
  const [grandTotal, setGrandTotal] = useState<ReportRow | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [sortKey, setSortKey] = useState<SortKey>("kode_kantor")
  const [sortDirection, setSortDirection] = useState<SortDirection>("asc")
  const [summaryOpen, setSummaryOpen] = useState(false)
  const [detail, setDetail] = useState<DetailSelection | null>(null)
  const [detailRows, setDetailRows] = useState<DetailRow[]>([])
  const [detailLoading, setDetailLoading] = useState(false)
  const [detailError, setDetailError] = useState<string | null>(null)
  const [detailSearch, setDetailSearch] = useState("")
  const [detailPage, setDetailPage] = useState(1)
  const [detailTotalPages, setDetailTotalPages] = useState(1)
  const [detailTotalRecords, setDetailTotalRecords] = useState(0)
  const detailAbortRef = useRef<AbortController | null>(null)
  const tableExportRef = useRef<HTMLTableElement>(null)

  useEffect(() => {
    const controller = new AbortController()

    const loadDefaults = async () => {
      const [dateResult, areaResult] = await Promise.allSettled([
        requestJson<{ last_created?: string; last_closing?: string }>("date", { signal: controller.signal }),
        requestJson<Array<{ kode_kantor?: string; nama_kantor?: string }>>("kode", {
          method: "POST",
          body: JSON.stringify({ type: "kode_kantor" }),
          signal: controller.signal,
        }),
      ])

      if (controller.signal.aborted) return

      if (dateResult.status === "fulfilled") {
        const dateResponse = dateResult.value
        const lastCreated = dateResponse.data?.last_created
        if (lastCreated) {
          setFilters((current) => ({
            ...current,
            actualDate: lastCreated,
            closingDate: dateResponse.data?.last_closing || previousMonthEnd(lastCreated),
          }))
        }
      }

      if (areaResult.status === "fulfilled") {
        const areaResponse = areaResult.value
        const loadedAreas = (areaResponse.data || [])
          .filter((area) => area.kode_kantor && area.kode_kantor !== "000")
          .map((area) => ({
            value: `CAB-${String(area.kode_kantor).padStart(3, "0")}`,
            label: `${String(area.kode_kantor).padStart(3, "0")} - ${area.nama_kantor || "Tanpa nama"}`,
          }))
        if (loadedAreas.length > 0) setAreas([...areaDefaults, ...loadedAreas])
      }

      setDefaultsReady(true)
    }

    void loadDefaults()
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!defaultsReady) return undefined

    const controller = new AbortController()
    const loadReport = async () => {
      setLoading(true)
      setError(null)
      try {
        const body: Record<string, string> = {
          type: "rekap_realisasi_growth",
          closing_date: filters.closingDate,
          harian_date: filters.actualDate,
          nominal_field: filters.nominalField,
        }
        if (filters.area.startsWith("KOR-")) body.korwil = filters.area.replace("KOR-", "")
        if (filters.area.startsWith("CAB-")) body.kode_kantor = filters.area.replace("CAB-", "")

        const response = await requestJson<ReportPayload>("kredit", {
          method: "POST",
          body: JSON.stringify(body),
          signal: controller.signal,
        })
        if (controller.signal.aborted) return
        setRows(response.data?.data || [])
        setGrandTotal(response.data?.grand_total || null)
      } catch (caught) {
        if (!controller.signal.aborted) {
          setRows([])
          setGrandTotal(null)
          setError(caught instanceof Error ? caught.message : "Data laporan gagal dimuat")
        }
      } finally {
        if (!controller.signal.aborted) setLoading(false)
      }
    }

    void loadReport()
    return () => controller.abort()
  }, [defaultsReady, filters, reloadKey])

  useEffect(() => {
    if (!detail) return undefined
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") setDetail(null)
    }
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [detail])

  useEffect(() => {
    if (!summaryOpen) return undefined
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") setSummaryOpen(false)
    }
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [summaryOpen])

  const sortedRows = useMemo(
    () => sortRows(rows, sortKey, sortDirection),
    [rows, sortDirection, sortKey],
  )

  const detailAreaLabel = detail?.row.nama_kantor || "Grand total"

  const loadDetail = useCallback(async (nextPage: number, selected: DetailSelection) => {
    detailAbortRef.current?.abort()
    const controller = new AbortController()
    detailAbortRef.current = controller
    setDetailLoading(true)
    setDetailError(null)

    const body: Record<string, string | number> = {
      type: "detail_realisasi_growth",
      closing_date: filters.closingDate,
      harian_date: filters.actualDate,
      kode_trans: selected.type === "Realisasi" ? 110 : 109,
      page: nextPage,
      limit: 50,
    }

    if (filters.area.startsWith("CAB-")) {
      body.kode_kantor = filters.area.replace("CAB-", "")
      if (selected.row.kode_kantor) body.kode_kankas = String(selected.row.kode_kantor)
    } else if (selected.row.kode_kantor && selected.row.kode_kantor !== "ALL") {
      body.kode_kantor = String(selected.row.kode_kantor)
    }
    if (filters.area.startsWith("KOR-")) body.korwil = filters.area.replace("KOR-", "")

    try {
      const response = await requestJson<DetailPayload>("kredit", {
        method: "POST",
        body: JSON.stringify(body),
        signal: controller.signal,
      })
      if (controller.signal.aborted) return
      setDetailRows(response.data?.data || [])
      setDetailPage(response.data?.current_page || nextPage)
      setDetailTotalPages(Math.max(1, response.data?.total_pages || 1))
      setDetailTotalRecords(response.data?.total_records || 0)
    } catch (caught) {
      if (!controller.signal.aborted) {
        setDetailRows([])
        setDetailError(caught instanceof Error ? caught.message : "Detail gagal dimuat")
      }
    } finally {
      if (!controller.signal.aborted) setDetailLoading(false)
    }
  }, [filters])

  const openDetail = (row: ReportRow, type: string) => {
    const nextDetail = { row, type }
    setDetail(nextDetail)
    setDetailRows([])
    setDetailSearch("")
    setDetailPage(1)
    setDetailTotalPages(1)
    setDetailTotalRecords(0)
    void loadDetail(1, nextDetail)
  }

  const closeDetail = () => {
    detailAbortRef.current?.abort()
    setDetail(null)
  }

  const handleSort = (key: SortKey) => {
    if (sortKey === key) {
      setSortDirection((current) => (current === "asc" ? "desc" : "asc"))
    } else {
      setSortKey(key)
      setSortDirection("asc")
    }
  }

  const handleExport = () => {
    if (rows.length === 0) return
    const headers = ["Kode", "Nama Kantor", "Saldo Closing", "Realisasi", "Realisasi NOA", "Restrukturisasi", "Restrukturisasi NOA", "Pelunasan", "Pelunasan NOA", "Angsuran", "Angsuran NOA", "Run Off", "Run Off NOA", "Saldo Actual", "Growth"]
    const values = rows.map((row) => [
      String(row.kode_kantor || ""),
      row.nama_kantor || "",
      rowNumber(row, "nominal_closing"),
      rowNumber(row, "total_realisasi"),
      Number(row.noa_realisasi || 0),
      rowNumber(row, "total_restruck"),
      Number(row.noa_restruck || 0),
      rowNumber(row, "pelunasan"),
      Number(row.noa_pelunasan || 0),
      rowNumber(row, "angsuran_murni"),
      Number(row.noa_angsuran_murni || 0),
      rowNumber(row, "total_run_off"),
      Number(row.noa_run_off || 0),
      rowNumber(row, "nominal_actual"),
      rowNumber(row, "growth"),
    ])
    downloadReportExcel("Realisasi_Kredit_Growth", headers, values, [0])
  }

  const filterPanel = (
    <RealisasiGrowthFilterPanel
      filters={filters}
      areas={areas}
      onChange={(next) => setFilters((current) => ({ ...current, ...next }))}
    />
  )

  return (
    <AppShell
      title="Realisasi Kredit & Growth"
      description="Perbandingan produksi, run off, saldo bank, dan pertumbuhan kredit."
      activeHref={appRoutes.realisasiGrowth}
      legacyHref="/pages/realisasi_kredit_growth.php"
      navbarContent={filterPanel}
      showPageTitle={false}
    >
      <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1600px] flex-col px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
        <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
          <div className="flex shrink-0 flex-nowrap items-center justify-between gap-2 border-b px-4 py-2.5 sm:gap-3 sm:px-5">
            <div className="flex min-w-0 flex-1 items-center gap-1.5">
              <h2 className="min-w-0 truncate text-sm font-semibold tracking-tight sm:text-base">Realisasi Kredit &amp; Growth</h2>
              <Button
                type="button"
                variant="ghost"
                size="icon"
                className="size-7 shrink-0 rounded-full text-primary"
                aria-label="Buka ringkasan Realisasi Kredit dan Growth"
                title="Lihat ringkasan"
                onClick={() => setSummaryOpen(true)}
              >
                <Info className="size-4" aria-hidden="true" />
              </Button>
            </div>
            <div className="flex shrink-0 items-center gap-2 text-xs text-muted-foreground sm:gap-3">
              <span className="hidden md:inline">
                {filters.nominalField === "saldo_bank" ? "Saldo bank" : "Baki debet"} | Closing {formatDateLabel(filters.closingDate)} | Actual {formatDateLabel(filters.actualDate)}
              </span>
              <span className="hidden sm:inline">{loading ? "Mengambil data..." : `${rows.length.toLocaleString("id-ID")} kantor`}</span>
              <ReportExportMenu target={tableExportRef} filename="Realisasi_Kredit_Growth" onExportExcel={handleExport} disabled={loading || rows.length === 0} />
            </div>
          </div>

          <div className="report-table-scroll relative min-h-0 flex-1 overflow-auto">
            {loading && (
              <div className="absolute inset-0 z-10 flex min-h-48 items-center justify-center gap-2 bg-background/80 text-sm text-muted-foreground backdrop-blur-[1px]">
                <LoaderCircle className="size-4 animate-spin" aria-hidden="true" />
                Mengambil data realisasi...
              </div>
            )}
            {error ? (
              <div className="flex min-h-48 flex-col items-center justify-center gap-3 px-4 text-center">
                <p className="text-sm font-medium text-destructive">Data laporan belum dapat dimuat.</p>
                <p className="max-w-lg text-xs text-muted-foreground">{error}</p>
                <Button type="button" variant="outline" size="sm" onClick={() => setReloadKey((value) => value + 1)}>Coba lagi</Button>
              </div>
            ) : rows.length === 0 && !loading ? (
              <div className="flex min-h-48 items-center justify-center px-4 text-sm text-muted-foreground">Tidak ada data pada filter ini.</div>
            ) : (
              <RealisasiGrowthTable
                sortedRows={sortedRows}
                grandTotal={grandTotal}
                sortKey={sortKey}
                sortDirection={sortDirection}
                onSort={handleSort}
                onDetail={openDetail}
                tableRef={tableExportRef}
              />
            )}
          </div>
        </section>

        {summaryOpen && (
          <SummaryModal filters={filters} grandTotal={grandTotal} rows={rows} onClose={() => setSummaryOpen(false)} />
        )}

        {detail && (
          <DetailModal
            areaLabel={detailAreaLabel}
            detailType={detail.type}
            rows={detailRows}
            loading={detailLoading}
            error={detailError}
            page={detailPage}
            totalPages={detailTotalPages}
            totalRecords={detailTotalRecords}
            search={detailSearch}
            onSearch={setDetailSearch}
            onClose={closeDetail}
            onPageChange={(nextPage) => void loadDetail(nextPage, detail)}
          />
        )}
      </div>
    </AppShell>
  )
}
