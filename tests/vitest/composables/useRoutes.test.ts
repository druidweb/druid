import useRoutes from '@/composables/useRoutes';
import { beforeEach, describe, expect, it, vi } from 'vitest';

// Mock ziggy-js
vi.mock('ziggy-js', () => ({
  route: vi.fn((name, params, absolute, config) => {
    // Simple mock implementation
    if (params) {
      const paramString = Object.entries(params)
        .map(([key, value]) => `${key}=${value}`)
        .join('&');
      return `http://localhost/${name}?${paramString}`;
    }
    return absolute ? `http://localhost/${name}` : `/${name}`;
  }),
  Config: {},
}));

// Mock ziggy.js
vi.mock('@/ziggy.js', () => ({
  Ziggy: {
    url: 'http://localhost',
    port: null,
    defaults: {},
    routes: {
      dashboard: { uri: 'dashboard', methods: ['GET', 'HEAD'] },
      login: { uri: 'login', methods: ['GET', 'HEAD'] },
      logout: { uri: 'logout', methods: ['POST'] },
    },
  },
}));

describe('useRoutes', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('returns a route URL for a given route name', () => {
    const url = useRoutes('dashboard');
    expect(url).toBe('http://localhost/dashboard');
  });

  it('returns a route URL with parameters', () => {
    const url = useRoutes('dashboard', { id: 123 });
    expect(url).toBe('http://localhost/dashboard?id=123');
  });

  it('returns absolute URL by default', () => {
    const url = useRoutes('login');
    expect(url).toContain('http://localhost');
  });

  it('returns relative URL when absolute is false', () => {
    const url = useRoutes('login', undefined, false);
    expect(url).toBe('/login');
  });

  it('handles multiple parameters', () => {
    const url = useRoutes('dashboard', { id: 123, tab: 'settings' });
    expect(url).toContain('id=123');
    expect(url).toContain('tab=settings');
  });
});
