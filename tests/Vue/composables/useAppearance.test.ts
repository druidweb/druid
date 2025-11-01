import { initializeTheme, updateTheme, useAppearance } from '@/composables/useAppearance';
import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

describe('updateTheme', () => {
  it('adds dark class when theme is dark', () => {
    updateTheme('dark');
    expect(document.documentElement.classList.contains('dark')).toBe(true);
  });

  it('removes dark class when theme is light', () => {
    document.documentElement.classList.add('dark');
    updateTheme('light');
    expect(document.documentElement.classList.contains('dark')).toBe(false);
  });

  it('applies system theme when theme is system and prefers dark', () => {
    const matchMediaMock = vi.fn().mockReturnValue({ matches: true });
    vi.stubGlobal('matchMedia', matchMediaMock);

    updateTheme('system');
    expect(document.documentElement.classList.contains('dark')).toBe(true);
  });

  it('applies system theme when theme is system and prefers light', () => {
    const matchMediaMock = vi.fn().mockReturnValue({ matches: false });
    vi.stubGlobal('matchMedia', matchMediaMock);

    updateTheme('system');
    expect(document.documentElement.classList.contains('dark')).toBe(false);
  });

  it('returns early when window is undefined (SSR)', () => {
    const originalWindow = global.window;
    // @ts-expect-error - Testing SSR scenario
    delete global.window;

    // Should not throw
    expect(() => updateTheme('dark')).not.toThrow();

    global.window = originalWindow;
  });
});

describe('initializeTheme', () => {
  it('initializes with system theme when no saved preference', () => {
    const matchMediaMock = vi.fn().mockReturnValue({
      matches: false,
      addEventListener: vi.fn(),
    });
    vi.stubGlobal('matchMedia', matchMediaMock);

    initializeTheme();
    expect(document.documentElement.classList.contains('dark')).toBe(false);
  });

  it('initializes with saved preference', () => {
    localStorage.setItem('appearance', 'dark');

    initializeTheme();
    expect(document.documentElement.classList.contains('dark')).toBe(true);
  });

  it('sets up system theme change listener', () => {
    const addEventListenerMock = vi.fn();
    const matchMediaMock = vi.fn().mockReturnValue({
      matches: false,
      addEventListener: addEventListenerMock,
    });
    vi.stubGlobal('matchMedia', matchMediaMock);

    initializeTheme();
    expect(addEventListenerMock).toHaveBeenCalledWith('change', expect.any(Function));
  });

  it('returns early when window is undefined (SSR)', () => {
    const originalWindow = global.window;
    // @ts-expect-error - Testing SSR scenario
    delete global.window;

    // Should not throw
    expect(() => initializeTheme()).not.toThrow();

    global.window = originalWindow;
  });
});

describe('useAppearance', () => {
  it('returns appearance ref and updateAppearance function', () => {
    const wrapper = mount({
      setup() {
        const { appearance, updateAppearance } = useAppearance();
        return { appearance, updateAppearance };
      },
      template: '<div></div>',
    });

    expect(wrapper.vm.appearance).toBeDefined();
    expect(wrapper.vm.updateAppearance).toBeInstanceOf(Function);
  });

  it('updates appearance and stores in localStorage', async () => {
    const wrapper = mount({
      setup() {
        const { appearance, updateAppearance } = useAppearance();
        return { appearance, updateAppearance };
      },
      template: '<div></div>',
    });

    wrapper.vm.updateAppearance('dark');

    expect(wrapper.vm.appearance).toBe('dark');
    expect(localStorage.getItem('appearance')).toBe('dark');
    expect(document.documentElement.classList.contains('dark')).toBe(true);
  });

  it('updates appearance to light', async () => {
    const wrapper = mount({
      setup() {
        const { appearance, updateAppearance } = useAppearance();
        return { appearance, updateAppearance };
      },
      template: '<div></div>',
    });

    wrapper.vm.updateAppearance('light');

    expect(wrapper.vm.appearance).toBe('light');
    expect(localStorage.getItem('appearance')).toBe('light');
    expect(document.documentElement.classList.contains('dark')).toBe(false);
  });

  it('updates appearance to system', async () => {
    const matchMediaMock = vi.fn().mockReturnValue({ matches: true });
    vi.stubGlobal('matchMedia', matchMediaMock);

    const wrapper = mount({
      setup() {
        const { appearance, updateAppearance } = useAppearance();
        return { appearance, updateAppearance };
      },
      template: '<div></div>',
    });

    wrapper.vm.updateAppearance('system');

    expect(wrapper.vm.appearance).toBe('system');
    expect(localStorage.getItem('appearance')).toBe('system');
  });

  it('sets cookie when updating appearance', () => {
    const wrapper = mount({
      setup() {
        const { updateAppearance } = useAppearance();
        return { updateAppearance };
      },
      template: '<div></div>',
    });

    wrapper.vm.updateAppearance('dark');

    expect(document.cookie).toContain('appearance=dark');
  });

  it('loads saved appearance from localStorage on mount', async () => {
    localStorage.setItem('appearance', 'light');

    // Mount a component to trigger onMounted
    const wrapper = mount({
      setup() {
        const { appearance } = useAppearance();
        return { appearance };
      },
      template: '<div>{{ appearance }}</div>',
    });

    await wrapper.vm.$nextTick();

    expect(wrapper.vm.appearance).toBe('light');
  });
});
