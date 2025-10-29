import { config } from '@vue/test-utils';
import { beforeEach } from 'vitest';

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
