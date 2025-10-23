import { getInitials } from '@/composables/useInitials';
import { describe, expect, it } from 'vitest';

describe('useInitials', () => {
  describe('getInitials', () => {
    it('returns empty string for undefined', () => {
      expect(getInitials(undefined)).toBe('');
    });

    it('returns empty string for empty string', () => {
      expect(getInitials('')).toBe('');
    });

    it('returns empty string for whitespace only', () => {
      expect(getInitials('   ')).toBe('');
    });

    it('returns single uppercase letter for single name', () => {
      expect(getInitials('John')).toBe('J');
    });

    it('returns single uppercase letter for single lowercase name', () => {
      expect(getInitials('madonna')).toBe('M');
    });

    it('returns first and last initials for two names', () => {
      expect(getInitials('John Doe')).toBe('JD');
    });

    it('returns first and last initials for multiple names', () => {
      expect(getInitials('John Michael Doe')).toBe('JD');
    });

    it('handles extra whitespace between names', () => {
      expect(getInitials('John   Doe')).toBe('JD');
    });

    it('handles leading and trailing whitespace', () => {
      expect(getInitials('  Jane Smith  ')).toBe('JS');
    });

    it('handles lowercase names', () => {
      expect(getInitials('john doe')).toBe('JD');
    });

    it('handles mixed case names', () => {
      expect(getInitials('jOhN dOe')).toBe('JD');
    });
  });
});
