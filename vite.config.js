import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Admin/Blade entry (Bootstrap via CDN, no Tailwind needed here)
                "resources/css/app.css",
                "resources/js/app.js",
                // Customer PWA React entry
                "resources/react/main.jsx",
            ],
            refresh: true,
        }),
        react(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
        cors: {
            origin: "*", // Mengizinkan semua origin termasuk domain ngrok Anda
            methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
            allowedHeaders: [
                "Content-Type",
                "Authorization",
                "X-Requested-With",
            ],
        },

        hmr: {
            host: "localhost", 
            port: 5173,
            strictPort: true,
            cors: true,
            allowedHosts: ["stark-recede-dilute.ngrok-free.dev"],
            hmr: {
                host: "stark-recede-dilute.ngrok-free.dev", 
                clientPort: 443, 
            },
        },
    },
});
