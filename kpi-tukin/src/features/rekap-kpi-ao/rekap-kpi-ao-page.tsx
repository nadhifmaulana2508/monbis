import { useEffect, useMemo, useState } from "react"
import { Info, RefreshCw } from "lucide-react"

import { KpiField, KpiMetric, KpiModal, KpiPage, KpiPanel, KpiSelect, KpiState } from "@/components/app/kpi-ui"
import { useKpiToolbar } from "@/components/app/kpi-shell"
import { loadAnnualKpi, loadKpiDirectory } from "@/features/kpi-data"
import type { AnnualKpiResponse } from "@/features/kpi-types"
import type { KpiJobCode } from "@/features/setting-kpi-jabatan/setting-kpi-jabatan-types"
import { formatNumber, formatTarget, formatWeight } from "@/lib/utils"

const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"]

export function RekapKpiAoPage() {
  const { setToolbar } = useKpiToolbar()
  const [year, setYear] = useState(String(new Date().getFullYear()))
  const [job, setJob] = useState<KpiJobCode>("AO_KREDIT")
  const [office, setOffice] = useState("")
  const [ao, setAo] = useState("")
  const [query, setQuery] = useState("")
  const [directory, setDirectory] = useState<Awaited<ReturnType<typeof loadKpiDirectory>> | null>(null)
  const [data, setData] = useState<AnnualKpiResponse | null>(null)
  const [tab, setTab] = useState<"summary" | "breakdown" | "tukin">("summary")
  const [breakdownLoadedFor, setBreakdownLoadedFor] = useState("")
  const [loading, setLoading] = useState(true)
  const [reloadKey, setReloadKey] = useState(0)
  const [error, setError] = useState("")
  const [infoOpen, setInfoOpen] = useState(false)

  useEffect(() => {
    const controller = new AbortController()
    setLoading(true)
    setError("")
    loadKpiDirectory({ year: Number(year), jabatan_kode: job, include_all_ao: true, include_generated: false }, controller.signal).then((result) => {
      setDirectory(result)
      setOffice((current) => result.kantor.some((item) => item.kode_kantor === current) ? current : "")
      setAo("")
    }).catch((reason) => {
      if (!(reason instanceof DOMException && reason.name === "AbortError")) setError(reason instanceof Error ? reason.message : "Direktori KPI gagal dimuat")
    }).finally(() => setLoading(false))
    return () => controller.abort()
  }, [job, year])

  useEffect(() => {
    const controller = new AbortController()
    setLoading(true)
    setBreakdownLoadedFor("")
    loadAnnualKpi({ year: Number(year), jabatan_kode: job, kode_kantor: office, kode_ao: ao }, controller.signal).then(setData).catch((reason) => {
      if (!(reason instanceof DOMException && reason.name === "AbortError")) setError(reason instanceof Error ? reason.message : "Rekap KPI gagal dimuat")
    }).finally(() => setLoading(false))
    return () => controller.abort()
  }, [ao, job, office, reloadKey, year])

  const filterKey = `${year}|${job}|${office}|${ao}`
  useEffect(() => {
    if (tab !== "breakdown" || !data || breakdownLoadedFor === filterKey) return
    const controller = new AbortController()
    loadAnnualKpi({ year: Number(year), jabatan_kode: job, kode_kantor: office, kode_ao: ao, include_breakdown: true }, controller.signal).then((result) => {
      setData((current) => current ? { ...current, indicator_breakdown: result.indicator_breakdown } : result)
      setBreakdownLoadedFor(filterKey)
    }).catch((reason) => {
      if (!(reason instanceof DOMException && reason.name === "AbortError")) setError(reason instanceof Error ? reason.message : "Breakdown KPI gagal dimuat")
    })
    return () => controller.abort()
  }, [ao, breakdownLoadedFor, data, filterKey, job, office, tab, year])

  const filteredAo = useMemo(() => (directory?.ao || []).filter((item) => !office || item.kode_kantor === office), [directory?.ao, office])
  const filteredRows = useMemo(() => (data?.ao || []).filter((row) => !query.trim() || `${row.nama_ao} ${row.kode_ao} ${row.kode_kantor}`.toLowerCase().includes(query.trim().toLowerCase())), [data?.ao, query])
  const avg = useMemo(() => { const values = filteredRows.map((row) => row.nilai_akhir).filter((value): value is number => value !== null); return values.length ? values.reduce((sum, value) => sum + value, 0) / values.length : null }, [filteredRows])

  useEffect(() => {
    setToolbar(<div className="flex min-w-max items-end gap-2"><KpiSelect label="Jabatan" value={job} onChange={(value) => { setJob(value as KpiJobCode); setOffice(""); setAo("") }}>{(directory?.jabatan || [{ kode: "AO_KREDIT", nama: "AO Kredit" }]).map((item) => <option key={item.kode} value={item.kode}>{item.nama}</option>)}</KpiSelect><KpiField label="Tahun"><input type="number" value={year} onChange={(event) => setYear(event.target.value)} className="h-9 w-20 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2.5 text-sm outline-none focus:border-[var(--accent)]" /></KpiField><KpiSelect label="Kantor" value={office} onChange={(value) => { setOffice(value); setAo("") }}><option value="">Semua kantor</option>{(directory?.kantor || []).map((item) => <option key={item.kode_kantor} value={item.kode_kantor}>{item.kode_kantor} · {item.nama_kantor}</option>)}</KpiSelect><KpiSelect label="AO" value={ao} onChange={setAo}><option value="">Semua AO</option>{filteredAo.map((item) => <option key={`${item.kode_ao}-${item.id_peg || ""}`} value={item.kode_ao}>{item.nama_ao}</option>)}</KpiSelect><KpiField label="Cari"><input value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Kantor atau AO" className="h-9 w-44 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2.5 text-sm outline-none focus:border-[var(--accent)]" /></KpiField></div>)
    return () => setToolbar(null)
  }, [ao, directory, filteredAo, job, query, setToolbar, year, office])

  return <KpiPage title="Rekap KPI AO" description="Rekap penilaian tahunan dari seluruh periode yang sudah digenerate. Filter berada di navbar agar pola antar halaman tetap konsisten." actions={<><button className="inline-flex size-9 items-center justify-center rounded-md text-[var(--muted)] hover:bg-[var(--surface-subtle)]" aria-label="Tentang rekap KPI" onClick={() => setInfoOpen(true)}><Info size={16} /></button><button className="inline-flex size-9 items-center justify-center rounded-md border border-[var(--line)] text-[var(--muted)] hover:bg-[var(--surface-subtle)]" aria-label="Muat ulang" onClick={() => { setData(null); setReloadKey((value) => value + 1) }}><RefreshCw size={15} /></button></>}>
    <section className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4"><KpiMetric label="AO dinilai" value={formatNumber(data?.ao.length || 0, 0)} detail={data?.is_konsolidasi && data.total_ao > (data.ao.length || 0) ? `Top ${data.ao.length} dari ${data.total_ao} AO terbaik` : "Sudah memiliki penilaian"} /><KpiMetric label="Periode terisi" value={data?.months.filter((item) => item.terisi > 0).length || 0} detail="Hanya bulan yang sudah digenerate" /><KpiMetric label="Rata-rata nilai" value={avg == null ? "-" : `${formatNumber(avg)} / 100`} tone="success" detail="Nilai berbobot" /><KpiMetric label="Mode rekap" value={data?.is_konsolidasi ? "Konsolidasi" : (office || "Semua kantor")} detail={data?.is_konsolidasi ? "Top 5 nilai terbaik" : "Filter kantor aktif"} /></section>
    <KpiPanel title="Report tahunan" actions={<div className="flex rounded-md border border-[var(--line)] p-0.5 text-xs">{([["summary", "Rekap AO"], ["breakdown", "Breakdown"], ["tukin", "Faktor Tukin"]] as const).map(([value, label]) => <button key={value} className={`rounded px-2.5 py-1.5 ${tab === value ? "bg-[var(--ink)] text-[var(--surface)]" : "text-[var(--muted)] hover:bg-[var(--surface-subtle)]"}`} onClick={() => setTab(value)}>{label}</button>)}</div>}>
      <KpiState loading={(loading && !data) || (tab === "breakdown" && breakdownLoadedFor !== filterKey)} error={error} empty={!data && !loading ? "Belum ada data rekap untuk filter ini." : undefined}>{data && tab === "summary" && <SummaryTable rows={filteredRows} />}{data && tab === "breakdown" && breakdownLoadedFor === filterKey && <BreakdownTable rows={data.indicator_breakdown} />}{data && tab === "tukin" && <TukinTable rows={data.tukin_rules} />}</KpiState>
    </KpiPanel>
    <KpiModal open={infoOpen} title="Rekap KPI AO" description="Data berasal dari penilaian yang sudah digenerate." onClose={() => setInfoOpen(false)}><p className="text-sm leading-6 text-[var(--muted)]">Pada konsolidasi, backend membatasi daftar ke lima AO terbaik agar halaman tetap ringan. Pilih kantor untuk melihat seluruh hasil kantor tersebut.</p></KpiModal>
  </KpiPage>
}

function SummaryTable({ rows }: { rows: AnnualKpiResponse["ao"] }) {
  return <div className="max-h-[calc(100dvh-330px)] overflow-auto"><table className="w-full min-w-[1120px] border-separate border-spacing-0 text-sm tabular-nums"><thead className="sticky top-0 z-10 bg-[var(--surface)] text-left text-xs text-[var(--muted)]"><tr><th className="sticky left-0 z-20 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Kantor</th><th className="sticky left-20 z-20 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">AO / ID Peg</th>{months.map((month) => <th key={month} className="border-b border-[var(--line)] px-3 py-3 text-center font-medium">{month}</th>)}<th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">Rata-rata</th><th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">Skor</th><th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">Tukin</th><th className="border-b border-[var(--line)] px-3 py-3 text-right font-medium">Bulan</th></tr></thead><tbody>{rows.length ? rows.map((row) => <tr key={`${row.id_peg}-${row.kode_ao}`} className="hover:bg-[var(--surface-subtle)]"><td className="sticky left-0 z-[1] border-b border-[var(--line-soft)] bg-[var(--surface)] px-4 py-3 text-[var(--muted)]">{row.kode_kantor}</td><td className="sticky left-20 z-[1] border-b border-[var(--line-soft)] bg-[var(--surface)] px-4 py-3"><div className="font-medium">{row.nama_ao}</div><div className="text-xs text-[var(--muted)]">{row.id_peg || row.kode_ao}</div></td>{months.map((_, index) => { const value = row.monthly[String(index + 1)] ; return <td key={index} className="border-b border-[var(--line-soft)] px-3 py-3 text-center">{value ? <><div>{formatNumber(value.nilai_akhir)}</div><div className="text-[10px] text-[var(--muted)]">{formatNumber(value.tukin_persen)}%</div></> : <span className="text-[var(--muted)]">-</span>}</td> })}<td className="border-b border-[var(--line-soft)] px-3 py-3 text-right font-medium">{row.nilai_akhir == null ? "-" : formatNumber(row.nilai_akhir)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 text-right">{row.skor_final == null ? "-" : `${formatNumber(row.skor_final)} / 5`}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 text-right text-emerald-600 dark:text-emerald-400">{row.tukin_persen == null ? "-" : `${formatNumber(row.tukin_persen)}%`}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 text-right">{row.bulan_terisi} / 12</td></tr>) : <tr><td colSpan={18} className="px-4 py-14 text-center text-[var(--muted)]">Belum ada penilaian KPI.</td></tr>}</tbody></table></div>
}

function BreakdownTable({ rows }: { rows: AnnualKpiResponse["indicator_breakdown"] }) {
  return <div className="max-h-[calc(100dvh-330px)] overflow-auto"><table className="w-full min-w-[1200px] border-separate border-spacing-0 text-sm tabular-nums"><thead className="sticky top-0 z-10 bg-[var(--surface)] text-left text-xs text-[var(--muted)]"><tr>{["Kantor", "AO", "Indikator", "Bulan", "Nilai akhir", "Tukin", "Target", "Realisasi", "Indeks", "Skor", "Bobot", "Nilai", "Input PA"].map((label) => <th key={label} className="border-b border-[var(--line)] px-3 py-3 font-medium">{label}</th>)}</tr></thead><tbody>{rows.length ? rows.map((row, index) => <tr key={`${row.id_peg}-${row.indikator_id}-${row.bulan}-${index}`}><td className="border-b border-[var(--line-soft)] px-3 py-3">{row.kode_kantor}</td><td className="border-b border-[var(--line-soft)] px-3 py-3"><div className="font-medium">{row.nama_ao}</div><div className="text-xs text-[var(--muted)]">{row.id_peg}</div></td><td className="border-b border-[var(--line-soft)] px-3 py-3"><div className="font-medium">{row.nama}</div><div className="text-xs text-[var(--muted)]">{row.kelompok || "-"}</div></td><td className="border-b border-[var(--line-soft)] px-3 py-3">{months[(row.bulan || 1) - 1] || row.bulan}<div className="text-[10px] text-[var(--muted)]">{row.closing_date}</div></td><td className="border-b border-[var(--line-soft)] px-3 py-3">{row.nilai_akhir_bulan == null ? "-" : formatNumber(row.nilai_akhir_bulan)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 text-emerald-600 dark:text-emerald-400">{row.tukin_persen_bulan == null ? "-" : `${formatNumber(row.tukin_persen_bulan)}%`}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatTarget(row.target, row.unit)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 font-medium">{formatTarget(row.realisasi, row.unit)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatTarget(row.indeks, row.unit === "PERSEN" ? "PERSEN" : "")}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatNumber(row.skor)} / 5</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatWeight(row.bobot)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatNumber(row.nilai_100)}</td><td className="max-w-52 border-b border-[var(--line-soft)] px-3 py-3 text-xs text-[var(--muted)]">{row.input_pa || "-"}</td></tr>) : <tr><td colSpan={13} className="px-4 py-14 text-center text-[var(--muted)]">Belum ada breakdown indikator.</td></tr>}</tbody></table></div>
}

function TukinTable({ rows }: { rows: AnnualKpiResponse["tukin_rules"] }) {
  return <div className="max-w-2xl overflow-hidden"><table className="w-full text-sm"><thead className="bg-[var(--surface-subtle)] text-left text-xs text-[var(--muted)]"><tr><th className="px-4 py-3 font-medium">Klasifikasi</th><th className="px-4 py-3 font-medium">Nilai KPI</th><th className="px-4 py-3 text-right font-medium">Faktor Tukin</th></tr></thead><tbody>{rows.map((row) => <tr key={`${row.label}-${row.urutan}`}><td className="border-t border-[var(--line-soft)] px-4 py-3">{row.label}</td><td className="border-t border-[var(--line-soft)] px-4 py-3">{row.min_nilai == null ? "-" : formatNumber(row.min_nilai)} – {row.max_nilai == null ? "100" : formatNumber(row.max_nilai)}</td><td className="border-t border-[var(--line-soft)] px-4 py-3 text-right font-medium text-emerald-600 dark:text-emerald-400">{formatNumber(row.faktor_persen)}%</td></tr>)}</tbody></table></div>
}
