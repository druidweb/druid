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
      include: ['resources/js/**/*.{js,ts,vue}'],
      exclude: [
        'resources/js/types/**/*.d.ts',
        'resources/js/actions/**',
        'resources/js/pages/**',
        'resources/js/routes/**',
        'resources/js/wayfinder/**',
        'resources/js/zorah.js',
        'resources/js/**/*/index.ts',
        'resources/js/app.ts',
        'resources/js/ssr.ts',
        '**/*.index.ts',
        /** template only components cannot be tested */
        'resources/js/components/AppLogoIcon.vue',
        'resources/js/components/NavFooter.vue',
        /** wrapper only components - simple pass-through wrappers for reka-ui */
        'resources/js/components/ui/collapsible/CollapsibleContent.vue',
        'resources/js/components/ui/collapsible/CollapsibleTrigger.vue',
        'resources/js/components/ui/dialog/DialogOverlay.vue',
        'resources/js/components/ui/dialog/DialogScrollContent.vue',
        'resources/js/components/ui/dialog/DialogTrigger.vue',
        'resources/js/components/ui/dropdown-menu/DropdownMenuCheckboxItem.vue',
        'resources/js/components/ui/dropdown-menu/DropdownMenuRadioItem.vue',
        'resources/js/components/ui/dropdown-menu/DropdownMenuSub.vue',
        'resources/js/components/ui/dropdown-menu/DropdownMenuSubContent.vue',
        'resources/js/components/ui/dropdown-menu/DropdownMenuSubTrigger.vue',
        'resources/js/components/ui/navigation-menu/NavigationMenuContent.vue',
        'resources/js/components/ui/navigation-menu/NavigationMenuIndicator.vue',
        'resources/js/components/ui/navigation-menu/NavigationMenuLink.vue',
        'resources/js/components/ui/navigation-menu/NavigationMenuTrigger.vue',
      ],
      thresholds: {
        lines: 89,
        functions: 80,
        branches: 64,
        statements: 89,
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
