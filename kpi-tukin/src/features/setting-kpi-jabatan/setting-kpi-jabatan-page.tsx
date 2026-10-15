import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Info, RefreshCw, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { KpiField, KpiSelect } from "@/components/app/kpi-ui"
import { useKpiToolbar } from "@/components/app/kpi-shell"
import { formatNumber } from "@/lib/utils"

import { loadSettingKpi } from "./setting-kpi-jabatan-data"
import { saveKpiIndicator } from "@/features/kpi-data"
import { SettingKpiJabatanTable } from "./setting-kpi-jabatan-table"
import type { KpiJobCode, KpiUnit, SettingKpiResponse } from "./setting-kpi-jabatan-types"

const jobs: Array<{ value: KpiJobCode; label: string }> = [
  { value: "AO_KREDIT", label: "AO Kredit" },
  { value: "AO_DANA", label: "AO Dana" },
  { value: "AO_REMEDIAL", label: "AO Remedial" },
]

const units: Array<{ value: KpiUnit; label: string }> = [
  { value: "", label: "Semua unit" },
  { value: "RUPIAH", label: "Rupiah" },
  { value: "NOA", label: "NOA" },
  { value: "PERSEN", label: "Persen" },
  { value: "JUMLAH", label: "Jumlah" },
]

function InfoDialog({ onClose }: { onClose: () => void }) {
  useEffect(() => {
    const onKeyDown = (event: KeyboardEvent) => event.key === "Escape" && onClose()
    window.addEventListener("keydown", onKeyDown)
    return () => window.removeEventListener("keydown", onKeyDown)
  }, [onClose])

  return (
    <div className="fixed inset-0 z-50 grid place-items-center bg-black/40 p-4" role="presentation" onMouseDown={onClose}>
      <section className="w-full max-w-lg rounded-xl border border-[var(--line)] bg-[var(--surface)] shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="kpi-info-title" onMouseDown={(event) => event.stopPropagation()}>
        <div className="flex items-start justify-between gap-4 border-b border-[var(--line-soft)] px-5 py-4">
          <div className="flex items-start gap-3"><span className="grid size-8 shrink-0 place-items-center rounded-md bg-[var(--ink-soft)] text-[var(--accent)]"><Info size={16} /></span><div><h2 id="kpi-info-title" className="font-semibold">Tentang Setting KPI</h2><p className="mt-1 text-xs text-[var(--muted)]">Konfigurasi yang ditampilkan berasal dari API MONBIS.</p></div></div>
          <Button variant="ghost" size="icon" aria-label="Tutup informasi" onClick={onClose}><X size={16} /></Button>
        </div>
        <div className="space-y-3 px-5 py-5 text-sm leading-6 text-[var(--muted)]">
          <p>Halaman ini menampilkan indikator, bobot, target default, arah penilaian, dan unit untuk tiga jabatan KPI Tukin.</p>
          <ul className="list-disc space-y-1 pl-5"><li>Perhitungan indeks, skor, nilai akhir, dan faktor Tukin tetap dilakukan di backend MONBIS.</li><li>React hanya mengambil response API dan menyajikannya tanpa menghitung ulang.</li><li>Perubahan parameter akan mengikuti permission dan kontrak API MONBIS.</li></ul>
        </div>
      </section>
    </div>
  )
}

export function SettingKpiJabatanPage() {
  const { setToolbar } = useKpiToolbar()
  const [data, setData] = useState<SettingKpiResponse | null>(null)
  const [job, setJob] = useState<KpiJobCode>("AO_KREDIT")
  const [unit, setUnit] = useState<KpiUnit>("")
  const [query, setQuery] = useState("")
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [infoOpen, setInfoOpen] = useState(false)
  const [drafts, setDrafts] = useState<Record<number, { bobot: string; target: string; status: string }>>({})
  const [savingId, setSavingId] = useState<number | null>(null)
  const abortRef = useRef<AbortController | null>(null)

  const load = useCallback(() => {
    abortRef.current?.abort()
    const controller = new AbortController()
    abortRef.current = controller
    setLoading(true)
    setError("")
    loadSettingKpi(controller.signal).then(setData).catch((reason: unknown) => {
      if (reason instanceof DOMException && reason.name === "AbortError") return
      setError(reason instanceof Error ? reason.message : "Data KPI gagal dimuat")
    }).finally(() => {
      if (abortRef.current === controller) setLoading(false)
    })
  }, [])

  useEffect(() => {
    load()
    return () => abortRef.current?.abort()
  }, [load])

  useEffect(() => {
    setToolbar(<div className="flex min-w-max items-end gap-2"><KpiSelect label="Jabatan" value={job} onChange={(value) => setJob(value as KpiJobCode)}>{jobs.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</KpiSelect><KpiSelect label="Unit" value={unit} onChange={(value) => setUnit(value as KpiUnit)}>{units.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}</KpiSelect><KpiField label="Cari indikator"><input value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Nama, kelompok, definisi" className="h-9 w-56 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2.5 text-sm text-[var(--ink)] outline-none placeholder:text-[var(--muted)] focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--ring)]" /></KpiField></div>)
    return () => setToolbar(null)
  }, [job, query, setToolbar, unit])

  const rows = useMemo(() => {
    const normalized = query.trim().toLowerCase()
    return (data?.indikator || []).filter((row) => row.jabatan_kode === job && row.status === "AKTIF" && (!unit || row.unit === unit) && (!normalized || [row.nama, row.kelompok, row.definisi, row.formula_key].filter(Boolean).join(" ").toLowerCase().includes(normalized)))
  }, [data, job, query, unit])

  const activeCount = data?.indikator.filter((row) => row.jabatan_kode === job && row.status === "AKTIF").length || 0
  const selectedJob = data?.jabatan.find((item) => item.kode === job)

  const updateDraft = (id: number, field: "bobot" | "target" | "status", value: string) => {
    setDrafts((current) => ({ ...current, [id]: { ...(current[id] || { bobot: "", target: "", status: "AKTIF" }), [field]: value } }))
  }

  const saveIndicator = async (row: (typeof rows)[number]) => {
    const draft = drafts[row.id] || { bobot: String(row.bobot * 100), target: String(row.target_default), status: row.status }
    const bobot = Number(draft.bobot.replace(",", ".")) / 100
    const target = Number(draft.target.replace(",", "."))
    if (!Number.isFinite(bobot) || !Number.isFinite(target)) return setError("Bobot dan target harus berupa angka.")
    setSavingId(row.id)
    setError("")
    try {
      await saveKpiIndicator({ id: row.id, bobot, target, status: draft.status })
      load()
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Parameter KPI gagal disimpan")
    } finally {
      setSavingId(null)
    }
  }

  return (
    <div className="mx-auto w-full max-w-[1500px] space-y-5 p-4 sm:p-6 lg:p-8">
      <section className="flex flex-wrap items-start justify-between gap-4">
        <div className="min-w-0"><div className="flex items-center gap-2"><h2 className="text-xl font-semibold tracking-tight sm:text-2xl">Setting KPI Jabatan</h2><Button variant="ghost" size="icon" className="size-8" aria-label="Tentang Setting KPI" onClick={() => setInfoOpen(true)}><Info size={16} /></Button></div><p className="mt-1 max-w-2xl text-sm text-[var(--muted)]">Kelola indikator dan parameter KPI Tukin. Data dan perhitungan tetap bersumber dari MONBIS.</p></div>
        <div className="flex items-center gap-2 text-xs text-[var(--muted)]"><span className="hidden sm:inline">{data?.can_manage === false ? "Mode baca" : "API MONBIS"}</span><Button variant="outline" size="sm" onClick={load} disabled={loading}><RefreshCw size={14} className={loading ? "animate-spin" : undefined} /> <span>Muat ulang</span></Button></div>
      </section>

      <section className="grid gap-3 sm:grid-cols-3">
        <div className="rounded-lg border border-[var(--line)] bg-[var(--surface)] px-4 py-3"><p className="text-xs text-[var(--muted)]">Jabatan aktif</p><p className="mt-1 font-semibold">{selectedJob?.nama || "-"}</p></div>
        <div className="rounded-lg border border-[var(--line)] bg-[var(--surface)] px-4 py-3"><p className="text-xs text-[var(--muted)]">Indikator aktif</p><p className="mt-1 font-semibold tabular-nums">{formatNumber(activeCount, 0)}</p></div>
        <div className="rounded-lg border border-[var(--line)] bg-[var(--surface)] px-4 py-3"><p className="text-xs text-[var(--muted)]">Sumber perhitungan</p><p className="mt-1 font-semibold">Backend MONBIS</p></div>
      </section>

      <section className="overflow-hidden rounded-xl border border-[var(--line)] bg-[var(--surface)]">
        {loading ? <div className="grid min-h-56 place-items-center gap-2 text-sm text-[var(--muted)]"><RefreshCw size={18} className="animate-spin text-[var(--accent)]" /><span>Mengambil setting KPI dari MONBIS...</span></div> : error ? <div className="grid min-h-56 place-items-center gap-3 px-6 text-center"><p className="text-sm text-red-600 dark:text-red-400">{error}</p><Button variant="outline" size="sm" onClick={load}>Coba lagi</Button></div> : <SettingKpiJabatanTable rows={rows} drafts={drafts} savingId={savingId} onChange={updateDraft} onSave={saveIndicator} />}
      </section>
      {infoOpen && <InfoDialog onClose={() => setInfoOpen(false)} />}
    </div>
  )
}
