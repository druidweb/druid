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
          '@headlessui/vue': [
            'Menu',
            'MenuButton',
            'MenuItem',
            'MenuItems',
            'Disclosure',
            'DisclosureButton',
            'DisclosurePanel',
            'Dialog',
            'DialogPanel',
            'DialogTitle',
            'TransitionRoot',
            'TransitionChild',
          ],
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
    environment: 'happy-dom',
    setupFiles: ['./tests/vitest/setup.ts'],
    include: ['tests/vitest/**/*.{test,spec}.{js,ts,jsx,tsx}'],
    coverage: {
      provider: 'v8',
      reporter: ['text'],
      include: ['resources/js/**/*.{js,ts,vue}'],
      exclude: [
        'resources/js/types/**',
        '**/*.d.ts',
        'resources/js/ssr.ts',
        'resources/js/echo.ts',
        'resources/js/ziggy.js',
        'resources/js/zorah.js',
        '**/*.{test,spec}.{js,ts,jsx,tsx}',
      ],
      all: true,
      lines: 100,
      functions: 100,
      branches: 100,
      statements: 100,
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js'),
      'ziggy-js': path.resolve(__dirname, './vendor/tightenco/ziggy'),
      'zorah-js': path.resolve(__dirname, './vendor/zenphp/zorah/dist/index.js'),
      zorah: path.resolve(__dirname, './vendor/zenphp/zorah/dist/vue.js'),
    },
  },
});
