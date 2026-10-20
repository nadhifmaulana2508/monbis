import { useState } from "react"
import { SlidersHorizontal } from "lucide-react"

import { Button } from "@/components/ui/button"
import { formatTarget } from "@/lib/utils"
import type { KpiIndicator, KpiScoreParameter } from "./setting-kpi-jabatan-types"

type Props = { rows: KpiIndicator[] }

export function shortIndicatorName(row: KpiIndicator) {
  switch (row.formula_key) {
    case "REALISASI_KREDIT": return "Pencairan Kredit Netto"
    case "GROWTH_KREDIT": return "Growth Kredit"
    case "GROWTH_NOA": return "Growth NOA"
    case "REPAYMENT_RATE": return "Repayment Rate"
    case "FLOW_DPK": return "Flow DPK 1–30 → 31+"
    default: return row.nama.replace(/\s*\([^)]*\)\s*$/, "")
  }
}

function indicatorDescription(row: KpiIndicator) {
  switch (row.formula_key) {
    case "REALISASI_KREDIT": return "Mengukur pencapaian realisasi pencairan kredit netto selama periode penilaian dibandingkan dengan target bulanan sebesar Rp450.000.000."
    case "GROWTH_KREDIT": return "Mengukur pertumbuhan outstanding (OS) kredit kelolaan AO selama periode penilaian, berdasarkan selisih OS akhir periode dengan OS awal periode."
    case "GROWTH_NOA": return "Mengukur pertumbuhan jumlah rekening atau nasabah kredit baru (NOA/CIF) selama periode penilaian berdasarkan selisih jumlah pada akhir dan awal periode."
    case "REPAYMENT_RATE": return "Mengukur persentase outstanding kredit lancar (DPD 0) pada akhir periode terhadap total outstanding kredit kelolaan AO pada akhir periode."
    case "FLOW_DPK": return "Mengukur persentase outstanding kredit dengan DPD 1–30 pada awal periode yang mengalami penurunan kualitas menjadi DPD 31–60 pada periode berjalan."
    default: return row.definisi || "-"
  }
}

function scoreValue(value: unknown, row: KpiIndicator) {
  const number = Number(value)
  if (!Number.isFinite(number)) return "-"
  const ratio = row.unit === "PERSEN" || row.formula_key === "REALISASI_KREDIT"
  if (ratio) return `${(number * 100).toLocaleString("id-ID", { maximumFractionDigits: 2 })}%`
  if (row.unit === "RUPIAH") return formatTarget(number, row.unit).replace(/^Rp\s*/, "")
  return number.toLocaleString("id-ID", { maximumFractionDigits: 2 })
}

function TargetInput({ row, value, onChange }: { row: KpiIndicator; value: string; onChange: (value: string) => void }) {
  const [editing, setEditing] = useState(false)
  const compact = row.unit === "RUPIAH" && value.trim() ? formatTarget(value, row.unit) : value
  const shown = !editing && compact !== "-" ? compact : value

  return <input aria-label={`Target ${row.nama}`} value={shown} onFocus={() => setEditing(true)} onBlur={() => setEditing(false)} onChange={(event) => onChange(event.target.value)} className="h-8 w-32 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2 text-right text-sm outline-none focus:border-[var(--accent)]" inputMode="decimal" />
}

export function formatScoreRange(item: { skor?: unknown; min_indeks: unknown; max_indeks: unknown }, row: KpiIndicator) {
  const min = Number(item.min_indeks)
  const max = Number(item.max_indeks)
  const score = Number(item.skor)
  if (!Number.isFinite(min) || !Number.isFinite(max)) return "-"
  if (row.unit === "NOA" || row.unit === "JUMLAH") {
    if (max === 0 && min <= 0) return "<= 0"
    if (max >= 999) return `> ${min - 1}`
    return `> ${min - 1} - <= ${max - 1}`
  }
  if (row.formula_key === "GROWTH_KREDIT") {
    if (max === 0) return "< 0"
    if (score === 1) return `0 - ${scoreValue(max, row)}`
    if (max >= 999) return `> ${scoreValue(min, row)}`
    return `> ${scoreValue(min, row)} - ${scoreValue(max, row)}`
  }
  if (row.formula_key === "FLOW_DPK") {
    if (max >= 999) return `> ${scoreValue(min, row)}`
    return `<= ${scoreValue(max, row)}`
  }
  if (row.arah === "LOWER") {
    if (max >= 999) return `> ${scoreValue(min, row)}`
    if (min === 0) return `<= ${scoreValue(max, row)}`
    return `> ${scoreValue(min, row)} - <= ${scoreValue(max, row)}`
  }
  if (min === 0 && max === 0) return "<= 0"
  if (max >= 999) return `>= ${scoreValue(min, row)}`
  if (min === 0) return `< ${scoreValue(max, row)}`
  return `>= ${scoreValue(min, row)} - < ${scoreValue(max, row)}`
}

export function SettingKpiJabatanTable({ rows, scores, drafts, savingId, onChange, onSave, onEditScores }: Props & { scores: KpiScoreParameter[]; drafts: Record<number, { bobot: string; target: string; status: string }>; savingId: number | null; onChange: (id: number, field: "bobot" | "target" | "status", value: string) => void; onSave: (row: KpiIndicator) => void; onEditScores: (row: KpiIndicator) => void }) {
  if (!rows.length) {
    return <div className="grid min-h-48 place-items-center px-6 text-sm text-[var(--muted)]">Tidak ada indikator aktif untuk filter ini.</div>
  }

  return (
    <div className="kpi-scrollbar max-h-[calc(100dvh-180px)] overflow-auto border-t border-[var(--line)]">
      <table className="w-full min-w-[1700px] border-separate border-spacing-0 text-sm tabular-nums">
        <thead className="text-left text-xs text-[var(--muted)]">
          <tr>
            <th className="sticky top-0 left-0 z-30 w-12 min-w-12 border-b border-r border-[var(--line)] bg-[var(--surface)] px-3 py-3 text-center font-medium">No</th>
            <th className="sticky top-0 left-12 z-30 min-w-72 border-b border-r border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium shadow-[4px_0_8px_-8px_rgba(15,23,42,0.45)]">Indikator</th>
            <th className="sticky top-0 left-[21rem] z-30 w-28 min-w-28 border-b border-r border-[var(--line)] bg-[var(--surface)] px-4 py-3 text-right font-medium shadow-[4px_0_8px_-8px_rgba(15,23,42,0.45)]">Bobot</th>
            <th className="sticky top-0 z-20 min-w-[420px] border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Deskripsi</th>
            <th className="sticky top-0 z-20 w-36 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Kelompok</th>
            <th className="sticky top-0 z-20 w-36 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 text-right font-medium">Target default</th>
            <th className="sticky top-0 z-20 w-24 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Arah</th>
            <th className="sticky top-0 z-20 w-24 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Unit</th>
            {Array.from({ length: 6 }, (_, score) => <th key={score} className="sticky top-0 z-20 min-w-32 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 text-center font-medium">Skor {score}</th>)}
            <th className="sticky top-0 z-20 w-28 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Parameter</th>
            <th className="sticky top-0 z-20 w-24 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Status</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((row, index) => (
            <tr key={row.id} className="group hover:bg-[var(--surface-subtle)]">
              <td className="sticky left-0 z-20 w-12 min-w-12 border-b border-r border-[var(--line-soft)] bg-[var(--surface)] px-3 py-3 text-center text-[var(--muted)] group-hover:bg-[var(--surface-subtle)]">{index + 1}</td>
              <td className="sticky left-12 z-20 min-w-72 border-b border-r border-[var(--line-soft)] bg-[var(--surface)] px-4 py-3 shadow-[4px_0_8px_-8px_rgba(15,23,42,0.45)] group-hover:bg-[var(--surface-subtle)]">
                <div className="font-medium">{shortIndicatorName(row)}</div>
              </td>
              <td className="sticky left-[21rem] z-20 w-28 min-w-28 border-b border-r border-[var(--line-soft)] bg-[var(--surface)] px-4 py-3 shadow-[4px_0_8px_-8px_rgba(15,23,42,0.45)] group-hover:bg-[var(--surface-subtle)]"><input aria-label={`Bobot ${shortIndicatorName(row)}`} value={drafts[row.id]?.bobot ?? String(row.bobot * 100)} onChange={(event) => onChange(row.id, "bobot", event.target.value)} className="h-8 w-20 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2 text-right text-sm outline-none focus:border-[var(--accent)]" inputMode="decimal" /></td>
              <td className="max-w-[520px] border-b border-[var(--line-soft)] px-4 py-3 text-xs leading-5 text-[var(--muted)]">{indicatorDescription(row)}</td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{row.kelompok || "-"}</td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3"><TargetInput row={row} value={drafts[row.id]?.target ?? String(row.target_default)} onChange={(value) => onChange(row.id, "target", value)} /></td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{row.arah || "-"}</td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{row.unit || "-"}</td>
              {Array.from({ length: 6 }, (_, score) => { const item = scores.find((candidate) => candidate.indikator_id === row.id && Number(candidate.skor) === score && Number(candidate.aktif) === 1); return <td key={score} title={item?.predikat || "Parameter skor belum tersedia"} className="border-b border-[var(--line-soft)] px-4 py-3 text-center text-xs leading-5 text-[var(--muted)]">{item ? formatScoreRange(item, row) : "-"}</td> })}
              <td className="border-b border-[var(--line-soft)] px-4 py-3"><Button variant="outline" size="sm" onClick={() => onEditScores(row)}><SlidersHorizontal size={14} /> Atur</Button></td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3">
                <div className="flex items-center gap-2"><select aria-label={`Status ${row.nama}`} value={drafts[row.id]?.status ?? row.status} onChange={(event) => onChange(row.id, "status", event.target.value)} className="h-8 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2 text-xs outline-none focus:border-[var(--accent)]"><option value="AKTIF">AKTIF</option><option value="PILOT">PILOT</option><option value="NONAKTIF">NONAKTIF</option></select><Button size="sm" disabled={savingId === row.id} onClick={() => onSave(row)}>{savingId === row.id ? "..." : "Simpan"}</Button></div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}
