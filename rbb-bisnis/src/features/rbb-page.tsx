import { useEffect, useMemo, useState } from "react"
import { Download, Plus, RefreshCw, Save, Search, Send, Trash2 } from "lucide-react"

import { RbbShell } from "@/components/app/rbb-shell"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Card } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Select } from "@/components/ui/select"
import { postApi } from "@/lib/api-base"
import { buildPeriods, detailCategories, loadOffices, loadRbbYear, monthLabels, periodTitle, previousActualLabel, projectionCategories, rowValues } from "@/lib/rbb-data"
import type { AbaHistory, AbaRow, Office, RbbPlan, RbbResponse, RbbRow, RbbTab } from "@/lib/rbb-types"
import { formatNumber, numberValue } from "@/lib/utils"

type Period = { year: number; month: number; key: string }
type AbaBundle = AbaRow & { yearRows: Record<number, AbaRow> }
type WorkflowAction = "REOPEN" | "SUBMIT_KANWIL" | "APPROVE_KANWIL" | "APPROVE_PUSAT" | "REJECT"

const now = new Date()
const defaultPeriod = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}`

function readTab(): RbbTab {
  const value = new URLSearchParams(window.location.search).get("tab")
  return value === "detail" || value === "aba" ? value : "projection"
}

function emptyAbaRow(): AbaRow {
  return { id: 0, no_urut: 0, jenis_penempatan: "Tabungan", nama_bank: "", no_rekening_aba: "", no_rekening_cbs: "", values: rowValues({}), interest_values: rowValues({}) }
}

function mergeRows(responses: RbbResponse[], periods: Period[]) {
  const byYear = new Map(responses.map((response) => [Number(response.tahun ?? response.year), response.rows || []]))
  const codes: string[] = []
  const seen = new Set<string>()
  responses.forEach((response) => response.rows?.forEach((row) => {
    const code = String(row.kode_monbis || "")
    if (code && !seen.has(code)) { seen.add(code); codes.push(code) }
  }))

  return codes.map((code) => {
    const source = responses.flatMap((response) => response.rows || []).find((row) => String(row.kode_monbis) === code)
    const yearValues: Record<number, Record<string, number>> = {}
    byYear.forEach((rows, year) => {
      const row = rows.find((item) => String(item.kode_monbis) === code)
      if (row) yearValues[year] = rowValues(row.values)
    })
    const periodValues = Object.fromEntries(periods.map((period) => [period.key, numberValue(yearValues[period.year]?.[period.month])]))
    return { ...source, kode_monbis: code, values: rowValues(source?.values), yearValues, periodValues, actualPrevious: source?.actual_previous } as RbbRow
  })
}

function mergeAbaRows(responses: RbbResponse[], years: number[]) {
  const count = Math.max(0, ...responses.map((response) => response.rows?.length || 0))
  return Array.from({ length: count }, (_, index) => {
    const yearRows: Record<number, AbaRow> = {}
    years.forEach((year) => {
      const response = responses.find((item) => Number(item.tahun) === year)
      const source = response?.rows?.[index]
      yearRows[year] = source ? { ...emptyAbaRow(), ...source, values: rowValues(source.values), interest_values: rowValues(source.interest_values) } : emptyAbaRow()
    })
    return { ...yearRows[years[0]], yearRows }
  })
}

function getPlanStatus(plan?: RbbPlan | null) {
  return plan?.status || "DRAFT BARU"
}

function actionLabel(action: WorkflowAction) {
  return { REOPEN: "Buka kembali", SUBMIT_KANWIL: "Ajukan ke Kanwil", APPROVE_KANWIL: "Approve Kanwil", APPROVE_PUSAT: "Approve Pusat", REJECT: "Tolak" }[action]
}

export function RbbPage() {
  const [tab, setTab] = useState<RbbTab>(readTab)
  const [period, setPeriod] = useState(defaultPeriod)
  const [branch, setBranch] = useState("")
  const [category, setCategory] = useState("NERACA")
  const [search, setSearch] = useState("")
  const [offices, setOffices] = useState<Office[]>([])
  const [responses, setResponses] = useState<RbbResponse[]>([])
  const [rows, setRows] = useState<RbbRow[]>([])
  const [abaRows, setAbaRows] = useState<AbaBundle[]>([])
  const [abaView, setAbaView] = useState<"placement" | "ckpn" | "interest" | "history">("placement")
  const [history, setHistory] = useState<AbaHistory | null>(null)
  const [interestRate, setInterestRate] = useState(0.0125)
  const [plans, setPlans] = useState<Record<number, RbbPlan | null>>({})
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState("")
  const [notice, setNotice] = useState("")
  const [reloadKey, setReloadKey] = useState(0)

  const startYear = Number(period.slice(0, 4)) || now.getFullYear()
  const startMonth = Number(period.slice(5, 7)) || now.getMonth() + 1
  const periods = useMemo(() => buildPeriods(startYear, startMonth), [startMonth, startYear])
  const years = useMemo(() => [...new Set(periods.map((item) => item.year))], [periods])
  const categories = tab === "projection" ? projectionCategories : detailCategories
  const actualPreviousDate = responses.find((response) => response.actual_previous_date)?.actual_previous_date
  const statusSummary = years.map((year) => `${year}: ${getPlanStatus(plans[year])}`).join(" · ")
  const visibleRows = rows.filter((row) => {
    const query = search.trim().toLowerCase()
    if (!query) return true
    return [row.kode_monbis, row.keterangan, row.kategori, row.kode_perkiraan, row.sandi_lbbpr].some((value) => String(value ?? "").toLowerCase().includes(query))
  })
  const canEdit = responses.length > 0 && responses.every((response) => response.permissions?.can_edit !== false && [null, undefined, "DRAFT", "REJECTED"].includes(response.plan?.status))

  useEffect(() => {
    const controller = new AbortController()
    loadOffices(controller.signal).then((items) => {
      setOffices(items)
      setBranch((current) => current && items.some((item) => String(item.kode_kantor) === current) ? current : String(items[0]?.kode_kantor || ""))
    }).catch((reason: unknown) => {
      if ((reason as Error)?.name !== "AbortError") setError((reason as Error)?.message || "Kantor gagal dimuat")
    })
    return () => controller.abort()
  }, [])

  useEffect(() => {
    const url = new URL(window.location.href)
    url.searchParams.set("tab", tab)
    window.history.replaceState({}, "", url)
    setSearch("")
    setAbaView("placement")
    setCategory(tab === "projection" ? "NERACA" : "ASET")
  }, [tab])

  useEffect(() => {
    if (!branch) return undefined
    const controller = new AbortController()
    setLoading(true)
    setError("")
    Promise.all(years.map((year) => loadRbbYear(tab, year, startYear, startMonth, branch, category, controller.signal)))
      .then((nextResponses) => {
        setResponses(nextResponses)
        setPlans(Object.fromEntries(nextResponses.map((response) => [Number(response.tahun), response.plan || null])))
        if (tab === "aba") {
          setAbaRows(mergeAbaRows(nextResponses, years))
          setHistory(nextResponses[0]?.history || null)
          setInterestRate(numberValue(nextResponses[0]?.interest_rate) || 0.0125)
        } else {
          setRows(mergeRows(nextResponses, periods))
        }
      })
      .catch((reason: unknown) => {
        if ((reason as Error)?.name !== "AbortError") setError((reason as Error)?.message || "Data RBB gagal dimuat")
      })
      .finally(() => setLoading(false))
    return () => controller.abort()
  }, [branch, category, periods, reloadKey, startMonth, startYear, tab, years])

  function changeTab(nextTab: RbbTab) {
    if (nextTab !== tab) setTab(nextTab)
  }

  function notify(message: string) {
    setNotice(message)
    window.setTimeout(() => setNotice((current) => current === message ? "" : current), 3200)
  }

  function updateDetailValue(code: string, year: number, month: number, value: string) {
    const amount = numberValue(value)
    setRows((current) => current.map((row) => {
      if (row.kode_monbis !== code) return row
      const nextYearValues = { ...(row.yearValues?.[year] || rowValues(row.values)), [String(month)]: amount }
      return { ...row, periodValues: { ...row.periodValues, [`${year}-${String(month).padStart(2, "0")}`]: amount }, yearValues: { ...row.yearValues, [year]: nextYearValues } }
    }))
  }

  function updateAbaMeta(index: number, field: keyof Pick<AbaRow, "jenis_penempatan" | "nama_bank" | "no_rekening_aba" | "no_rekening_cbs">, value: string) {
    setAbaRows((current) => current.map((row, rowIndex) => rowIndex === index ? { ...row, [field]: value, yearRows: Object.fromEntries(years.map((year) => [year, { ...row.yearRows[year], [field]: value }])) } : row))
  }

  function updateAbaValue(index: number, year: number, month: number, value: string) {
    const amount = numberValue(value)
    setAbaRows((current) => current.map((row, rowIndex) => {
      if (rowIndex !== index) return row
      const source = row.yearRows[year] || emptyAbaRow()
      return { ...row, yearRows: { ...row.yearRows, [year]: { ...source, values: { ...source.values, [String(month)]: amount } } } }
    }))
  }

  async function saveDetail() {
    setSaving(true)
    try {
      for (const response of responses) {
        const year = Number(response.tahun)
        const sourceRows = response.rows || []
        const payloadRows = sourceRows.map((source) => {
          const edited = rows.find((row) => row.kode_monbis === source.kode_monbis)
          const values = rowValues(edited?.yearValues?.[year] || source.values)
          periods.filter((item) => item.year === year).forEach((item) => { values[String(item.month)] = numberValue(edited?.periodValues?.[item.key]) })
          return { kode_monbis: source.kode_monbis, values }
        })
        await postApi("rbb_v2/", { type: "rbb_planning_save", tahun: year, kode_kantor: branch, kategori: category, rows: payloadRows })
      }
      notify("Draft detail RBB berhasil disimpan.")
      setReloadKey((value) => value + 1)
    } catch (reason: unknown) {
      notify((reason as Error)?.message || "Draft detail gagal disimpan")
    } finally {
      setSaving(false)
    }
  }

  async function saveAba() {
    setSaving(true)
    try {
      for (const year of years) {
        const payloadRows = abaRows.map((row, index) => {
          const source = row.yearRows[year] || emptyAbaRow()
          return { id: source.id, no_urut: index + 1, jenis_penempatan: source.jenis_penempatan || "Tabungan", nama_bank: source.nama_bank, no_rekening_aba: source.no_rekening_aba, no_rekening_cbs: source.no_rekening_cbs, values: rowValues(source.values) }
        })
        await postApi("rbb_v2/", { type: "rbb_aba_save", tahun: year, kode_kantor: branch, rows: payloadRows })
      }
      notify("Input ABA berhasil disimpan.")
      setReloadKey((value) => value + 1)
    } catch (reason: unknown) {
      notify((reason as Error)?.message || "Input ABA gagal disimpan")
    } finally {
      setSaving(false)
    }
  }

  async function transition(action: WorkflowAction) {
    if (years.some((year) => !plans[year]?.status)) { notify("Simpan draft untuk semua tahun terlebih dahulu."); return }
    if (!window.confirm(`${actionLabel(action)} untuk seluruh periode RBB?`)) return
    setSaving(true)
    try {
      const actionYears = action === "REOPEN" ? years.filter((year) => !["DRAFT", "REJECTED"].includes(plans[year]?.status || "")) : years
      for (const year of actionYears) {
        if (action === "REOPEN") await postApi("rbb_v2/", { type: "rbb_planning_reopen", tahun: year, kode_kantor: branch })
        else if (action === "SUBMIT_KANWIL") await postApi("rbb_v2/", { type: "rbb_planning_submit", tahun: year, kode_kantor: branch })
        else await postApi("rbb_v2/", { type: "rbb_planning_approve", action, tahun: year, kode_kantor: branch })
      }
      notify(`Status RBB berhasil diperbarui: ${actionLabel(action)}.`)
      setReloadKey((value) => value + 1)
    } catch (reason: unknown) {
      notify((reason as Error)?.message || "Status RBB gagal diperbarui")
    } finally {
      setSaving(false)
    }
  }

  function exportCsv() {
    const header = ["Kode", "COA / Indikator", "Kategori", previousActualLabel(actualPreviousDate), ...periods.map(periodTitle)]
    const data = visibleRows.map((row) => [row.kode_monbis, row.keterangan || "", row.kategori || "", row.actualPrevious == null ? "" : String(row.actualPrevious), ...periods.map((period) => String(row.periodValues?.[period.key] ?? 0))])
    const csv = [header, ...data].map((line) => line.map((value) => `"${String(value).replaceAll('"', '""')}"`).join(",")).join("\r\n")
    const link = document.createElement("a")
    link.href = URL.createObjectURL(new Blob(["\ufeff" + csv], { type: "text/csv;charset=utf-8" }))
    link.download = `rbb-${branch || "kantor"}-${period}.csv`
    link.click()
    URL.revokeObjectURL(link.href)
  }

  const statuses = years.map((year) => plans[year]?.status || "")
  const actions: WorkflowAction[] = statuses.length === years.length && statuses.every(Boolean)
    ? statuses.every((status) => ["DRAFT", "REJECTED"].includes(status)) ? ["SUBMIT_KANWIL"]
      : statuses.every((status) => status === "SUBMITTED_KANWIL") ? ["APPROVE_KANWIL", "REJECT"]
        : statuses.every((status) => status === "APPROVED_KANWIL") ? ["APPROVE_PUSAT", "REJECT"]
          : statuses.some((status) => !["DRAFT", "REJECTED"].includes(status)) ? ["REOPEN"] : []
    : []

  return (
    <RbbShell activeTab={tab} onTabChange={changeTab}>
      <div className="mx-auto w-full max-w-[1800px] space-y-4 p-4 sm:p-6 lg:p-8">
        <div className="flex flex-wrap items-start justify-between gap-3">
          <div>
            <p className="text-xs font-medium uppercase tracking-[0.14em] text-[var(--muted)]">RBB Bisnis</p>
            <h2 className="mt-1 text-xl font-semibold tracking-tight">{tab === "projection" ? "Proyeksi RBB" : tab === "detail" ? "Input Detail RBB" : "Input RBB ABA"}</h2>
            <p className="mt-1 text-sm text-[var(--muted)]">Periode input dimulai {periodTitle(periods[0])} sampai {periodTitle(periods[periods.length - 1])}.</p>
          </div>
          <div className="flex flex-wrap items-center gap-2">
            {tab === "projection" && <Button variant="outline" size="sm" onClick={exportCsv} disabled={!rows.length}><Download size={15} /> Export CSV</Button>}
            <Button variant="outline" size="sm" onClick={() => setReloadKey((value) => value + 1)} disabled={loading}><RefreshCw size={15} className={loading ? "animate-spin" : undefined} /> Muat ulang</Button>
          </div>
        </div>

        <Card className="p-4">
          <div className="grid gap-3 md:grid-cols-[minmax(170px,220px)_minmax(170px,1fr)_minmax(170px,220px)_minmax(200px,1fr)] md:items-end">
            <label className="grid gap-1.5 text-xs font-medium text-[var(--muted)]"><span>Periode mulai</span><Input type="month" value={period} onChange={(event) => setPeriod(event.target.value)} /></label>
            <label className="grid gap-1.5 text-xs font-medium text-[var(--muted)]"><span>Kantor / cabang</span><Select value={branch} onChange={(event) => setBranch(event.target.value)}><option value="">Pilih kantor</option>{offices.map((office) => <option key={String(office.kode_kantor)} value={String(office.kode_kantor)}>{String(office.kode_kantor)} · {office.nama_kantor || "Cabang"}</option>)}</Select></label>
            {tab !== "aba" && <label className="grid gap-1.5 text-xs font-medium text-[var(--muted)]"><span>Kategori</span><Select value={category} onChange={(event) => setCategory(event.target.value)}>{categories.map(([value, label]) => <option key={value} value={value}>{label}</option>)}</Select></label>}
            {tab !== "aba" && <label className="grid gap-1.5 text-xs font-medium text-[var(--muted)]"><span>Cari COA</span><span className="relative"><Search size={15} className="absolute left-3 top-2.5 text-[var(--muted)]" /><Input className="pl-9" value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Kode atau keterangan..." /></span></label>}
          </div>
          <div className="mt-3 flex flex-wrap items-center gap-2 text-xs text-[var(--muted)]"><Badge>{statusSummary || "Memuat status"}</Badge>{actualPreviousDate && <span>Realisasi sebelumnya: {previousActualLabel(actualPreviousDate)}</span>}{loading && <span className="text-[var(--accent)]">Memuat data...</span>}</div>
        </Card>

        {error && <Card className="border-[color-mix(in_srgb,var(--danger)_35%,var(--line))] bg-[color-mix(in_srgb,var(--danger)_5%,var(--surface))] p-4 text-sm text-[var(--danger)]">{error}</Card>}
        {notice && <div className="fixed bottom-5 right-5 z-50 rounded-lg border border-[var(--line)] bg-[var(--ink)] px-4 py-3 text-sm text-[var(--surface)] shadow-xl" role="status">{notice}</div>}

        {tab === "projection" && <ProjectionTable rows={visibleRows} periods={periods} actualPreviousDate={actualPreviousDate} actions={actions} saving={saving} onAction={transition} />}
        {tab === "detail" && <DetailTable rows={visibleRows} periods={periods} actualPreviousDate={actualPreviousDate} canEdit={canEdit} saving={saving} onChange={updateDetailValue} onSave={saveDetail} />}
        {tab === "aba" && <AbaTable rows={abaRows} periods={periods} view={abaView} history={history} interestRate={interestRate} canEdit={canEdit} saving={saving} onViewChange={setAbaView} onAdd={() => setAbaRows((current) => [...current, { ...emptyAbaRow(), yearRows: Object.fromEntries(years.map((year) => [year, emptyAbaRow()])) }])} onDelete={(index) => setAbaRows((current) => current.filter((_, rowIndex) => rowIndex !== index))} onMetaChange={updateAbaMeta} onValueChange={updateAbaValue} onSave={saveAba} />}
      </div>
    </RbbShell>
  )
}

function TableFrame({ children, className }: { children: React.ReactNode; className?: string }) {
  return <Card className={`min-w-0 overflow-hidden ${className || ""}`}><div className="overflow-x-auto overscroll-x-contain">{children}</div></Card>
}

function ProjectionTable({ rows, periods, actualPreviousDate, actions, saving, onAction }: { rows: RbbRow[]; periods: Period[]; actualPreviousDate?: string | null; actions: WorkflowAction[]; saving: boolean; onAction: (action: WorkflowAction) => void }) {
  return <TableFrame><div className="flex flex-wrap items-center justify-between gap-2 border-b border-[var(--line-soft)] px-4 py-3"><div><p className="text-sm font-semibold">Ringkasan proyeksi</p><p className="text-xs text-[var(--muted)]">Data otomatis dari draft RBB dan ABA.</p></div><div className="flex flex-wrap gap-2">{actions.map((action) => <Button key={action} variant={action === "REJECT" ? "danger" : "outline"} size="sm" onClick={() => onAction(action)} disabled={saving}>{action === "SUBMIT_KANWIL" && <Send size={14} />}{actionLabel(action)}</Button>)}</div></div><table className="rbb-table"><thead><tr><th>Kode</th><th>COA / indikator</th><th>Kategori</th><th>{previousActualLabel(actualPreviousDate)}</th>{periods.map((period) => <th key={period.key}>{periodTitle(period)}</th>)}</tr></thead><tbody>{rows.length ? rows.map((row) => <tr key={row.kode_monbis}><td className="font-medium">{row.kode_monbis}</td><td><strong>{row.keterangan || "-"}</strong><small>{row.kode_perkiraan || "-"}</small></td><td>{row.kategori || "-"}</td><td className="num">{row.actualPrevious == null ? "-" : formatNumber(row.actualPrevious)}</td>{periods.map((period) => <td key={period.key} className="num">{formatNumber(row.periodValues?.[period.key])}</td>)}</tr>) : <EmptyRow colSpan={4 + periods.length} />}</tbody></table></TableFrame>
}

function DetailTable({ rows, periods, actualPreviousDate, canEdit, saving, onChange, onSave }: { rows: RbbRow[]; periods: Period[]; actualPreviousDate?: string | null; canEdit: boolean; saving: boolean; onChange: (code: string, year: number, month: number, value: string) => void; onSave: () => void }) {
  const totals = Object.fromEntries(periods.map((period) => [period.key, rows.reduce((sum, row) => sum + numberValue(row.periodValues?.[period.key]), 0)]))
  return <TableFrame><div className="flex flex-wrap items-center justify-between gap-2 border-b border-[var(--line-soft)] px-4 py-3"><div><p className="text-sm font-semibold">Input detail RBB</p><p className="text-xs text-[var(--muted)]">Baris AUTO terkunci; hanya baris MANUAL yang dapat diubah.</p></div><Button size="sm" onClick={onSave} disabled={!canEdit || saving}><Save size={14} /> {saving ? "Menyimpan..." : "Simpan draft"}</Button></div><table className="rbb-table"><thead><tr><th>Kode</th><th>COA / indikator</th><th>Input</th><th>{previousActualLabel(actualPreviousDate)}</th>{periods.map((period) => <th key={period.key}>{periodTitle(period)}</th>)}</tr></thead><tbody>{rows.length ? <><tr className="total-row"><td>-</td><td><strong>GRAND TOTAL</strong></td><td>-</td><td className="num">{formatNumber(rows.reduce((sum, row) => sum + numberValue(row.actualPrevious), 0))}</td>{periods.map((period) => <td key={period.key} className="num"><strong>{formatNumber(totals[period.key])}</strong></td>)}</tr>{rows.map((row) => { const auto = row.input_mode === "AUTO"; return <tr key={row.kode_monbis}><td className="font-medium">{row.kode_monbis}</td><td><strong>{row.keterangan || "-"}</strong><small>{row.kode_perkiraan || "-"}</small></td><td><Badge className={auto ? "" : "border-emerald-200 bg-emerald-50 text-emerald-700"}>{auto ? "AUTO" : "MANUAL"}</Badge></td><td className="num">{row.actualPrevious == null ? "-" : formatNumber(row.actualPrevious)}</td>{periods.map((period) => <td key={period.key}><input className="rbb-number-input" type="number" min="0" step="0.01" value={numberValue(row.periodValues?.[period.key])} disabled={auto || !canEdit} onChange={(event) => onChange(row.kode_monbis, period.year, period.month, event.target.value)} /></td>)}</tr>})}</> : <EmptyRow colSpan={4 + periods.length} />}</tbody></table></TableFrame>
}

function AbaTable({ rows, periods, view, history, interestRate, canEdit, saving, onViewChange, onAdd, onDelete, onMetaChange, onValueChange, onSave }: { rows: AbaBundle[]; periods: Period[]; view: "placement" | "ckpn" | "interest" | "history"; history: AbaHistory | null; interestRate: number; canEdit: boolean; saving: boolean; onViewChange: (view: "placement" | "ckpn" | "interest" | "history") => void; onAdd: () => void; onDelete: (index: number) => void; onMetaChange: (index: number, field: keyof Pick<AbaRow, "jenis_penempatan" | "nama_bank" | "no_rekening_aba" | "no_rekening_cbs">, value: string) => void; onValueChange: (index: number, year: number, month: number, value: string) => void; onSave: () => void }) {
  const readonly = view !== "placement"
  return <TableFrame><div className="flex flex-wrap items-center justify-between gap-2 border-b border-[var(--line-soft)] px-4 py-3"><div><p className="text-sm font-semibold">Input ABA</p><p className="text-xs text-[var(--muted)]">Bunga otomatis {(interestRate * 100).toFixed(2)}% per tahun · CKPN 0,50%.</p></div><div className="flex flex-wrap gap-2"><Button variant={view === "placement" ? "default" : "outline"} size="sm" onClick={() => onViewChange("placement")}>COA ABA</Button><Button variant={view === "ckpn" ? "default" : "outline"} size="sm" onClick={() => onViewChange("ckpn")}>CKPN ABA</Button><Button variant={view === "interest" ? "default" : "outline"} size="sm" onClick={() => onViewChange("interest")}>Pendapatan bunga</Button><Button variant={view === "history" ? "default" : "outline"} size="sm" onClick={() => onViewChange("history")}>History 3 tahun</Button></div></div>{view === "history" ? <HistoryTable history={history} /> : <><div className="flex flex-wrap justify-end gap-2 border-b border-[var(--line-soft)] px-4 py-2">{!readonly && <Button variant="outline" size="sm" onClick={onAdd} disabled={!canEdit}><Plus size={14} /> Tambah baris</Button>}{view === "placement" && <Button size="sm" onClick={onSave} disabled={!canEdit || saving}><Save size={14} /> {saving ? "Menyimpan..." : "Simpan ABA"}</Button>}</div><table className="rbb-table"><thead><tr><th>Aksi</th><th>No</th><th>Jenis</th><th>Nama bank</th><th>No. ABA</th><th>No. CBS</th>{periods.map((period) => <th key={period.key}>{periodTitle(period)}</th>)}</tr></thead><tbody>{rows.length ? rows.map((row, index) => <tr key={`${row.id}-${index}`}><td>{readonly ? "-" : <Button variant="ghost" size="icon" aria-label={`Hapus baris ${index + 1}`} onClick={() => onDelete(index)} disabled={!canEdit}><Trash2 size={14} /></Button>}</td><td>{index + 1}</td><td><select className="rbb-inline-control" value={row.jenis_penempatan} disabled={readonly || !canEdit} onChange={(event) => onMetaChange(index, "jenis_penempatan", event.target.value)}><option>Tabungan</option><option>Deposito</option><option>Giro</option></select></td><td><Input className="min-w-36" value={row.nama_bank} disabled={readonly || !canEdit} onChange={(event) => onMetaChange(index, "nama_bank", event.target.value)} /></td><td><Input className="min-w-32" value={row.no_rekening_aba} disabled={readonly || !canEdit} onChange={(event) => onMetaChange(index, "no_rekening_aba", event.target.value)} /></td><td><Input className="min-w-32" value={row.no_rekening_cbs} disabled={readonly || !canEdit} onChange={(event) => onMetaChange(index, "no_rekening_cbs", event.target.value)} /></td>{periods.map((period) => { const source = row.yearRows[period.year] || emptyAbaRow(); const nominal = numberValue(source.values[period.month]); const value = view === "ckpn" ? nominal * 0.005 : view === "interest" ? numberValue(source.interest_values[period.month]) : nominal; return <td key={period.key}>{readonly ? <span className="num">{formatNumber(value)}</span> : <input className="rbb-number-input" type="number" min="0" step="0.01" value={value} disabled={!canEdit} onChange={(event) => onValueChange(index, period.year, period.month, event.target.value)} />}</td>})}</tr>) : <EmptyRow colSpan={6 + periods.length} />}</tbody></table></>}</TableFrame>
}

function HistoryTable({ history }: { history: AbaHistory | null }) {
  const years = history?.years || []
  return <table className="rbb-table"><thead><tr><th rowSpan={2}>Bulan</th><th colSpan={years.length}>Penempatan ABA</th><th colSpan={years.length}>CKPN ABA</th><th colSpan={years.length}>Pendapatan bunga</th></tr><tr>{years.map((year) => <th key={`n-${year}`}>{year}</th>)}{years.map((year) => <th key={`c-${year}`}>{year}</th>)}{years.map((year) => <th key={`i-${year}`}>{year}</th>)}</tr></thead><tbody>{monthLabels.map((month, index) => <tr key={month}><td>{month}</td>{years.map((year) => <td className="num" key={`n-${year}`}>{formatNumber(history?.nominal?.[year]?.[index + 1])}</td>)}{years.map((year) => <td className="num" key={`c-${year}`}>{formatNumber(history?.ckpn?.[year]?.[index + 1])}</td>)}{years.map((year) => <td className="num" key={`i-${year}`}>{formatNumber(history?.interest?.[year]?.[index + 1])}</td>)}</tr>)}</tbody></table>
}

function EmptyRow({ colSpan }: { colSpan: number }) {
  return <tr><td colSpan={colSpan} className="empty-row">Belum ada data untuk filter ini.</td></tr>
}
