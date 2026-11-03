import { defineConfig } from 'vite';

// Chaque fichier est construit à part : les vues le chargent en script classique, avant leurs scripts en ligne.
export default defineConfig(({ mode }) => ({
    publicDir: false,
    build: {
        outDir: 'public',
        emptyOutDir: false,
        cssCodeSplit: false,
        rollupOptions: {
            input: `resources/js/${mode}.js`,
            output: {
                format: 'iife',
                entryFileNames: `js/${mode}.js`,
                assetFileNames: `css/${mode}.css`,
            },
        },
    },
}));
