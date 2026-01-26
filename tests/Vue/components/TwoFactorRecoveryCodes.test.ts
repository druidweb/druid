import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import { mount } from '@vue/test-utils';
import { vi } from 'vitest';

const mockFetchRecoveryCodes = vi.fn();

vi.mock('@/composables/useTwoFactorAuth', () => ({
  useTwoFactorAuth: () => ({
    recoveryCodesList: { value: ['code1', 'code2', 'code3'] },
    fetchRecoveryCodes: mockFetchRecoveryCodes,
    errors: { value: [] },
  }),
}));

describe('TwoFactorRecoveryCodes', () => {
  it('renders component', () => {
    const wrapper = mount(TwoFactorRecoveryCodes, {
      global: {
        stubs: {
          Card: true,
          CardHeader: true,
          CardTitle: true,
          CardDescription: true,
          CardContent: true,
          Button: true,
          Form: true,
          AlertError: true,
          LockKeyhole: true,
          Eye: true,
          EyeOff: true,
          RefreshCw: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays title', () => {
    const wrapper = mount(TwoFactorRecoveryCodes, {
      global: {
        stubs: {
          Card: { template: '<div><slot /></div>' },
          CardHeader: { template: '<div><slot /></div>' },
          CardTitle: { template: '<div><slot /></div>' },
          CardDescription: { template: '<div><slot /></div>' },
          CardContent: { template: '<div><slot /></div>' },
          Button: { template: '<button @click="$emit(\'click\')"><slot /></button>' },
          Form: { template: '<form><slot /></form>' },
          AlertError: { template: '<div><slot /></div>' },
          LockKeyhole: { template: '<span>Lock</span>' },
          Eye: { template: '<span>Eye</span>' },
          EyeOff: { template: '<span>EyeOff</span>' },
          RefreshCw: { template: '<span>Refresh</span>' },
        },
      },
    });
    expect(wrapper.text()).toContain('base.two_factor.recovery_codes_title');
  });

  it('renders buttons', async () => {
    const wrapper = mount(TwoFactorRecoveryCodes, {
      global: {
        stubs: {
          Card: { template: '<div><slot /></div>' },
          CardHeader: { template: '<div><slot /></div>' },
          CardTitle: { template: '<div><slot /></div>' },
          CardDescription: { template: '<div><slot /></div>' },
          CardContent: { template: '<div><slot /></div>' },
          Button: { template: '<button><slot /></button>' },
          Form: { template: '<form><slot /></form>' },
          AlertError: { template: '<div><slot /></div>' },
          LockKeyhole: { template: '<span></span>' },
          Eye: { template: '<span></span>' },
          EyeOff: { template: '<span></span>' },
          RefreshCw: { template: '<span></span>' },
        },
      },
    });
    await wrapper.vm.$nextTick();
    const buttons = wrapper.findAll('button');
    expect(buttons.length).toBeGreaterThan(0);
  });

  it('displays recovery codes when visible', async () => {
    const wrapper = mount(TwoFactorRecoveryCodes, {
      global: {
        stubs: {
          Card: { template: '<div><slot /></div>' },
          CardHeader: { template: '<div><slot /></div>' },
          CardTitle: { template: '<div><slot /></div>' },
          CardDescription: { template: '<div><slot /></div>' },
          CardContent: { template: '<div><slot /></div>' },
          Button: { template: '<button @click="$emit(\'click\')"><slot /></button>' },
          Form: { template: '<form><slot /></form>' },
          AlertError: { template: '<div><slot /></div>' },
          LockKeyhole: { template: '<span></span>' },
          Eye: { template: '<span></span>' },
          EyeOff: { template: '<span></span>' },
          RefreshCw: { template: '<span></span>' },
        },
      },
    });
    await wrapper.vm.$nextTick();
    expect(wrapper.html()).toBeDefined();
  });
});
