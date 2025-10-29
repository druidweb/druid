import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'node:path';
import AutoImport from 'unplugin-auto-import/vite';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.ts'],
      ssr: 'resources/js/ssr.ts',
      refresh: true,
    }),
    tailwindcss(),
    wayfinder({
      formVariants: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    AutoImport({
      include: [
        /\.[tj]sx?$/, // .ts, .tsx, .js, .jsx
        /\.vue$/,
        /\.vue\?vue/, // .vue
        /\.vue\.[tj]sx?\?vue/, // .vue (vue-loader with experimentalInlineMatchResource enabled)
        /\.md$/, // .md
      ],
      imports: [
        'vue',
        '@vueuse/core',
        {
          '@inertiajs/vue3': ['router', 'useForm', 'usePage', 'usePoll', 'usePrefetch', 'useRemember'],
          'lucide-vue-next': [],
          'reka-ui': [],
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
  resolve: {
    alias: {
      'zorah-js': path.resolve('vendor/zenphp/zorah/dist/index.js'),
      zorah: path.resolve('vendor/zenphp/zorah/dist/vue.js'),
    },
  },
});
