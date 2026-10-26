import { useEffect, useMemo, useRef, useState } from "react"
import { Info, LoaderCircle, Search, X } from "lucide-react"
import { AppShell } from "@/components/app/app-shell"
import { ReportExportMenu } from "@/components/report-export-menu"
import { ReportInfoDialog } from "@/components/report-info-dialog"
import { Button } from "@/components/ui/button"
import { appRoutes } from "@/lib/routes"
import { exportLabaRugi, labaRugiAccess, labaRugiAreas, labaRugiPayload, labaRugiUser, labaRugiUserCode, requestLabaRugi, type LabaRugiData } from "./laba-rugi-data"
import { LabaRugiFilterPanel } from "./laba-rugi-filter-panel"
import { LabaRugiTable } from "./laba-rugi-table"

type Office = { kode_kantor?: string; nama_kantor?: string; nama?: string }
type Filters = { date: string; area: string; level: string }

export function LabaRugiPage() {
  const userCode = labaRugiUserCode(labaRugiUser())
  const [filters, setFilters] = useState<Filters>({ date: "", area: userCode === "000" ? "000" : userCode, level: "5" })
  const [offices, setOffices] = useState<Office[]>([])
  const [defaultsReady, setDefaultsReady] = useState(false)
  const [access, setAccess] = useState<boolean | null>(null)
  const [data, setData] = useState<LabaRugiData | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [reloadKey, setReloadKey] = useState(0)
  const [infoOpen, setInfoOpen] = useState(false)
  const [searchOpen, setSearchOpen] = useState(false)
  const [query, setQuery] = useState("")
  const targetRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const controller = new AbortController()
    void Promise.allSettled([
      requestLabaRugi<Record<string, unknown>>("rbac", { method: "POST", body: JSON.stringify({ type: "me" }), signal: controller.signal }),
      requestLabaRugi<{ last_created?: string }>("lapkeu", { method: "POST", body: JSON.stringify({ type: "default_acc_history_date" }), signal: controller.signal }),
      requestLabaRugi<Office[]>("kode", { method: "POST", body: JSON.stringify({ type: "kode_kantor" }), signal: controller.signal }),
    ]).then(([permission, dateResult, officesResult]) => {
      if (controller.signal.aborted) return
      if (permission.status === "fulfilled") {
        const allowed = labaRugiAccess(permission.value.data || null)
        setAccess(allowed)
        if (!allowed) setLoading(false)
      } else if (import.meta.env.DEV) setAccess(true)
      else { setAccess(false); setLoading(false); setError(permission.reason instanceof Error ? permission.reason.message : "Hak akses Laba Rugi belum dapat diverifikasi.") }

      const date = dateResult.status === "fulfilled" ? dateResult.value.data?.last_created || "" : ""
      setFilters((current) => ({ ...current, date }))
      setOffices(officesResult.status === "fulfilled" && Array.isArray(officesResult.value.data) ? officesResult.value.data : [])
      setDefaultsReady(true)
    })
    return () => controller.abort()
  }, [])

  useEffect(() => {
    if (!defaultsReady || access !== true) return undefined
    const controller = new AbortController()
    void requestLabaRugi<LabaRugiData>("lapkeu", {
      method: "POST",
      body: JSON.stringify(labaRugiPayload(filters.date, filters.area)),
      signal: controller.signal,
    }).then((response) => {
      if (!controller.signal.aborted) setData(response.data || {})
    }).catch((reason: unknown) => {
      if (!controller.signal.aborted) setError(reason instanceof Error ? reason.message : "Data Laba Rugi gagal dimuat.")
    }).finally(() => {
      if (!controller.signal.aborted) setLoading(false)
    })
    return () => controller.abort()
  }, [access, defaultsReady, filters.area, filters.date, reloadKey])

  const areas = useMemo(() => labaRugiAreas(offices, userCode), [offices, userCode])
  const areaLabel = areas.find((option) => option.value === filters.area)?.label || filters.area
  const updateFilters = (next: Partial<Filters>) => {
    if (next.date !== undefined || next.area !== undefined) { setData(null); setLoading(true); setError(null) }
    setFilters((current) => ({ ...current, ...next }))
  }
  const navbarFilters = <LabaRugiFilterPanel date={filters.date} area={filters.area} level={filters.level} areas={areas} disabled={access === false || userCode !== "000"} onChange={updateFilters} />
  const search = query.trim().toLocaleLowerCase("id-ID")

  return <AppShell title="Laba Rugi" description="Posisi pendapatan dan biaya berdasarkan acc_history aktual." activeHref={appRoutes.labaRugi} legacyHref="/lap_laba_rugi" navbarContent={navbarFilters} showPageTitle={false}>
    <div className="density-page mx-auto flex h-full min-h-0 w-full max-w-[1700px] flex-col px-3 py-3 sm:px-6 sm:py-5 lg:px-8">
      <section className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border bg-background">
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-3 py-2.5 sm:px-5">
          <div className="flex min-w-0 items-center gap-1"><div className="min-w-0"><h1 className="truncate text-sm font-semibold tracking-tight sm:text-base">Laba Rugi Aktual</h1><p className="mt-0.5 truncate text-[0.68rem] text-muted-foreground">{data?.scope_label || areaLabel} · Posisi {data?.tanggal || filters.date || "Memuat tanggal…"}</p></div><Button type="button" variant="ghost" size="icon-sm" className="shrink-0 rounded-full text-primary" aria-label="Informasi Laba Rugi" onClick={() => setInfoOpen(true)}><Info className="size-4" aria-hidden="true" /></Button></div>
          <div className="flex min-w-0 items-center gap-2">
            {searchOpen && <label className="flex h-9 min-w-0 items-center gap-2 rounded-md border px-2"><Search className="size-4 shrink-0 text-muted-foreground" aria-hidden="true" /><input autoFocus value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Cari kode / akun…" className="min-w-0 bg-transparent text-sm outline-none placeholder:text-muted-foreground" aria-label="Cari kode atau akun" /></label>}
            <Button type="button" variant="outline" size="icon" className="size-9 shrink-0" aria-label={searchOpen ? "Tutup pencarian" : "Buka pencarian"} title={searchOpen ? "Tutup pencarian" : "Buka pencarian"} aria-expanded={searchOpen} onClick={() => { setSearchOpen((open) => !open); if (searchOpen) setQuery("") }}>{searchOpen ? <X aria-hidden="true" /> : <Search aria-hidden="true" />}</Button>
            <ReportExportMenu target={targetRef} filename={`Laba_Rugi_Aktual_${data?.tanggal || "data"}`} disabled={loading || !data} onExportExcel={() => data && exportLabaRugi(data)} />
          </div>
        </header>
        <div className="relative flex min-h-0 flex-1 flex-col overflow-hidden">
          {loading && <div className="absolute inset-0 z-30 flex items-center justify-center gap-2 bg-background/75 text-sm text-muted-foreground"><LoaderCircle className="size-4 animate-spin" aria-hidden="true" />Memuat Laba Rugi…</div>}
          {!loading && error ? <div className="flex min-h-48 flex-1 flex-col items-center justify-center gap-2 p-6 text-center"><p className="text-sm font-medium text-destructive">Data Laba Rugi gagal dimuat.</p><p className="max-w-xl text-xs text-muted-foreground">{error}</p><Button variant="outline" size="sm" onClick={() => { setLoading(true); setError(null); setData(null); setReloadKey((key) => key + 1) }}>Coba lagi</Button></div>
            : access === false ? <div className="flex min-h-48 flex-1 items-center justify-center p-6 text-center text-sm text-muted-foreground">Akses menu Laba Rugi belum diberikan.</div>
              : data && <LabaRugiTable key={filters.level} data={data} level={filters.level} query={search} targetRef={targetRef} />}
        </div>
      </section>
    </div>
    <ReportInfoDialog open={infoOpen} title="Informasi Laba Rugi Aktual" description="Posisi pendapatan, biaya, dan laba aktual berdasarkan acc_history." meta={`${data?.scope_label || areaLabel} · ${data?.tanggal || filters.date || "-"}`} onClose={() => setInfoOpen(false)}>
      <div className="space-y-3 text-sm"><p className="rounded-lg bg-muted p-3 text-muted-foreground">Laba kotor dihitung dari total pendapatan dikurangi total biaya. Pada Konsolidasi, pajak dari akun kode 6 diperhitungkan untuk mendapatkan laba bersih.</p><div className="grid gap-2 sm:grid-cols-3"><InfoValue label="Total Pendapatan" value={data?.totals?.pendapatan} /><InfoValue label="Total Biaya" value={data?.totals?.biaya} /><InfoValue label="Laba Rugi Kotor" value={data?.totals?.laba_kotor} />{data?.pajak_dihitung && <><InfoValue label="Pajak" value={data.totals?.pajak} /><InfoValue label="Laba Rugi Bersih" value={data.totals?.laba_bersih} /></>}</div><p className="text-xs text-muted-foreground">Pencarian mencocokkan kode dan uraian akun. Di perangkat mobile, gunakan tombol Pendapatan/Biaya untuk berpindah daftar.</p></div>
    </ReportInfoDialog>
  </AppShell>
}

function InfoValue({ label, value }: { label: string; value?: number }) { return <div className="rounded-lg border p-3"><p className="text-xs text-muted-foreground">{label}</p><strong className="mt-1 block text-sm tabular-nums">{value === undefined ? "-" : `Rp ${new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 }).format(Number(value))}`}</strong></div> }
