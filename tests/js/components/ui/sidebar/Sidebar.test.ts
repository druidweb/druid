import { Sidebar } from '@/components/ui/sidebar';
import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

// Mock the composables
vi.mock('@/components/ui/sidebar/utils', () => ({
  useSidebar: vi.fn(() => ({
    state: 'expanded',
    open: true,
    setOpen: vi.fn(),
    openMobile: false,
    setOpenMobile: vi.fn(),
    isMobile: false,
    toggleSidebar: vi.fn(),
  })),
}));

describe('Sidebar', () => {
  it('renders correctly', () => {
    const wrapper = mount(Sidebar, {
      props: {
        side: 'left',
        variant: 'sidebar',
        collapsible: 'offcanvas',
      },
      slots: {
        default: '<div>Sidebar content</div>',
      },
    });

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.text()).toContain('Sidebar content');
  });

  it('applies correct CSS classes', () => {
    const wrapper = mount(Sidebar, {
      props: {
        side: 'left',
        variant: 'sidebar',
        collapsible: 'offcanvas',
      },
    });

    expect(wrapper.classes()).toContain('group');
  });

  it('handles different side props', () => {
    const wrapper = mount(Sidebar, {
      props: {
        side: 'right',
        variant: 'sidebar',
        collapsible: 'offcanvas',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('handles different variant props', () => {
    const wrapper = mount(Sidebar, {
      props: {
        side: 'left',
        variant: 'floating',
        collapsible: 'offcanvas',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('handles different collapsible props', () => {
    const wrapper = mount(Sidebar, {
      props: {
        side: 'left',
        variant: 'sidebar',
        collapsible: 'icon',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });
});
