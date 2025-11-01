import { config, type VueWrapper } from '@vue/test-utils';
import { beforeEach, expect, vi } from 'vitest';

// Mock window.matchMedia
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation((query) => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(), // deprecated
    removeListener: vi.fn(), // deprecated
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
});

// Configure Vue Test Utils globally
config.global.stubs = {
  // Stub Inertia components
  Link: {
    template: '<a><slot /></a>',
  },
  Head: {
    template: '<div><slot /></div>',
  },
};

// Reset DOM state before each test
beforeEach(() => {
  // Clear localStorage
  if (typeof localStorage !== 'undefined') {
    localStorage.clear();
  }

  // Clear cookies
  if (typeof document !== 'undefined') {
    document.cookie = '';
  }

  // Reset document classes
  if (typeof document !== 'undefined' && document.documentElement) {
    document.documentElement.className = '';
  }
});

/**
 * Helper to test translation rendering in components
 * @param wrapper - Vue wrapper instance
 * @param _translationKey - The translation key to look for
 * @param expectedText - The expected translated text
 */
export function expectTranslation(wrapper: VueWrapper, _translationKey: string, expectedText: string) {
  const html = wrapper.html();
  expect(html).toContain(expectedText);
}

/**
 * Helper to snapshot template-only components
 * @param wrapper - Vue wrapper instance
 */
export function snapshotTemplate(wrapper: VueWrapper) {
  expect(wrapper.html()).toMatchSnapshot();
}
