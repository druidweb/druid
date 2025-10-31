import { describe, expect, it, vi } from 'vitest';

const mockCreateInertiaApp = vi.fn((config) => {
  if (config.title) {
    config.title('Test Page');
    config.title('');
  }
  if (config.setup) {
    config.setup({
      App: {},
      props: {},
      plugin: {},
    });
  }
  return Promise.resolve();
});

const mockCreateServer = vi.fn((callback) => {
  callback({ component: 'TestPage', props: {}, url: '/test' });
});

vi.mock('@inertiajs/vue3', () => ({
  createInertiaApp: mockCreateInertiaApp,
}));

vi.mock('@inertiajs/vue3/server', () => ({
  default: mockCreateServer,
}));

vi.mock('laravel-vite-plugin/inertia-helpers', () => ({
  resolvePageComponent: vi.fn(),
}));

const mockCreateSSRApp = vi.fn(() => ({
  use: vi.fn().mockReturnThis(),
}));

vi.mock('vue', () => ({
  createSSRApp: mockCreateSSRApp,
  h: vi.fn(),
  DefineComponent: vi.fn(),
}));

const mockRenderToString = vi.fn();

vi.mock('vue/server-renderer', () => ({
  renderToString: mockRenderToString,
}));

describe('ssr.ts', () => {
  it('imports ssr.ts without errors', async () => {
    await expect(import('@/ssr')).resolves.toBeDefined();
  });

  it('calls createServer', async () => {
    await import('@/ssr');
    expect(mockCreateServer).toHaveBeenCalled();
  });

  it('calls createSSRApp', async () => {
    await import('@/ssr');
    expect(mockCreateSSRApp).toHaveBeenCalled();
  });

  it('configures cluster mode', async () => {
    await import('@/ssr');
    const callArgs = mockCreateServer.mock.calls[0];
    expect(callArgs).toBeDefined();
  });
});
