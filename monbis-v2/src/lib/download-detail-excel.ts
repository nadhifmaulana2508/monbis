import { downloadReportExcel } from "@/lib/download-report-excel"

export function downloadDetailExcel<T extends object>(rows: T[], columns: Array<[string, keyof T]>, filename: string, textColumns: number[] = []) {
  const identifierColumns = columns.flatMap(([, key], index) => /(^no_rekening|^rekening|^kode|^no_hp)/i.test(String(key)) ? [index] : [])
  downloadReportExcel(filename, columns.map(([label]) => label), rows.map((row) => columns.map(([, key]) => row[key] as string | number | boolean | null | undefined)), [...new Set([...textColumns, ...identifierColumns])])
}
