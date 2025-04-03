import './types/ziggy.js.d.ts';
import '../css/app.css';
import './echo';

import { createInertiaApp } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import type { Config } from 'ziggy-js';
import { Ziggy } from './ziggy.js';

/// <reference types="vite/client" />

const appName = import.meta.env.VITE_APP_NAME || 'Starter Kit';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy as Config)
      .component('Head', Head)
      .component('Link', Link)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

initializeTheme();
