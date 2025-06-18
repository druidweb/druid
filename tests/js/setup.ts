import type { ComponentMountingOptions } from '@vue/test-utils';
import { mount, VueWrapper } from '@vue/test-utils';
import { vi } from 'vitest';

// Mock Inertia.js
vi.mock('@inertiajs/vue3', () => ({
  createInertiaApp: vi.fn(),
  router: {
    visit: vi.fn(),
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    patch: vi.fn(),
    delete: vi.fn(),
    reload: vi.fn(),
    replace: vi.fn(),
    remember: vi.fn(),
    restore: vi.fn(),
  },
  usePage: vi.fn(() => ({
    props: {},
    url: '/',
    component: 'Test',
    version: '1',
  })),
  useForm: vi.fn(() => ({
    data: {},
    errors: {},
    hasErrors: false,
    processing: false,
    progress: null,
    wasSuccessful: false,
    recentlySuccessful: false,
    transform: vi.fn(),
    defaults: vi.fn(),
    reset: vi.fn(),
    clearErrors: vi.fn(),
    setError: vi.fn(),
    submit: vi.fn(),
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    patch: vi.fn(),
    delete: vi.fn(),
    cancel: vi.fn(),
  })),
  Head: {
    name: 'Head',
    template: '<head><slot /></head>',
  },
  Link: {
    name: 'Link',
    template: '<a><slot /></a>',
  },
}));

// Mock Ziggy
vi.mock('ziggy-js', () => ({
  default: vi.fn((name: string) => `/${name}`),
  route: vi.fn((name: string) => `/${name}`),
}));

// Global test utilities
global.route = vi.fn((name: string) => `/${name}`);

// Test utility functions
export function mountComponent<T>(component: T, options: ComponentMountingOptions<T> = {}): VueWrapper {
  return mount(component, {
    global: {
      ...options.global,
    },
    ...options,
  });
}

export function createMockProps(overrides = {}) {
  return {
    title: 'Test Component',
    isVisible: true,
    items: [],
    ...overrides,
  };
}

export async function triggerAsyncAction(wrapper: VueWrapper, action: () => Promise<void>) {
  await action();
  await wrapper.vm.$nextTick();
}
