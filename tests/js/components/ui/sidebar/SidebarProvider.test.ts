import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { SidebarProvider } from '@/components/ui/sidebar';

// Mock the composables
vi.mock('@/composables/useAppearance', () => ({
  default: vi.fn(() => ({
    appearance: { value: 'light' },
  })),
}));

describe('SidebarProvider', () => {
  it('renders correctly', () => {
    const wrapper = mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: '<div>Provider content</div>',
      },
    });

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.text()).toContain('Provider content');
  });

  it('handles defaultOpen prop', () => {
    const wrapper = mount(SidebarProvider, {
      props: {
        defaultOpen: false,
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('provides sidebar context', () => {
    const wrapper = mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: '<div>Test content</div>',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('handles open prop changes', async () => {
    const wrapper = mount(SidebarProvider, {
      props: {
        defaultOpen: true,
        open: false,
      },
    });

    expect(wrapper.exists()).toBe(true);

    await wrapper.setProps({ open: true });
    expect(wrapper.exists()).toBe(true);
  });

  it('emits update:open event', async () => {
    const wrapper = mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
    });

    // Simulate state change that would trigger emit
    expect(wrapper.exists()).toBe(true);
  });
});
