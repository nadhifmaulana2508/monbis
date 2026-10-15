import type { KpiIndicator } from "./setting-kpi-jabatan-types"

type Props = { rows: KpiIndicator[] }

export function SettingKpiJabatanTable({ rows, drafts, savingId, onChange, onSave }: Props & { drafts: Record<number, { bobot: string; target: string; status: string }>; savingId: number | null; onChange: (id: number, field: "bobot" | "target" | "status", value: string) => void; onSave: (row: KpiIndicator) => void }) {
  if (!rows.length) {
    return <div className="grid min-h-48 place-items-center px-6 text-sm text-[var(--muted)]">Tidak ada indikator aktif untuk filter ini.</div>
  }

  return (
    <div className="max-h-[calc(100dvh-260px)] overflow-auto border-t border-[var(--line)]">
      <table className="w-full min-w-[900px] border-separate border-spacing-0 text-sm tabular-nums">
        <thead className="sticky top-0 z-10 bg-[var(--surface)] text-left text-xs text-[var(--muted)]">
          <tr>
            <th className="sticky left-0 z-20 w-32 border-b border-[var(--line)] bg-[var(--surface)] px-4 py-3 font-medium">Jabatan</th>
            <th className="w-36 border-b border-[var(--line)] px-4 py-3 font-medium">Kelompok</th>
            <th className="min-w-64 border-b border-[var(--line)] px-4 py-3 font-medium">Indikator</th>
            <th className="w-28 border-b border-[var(--line)] px-4 py-3 text-right font-medium">Bobot</th>
            <th className="w-36 border-b border-[var(--line)] px-4 py-3 text-right font-medium">Target default</th>
            <th className="w-24 border-b border-[var(--line)] px-4 py-3 font-medium">Arah</th>
            <th className="w-24 border-b border-[var(--line)] px-4 py-3 font-medium">Unit</th>
            <th className="w-24 border-b border-[var(--line)] px-4 py-3 font-medium">Status</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.id} className="group hover:bg-[var(--surface-subtle)]">
              <td className="sticky left-0 z-[1] border-b border-[var(--line-soft)] bg-[var(--surface)] px-4 py-3 font-medium group-hover:bg-[var(--surface-subtle)]">{row.jabatan_nama}</td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{row.kelompok || "-"}</td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3">
                <div className="font-medium">{row.nama}</div>
                {row.definisi && <div className="mt-1 max-w-[360px] text-xs leading-5 text-[var(--muted)]">{row.definisi}</div>}
              </td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3"><input aria-label={`Bobot ${row.nama}`} value={drafts[row.id]?.bobot ?? String(row.bobot * 100)} onChange={(event) => onChange(row.id, "bobot", event.target.value)} className="h-8 w-24 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2 text-right text-sm outline-none focus:border-[var(--accent)]" inputMode="decimal" /></td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3"><input aria-label={`Target ${row.nama}`} value={drafts[row.id]?.target ?? String(row.target_default)} onChange={(event) => onChange(row.id, "target", event.target.value)} className="h-8 w-32 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2 text-right text-sm outline-none focus:border-[var(--accent)]" inputMode="decimal" /></td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{row.arah || "-"}</td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3 text-[var(--muted)]">{row.unit || "-"}</td>
              <td className="border-b border-[var(--line-soft)] px-4 py-3">
                <div className="flex items-center gap-2"><select aria-label={`Status ${row.nama}`} value={drafts[row.id]?.status ?? row.status} onChange={(event) => onChange(row.id, "status", event.target.value)} className="h-8 rounded-md border border-[var(--line)] bg-[var(--surface)] px-2 text-xs outline-none focus:border-[var(--accent)]"><option value="AKTIF">AKTIF</option><option value="PILOT">PILOT</option><option value="NONAKTIF">NONAKTIF</option></select><button type="button" disabled={savingId === row.id} onClick={() => onSave(row)} className="h-8 rounded-md bg-[var(--accent)] px-2 text-xs font-medium text-white hover:bg-[var(--accent-strong)] disabled:opacity-50">{savingId === row.id ? "..." : "Simpan"}</button></div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}
