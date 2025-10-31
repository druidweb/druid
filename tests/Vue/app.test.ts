import { describe, expect, it, vi } from 'vitest';

const mockCreateInertiaApp = vi.fn((config) => {
  if (config.title) {
    config.title('Test Page');
    config.title('');
  }
  if (config.setup) {
    config.setup({
      el: document.createElement('div'),
      App: {},
      props: {},
      plugin: {},
    });
  }
});

vi.mock('@inertiajs/vue3', () => ({
  createInertiaApp: mockCreateInertiaApp,
}));

vi.mock('laravel-vite-plugin/inertia-helpers', () => ({
  resolvePageComponent: vi.fn(),
}));

const mockCreateApp = vi.fn(() => ({
  use: vi.fn().mockReturnThis(),
  mount: vi.fn(),
}));

vi.mock('vue', () => ({
  createApp: mockCreateApp,
  h: vi.fn(),
}));

const mockInitializeTheme = vi.fn();

vi.mock('@/composables/useAppearance', () => ({
  initializeTheme: mockInitializeTheme,
}));

describe('app.ts', () => {
  it('imports app.ts without errors', async () => {
    await expect(import('@/app')).resolves.toBeDefined();
  });

  it('calls createInertiaApp', async () => {
    await import('@/app');
    expect(mockCreateInertiaApp).toHaveBeenCalled();
  });

  it('calls initializeTheme', async () => {
    await import('@/app');
    expect(mockInitializeTheme).toHaveBeenCalled();
  });

  it('configures progress color', async () => {
    await import('@/app');
    const callArgs = mockCreateInertiaApp.mock.calls[0][0];
    expect(callArgs.progress).toBeDefined();
    expect(callArgs.progress.color).toBe('#4B5563');
  });

  it('configures title function', async () => {
    await import('@/app');
    const callArgs = mockCreateInertiaApp.mock.calls[0][0];
    expect(callArgs.title).toBeDefined();
    expect(typeof callArgs.title).toBe('function');
  });

  it('configures resolve function', async () => {
    await import('@/app');
    const callArgs = mockCreateInertiaApp.mock.calls[0][0];
    expect(callArgs.resolve).toBeDefined();
    expect(typeof callArgs.resolve).toBe('function');
  });

  it('configures setup function', async () => {
    await import('@/app');
    const callArgs = mockCreateInertiaApp.mock.calls[0][0];
    expect(callArgs.setup).toBeDefined();
    expect(typeof callArgs.setup).toBe('function');
  });

  it('title function returns title with app name when title provided', async () => {
    await import('@/app');
    const callArgs = mockCreateInertiaApp.mock.calls[0][0];
    const result = callArgs.title('Dashboard');
    expect(result).toContain('Dashboard');
    expect(result).toContain('-');
  });

  it('title function returns only app name when no title provided', async () => {
    await import('@/app');
    const callArgs = mockCreateInertiaApp.mock.calls[0][0];
    const result = callArgs.title('');
    expect(result).not.toContain('-');
  });

  it('title function handles null/undefined title', async () => {
    await import('@/app');
    const callArgs = mockCreateInertiaApp.mock.calls[0][0];
    const result = callArgs.title(null);
    expect(typeof result).toBe('string');
  });
});
