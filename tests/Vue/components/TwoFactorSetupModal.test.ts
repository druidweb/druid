import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { mount } from '@vue/test-utils';
import { vi } from 'vitest';

vi.mock('@/composables/useTwoFactorAuth', () => ({
  useTwoFactorAuth: () => ({
    qrCodeSvg: { value: '<svg></svg>' },
    manualSetupKey: { value: 'TEST123' },
    clearSetupData: vi.fn(),
    fetchSetupData: vi.fn(),
    errors: { value: [] },
  }),
}));

vi.mock('@vueuse/core', () => ({
  useClipboard: () => ({
    copy: vi.fn(),
    copied: { value: false },
  }),
}));

describe('TwoFactorSetupModal', () => {
  it('renders component', () => {
    const wrapper = mount(TwoFactorSetupModal, {
      props: {
        requiresConfirmation: false,
        twoFactorEnabled: false,
        isOpen: true,
      },
      global: {
        stubs: {
          Dialog: true,
          DialogContent: true,
          DialogHeader: true,
          DialogTitle: true,
          DialogDescription: true,
          Button: true,
          Form: true,
          PinInput: true,
          PinInputGroup: true,
          PinInputSlot: true,
          AlertError: true,
          InputError: true,
          ScanLine: true,
          Loader2: true,
          Check: true,
          Copy: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays correct title when not enabled', () => {
    const wrapper = mount(TwoFactorSetupModal, {
      props: {
        requiresConfirmation: false,
        twoFactorEnabled: false,
        isOpen: true,
      },
      global: {
        stubs: {
          Dialog: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          Button: true,
          Form: true,
          PinInput: true,
          PinInputGroup: true,
          PinInputSlot: true,
          AlertError: true,
          InputError: true,
          ScanLine: true,
          Loader2: true,
          Check: true,
          Copy: true,
        },
      },
    });
    expect(wrapper.text()).toContain('Enable Two-Factor Authentication');
  });

  it('displays correct title when enabled', () => {
    const wrapper = mount(TwoFactorSetupModal, {
      props: {
        requiresConfirmation: false,
        twoFactorEnabled: true,
        isOpen: true,
      },
      global: {
        stubs: {
          Dialog: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          Button: true,
          Form: true,
          PinInput: true,
          PinInputGroup: true,
          PinInputSlot: true,
          AlertError: true,
          InputError: true,
          ScanLine: true,
          Loader2: true,
          Check: true,
          Copy: true,
        },
      },
    });
    expect(wrapper.text()).toContain('Two-Factor Authentication Enabled');
  });

  it('renders with requiresConfirmation', () => {
    const wrapper = mount(TwoFactorSetupModal, {
      props: {
        requiresConfirmation: true,
        twoFactorEnabled: false,
        isOpen: true,
      },
      global: {
        stubs: {
          Dialog: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          Button: { template: '<button><slot /></button>' },
          Form: { template: '<form><slot /></form>' },
          PinInput: true,
          PinInputGroup: true,
          PinInputSlot: true,
          AlertError: true,
          InputError: true,
          ScanLine: true,
          Loader2: true,
          Check: true,
          Copy: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});
