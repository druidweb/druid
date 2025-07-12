import '../css/app.css';
import './echo';
import './types/ziggy.js.d.ts';

import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp } from 'vue';
import type { Config } from 'ziggy-js';
import { ZiggyVue } from 'ziggy-js';
import { ZorahVue } from 'zorah-js';
import { Ziggy } from './ziggy.js';
import { Zorah } from './zorah.js';
/// <reference types="vite/client" />

const appName = import.meta.env.VITE_APP_NAME || 'Starter Kit';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy as Config)
      .use(ZorahVue, Zorah)
      .component('Head', Head)
      .component('Link', Link)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

initializeTheme();
