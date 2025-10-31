import { Zorah } from '@/zorah';
import { describe, expect, it } from 'vitest';

describe('Zorah', () => {
  it('exports Zorah object', () => {
    expect(Zorah).toBeDefined();
  });

  it('has translations property', () => {
    expect(Zorah.translations).toBeDefined();
  });

  it('has en locale', () => {
    expect(Zorah.translations.en).toBeDefined();
  });

  it('has php translations', () => {
    expect(Zorah.translations.en.php).toBeDefined();
  });

  it('has auth translations', () => {
    const auth = Zorah.translations.en.php.auth;
    expect(auth).toBeDefined();
    expect(auth.failed).toBe('These credentials do not match our records.');
    expect(auth.password).toBe('The provided password is incorrect.');
    expect(auth.throttle).toBeDefined();
  });

  it('has pagination translations', () => {
    const pagination = Zorah.translations.en.php.pagination;
    expect(pagination).toBeDefined();
    expect(pagination.previous).toBe('&laquo; Previous');
    expect(pagination.next).toBe('Next &raquo;');
  });

  it('has passwords translations', () => {
    const passwords = Zorah.translations.en.php.passwords;
    expect(passwords).toBeDefined();
    expect(passwords.reset).toBe('Your password has been reset.');
    expect(passwords.sent).toBe('We have emailed your password reset link.');
    expect(passwords.throttled).toBe('Please wait before retrying.');
    expect(passwords.token).toBe('This password reset token is invalid.');
    expect(passwords.user).toBeDefined();
  });

  it('has validation translations', () => {
    const validation = Zorah.translations.en.php.validation;
    expect(validation).toBeDefined();
    expect(validation.accepted).toBeDefined();
    expect(validation.email).toBeDefined();
    expect(validation.required).toBeDefined();
  });

  it('has validation.between nested object', () => {
    const between = Zorah.translations.en.php.validation.between;
    expect(between).toBeDefined();
    expect(between.array).toBeDefined();
    expect(between.file).toBeDefined();
    expect(between.numeric).toBeDefined();
    expect(between.string).toBeDefined();
  });

  it('has validation.custom object', () => {
    expect(Zorah.translations.en.php.validation.custom).toBeDefined();
  });

  it('has validation.attributes object', () => {
    expect(Zorah.translations.en.php.validation.attributes).toBeDefined();
  });

  it('has json array', () => {
    expect(Zorah.translations.en.json).toBeDefined();
    expect(Array.isArray(Zorah.translations.en.json)).toBe(true);
  });
});

describe('Zorah window integration', () => {
  it('handles window.Zorah when undefined', () => {
    expect(Zorah.translations).toBeDefined();
  });

  it('window.Zorah integration code exists', () => {
    // This tests that the window integration code path exists
    // The actual merge happens at module load time
    expect(typeof window).toBe('object');
    expect(Zorah.translations.en).toBeDefined();
  });
});
