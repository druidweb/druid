import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { beforeEach, describe, expect, it, vi } from 'vitest';

// Mock fetch globally
global.fetch = vi.fn();

describe('useTwoFactorAuth', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    // Clear the shared state
    const { clearTwoFactorAuthData } = useTwoFactorAuth();
    clearTwoFactorAuthData();
  });

  describe('fetchQrCode', () => {
    it('fetches and stores QR code successfully', async () => {
      const mockResponse = {
        svg: '<svg>QR Code</svg>',
        url: 'otpauth://totp/...',
      };

      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const { fetchQrCode, qrCodeSvg } = useTwoFactorAuth();
      await fetchQrCode();

      expect(qrCodeSvg.value).toBe('<svg>QR Code</svg>');
    });

    it('handles fetch error', async () => {
      (global.fetch as any).mockResolvedValueOnce({
        ok: false,
        status: 500,
      });

      const { fetchQrCode, qrCodeSvg, errors } = useTwoFactorAuth();
      await fetchQrCode();

      expect(qrCodeSvg.value).toBeNull();
      expect(errors.value).toContain('Failed to fetch QR code');
    });

    it('handles network error', async () => {
      (global.fetch as any).mockRejectedValueOnce(new Error('Network error'));

      const { fetchQrCode, qrCodeSvg, errors } = useTwoFactorAuth();
      await fetchQrCode();

      expect(qrCodeSvg.value).toBeNull();
      expect(errors.value).toContain('Failed to fetch QR code');
    });
  });

  describe('fetchSetupKey', () => {
    it('fetches and stores setup key successfully', async () => {
      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => ({ secretKey: 'ABCD1234' }),
      });

      const { fetchSetupKey, manualSetupKey } = useTwoFactorAuth();
      await fetchSetupKey();

      expect(manualSetupKey.value).toBe('ABCD1234');
    });

    it('handles fetch error', async () => {
      (global.fetch as any).mockResolvedValueOnce({
        ok: false,
        status: 500,
      });

      const { fetchSetupKey, manualSetupKey, errors } = useTwoFactorAuth();
      await fetchSetupKey();

      expect(manualSetupKey.value).toBeNull();
      expect(errors.value).toContain('Failed to fetch a setup key');
    });
  });

  describe('fetchRecoveryCodes', () => {
    it('fetches and stores recovery codes successfully', async () => {
      const mockCodes = ['code1', 'code2', 'code3'];

      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockCodes,
      });

      const { fetchRecoveryCodes, recoveryCodesList } = useTwoFactorAuth();
      await fetchRecoveryCodes();

      expect(recoveryCodesList.value).toEqual(mockCodes);
    });

    it('handles fetch error', async () => {
      (global.fetch as any).mockResolvedValueOnce({
        ok: false,
        status: 500,
      });

      const { fetchRecoveryCodes, recoveryCodesList, errors } = useTwoFactorAuth();
      await fetchRecoveryCodes();

      expect(recoveryCodesList.value).toEqual([]);
      expect(errors.value).toContain('Failed to fetch recovery codes');
    });

    it('clears errors before fetching', async () => {
      const { fetchRecoveryCodes, errors } = useTwoFactorAuth();
      errors.value = ['old error'];

      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => [],
      });

      await fetchRecoveryCodes();

      expect(errors.value).toEqual([]);
    });
  });

  describe('fetchSetupData', () => {
    it('fetches both QR code and setup key successfully', async () => {
      (global.fetch as any)
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ svg: '<svg>QR</svg>', url: 'otpauth://...' }),
        })
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ secretKey: 'KEY123' }),
        });

      const { fetchSetupData, qrCodeSvg, manualSetupKey } = useTwoFactorAuth();
      await fetchSetupData();

      expect(qrCodeSvg.value).toBe('<svg>QR</svg>');
      expect(manualSetupKey.value).toBe('KEY123');
    });

    it('clears errors before fetching', async () => {
      const { fetchSetupData, errors } = useTwoFactorAuth();
      errors.value = ['old error'];

      (global.fetch as any)
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ svg: '<svg>QR</svg>', url: 'otpauth://...' }),
        })
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ secretKey: 'KEY123' }),
        });

      await fetchSetupData();

      expect(errors.value).toEqual([]);
    });

    it('handles partial failure', async () => {
      (global.fetch as any)
        .mockResolvedValueOnce({
          ok: false,
          status: 500,
        })
        .mockResolvedValueOnce({
          ok: false,
          status: 500,
        });

      const { fetchSetupData, qrCodeSvg, manualSetupKey } = useTwoFactorAuth();
      await fetchSetupData();

      expect(qrCodeSvg.value).toBeNull();
      expect(manualSetupKey.value).toBeNull();
    });
  });

  describe('clearSetupData', () => {
    it('clears QR code and setup key', async () => {
      (global.fetch as any)
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ svg: '<svg>QR</svg>', url: 'otpauth://...' }),
        })
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ secretKey: 'KEY123' }),
        });

      const { fetchSetupData, clearSetupData, qrCodeSvg, manualSetupKey } = useTwoFactorAuth();
      await fetchSetupData();

      expect(qrCodeSvg.value).toBe('<svg>QR</svg>');
      expect(manualSetupKey.value).toBe('KEY123');

      clearSetupData();

      expect(qrCodeSvg.value).toBeNull();
      expect(manualSetupKey.value).toBeNull();
    });

    it('clears errors when clearing setup data', async () => {
      const { clearSetupData, errors } = useTwoFactorAuth();
      errors.value = ['error1', 'error2'];

      clearSetupData();

      expect(errors.value).toEqual([]);
    });
  });

  describe('clearErrors', () => {
    it('clears all errors', () => {
      const { clearErrors, errors } = useTwoFactorAuth();
      errors.value = ['error1', 'error2', 'error3'];

      clearErrors();

      expect(errors.value).toEqual([]);
    });
  });

  describe('clearTwoFactorAuthData', () => {
    it('clears all data including recovery codes', async () => {
      (global.fetch as any)
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ svg: '<svg>QR</svg>', url: 'otpauth://...' }),
        })
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ secretKey: 'KEY123' }),
        })
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ['code1', 'code2'],
        });

      const { fetchSetupData, fetchRecoveryCodes, clearTwoFactorAuthData, qrCodeSvg, manualSetupKey, recoveryCodesList, errors } = useTwoFactorAuth();

      await fetchSetupData();
      await fetchRecoveryCodes();

      errors.value = ['error'];

      clearTwoFactorAuthData();

      expect(qrCodeSvg.value).toBeNull();
      expect(manualSetupKey.value).toBeNull();
      expect(recoveryCodesList.value).toEqual([]);
      expect(errors.value).toEqual([]);
    });
  });

  describe('hasSetupData', () => {
    it('returns true when both QR code and setup key are present', async () => {
      (global.fetch as any)
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ svg: '<svg>QR</svg>', url: 'otpauth://...' }),
        })
        .mockResolvedValueOnce({
          ok: true,
          json: async () => ({ secretKey: 'KEY123' }),
        });

      const { fetchSetupData, hasSetupData } = useTwoFactorAuth();
      await fetchSetupData();

      expect(hasSetupData.value).toBe(true);
    });

    it('returns false when QR code is missing', async () => {
      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => ({ secretKey: 'KEY123' }),
      });

      const { fetchSetupKey, hasSetupData } = useTwoFactorAuth();
      await fetchSetupKey();

      expect(hasSetupData.value).toBe(false);
    });

    it('returns false when setup key is missing', async () => {
      (global.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => ({ svg: '<svg>QR</svg>', url: 'otpauth://...' }),
      });

      const { fetchQrCode, hasSetupData } = useTwoFactorAuth();
      await fetchQrCode();

      expect(hasSetupData.value).toBe(false);
    });

    it('returns false when both are missing', () => {
      const { hasSetupData } = useTwoFactorAuth();

      expect(hasSetupData.value).toBe(false);
    });
  });
});
