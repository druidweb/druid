import { getInitials } from '@/composables/useInitials';
import { describe, expect, it } from 'vitest';

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

  it('returns first letter uppercase for single name', () => {
    expect(getInitials('John')).toBe('J');
  });

  it('returns first and last initials for full name', () => {
    expect(getInitials('John Doe')).toBe('JD');
  });

  it('returns first and last initials for three names', () => {
    expect(getInitials('John Michael Doe')).toBe('JD');
  });

  it('handles lowercase names', () => {
    expect(getInitials('john doe')).toBe('JD');
  });

  it('handles mixed case names', () => {
    expect(getInitials('jOhN dOe')).toBe('JD');
  });

  it('handles extra whitespace', () => {
    expect(getInitials('  John   Doe  ')).toBe('JD');
  });

  it('handles single letter names', () => {
    expect(getInitials('J D')).toBe('JD');
  });
});

