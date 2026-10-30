import path from "node:path"

import react from "@vitejs/plugin-react"
import tailwindcss from "@tailwindcss/vite"
import { defineConfig } from "vite"

export default defineConfig(({ mode }) => ({
  base: mode === "production" ? "/rbb/" : mode === "xampp" ? "/report-dpk/rbb/" : "/",
  plugins: [react(), tailwindcss()],
  build: {
    outDir: path.resolve(process.cwd(), "../rbb"),
    emptyOutDir: true,
  },
  server: {
    port: 5175,
    strictPort: true,
    proxy: {
      "/api": {
        target: "http://localhost",
        changeOrigin: true,
        rewrite: (requestPath) => requestPath.replace(/^\/api/, "/report-dpk/api"),
      },
    },
  },
  resolve: {
    alias: {
      "@": path.resolve(process.cwd(), "src"),
    },
  },
}))
