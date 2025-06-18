import { vi } from 'vitest'

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
}))

// Mock Ziggy
vi.mock('ziggy-js', () => ({
  default: vi.fn((name: string) => `/${name}`),
  route: vi.fn((name: string) => `/${name}`),
}))

// Global test utilities
global.route = vi.fn((name: string) => `/${name}`)
