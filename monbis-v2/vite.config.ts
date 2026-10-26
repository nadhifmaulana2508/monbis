import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { defineConfig } from 'vite'
import path from 'node:path'

const apiProxy = {
  '/api': {
    target: 'http://localhost',
    changeOrigin: true,
    rewrite: (requestPath: string) => requestPath.replace(/^\/api/, '/report-dpk/api'),
  },
}

// https://vite.dev/config/
export default defineConfig(({ command }) => ({
  base: command === 'build' ? '/v2-react/' : '/',
  plugins: [react(), tailwindcss()],
  server: { proxy: apiProxy },
  preview: { host: 'localhost', port: 4173, strictPort: true, proxy: apiProxy },
  resolve: {
    alias: {
      '@': path.resolve(process.cwd(), 'src'),
    },
  },
}))
