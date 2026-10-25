import { useCallback, useEffect, useRef, useState } from "react"
import { ArrowLeftRight, Download, Info, LoaderCircle } from "lucide-react"

import { AppShell } from "@/components/app/app-shell"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"

import {
  buildJtAreaOptions,
  buildJtDetailPayload,
  buildJtRekapPayload,
  defaultJtFilters,
  downloadJtDetailExcel,
  downloadJtRekapExcel,
  formatJtDate,
  getUserOfficeCode,
  jtNumberFormat,
  requestJtJson,
} from "./jatuh-tempo-data"
import { JatuhTempoFilterPanel } from "./jatuh-tempo-filter-panel"
import { JatuhTempoDetailModal, JatuhTempoInfoPopover } from "./jatuh-tempo-modals"
import { JatuhTempoTable } from "./jatuh-tempo-table"
import { jtAreaDefaults } from "./jatuh-tempo-types"
import type {
  JtAreaOption,
  JtAoOption,
  JtBreakdown,
  JtCodeOffice,
  JtDetailPayload,
  JtDetailSelection,
  JtDetailRow,
  JtFilters,
  JtKankasOption,
  JtRekapPayload,
  JtReportRow,
} from "./jatuh-tempo-types"

export function JatuhTempoPage() {
  const [userCode] = useState(() => getUserOfficeCode())
  const [filters, setFilters] = useState<JtFilters>(() => {
    const initial = defaultJtFilters()
    return userCode !== "000" ? { ...initial, area: `CAB-${userCode}` } : initial
  })
  const [areas, setAreas] = useState<JtAreaOption[]>(jtAreaDefaults)
  const [areaDisabled] = useState(userCode !== "000")
  const [ready, setReady] = useState(false)
  const [breakdown, setBreakdown] = useState<JtBreakdown>(userCode !== "000" ? "KANKAS" : "CABANG")
  const autoBreakdownArea = useRef("")
  const [rows, setRows] = useState<JtReportRow[]>([])
  const [grandTotal, setGrandTotal] = useState<JtReportRow | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [infoOpen, setInfoOpen] = useState(false)
  const [detail, setDetail] = useState<JtDetailSelection | null>(null)
  const [detailRows, setDetailRows] = useState<JtDetailRow[]>([])
  const [detailLoading, setDetailLoading] = useState(false)
  const [detailError, setDetailError] = useState<string | null>(null)
  const [detailPage, setDetailPage] = useState(1)
  const [detailTotalPages, setDetailTotalPages] = useState(1)
  const [detailTotalRecords, setDetailTotalRecords] = useState(0)
  const [detailKankas, setDetailKankas] = useState("")
  const [detailAo, setDetailAo] = useState("")
  const [kankasOptions, setKankasOptions] = useState<JtKankasOption[]>([])
  const [aoOptions, setAoOptions] = useState<JtAoOption[]>([])
  const detailAbortRef = useRef<AbortController | null>(null)

  useEffect(() => {
    const controller = new AbortController()
    const officeCode = getUserOfficeCode()

    const loadDefaults = async () => {
      const [dateResult, officeResult] = await Promise.allSettled([
        requestJtJson<{ last_created?: string; last_closing?: string }>("date", { signal: controller.signal }),
        officeCode === "000"
          ? requestJtJson<JtCodeOffice[]>("kode", {
              method: "POST",
              body: JSON.stringify({ type: "kode_kantor" }),
              signal: controller.signal,
            })
          : Promise.resolve({ status: 200, data: [] as JtCodeOffice[] }),
      ])
      if (controller.signal.aborted) return

      if (dateResult.status === "fulfilled") {
        const lastCreated = dateResult.value.data?.last_created
        if (lastCreated) {
          const date = new Date(`${lastCreated}T00:00:00`)
          setFilters((current) => ({
            ...current,
            actualDate: lastCreated,
            closingDate: dateResult.value.data?.last_closing || current.closingDate,
            month: String(date.getMonth() + 1).padStart(2, "0"),
            year: String(date.getFullYear()),
          }))
        }
      }

      if (officeCode !== "000") {
        setAreas([{ value: `CAB-${officeCode}`, label: `${officeCode} - Cabang Login` }])
      } else if (officeResult.status === "fulfilled") {
        const loadedAreas = buildJtAreaOptions(officeResult.value.data || [])
        if (loadedAreas.length > 0) setAreas([...jtAreaDefaults, ...loadedAreas])
      }
      setReady(true)
    }

    void loadDefaults()
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!ready) return undefined
    const controller = new AbortController()
    const branch = filters.area.startsWith("CAB-")
    const requestedBreakdown = branch ? (breakdown === "AO" ? "AO" : "KANKAS") : null

    const loadReport = async () => {
      setLoading(true)
      setError(null)
      try {
        const response = await requestJtJson<JtRekapPayload>("jt", {
          method: "POST",
          body: JSON.stringify(buildJtRekapPayload(filters, userCode, requestedBreakdown)),
          signal: controller.signal,
        })
        if (controller.signal.aborted) return
        const meta = response.data?.breakdown
        const recommended = meta?.recommended
        if (branch && breakdown === "KANKAS" && recommended === "AO" && autoBreakdownArea.current !== filters.area) {
          autoBreakdownArea.current = filters.area
          setBreakdown("AO")
          return
        }
        const applied = meta?.applied || (branch ? breakdown : "CABANG")
        if (applied !== breakdown) {
          setBreakdown(applied)
          return
        }
        setRows(response.data?.rekap_per_cabang || [])
        setGrandTotal(response.data?.grand_total || null)
      } catch (caught) {
        if (!controller.signal.aborted) {
          setRows([])
          setGrandTotal(null)
          setError(caught instanceof Error ? caught.message : "Data jatuh tempo gagal dimuat")
        }
      } finally {
        if (!controller.signal.aborted) setLoading(false)
      }
    }

    void loadReport()
    return () => controller.abort()
  }, [breakdown, filters, ready, userCode])

  useEffect(() => {
    if (!detail && !infoOpen) return undefined
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key !== "Escape") return
      if (detail) setDetail(null)
      else setInfoOpen(false)
    }
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [detail, infoOpen])

  const handleFilterChange = (next: Partial<JtFilters>) => {
    setFilters((current) => {
      const updated = { ...current, ...next }
      if (next.area !== undefined) {
        autoBreakdownArea.current = ""
        setBreakdown(next.area.startsWith("CAB-") ? "KANKAS" : "CABANG")
      }
      return updated
    })
  }

  const loadDetail = useCallback(async (
    page: number,
    selection: JtDetailSelection,
    selectedKankas = detailKankas,
    selectedAo = detailAo,
  ) => {
    detailAbortRef.current?.abort()
    const controller = new AbortController()
    detailAbortRef.current = controller
    setDetailLoading(true)
    setDetailError(null)
    try {
      const response = await requestJtJson<JtDetailPayload>("jt", {
        method: "POST",
        body: JSON.stringify(buildJtDetailPayload(filters, userCode, selection, page, selectedKankas, selectedAo)),
        signal: controller.signal,
      })
      if (controller.signal.aborted) return
      const pagination = response.data?.pagination
      setDetailRows(response.data?.data || [])
      setAoOptions(response.data?.ao_list || [])
      setDetailPage(pagination?.current_page || page)
      setDetailTotalPages(Math.max(1, pagination?.total_pages || 1))
      setDetailTotalRecords(pagination?.total_records || 0)
    } catch (caught) {
      if (!controller.signal.aborted) {
        setDetailRows([])
        setDetailError(caught instanceof Error ? caught.message : "Detail gagal dimuat")
      }
    } finally {
      if (!controller.signal.aborted) setDetailLoading(false)
    }
  }, [detailAo, detailKankas, filters, userCode])

  const openDetail = async (selection: JtDetailSelection) => {
    if (!selection.isTotal && userCode !== "000" && selection.kode !== userCode) {
      window.alert(`AKSES DITOLAK!\nAnda tidak memiliki izin untuk melihat detail Cabang ${selection.kode}.`)
      return
    }
    const initialKankas = selection.groupType === "KANKAS" ? selection.groupCode : ""
    const initialAo = selection.groupType === "AO" ? selection.groupCode : ""
    setDetail(selection)
    setDetailRows([])
    setDetailPage(1)
    setDetailTotalPages(1)
    setDetailTotalRecords(0)
    setDetailKankas(initialKankas)
    setDetailAo(initialAo)
    setKankasOptions([])
    setAoOptions([])

    const code = selection.isTotal
      ? filters.area.startsWith("CAB-") ? filters.area.replace("CAB-", "") : userCode !== "000" ? userCode : ""
      : selection.kode
    if (code) {
      try {
        const response = await requestJtJson<JtKankasOption[]>("kode", {
          method: "POST",
          body: JSON.stringify({ type: "kode_kankas", kode_kantor: code }),
        })
        setKankasOptions(response.data || [])
      } catch {
        setKankasOptions([])
      }
    }
    void loadDetail(1, selection, initialKankas, initialAo)
  }

  const closeDetail = () => {
    detailAbortRef.current?.abort()
    setDetail(null)
  }

  const handleDetailKankas = (value: string) => {
    setDetailKankas(value)
    if (detail) void loadDetail(1, detail, value, detailAo)
  }

  const handleDetailAo = (value: string) => {
    setDetailAo(value)
    if (detail) void loadDetail(1, detail, detailKankas, value)
  }

  const handleDetailExport = async () => {
    if (!detail) return
    try {
      const payload = buildJtDetailPayload(filters, userCode, detail, 1, detailKankas, detailAo)
      payload.limit = 10000
      const response = await requestJtJson<JtDetailPayload>("jt", {
        method: "POST",
        body: JSON.stringify(payload),
      })
      if (!downloadJtDetailExcel(response.data?.data || [], filters, detail.kode)) window.alert("Tidak ada data.")
    } catch {
      window.alert("Gagal export detail.")
    }
  }

  const branch = filters.area.startsWith("CAB-")
  const areaLabel = areas.find((area) => area.value === filters.area)?.label || (branch ? filters.area : "Konsolidasi")

  const filterPanel = (
    <JatuhTempoFilterPanel
      filters={filters}
      areas={areas}
      areaDisabled={areaDisabled}
      onChange={handleFilterChange}
    />
  )

  return (
    <AppShell
      title="Jatuh Tempo Kredit"
      description="Rekap kredit jatuh tempo berdasarkan periode dan basis nominal terpilih."
      activeHref={appRoutes.jatuhTempo}
      legacyHref="/pages/jatuh_tempo.php"
      navbarContent={filterPanel}
      showPageTitle={false}
    >
      <div className="mx-auto flex h-full min-h-0 w-full max-w-[1700px] flex-col px-3 py-4 sm:px-5 sm:py-5 lg:px-7">
        <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
          <header className="relative flex shrink-0 flex-wrap items-center justify-between gap-3 border-b px-4 py-3 sm:px-5">
            <div className="flex min-w-0 items-center gap-2">
              <div className="min-w-0">
                <div className="flex items-center gap-1.5">
                  <h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Jatuh Tempo Kredit</h1>
                  <Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Buka informasi Jatuh Tempo Kredit" aria-expanded={infoOpen} onClick={() => setInfoOpen((value) => !value)}><Info className="size-4" aria-hidden="true" /></Button>
                </div>
                <p className="hidden truncate text-xs text-muted-foreground sm:block">{areaLabel} · {filters.nominalField === "baki_debet" ? "Baki debet" : "Saldo bank"} · {formatJtDate(filters.closingDate)} → {formatJtDate(filters.actualDate)}</p>
              </div>
            </div>
            <div className="flex shrink-0 items-center gap-2">
              <span className="hidden text-xs text-muted-foreground md:inline">{loading ? "Mengambil data..." : `${jtNumberFormat.format(rows.length)} kelompok`}</span>
              {branch && (
                <Button type="button" variant="outline" size="sm" className="min-h-9" aria-label={`Ganti breakdown, saat ini ${breakdown === "AO" ? "AO kredit" : "Kankas"}`} title={`Saat ini ${breakdown === "AO" ? "AO kredit" : "Kankas"}`} onClick={() => { autoBreakdownArea.current = ""; setBreakdown((value) => value === "AO" ? "KANKAS" : "AO") }}>
                  <ArrowLeftRight data-icon="inline-start" aria-hidden="true" /><span className="hidden sm:inline">{breakdown === "AO" ? "AO kredit" : "Kankas"}</span>
                </Button>
              )}
              <Button type="button" variant="signalGreen" size="icon-lg" aria-label="Export rekap Excel" title="Export rekap Excel" disabled={loading || rows.length === 0} onClick={() => downloadJtRekapExcel(rows, filters, breakdown)}><Download aria-hidden="true" /></Button>
            </div>
            <JatuhTempoInfoPopover open={infoOpen} onClose={() => setInfoOpen(false)} />
          </header>

          <div className="relative min-h-0 flex-1 overflow-auto report-table-scroll">
            {loading && (
              <div className="absolute inset-0 z-20 flex items-center justify-center gap-2 bg-background/80 text-sm text-muted-foreground backdrop-blur-[1px]"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" /> Menyiapkan data jatuh tempo...</div>
            )}
            {error ? (
              <div className="flex min-h-56 flex-col items-center justify-center gap-3 px-4 text-center">
                <p className="text-sm font-medium text-destructive">Data jatuh tempo belum dapat dimuat.</p>
                <p className="max-w-lg text-xs text-muted-foreground">{error}</p>
                <Button type="button" variant="outline" size="sm" onClick={() => setFilters((current) => ({ ...current }))}>Coba lagi</Button>
              </div>
            ) : rows.length === 0 && !loading ? (
              <div className="flex min-h-56 items-center justify-center px-4 text-sm text-muted-foreground">Tidak ada data pada filter ini.</div>
            ) : (
              <JatuhTempoTable rows={rows} grandTotal={grandTotal} breakdown={breakdown} onDetail={openDetail} />
            )}
          </div>
        </section>

        {detail && (
          <JatuhTempoDetailModal
            key={`${detail.kode}-${detail.groupType}-${detail.groupCode}-${detail.category}`}
            selection={detail}
            filters={filters}
            rows={detailRows}
            loading={detailLoading}
            error={detailError}
            page={detailPage}
            totalPages={detailTotalPages}
            totalRecords={detailTotalRecords}
            kankasOptions={kankasOptions}
            aoOptions={aoOptions}
            selectedKankas={detailKankas}
            selectedAo={detailAo}
            onKankasChange={handleDetailKankas}
            onAoChange={handleDetailAo}
            onClose={closeDetail}
            onPageChange={(page) => void loadDetail(page, detail)}
            onExport={() => void handleDetailExport()}
          />
        )}
      </div>
    </AppShell>
  )
}
