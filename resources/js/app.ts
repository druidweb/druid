import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZorahVue } from 'zorah-js';
import { Toaster } from './components/ui/toast';
import { initializeTheme } from './composables/useAppearance';
import { showFlashBanner } from './composables/useFlashBanner';
import { Zorah } from './zorah';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    // Banners flashed by the server arrive on the first page load (full redirects) and on Inertia visits.
    showFlashBanner(props.initialPage.props.teams?.flash);
    router.on('success', (event) => showFlashBanner(event.detail.page.props.teams?.flash));

    createApp({ render: () => [h(App, props), h(Toaster)] })
      .use(plugin)
      .use(ZorahVue, Zorah)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

// This will set light / dark mode on page load...
initializeTheme();
