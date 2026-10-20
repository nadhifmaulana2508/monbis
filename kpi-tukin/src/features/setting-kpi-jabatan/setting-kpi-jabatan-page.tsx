import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { ArrowLeftRight, Info, RefreshCw, X } from "lucide-react"

import { Button } from "@/components/ui/button"
import { KpiField, KpiInput, KpiModal, KpiSelect } from "@/components/app/kpi-ui"
import { useKpiToolbar } from "@/components/app/kpi-shell"
import { formatNumber, formatTarget } from "@/lib/utils"

import { loadSettingKpi } from "./setting-kpi-jabatan-data"
import { saveKpiIndicator, saveKpiScore } from "@/features/kpi-data"
import { SettingKpiJabatanTable } from "./setting-kpi-jabatan-table"
import { formatScoreRange, shortIndicatorName } from "./setting-kpi-jabatan-table"
import type { KpiIndicator, KpiJobCode, KpiScoreParameter, KpiUnit, SettingKpiResponse } from "./setting-kpi-jabatan-types"
import type { KpiTukinRule } from "@/features/kpi-types"

const jobs: Array<{ value: KpiJobCode; label: string }> = [
  { value: "AO_KREDIT", label: "AO Kredit" },
  { value: "AO_DANA", label: "AO Dana" },
  { value: "AO_REMEDIAL", label: "AO Remedial" },
]

const units: Array<{ value: KpiUnit; label: string }> = [
  { value: "", label: "Semua unit" },
  { value: "RUPIAH", label: "Rupiah" },
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
          <div className="flex items-start gap-3"><span className="grid size-8 shrink-0 place-items-center rounded-md bg-[var(--ink-soft)] text-[var(--accent)]"><Info size={16} /></span><div><h2 id="kpi-info-title" className="font-semibold">Tentang Setting KPI</h2><p className="mt-1 text-xs text-[var(--muted)]">Konfigurasi indikator dan parameter penilaian.</p></div></div>
          <Button variant="ghost" size="icon" aria-label="Tutup informasi" onClick={onClose}><X size={16} /></Button>
        </div>
        <div className="space-y-3 px-5 py-5 text-sm leading-6 text-[var(--muted)]">
          <p>Halaman ini menampilkan indikator, bobot, target default, arah penilaian, dan unit untuk tiga jabatan KPI Tukin.</p>
          <ul className="list-disc space-y-1 pl-5"><li>Perhitungan indeks, skor, nilai akhir, dan faktor Tukin dilakukan oleh layanan backend.</li><li>React hanya mengambil response API dan menyajikannya tanpa menghitung ulang.</li><li>Perubahan parameter mengikuti permission dan kontrak API.</li></ul>
        </div>
      </section>
    </div>
  )
}

type ScoreDraft = Record<number, { min: string; max: string; predikat: string }>

function compactDecimal(value: unknown) {
  const text = String(value ?? "").trim().replace(",", ".")
  const number = Number(text)
  return text && Number.isFinite(number) ? String(number) : text
}

function compactScoreValue(value: string, row: KpiIndicator) {
  const compact = compactDecimal(value)
  const number = Number(compact)
  if (row.formula_key === "GROWTH_KREDIT" && Number.isFinite(number) && Math.abs(number) >= 1_000_000) return formatTarget(number, "RUPIAH")
  return compact
}

function ScoreInput({ label, value, row, onChange }: { label: string; value: string; row: KpiIndicator; onChange: (value: string) => void }) {
  const [editing, setEditing] = useState(false)
  return <KpiInput label={label} value={editing ? value : compactScoreValue(value, row)} onChange={onChange} onFocus={() => setEditing(true)} onBlur={() => setEditing(false)} />
}

function tukinValueRange(rule: KpiTukinRule) {
  const min = rule.min_nilai == null ? null : Number(rule.min_nilai)
  const max = rule.max_nilai == null ? null : Number(rule.max_nilai)
  if (min == null && max == null) return "-"
  if (min == null) return `< ${formatNumber(max, 2)}`
  if (max == null) return `>= ${formatNumber(min, 2)}`
  if (min === 0) return `< ${formatNumber(max, 2)}`
  if (max >= 100) return `>= ${formatNumber(min, 2)} - ${formatNumber(max, 2)}`
  return `>= ${formatNumber(min, 2)} - < ${formatNumber(max, 2)}`
}

function tukinBonus(rule: KpiTukinRule) {
  const bonusByOrder: Record<number, number> = { 5: 500_000, 6: 750_000, 7: 1_000_000 }
  const bonus = bonusByOrder[Number(rule.urutan)]
  return bonus ? `Rp ${formatNumber(bonus, 0)}` : "-"
}

function TukinRuleTable({ rows }: { rows: KpiTukinRule[] }) {
  return <div className="kpi-scrollbar max-h-[calc(100dvh-180px)] overflow-auto border-t border-[var(--line-soft)]">
    <table className="w-full min-w-[720px] border-separate border-spacing-0 text-sm tabular-nums">
      <thead><tr className="bg-[var(--surface-muted)] text-left text-xs text-[var(--muted)]">
        <th className="sticky top-0 z-10 border-b border-[var(--line)] bg-[var(--surface-muted)] px-4 py-3 font-medium">Skor KPI Final (Skala 1-5)</th>
        <th className="sticky top-0 z-10 border-b border-[var(--line)] bg-[var(--surface-muted)] px-4 py-3 font-medium">Nilai KPI Final (Skala 0-100)</th>
        <th className="sticky top-0 z-10 border-b border-[var(--line)] bg-[var(--surface-muted)] px-4 py-3 font-medium">Faktor Tukin</th>
        <th className="sticky top-0 z-10 border-b border-[var(--line)] bg-[var(--surface-muted)] px-4 py-3 font-medium">Bonus Tukin</th>
      </tr></thead>
      <tbody>{rows.length ? rows.map((rule) => <tr key={`${rule.urutan}-${rule.label}`}>
        <td className="border-b border-[var(--line-soft)] px-4 py-3 font-medium text-[var(--ink)]">{rule.label || "-"}</td>
        <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{tukinValueRange(rule)}</td>
        <td className="border-b border-[var(--line-soft)] px-4 py-3 font-medium text-[var(--accent)]">{formatNumber(rule.faktor_persen, 2)}%</td>
        <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{tukinBonus(rule)}</td>
      </tr>) : <tr><td colSpan={4} className="px-4 py-10 text-center text-sm text-[var(--muted)]">Belum ada parameter faktor Tukin.</td></tr>}</tbody>
    </table>
  </div>
}

function ScoreDialog({ row, scores, saving, onClose, onSave }: { row: KpiIndicator; scores: KpiScoreParameter[]; saving: boolean; onClose: () => void; onSave: (drafts: ScoreDraft) => void }) {
  const [drafts, setDrafts] = useState<ScoreDraft>(() => Object.fromEntries(Array.from({ length: 6 }, (_, score) => {
    const item = scores.find((candidate) => Number(candidate.skor) === score)
    return [score, { min: compactDecimal(item?.min_indeks ?? "0"), max: compactDecimal(item?.max_indeks ?? "999"), predikat: item?.predikat || "" }]
  })))

  const update = (score: number, field: "min" | "max" | "predikat", value: string) => {
    setDrafts((current) => ({ ...current, [score]: { ...current[score], [field]: value } }))
  }

  return <KpiModal open title={`Parameter skor · ${shortIndicatorName(row)}`} description="Nilai indeks disimpan sebagai rasio desimal. Contoh 0,80 = 80%." onClose={onClose}>
    <div className="overflow-x-auto">
      <table className="w-full min-w-[820px] border-separate border-spacing-0 text-sm tabular-nums">
        <thead><tr className="text-left text-xs text-[var(--muted)]"><th className="border-b border-[var(--line)] px-3 py-2 font-medium">Skor</th><th className="border-b border-[var(--line)] px-3 py-2 font-medium">Tampilan</th><th className="border-b border-[var(--line)] px-3 py-2 font-medium">Minimum indeks</th><th className="border-b border-[var(--line)] px-3 py-2 font-medium">Maksimum indeks</th><th className="border-b border-[var(--line)] px-3 py-2 font-medium">Predikat</th></tr></thead>
        <tbody>{Array.from({ length: 6 }, (_, score) => { const draft = drafts[score] || { min: "0", max: "999", predikat: "" }; return <tr key={score}><td className="border-b border-[var(--line-soft)] px-3 py-2 font-semibold">{score}</td><td className="border-b border-[var(--line-soft)] px-3 py-2 text-xs font-medium text-[var(--accent)]">{formatScoreRange({ skor: score, min_indeks: draft.min, max_indeks: draft.max }, row)}</td><td className="border-b border-[var(--line-soft)] px-3 py-2"><ScoreInput label="" row={row} value={draft.min} onChange={(value) => update(score, "min", value)} /></td><td className="border-b border-[var(--line-soft)] px-3 py-2"><ScoreInput label="" row={row} value={draft.max} onChange={(value) => update(score, "max", value)} /></td><td className="border-b border-[var(--line-soft)] px-3 py-2"><KpiInput label="" value={draft.predikat} onChange={(value) => update(score, "predikat", value)} /></td></tr> })}</tbody>
      </table>
    </div>
    <div className="mt-4 flex justify-end gap-2"><Button variant="outline" onClick={onClose}>Batal</Button><Button onClick={() => onSave(drafts)} disabled={saving}>{saving ? "Menyimpan..." : "Simpan skor"}</Button></div>
  </KpiModal>
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
  const [scoreEditorRow, setScoreEditorRow] = useState<KpiIndicator | null>(null)
  const [savingScoreId, setSavingScoreId] = useState<number | null>(null)
  const [showTukin, setShowTukin] = useState(false)
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

  const saveScores = async (row: KpiIndicator, scoreDrafts: ScoreDraft) => {
    const scores = (data?.parameter_skor || []).filter((item) => item.indikator_id === row.id && Number(item.aktif) === 1)
    if (scores.length !== 6) { setError(`Parameter skor ${row.nama} belum lengkap. Ditemukan ${scores.length} dari 6 level.`); return }
    let payload: Array<{ id: number; min_indeks: number; max_indeks: number; predikat: string }>
    try {
      payload = scores.map((item) => {
        const draft = scoreDrafts[Number(item.skor)]
        const min = Number(String(draft?.min ?? "").replace(",", "."))
        const max = Number(String(draft?.max ?? "").replace(",", "."))
        if (!Number.isFinite(min) || !Number.isFinite(max) || min > max) throw new Error(`Range skor ${item.skor} tidak valid.`)
        return { id: Number(item.id), min_indeks: min, max_indeks: max, predikat: draft?.predikat?.trim() || `Skor ${item.skor}` }
      })
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Range skor tidak valid")
      return
    }
    setSavingScoreId(row.id)
    setError("")
    try {
      await Promise.all(payload.map((item) => saveKpiScore(item)))
      setScoreEditorRow(null)
      load()
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Parameter skor gagal disimpan")
    } finally {
      setSavingScoreId(null)
    }
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
    <div className="mx-auto w-full max-w-[1500px] space-y-3 p-3 sm:p-4 lg:p-5">
      <section className="flex flex-wrap items-start justify-between gap-4">
        <div className="min-w-0"><div className="flex items-center gap-1.5"><h2 className="text-lg font-semibold tracking-tight sm:text-xl">Setting KPI Jabatan</h2><Button variant="ghost" size="icon" className="size-7" aria-label="Tentang Setting KPI" onClick={() => setInfoOpen(true)}><Info size={15} /></Button></div><p className="mt-0.5 max-w-2xl text-xs text-[var(--muted)]">Kelola indikator dan parameter KPI Tukin.</p></div>
        <div className="flex items-center gap-3 text-xs text-[var(--muted)]"><div className="hidden text-right sm:block"><p className="text-[10px]">Jabatan aktif</p><p className="font-medium text-[var(--ink)]">{selectedJob?.nama || "-"}</p></div><span className="hidden h-7 w-px bg-[var(--line)] sm:block" /><div className="hidden text-right sm:block"><p className="text-[10px]">{showTukin ? "Aturan Tukin" : "Indikator"}</p><p className="font-medium text-[var(--ink)]">{formatNumber(showTukin ? data?.tukin_rules.length : activeCount, 0)}</p></div>{data?.can_manage === false && <span>Mode baca</span>}<Button variant="outline" size="icon" title={showTukin ? "Tampilkan indikator" : "Tampilkan skema Tukin"} aria-label={showTukin ? "Tampilkan indikator" : "Tampilkan skema Tukin"} onClick={() => setShowTukin((current) => !current)}><ArrowLeftRight size={15} /></Button><Button variant="outline" size="sm" onClick={load} disabled={loading}><RefreshCw size={14} className={loading ? "animate-spin" : undefined} /> <span>Muat ulang</span></Button></div>
      </section>

      <section className="overflow-hidden rounded-xl border border-[var(--line)] bg-[var(--surface)]">
        {loading ? <div className="grid min-h-40 place-items-center gap-2 text-sm text-[var(--muted)]"><RefreshCw size={17} className="animate-spin text-[var(--accent)]" /><span>Mengambil konfigurasi...</span></div> : error ? <div className="grid min-h-40 place-items-center gap-3 px-6 text-center"><p className="text-sm text-red-600 dark:text-red-400">{error}</p><Button variant="outline" size="sm" onClick={load}>Coba lagi</Button></div> : showTukin ? <TukinRuleTable rows={data?.tukin_rules || []} /> : <SettingKpiJabatanTable rows={rows} scores={data?.parameter_skor || []} drafts={drafts} savingId={savingId} onChange={updateDraft} onSave={saveIndicator} onEditScores={setScoreEditorRow} />}
      </section>
      {infoOpen && <InfoDialog onClose={() => setInfoOpen(false)} />}
      {scoreEditorRow && <ScoreDialog key={scoreEditorRow.id} row={scoreEditorRow} scores={(data?.parameter_skor || []).filter((item) => item.indikator_id === scoreEditorRow.id && Number(item.aktif) === 1)} saving={savingScoreId === scoreEditorRow.id} onClose={() => setScoreEditorRow(null)} onSave={(scoreDrafts) => { void saveScores(scoreEditorRow, scoreDrafts) }} />}
    </div>
  )
}
