import path from "node:path"

import react from "@vitejs/plugin-react"
import tailwindcss from "@tailwindcss/vite"
import { defineConfig } from "vite"

export default defineConfig(({ mode }) => ({
  base: mode === "production" ? "/kpi/" : mode === "xampp" ? "/report-dpk/kpi/" : "/",
  plugins: [react(), tailwindcss()],
  build: {
    outDir: path.resolve(process.cwd(), "../kpi"),
    emptyOutDir: true,
  },
  server: {
    port: 5174,
    strictPort: true,
    proxy: {
      "/report-dpk/api": {
        target: "http://localhost",
        changeOrigin: true,
      },
      "/api": {
        target: "http://localhost",
        changeOrigin: true,
        rewrite: (requestPath) => requestPath.replace(/^\/api/, "/report-dpk/api"),
      },
    },
    fs: {
      allow: [process.cwd(), path.resolve(process.cwd(), "../frontend/node_modules")],
    },
  },
  resolve: {
    alias: {
      "@": path.resolve(process.cwd(), "src"),
    },
  },
}))
