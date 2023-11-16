import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react-swc';

export default defineConfig({
    plugins: [
        react(),
    ],
    build: {
        outDir: 'resources/build', // establece el directorio de salida para los archivos compilados
        rollupOptions: {
            input: 'resources/js/main.jsx' // archivo de entrada para el proceso de compilación
        },
        manifest: true, // genera un manifest.json con los nombres de los archivos compilados
    },
    resolve: {
        alias: [
            {
                // this is required for the SCSS modules
                find: /^~(.*)$/,
                replacement: '$1',
            },
        ],
    },
});
