import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Info, Play, RefreshCw, Search } from "lucide-react"

import { Button } from "@/components/ui/button"
import { KpiField, KpiInput, KpiMetric, KpiModal, KpiPage, KpiPanel, KpiSelect, KpiState, KpiStatus } from "@/components/app/kpi-ui"
import { useKpiToolbar } from "@/components/app/kpi-shell"
import { calculateKpi, loadKpiAoList, loadKpiDetail, loadKpiDirectory, loadKpiEvaluation } from "@/features/kpi-data"
import type { KpiAo, KpiDetail, KpiDirectory, KpiEvaluation } from "@/features/kpi-types"
import type { KpiJobCode } from "@/features/setting-kpi-jabatan/setting-kpi-jabatan-types"
import { formatNumber, formatTarget, formatWeight } from "@/lib/utils"

const defaultYear = new Date().getFullYear()

function detailValue(value: number | null | undefined, unit = "") {
  if (value === null || value === undefined) return "-"
  return formatTarget(value, unit)
}

function sourceNote(row: KpiDetail) {
  const parts = [row.catatan].filter(Boolean) as string[]
  if (row.formula_key === "REPAYMENT_RATE") parts.push(`OS DPD 0 ${detailValue(row.os_dpd0, "RUPIAH")} / OS kelolaan ${detailValue(row.os_kelolaan, "RUPIAH")}`)
  if (row.formula_key === "MOB_6") parts.push(`OS menunggak ${detailValue(row.os_mob_menunggak, "RUPIAH")} / total ${detailValue(row.os_mob_total, "RUPIAH")}`)
  if (row.formula_key === "EARLY_RUN_OFF") parts.push(`Run off ${detailValue(row.os_run_off, "RUPIAH")} / DPD 0 M-1 ${detailValue(row.os_dpd0_m1, "RUPIAH")}`)
  if (row.input_pa) parts.push(`Input PA: ${row.input_pa}`)
  return parts.join(" · ") || `Target ${detailValue(row.target, row.unit)} · Realisasi ${detailValue(row.realisasi, row.unit)}`
}

type Props = { mode?: "single" | "bulk" }
type BulkWorkItem = { person: KpiAo; date: string }

export function NilaiKpiAoPage({ mode = "single" }: Props) {
  const { setToolbar } = useKpiToolbar()
  const bulk = mode === "bulk"
  const [year, setYear] = useState(String(defaultYear))
  const [job, setJob] = useState<KpiJobCode>("AO_KREDIT")
  const [office, setOffice] = useState("")
  const [aoCode, setAoCode] = useState("")
  const [closing, setClosing] = useState("")
  const [search, setSearch] = useState("")
  const [directory, setDirectory] = useState<KpiDirectory | null>(null)
  const [evaluation, setEvaluation] = useState<KpiEvaluation | null>(null)
  const [details, setDetails] = useState<KpiDetail[]>([])
  const [loading, setLoading] = useState(true)
  const [loadingDetail, setLoadingDetail] = useState(false)
  const [running, setRunning] = useState(false)
  const [error, setError] = useState("")
  const [infoOpen, setInfoOpen] = useState(false)
  const [bulkRows, setBulkRows] = useState<Array<{ nama: string; kantor: string; closing: string; status: string; nilai: number | null }>>([])
  const [singleConfirmOpen, setSingleConfirmOpen] = useState(false)
  const [bulkConfirm, setBulkConfirm] = useState<{ work: BulkWorkItem[]; pending: BulkWorkItem[]; existingCount: number } | null>(null)
  const abortRef = useRef<AbortController | null>(null)

  const refreshDirectory = useCallback(async (nextOffice = office) => {
    abortRef.current?.abort()
    const controller = new AbortController()
    abortRef.current = controller
    setLoading(true)
    setError("")
    try {
      const data = await loadKpiDirectory({ year: Number(year), jabatan_kode: job, kode_kantor: nextOffice, include_generated: true }, controller.signal)
      setDirectory(data)
      setAoCode((current) => data.ao.some((item) => item.kode_ao === current) ? current : "")
      setClosing((current) => current && data.closing_dates.includes(current) ? current : data.closing_dates.at(-1) || "")
    } catch (reason) {
      if (reason instanceof DOMException && reason.name === "AbortError") return
      setError(reason instanceof Error ? reason.message : "Direktori KPI gagal dimuat")
    } finally {
      if (abortRef.current === controller) setLoading(false)
    }
  }, [job, office, year])

  useEffect(() => {
    refreshDirectory()
    return () => abortRef.current?.abort()
  }, [refreshDirectory])

  const selectedAo = useMemo(() => directory?.ao.find((item) => item.kode_ao === aoCode), [aoCode, directory])
  const offices = useMemo(() => directory?.kantor ?? [], [directory?.kantor])
  const jobs = useMemo(() => directory?.jabatan ?? [], [directory?.jabatan])
  const closingDates = useMemo(() => directory?.closing_dates ?? [], [directory?.closing_dates])
  const availableAo = useMemo(() => {
    const rows = directory?.ao || []
    return office ? rows.filter((item) => String(item.kode_kantor) === String(office)) : rows
  }, [directory?.ao, office])

  const loadSaved = useCallback(async () => {
    if (!selectedAo || !closing) {
      setEvaluation(null)
      setDetails([])
      return
    }
    setLoadingDetail(true)
    try {
      const result = await loadKpiEvaluation({ year: Number(year), jabatan_kode: job, kode_ao: selectedAo.kode_ao, id_peg: selectedAo.id_peg, kode_kantor: selectedAo.kode_kantor, closing_date: closing })
      const row = result.data?.[0] || null
      setEvaluation(row)
      setDetails(row ? (await loadKpiDetail(row.id)).data || [] : [])
    } catch (reason) {
      setEvaluation(null)
      setDetails([])
      if (!(reason instanceof DOMException && reason.name === "AbortError")) setError(reason instanceof Error ? reason.message : "Detail KPI gagal dimuat")
    } finally {
      setLoadingDetail(false)
    }
  }, [closing, job, selectedAo, year])

  useEffect(() => {
    if (!bulk) loadSaved()
  }, [bulk, loadSaved])

  const filteredDetails = useMemo(() => {
    const query = search.trim().toLowerCase()
    return details.filter((row) => !query || `${row.nama} ${row.kelompok || ""} ${row.formula_key || ""} ${sourceNote(row)}`.toLowerCase().includes(query))
  }, [details, search])

  const handleOfficeChange = useCallback(async (value: string) => {
    setOffice(value)
    setAoCode("")
    setEvaluation(null)
    setDetails([])
    if (!value) {
      if (job === "AO_KREDIT") return
      await refreshDirectory(value)
      return
    }
    try {
      const result = await loadKpiAoList({ jabatan_kode: job, kode_kantor: value })
      setDirectory((current) => current ? { ...current, ao: result.ao } : current)
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Daftar AO gagal dimuat")
    }
  }, [job, refreshDirectory])

  useEffect(() => {
    setToolbar(<div className="flex min-w-max items-end gap-2"><KpiSelect label="Jabatan" value={job} onChange={(value) => { setJob(value as KpiJobCode); setOffice(""); setAoCode("") }}>{(jobs.length ? jobs : [{ kode: "AO_KREDIT", nama: "AO Kredit" }]).map((item) => <option key={item.kode} value={item.kode}>{item.nama}</option>)}</KpiSelect><KpiInput label="Tahun" type="number" value={year} onChange={setYear} className="w-20" /><KpiSelect label="Kantor" value={office} onChange={handleOfficeChange}><option value="">{bulk || job !== "AO_KREDIT" ? "Pilih kantor" : "Semua kantor"}</option>{offices.map((item) => <option key={item.kode_kantor} value={item.kode_kantor}>{item.kode_kantor} · {item.nama_kantor}</option>)}</KpiSelect><KpiSelect label="AO" value={aoCode} onChange={setAoCode} disabled={!availableAo.length}><option value="">{availableAo.length ? "Pilih AO" : "Belum tersedia"}</option>{availableAo.map((item) => <option key={`${item.kode_ao}-${item.id_peg || ""}`} value={item.kode_ao}>{item.nama_ao}</option>)}</KpiSelect>{!bulk && <KpiSelect label="Closing" value={closing} onChange={setClosing}><option value="">Pilih closing</option>{closingDates.map((date) => <option key={date} value={date}>{date}</option>)}</KpiSelect>} {!bulk && <KpiField label="Cari"><span className="relative"><Search size={14} className="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[var(--muted)]" /><input value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Cari indikator" className="h-9 w-44 rounded-md border border-[var(--line)] bg-[var(--surface)] pl-8 pr-2 text-sm outline-none focus:border-[var(--accent)]" /></span></KpiField>}</div>)
    return () => setToolbar(null)
  }, [aoCode, availableAo, bulk, closing, closingDates, handleOfficeChange, job, jobs, office, search, setToolbar, year, offices])

  const executeSingle = async () => {
    if (!selectedAo || !closing) return
    setRunning(true)
    setError("")
    try {
      await calculateKpi({ year: Number(year), jabatan_kode: job, kode_ao: selectedAo.kode_ao, id_peg: selectedAo.id_peg, kode_kantor: selectedAo.kode_kantor, closing_date: closing })
      await refreshDirectory()
      await loadSaved()
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "KPI gagal dihitung")
    } finally {
      setRunning(false)
    }
  }

  const runSingle = () => {
    if (!selectedAo || !closing) return setError("Pilih kantor, AO, dan closing terlebih dahulu.")
    const saved = directory?.generated.find((item) => String(item.id_peg || item.kode_ao) === String(selectedAo.id_peg || selectedAo.kode_ao) && item.closing_date === closing)
    if (saved) return setSingleConfirmOpen(true)
    void executeSingle()
  }

  const executeBulk = async (jobs: BulkWorkItem[]) => {
    setRunning(true)
    setError("")
    setBulkRows([])
    try {
      const results: Array<{ nama: string; kantor: string; closing: string; status: string; nilai: number | null }> = []
      for (const item of jobs) {
        try {
          const result = await calculateKpi({ year: Number(year), jabatan_kode: job, kode_ao: item.person.kode_ao, id_peg: item.person.id_peg, kode_kantor: item.person.kode_kantor, closing_date: item.date })
          const saved = result.data?.[0]
          results.push({ nama: item.person.nama_ao, kantor: item.person.kode_kantor, closing: item.date, status: saved?.status || "Selesai", nilai: saved?.nilai_akhir ?? null })
        } catch (reason) {
          results.push({ nama: item.person.nama_ao, kantor: item.person.kode_kantor, closing: item.date, status: reason instanceof Error ? reason.message : "Gagal", nilai: null })
        }
        setBulkRows([...results])
      }
      await refreshDirectory()
    } finally {
      setRunning(false)
    }
  }

  const runBulk = () => {
    if (!office) return setError("Pilih kantor terlebih dahulu untuk generate per cabang.")
    const people = availableAo
    const dates = closingDates
    if (!people.length || !dates.length) return setError("AO atau closing belum tersedia untuk pilihan ini.")
    const work = people.flatMap((person) => dates.map((date) => ({ person, date })))
    const existing = new Set((directory?.generated || []).map((item) => `${item.id_peg || item.kode_ao}|${item.closing_date}`))
    const pending = work.filter((item) => !existing.has(`${item.person.id_peg || item.person.kode_ao}|${item.date}`))
    const existingCount = work.length - pending.length
    if (!pending.length && existingCount) return setBulkConfirm({ work, pending, existingCount })
    if (existingCount) return setBulkConfirm({ work, pending, existingCount })
    void executeBulk(pending)
  }

  const title = bulk ? "Generate KPI AO" : "Nilai KPI AO"
  return <KpiPage title={title} description={bulk ? "Generate penilaian KPI per AO dan seluruh closing yang tersedia." : "Hitung dan lihat detail penilaian satu AO untuk satu periode closing."} actions={<><Button variant="ghost" size="icon" aria-label="Tentang halaman" onClick={() => setInfoOpen(true)}><Info size={16} /></Button><Button variant="outline" size="sm" onClick={() => refreshDirectory()} disabled={loading}><RefreshCw size={14} className={loading ? "animate-spin" : undefined} /> Muat ulang</Button></>}> 
    {error && <p className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300">{error}</p>}

    {bulk ? <BulkPanel rows={bulkRows} running={running} onRun={runBulk} office={office} /> : <>
      <section className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4"><KpiMetric label="Jabatan" value={directory?.jabatan_terpilih?.nama || "-"} detail={directory?.jabatan_terpilih?.deskripsi || "Indikator aktif sesuai jabatan"} /><KpiMetric label="AO terpilih" value={selectedAo?.nama_ao || "-"} detail={selectedAo ? `${selectedAo.kode_ao} · Kantor ${selectedAo.kode_kantor}` : "Pilih kantor dan AO"} /><KpiMetric label="Nilai akhir" value={evaluation?.nilai_akhir == null ? "-" : formatNumber(evaluation.nilai_akhir)} tone="success" detail={evaluation?.predikat || "Kontribusi indikator aktif"} /><KpiMetric label="Status" value={evaluation?.status || "BELUM DIHITUNG"} tone={evaluation?.status === "DISETUJUI" ? "success" : "warning"} detail={evaluation?.closing_date || "Pilih periode closing"} /></section>
      <KpiPanel title="Detail penilaian bulanan" actions={<Button onClick={runSingle} disabled={running || loadingDetail || !selectedAo || !closing}><Play size={14} /> {running ? "Menghitung..." : "Hitung KPI"}</Button>}>
        <KpiState loading={loadingDetail} error={error && !loading ? error : ""} empty={!details.length ? "Pilih jabatan, kantor, AO, dan closing. Detail yang sudah tersimpan akan dimuat otomatis." : undefined}>{<DetailTable rows={filteredDetails} rules={directory?.tukin_rules || []} />}</KpiState>
      </KpiPanel>
    </>}
    <KpiModal open={infoOpen} title={title} description="Perhitungan KPI tetap berada di layanan backend." onClose={() => setInfoOpen(false)}><div className="space-y-3 text-sm leading-6 text-[var(--muted)]"><p>{bulk ? "Mode ini menjalankan calculate untuk setiap AO dan closing pada kantor yang dipilih." : "Mode ini memuat evaluation dan detail setelah AO serta closing dipilih."}</p><ul className="list-disc space-y-1 pl-5"><li>React hanya mengatur filter, request, dan tampilan.</li><li>Nilai, skor, bobot, risk gate, dan faktor Tukin berasal dari API.</li><li>AO Dana dan AO Remedial mengikuti status sumber data yang dikembalikan backend.</li></ul></div></KpiModal>
    <KpiModal open={singleConfirmOpen} title="Generate ulang penilaian?" description={`Nilai untuk closing ${closing} sudah tersedia.`} onClose={() => setSingleConfirmOpen(false)}><div className="space-y-5"><p className="text-sm leading-6 text-[var(--muted)]">Generate ulang akan menghitung kembali nilai AO terpilih untuk memperbarui hasil yang tersimpan.</p><div className="flex justify-end gap-2"><Button variant="outline" onClick={() => setSingleConfirmOpen(false)}>Batal</Button><Button onClick={() => { setSingleConfirmOpen(false); void executeSingle() }}>Generate ulang</Button></div></div></KpiModal>
    <KpiModal open={Boolean(bulkConfirm)} title="Periode sudah tersedia" description={bulkConfirm ? `${bulkConfirm.existingCount} dari ${bulkConfirm.work.length} periode sudah pernah digenerate.` : undefined} onClose={() => setBulkConfirm(null)}><div className="space-y-5"><p className="text-sm leading-6 text-[var(--muted)]">Pilih apakah hanya periode yang belum ada yang diproses, atau seluruh periode dihitung ulang.</p><div className="flex flex-wrap justify-end gap-2"><Button variant="outline" onClick={() => setBulkConfirm(null)}>Batal</Button><Button variant="subtle" disabled={!bulkConfirm?.pending.length} onClick={() => { const next = bulkConfirm?.pending || []; setBulkConfirm(null); void executeBulk(next) }}>Hanya yang belum ada</Button><Button onClick={() => { const next = bulkConfirm?.work || []; setBulkConfirm(null); void executeBulk(next) }}>Generate ulang semua</Button></div></div></KpiModal>
  </KpiPage>
}

function DetailTable({ rows, rules }: { rows: KpiDetail[]; rules: KpiDirectory["tukin_rules"] }) {
  const total100 = rows.reduce((sum, row) => sum + Number(row.nilai_100 || 0), 0)
  const totalWeight = rows.reduce((sum, row) => sum + Number(row.bobot || 0), 0)
  const score = total100 / 20
  const tukin = rules.find((rule) => score >= Number(rule.min_skor) && (rule.max_skor == null || score < Number(rule.max_skor)))
  return <div className="max-h-[calc(100dvh-350px)] overflow-auto"><table className="w-full min-w-[1120px] border-separate border-spacing-0 text-sm tabular-nums"><thead className="sticky top-0 z-10 bg-[var(--surface)] text-left text-xs text-[var(--muted)]"><tr>{["Indikator", "Target", "Realisasi", "Indeks", "Skor", "Skor 2", "Bobot", "Nilai", "Nilai 2", "Tukin", "Keterangan"].map((label) => <th key={label} className="border-b border-[var(--line)] px-3 py-3 font-medium">{label}</th>)}</tr></thead><tbody><tr className="bg-[var(--surface-subtle)] font-semibold"><td className="sticky left-0 z-[1] bg-[var(--surface-subtle)] px-3 py-3">TOTAL</td><td /><td /><td /><td className="px-3 py-3">{formatNumber(rows.reduce((sum, row) => sum + Number(row.skor || 0), 0))}</td><td className="px-3 py-3">{formatNumber(score)}</td><td className="px-3 py-3">{formatWeight(totalWeight)}</td><td className="px-3 py-3">{formatNumber(rows.reduce((sum, row) => sum + Number(row.nilai_tertimbang || 0), 0))}</td><td className="px-3 py-3">{formatNumber(total100)}</td><td className="px-3 py-3 text-emerald-600 dark:text-emerald-400">{tukin ? `${formatNumber(tukin.faktor_persen)}%` : "-"}</td><td className="px-3 py-3 text-xs font-normal text-[var(--muted)]">Nilai akhir berbasis indikator aktif</td></tr>{rows.map((row) => <tr key={`${row.indikator_id}-${row.nama}`} className="group hover:bg-[var(--surface-subtle)]"><td className="sticky left-0 z-[1] min-w-56 border-b border-[var(--line-soft)] bg-[var(--surface)] px-3 py-3 group-hover:bg-[var(--surface-subtle)]"><div className="font-medium">{row.nama}</div><div className="mt-1 text-xs text-[var(--muted)]">{row.kelompok || "-"}</div></td><td className="border-b border-[var(--line-soft)] px-3 py-3">{detailValue(row.target, row.unit)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 font-medium">{detailValue(row.realisasi, row.unit)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{detailValue(row.indeks, row.unit === "PERSEN" ? "PERSEN" : "")}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatNumber(row.skor)} / 5</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatNumber(Number(row.skor || 0) * 20)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatWeight(row.bobot)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">{formatNumber(row.nilai_tertimbang)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3 font-medium">{formatNumber(row.nilai_100)}</td><td className="border-b border-[var(--line-soft)] px-3 py-3">-</td><td className="max-w-72 border-b border-[var(--line-soft)] px-3 py-3 text-xs leading-5 text-[var(--muted)]">{sourceNote(row)}</td></tr>)}</tbody></table></div>
}

function BulkPanel({ rows, running, onRun, office }: { rows: Array<{ nama: string; kantor: string; closing: string; status: string; nilai: number | null }>; running: boolean; onRun: () => void; office: string }) {
  return <KpiPanel title="Generate per cabang" description="Proses berjalan berurutan agar beban API tetap terkontrol." actions={<Button onClick={onRun} disabled={running || !office}><Play size={14} /> {running ? "Memproses..." : "Generate semua"}</Button>}><div className="max-h-[calc(100dvh-320px)] overflow-auto"><table className="w-full min-w-[620px] border-separate border-spacing-0 text-sm"><thead className="sticky top-0 z-10 bg-[var(--surface)] text-left text-xs text-[var(--muted)]"><tr>{["AO", "Kantor", "Closing", "Status", "Nilai"].map((label) => <th key={label} className="border-b border-[var(--line)] px-4 py-3 font-medium">{label}</th>)}</tr></thead><tbody>{rows.length ? rows.map((row, index) => <tr key={`${row.kantor}-${row.closing}-${index}`}><td className="border-b border-[var(--line-soft)] px-4 py-3 font-medium">{row.nama}</td><td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{row.kantor}</td><td className="border-b border-[var(--line-soft)] px-4 py-3">{row.closing}</td><td className="border-b border-[var(--line-soft)] px-4 py-3"><KpiStatus tone={row.status === "DISETUJUI" || row.status.startsWith("Selesai") ? "success" : row.status === "Gagal" ? "danger" : "neutral"}>{row.status}</KpiStatus></td><td className="border-b border-[var(--line-soft)] px-4 py-3 tabular-nums">{row.nilai == null ? "-" : formatNumber(row.nilai)}</td></tr>) : <tr><td colSpan={5} className="px-4 py-14 text-center text-sm text-[var(--muted)]">Pilih kantor lalu jalankan generate. Hasil proses akan tampil di sini.</td></tr>}</tbody></table></div></KpiPanel>
}
