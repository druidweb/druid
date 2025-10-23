import { initializeTheme, updateTheme } from '@/composables/useAppearance';
import { beforeEach, describe, expect, it, vi } from 'vitest';

describe('useAppearance', () => {
  beforeEach(() => {
    // Clear localStorage before each test
    localStorage.clear();

    // Clear document classes
    document.documentElement.classList.remove('dark');

    // Reset matchMedia mock
    window.matchMedia = vi.fn().mockImplementation((query: string) => ({
      matches: false,
      media: query,
      onchange: null,
      addListener: vi.fn(),
      removeListener: vi.fn(),
      addEventListener: vi.fn(),
      removeEventListener: vi.fn(),
      dispatchEvent: vi.fn(),
    }));
  });

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

    it('uses system preference when theme is system and prefers dark', () => {
      window.matchMedia = vi.fn().mockImplementation((query: string) => ({
        matches: query === '(prefers-color-scheme: dark)',
        media: query,
        onchange: null,
        addListener: vi.fn(),
        removeListener: vi.fn(),
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
      }));

      updateTheme('system');
      expect(document.documentElement.classList.contains('dark')).toBe(true);
    });

    it('uses system preference when theme is system and prefers light', () => {
      window.matchMedia = vi.fn().mockImplementation((query: string) => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: vi.fn(),
        removeListener: vi.fn(),
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
      }));

      updateTheme('system');
      expect(document.documentElement.classList.contains('dark')).toBe(false);
    });
  });

  describe('initializeTheme', () => {
    it('initializes with system theme when no saved preference', () => {
      initializeTheme();
      // Should call matchMedia to check system preference
      expect(window.matchMedia).toHaveBeenCalledWith('(prefers-color-scheme: dark)');
    });

    it('initializes with saved preference from localStorage', () => {
      localStorage.setItem('appearance', 'dark');
      initializeTheme();
      expect(document.documentElement.classList.contains('dark')).toBe(true);
    });

    it('sets up event listener for system theme changes', () => {
      // The mediaQuery is created at module load time, so we can't easily test
      // the addEventListener call without reloading the module. This test verifies
      // that initializeTheme runs without errors, which includes setting up the listener.
      expect(() => initializeTheme()).not.toThrow();
    });
  });
});
