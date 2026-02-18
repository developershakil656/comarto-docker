import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";
import { VitePWA } from "vite-plugin-pwa";

// https://vitejs.dev/config/
export default defineConfig({
  base: '/', // ensure assets use root-relative paths
  plugins: [
    vue(),
    VitePWA({
      registerType: "autoUpdate",
      includeAssets: ["favicon.ico", "apple-touch-icon.png"],
      manifest: {
        name: "Comarto",
        short_name: "Comarto",
        description: "B2B e-commerce platform for wholesale buyers and sellers",
        theme_color: "#0B845C",
        start_url: "/",
        scope: "/",
        icons: [
          {
            src: "logo-192x192.png",
            sizes: "192x192",
            type: "image/png",
          },
          {
            src: "logo.png",
            sizes: "512x512",
            type: "image/png",
          },
        ],
      },
      // ensure navigation requests fall back to index.html
      workbox: {
        navigateFallback: '/',
        navigateFallbackDenylist: [/^\/api\//],
      },
      // enable during dev so SW behaves same way
      devOptions: {
        enabled: true,
        navigateFallback: '/',
      },
    }),
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  server: {
    host: '0.0.0.0', // Necessary for Docker to listen on all interfaces
    port: 5173,
    strictPort: true,
    hmr: {
      // Replace with your actual domain or use an environment variable
      host: process.env.VITE_ALLOWED_HOST || "mydomain.com",
      clientPort: 80, // Port 80 because your nginx listens on 80
      protocol: 'ws'  // Use 'wss' if you are using HTTPS/443
    },
    watch: {
      usePolling: true, // Fixes file change detection issues in Docker volumes
    }
  },
  css: {
    postcss: "./postcss.config.js",
  },
  // Production optimizations
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          // Split vendor chunks
          "vendor-vue": ["vue", "vue-router"],
          "vendor-forms": ["vee-validate", "yup", "@vee-validate/rules"],
          "vendor-ui": ["@heroicons/vue", "sweetalert2", "notivue"],
          "vendor-utils": ["axios"],
        },
      },
    },
    // Enable CSS code splitting
    cssCodeSplit: true,
    // Minify CSS and JS
    minify: "terser",
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
      },
    },
    // Generate separate CSS file
    cssMinify: true,
  },
  // Preload important resources
  optimizeDeps: {
    include: ["vue", "vue-router"],
  },
});
