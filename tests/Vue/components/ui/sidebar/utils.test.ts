import {
  SIDEBAR_COOKIE_MAX_AGE,
  SIDEBAR_COOKIE_NAME,
  SIDEBAR_KEYBOARD_SHORTCUT,
  SIDEBAR_WIDTH,
  SIDEBAR_WIDTH_ICON,
  SIDEBAR_WIDTH_MOBILE,
} from '@/components/ui/sidebar/utils';
import { describe, expect, it } from 'vitest';

describe('Sidebar Constants', () => {
  it('has correct cookie name', () => {
    expect(SIDEBAR_COOKIE_NAME).toBe('sidebar_state');
  });

  it('has correct cookie max age', () => {
    expect(SIDEBAR_COOKIE_MAX_AGE).toBe(60 * 60 * 24 * 7); // 7 days in seconds
  });

  it('has correct sidebar width', () => {
    expect(SIDEBAR_WIDTH).toBe('16rem');
  });

  it('has correct mobile sidebar width', () => {
    expect(SIDEBAR_WIDTH_MOBILE).toBe('18rem');
  });

  it('has correct icon sidebar width', () => {
    expect(SIDEBAR_WIDTH_ICON).toBe('3rem');
  });

  it('has correct keyboard shortcut', () => {
    expect(SIDEBAR_KEYBOARD_SHORTCUT).toBe('b');
  });
});

