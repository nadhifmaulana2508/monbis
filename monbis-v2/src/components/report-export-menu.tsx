import { useState, type RefObject } from "react"
import { ChevronDown, Download, FileSpreadsheet, ImageDown } from "lucide-react"

import { Button } from "@/components/ui/button"
import { Popover } from "@/components/ui/popover"

type ReportExportMenuProps = {
  target: RefObject<HTMLElement | null> | (() => HTMLElement | null)
  filename: string
  disabled?: boolean
  onExportExcel: () => void
}

export function ReportExportMenu({ target, filename, disabled = false, onExportExcel }: ReportExportMenuProps) {
  const [open, setOpen] = useState(false)
  const [exportError, setExportError] = useState("")
  const [exportingImage, setExportingImage] = useState(false)

  const exportImage = async () => {
    const element = typeof target === "function" ? target() : target.current
    if (!element || exportingImage) return

    setExportError("")
    setExportingImage(true)
    try {
      const { toPng } = await import("html-to-image")
      const backgroundColor = getComputedStyle(element.closest("section") || element).backgroundColor
      const dataUrl = await toPng(element, {
        backgroundColor,
        cacheBust: true,
        pixelRatio: 2,
        width: element.scrollWidth,
        height: element.scrollHeight,
        style: {
          width: `${element.scrollWidth}px`,
          height: `${element.scrollHeight}px`,
          maxWidth: "none",
          overflow: "visible",
        },
      })
      const link = document.createElement("a")
      link.href = dataUrl
      link.download = `${filename}.png`
      link.click()
      setOpen(false)
    } catch {
      setExportError("Gambar belum bisa dibuat. Coba lagi atau pilih Excel.")
    } finally {
      setExportingImage(false)
    }
  }

  return (
    <Popover.Root open={open} onOpenChange={setOpen}>
      <Popover.Trigger render={<Button type="button" variant="signalGreen" size="sm" className="size-10 min-h-10 shrink-0 px-2 sm:size-auto sm:min-h-7 sm:px-3" aria-label="Pilihan export rekap" title="Pilihan export rekap" disabled={disabled || exportingImage} />}>
        <Download data-icon="inline-start" aria-hidden="true" />
        <span className="hidden sm:inline">Export</span>
        <ChevronDown className="hidden size-3.5 sm:inline" aria-hidden="true" />
      </Popover.Trigger>
      <Popover.Portal>
        <Popover.Positioner side="bottom" align="end" sideOffset={6} className="z-[2147483647] outline-none">
          <Popover.Popup className="min-w-44 overflow-hidden rounded-lg border bg-popover p-1 text-popover-foreground shadow-lg">
            <button type="button" onClick={() => { onExportExcel(); setOpen(false) }} className="flex min-h-9 w-full items-center gap-2 rounded-md px-2 text-left text-sm hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
              <FileSpreadsheet className="size-4" aria-hidden="true" />
              Export Excel
            </button>
            <button type="button" onClick={() => void exportImage()} disabled={exportingImage} className="flex min-h-9 w-full items-center gap-2 rounded-md px-2 text-left text-sm hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:opacity-60">
              <ImageDown className="size-4" aria-hidden="true" />
              {exportingImage ? "Menyiapkan gambar..." : "Download gambar"}
            </button>
            {exportError && <p role="status" className="px-2 py-1.5 text-xs text-destructive">{exportError}</p>}
          </Popover.Popup>
        </Popover.Positioner>
      </Popover.Portal>
    </Popover.Root>
  )
}
