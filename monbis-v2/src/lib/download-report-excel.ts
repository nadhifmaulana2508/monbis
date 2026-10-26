export type ReportExcelValue = string | number | boolean | null | undefined

function escapeXml(value: string) {
  return value.replace(/[&<>"']/g, (character) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&apos;" })[character] || character)
}

function columnName(index: number) {
  let result = ""
  let value = index + 1
  while (value > 0) {
    const remainder = (value - 1) % 26
    result = String.fromCharCode(65 + remainder) + result
    value = Math.floor((value - 1) / 26)
  }
  return result
}

function crc32(bytes: Uint8Array) {
  let crc = 0xffffffff
  for (const byte of bytes) {
    crc ^= byte
    for (let bit = 0; bit < 8; bit++) crc = (crc >>> 1) ^ (0xedb88320 & -(crc & 1))
  }
  return (crc ^ 0xffffffff) >>> 0
}

function zipStored(files: Array<{ name: string; content: string }>) {
  const encoder = new TextEncoder()
  const localParts: Uint8Array[] = []
  const centralParts: Uint8Array[] = []
  let localOffset = 0
  const now = new Date()
  const dosTime = (now.getHours() << 11) | (now.getMinutes() << 5) | Math.floor(now.getSeconds() / 2)
  const dosDate = ((now.getFullYear() - 1980) << 9) | ((now.getMonth() + 1) << 5) | now.getDate()

  files.forEach(({ name, content }) => {
    const nameBytes = encoder.encode(name)
    const data = encoder.encode(content)
    const checksum = crc32(data)
    const localHeader = new Uint8Array(30 + nameBytes.length)
    const local = new DataView(localHeader.buffer)
    local.setUint32(0, 0x04034b50, true); local.setUint16(4, 20, true); local.setUint16(6, 0x0800, true)
    local.setUint16(8, 0, true); local.setUint16(10, dosTime, true); local.setUint16(12, dosDate, true)
    local.setUint32(14, checksum, true); local.setUint32(18, data.length, true); local.setUint32(22, data.length, true)
    local.setUint16(26, nameBytes.length, true); local.setUint16(28, 0, true); localHeader.set(nameBytes, 30)
    localParts.push(localHeader, data)

    const centralHeader = new Uint8Array(46 + nameBytes.length)
    const central = new DataView(centralHeader.buffer)
    central.setUint32(0, 0x02014b50, true); central.setUint16(4, 20, true); central.setUint16(6, 20, true)
    central.setUint16(8, 0x0800, true); central.setUint16(10, 0, true); central.setUint16(12, dosTime, true)
    central.setUint16(14, dosDate, true); central.setUint32(16, checksum, true); central.setUint32(20, data.length, true)
    central.setUint32(24, data.length, true); central.setUint16(28, nameBytes.length, true); central.setUint16(30, 0, true)
    central.setUint16(32, 0, true); central.setUint16(34, 0, true); central.setUint16(36, 0, true)
    central.setUint32(38, 0, true); central.setUint32(42, localOffset, true); centralHeader.set(nameBytes, 46)
    centralParts.push(centralHeader)
    localOffset += localHeader.length + data.length
  })

  const centralSize = centralParts.reduce((sum, part) => sum + part.length, 0)
  const end = new Uint8Array(22)
  const endView = new DataView(end.buffer)
  endView.setUint32(0, 0x06054b50, true); endView.setUint16(8, files.length, true); endView.setUint16(10, files.length, true)
  endView.setUint32(12, centralSize, true); endView.setUint32(16, localOffset, true)
  const allParts = [...localParts, ...centralParts, end]
  const result = new Uint8Array(allParts.reduce((sum, part) => sum + part.length, 0))
  let offset = 0
  allParts.forEach((part) => { result.set(part, offset); offset += part.length })
  return result
}

export function downloadReportExcel(
  filename: string,
  headers: string[],
  rows: ReadonlyArray<ReadonlyArray<ReportExcelValue>>,
  textColumns: number[] = [],
) {
  if (rows.length === 0) return false
  void textColumns

  const allRows: ReadonlyArray<ReadonlyArray<ReportExcelValue>> = [headers, ...rows]
  const maxColumns = Math.max(0, ...allRows.map((row) => row.length))
  const worksheetRows = allRows.map((row, rowIndex) => {
    const cells = Array.from({ length: maxColumns }, (_, columnIndex) => {
      const value = row[columnIndex]
      if (typeof value === "number") return Number.isFinite(value) ? `<c r="${columnName(columnIndex)}${rowIndex + 1}"><v>${value}</v></c>` : ""
      if (typeof value === "boolean") return `<c r="${columnName(columnIndex)}${rowIndex + 1}" t="b"><v>${value ? 1 : 0}</v></c>`
      if (value === null || value === undefined) return ""
      const text = escapeXml(String(value).replace(/[\t\r\n]+/g, " "))
      return `<c r="${columnName(columnIndex)}${rowIndex + 1}" t="inlineStr"><is><t xml:space="preserve">${text}</t></is></c>`
    }).join("")
    return `<row r="${rowIndex + 1}">${cells}</row>`
  }).join("")
  const lastCell = `${columnName(Math.max(maxColumns - 1, 0))}${allRows.length}`
  const xmlFiles = [
    { name: "[Content_Types].xml", content: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>` },
    { name: "_rels/.rels", content: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>` },
    { name: "xl/workbook.xml", content: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Rekap" sheetId="1" r:id="rId1"/></sheets></workbook>` },
    { name: "xl/_rels/workbook.xml.rels", content: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>` },
    { name: "xl/worksheets/sheet1.xml", content: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><dimension ref="A1:${lastCell}"/><sheetData>${worksheetRows}</sheetData></worksheet>` },
  ]
  const archive = zipStored(xmlFiles)
  const blob = new Blob([archive], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" })
  const url = URL.createObjectURL(blob)
  const link = document.createElement("a")
  link.href = url
  link.download = `${filename.replace(/\.(xls|xlsx)$/i, "")}.xlsx`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
  return true
}
