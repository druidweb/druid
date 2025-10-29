import { cn, toUrl, urlIsActive } from '@/lib/utils';
import { describe, expect, it } from 'vitest';

describe('cn', () => {
  it('merges class names', () => {
    expect(cn('foo', 'bar')).toBe('foo bar');
  });

  it('handles conditional classes', () => {
    expect(cn('foo', false && 'bar', 'baz')).toBe('foo baz');
  });

  it('merges tailwind classes correctly', () => {
    expect(cn('px-2 py-1', 'px-4')).toBe('py-1 px-4');
  });

  it('handles empty input', () => {
    expect(cn()).toBe('');
  });

  it('handles undefined and null', () => {
    expect(cn('foo', undefined, null, 'bar')).toBe('foo bar');
  });

  it('handles arrays', () => {
    expect(cn(['foo', 'bar'])).toBe('foo bar');
  });

  it('handles objects', () => {
    expect(cn({ foo: true, bar: false, baz: true })).toBe('foo baz');
  });
});

describe('toUrl', () => {
  it('returns string href as-is', () => {
    expect(toUrl('/dashboard')).toBe('/dashboard');
  });

  it('extracts url from object href', () => {
    expect(toUrl({ url: '/settings' })).toBe('/settings');
  });

  it('handles empty object', () => {
    expect(toUrl({} as any)).toBeUndefined();
  });
});

describe('urlIsActive', () => {
  it('returns true when urls match exactly', () => {
    expect(urlIsActive('/dashboard', '/dashboard')).toBe(true);
  });

  it('returns false when urls do not match', () => {
    expect(urlIsActive('/dashboard', '/settings')).toBe(false);
  });

  it('works with string href', () => {
    expect(urlIsActive('/dashboard', '/dashboard')).toBe(true);
  });

  it('works with object href', () => {
    expect(urlIsActive({ url: '/dashboard' }, '/dashboard')).toBe(true);
  });

  it('returns false when object href does not match', () => {
    expect(urlIsActive({ url: '/settings' }, '/dashboard')).toBe(false);
  });
});

