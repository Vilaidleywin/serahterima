import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    server: {
        host: "127.0.0.1", // samakan dengan host Laravel
        port: 5173,
        cors: true,
        hmr: {
            host: "127.0.0.1",
            port: 5173,
            protocol: "ws",
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
