import { cn } from '@/lib/utils';
import { describe, expect, it } from 'vitest';

describe('utils', () => {
  describe('cn', () => {
    it('merges class names', () => {
      expect(cn('foo', 'bar')).toBe('foo bar');
    });

    it('handles conditional classes', () => {
      expect(cn('foo', false && 'bar', 'baz')).toBe('foo baz');
    });

    it('merges Tailwind classes correctly', () => {
      // twMerge should handle conflicting Tailwind classes
      expect(cn('px-2 py-1', 'px-4')).toBe('py-1 px-4');
    });

    it('handles arrays of classes', () => {
      expect(cn(['foo', 'bar'])).toBe('foo bar');
    });

    it('handles objects with boolean values', () => {
      expect(cn({ foo: true, bar: false, baz: true })).toBe('foo baz');
    });

    it('handles undefined and null', () => {
      expect(cn('foo', undefined, null, 'bar')).toBe('foo bar');
    });

    it('handles empty input', () => {
      expect(cn()).toBe('');
    });

    it('handles duplicate classes', () => {
      // clsx doesn't deduplicate, it just merges
      expect(cn('foo', 'foo')).toBe('foo foo');
    });

    it('handles complex Tailwind merge scenarios', () => {
      // Later classes should override earlier ones for the same property
      expect(cn('text-red-500', 'text-blue-500')).toBe('text-blue-500');
    });
  });
});
