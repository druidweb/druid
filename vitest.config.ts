import vue from '@vitejs/plugin-vue';
import path from 'node:path';
import AutoImport from 'unplugin-auto-import/vite';
import { defineConfig } from 'vitest/config';

export default defineConfig({
  plugins: [
    vue(),
    AutoImport({
      include: [
        /\.[tj]sx?$/, // .ts, .tsx, .js, .jsx
        /\.vue$/,
        /\.vue\?vue/, // .vue
        /\.test\.[tj]sx?$/, // .test.ts, .test.tsx, .test.js, .test.jsx
      ],
      imports: [
        'vue',
        '@vueuse/core',
        'vitest',
        {
          '@inertiajs/vue3': ['router', 'useForm', 'usePage', 'usePoll', 'usePrefetch', 'useRemember'],
          'lucide-vue-next': [],
          'radix-vue': [],
          '@/lib/utils': [['default', 'utils']],
        },
      ],
      dirs: ['./resources/js/components', './resources/js/composables', './resources/js/layouts', './resources/js/pages'],
      dts: './resources/js/types/auto-imports.d.ts',
      vueTemplate: true,
      defaultExportByFilename: true,
      injectAtEnd: true,
    }),
  ],
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./tests/Vue/setup.ts'],
    include: ['tests/Vue/**/*.{test,spec}.{js,ts,jsx,tsx}'],
    exclude: ['**/node_modules/**', '**/vendor/**'],
    coverage: {
      provider: 'v8',
      reporter: ['text'],
      // Scope coverage to framework-agnostic logic only. Everything rendered/Inertia-coupled
      // (pages, layouts, most components) is covered by the Pest browser E2E suite instead.
      include: [
        'resources/js/composables/**/*.ts',
        'resources/js/lib/**/*.ts',
        'resources/js/components/ui/sidebar/utils.ts',
        'resources/js/components/AlertError.vue',
        'resources/js/components/Icon.vue',
      ],
      exclude: ['**/*.d.ts'],
      thresholds: {
        lines: 100,
        functions: 100,
        branches: 100,
        statements: 100,
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js'),
      'zorah-js': path.resolve(__dirname, './vendor/zenphp/zorah/dist/index.esm.js'),
      zorah: path.resolve(__dirname, './vendor/zenphp/zorah/dist/index.esm.js'),
    },
  },
});
