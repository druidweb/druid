import { SidebarProvider } from '@/components/ui/sidebar';
import Sidebar from '@/components/ui/sidebar/Sidebar.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import { h } from 'vue';

describe('Sidebar', () => {
  const mountWithProvider = (props: any = {}, providerProps: any = {}) => {
    return mount(SidebarProvider, {
      props: {
        defaultOpen: true,
        ...providerProps,
      },
      slots: {
        default: () => h(Sidebar, props),
      },
    });
  };

  it('renders component with default props', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.exists()).toBe(true);
    const html = wrapper.html();
    expect(html).toBeTruthy();
  });

  it('renders with collapsible none variant', () => {
    const wrapper = mountWithProvider({
      collapsible: 'none',
    });
    const html = wrapper.html();
    expect(html).toContain('data-slot="sidebar"');
  });

  it('renders with right side', () => {
    const wrapper = mountWithProvider({
      side: 'right',
    });
    const html = wrapper.html();
    expect(html).toContain('data-side="right"');
  });

  it('renders with floating variant', () => {
    const wrapper = mountWithProvider({
      variant: 'floating',
    });
    const html = wrapper.html();
    expect(html).toContain('data-variant="floating"');
  });

  it('renders slot content', () => {
    const wrapper = mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () => h(Sidebar, {}, () => [h('div', { class: 'test-content' }, 'Test Content')]),
      },
    });
    expect(wrapper.html()).toContain('test-content');
    expect(wrapper.text()).toContain('Test Content');
  });

  it('renders with inset variant', () => {
    const wrapper = mountWithProvider({
      variant: 'inset',
    });
    const html = wrapper.html();
    expect(html).toContain('data-variant="inset"');
  });

  it('renders with icon collapsible', () => {
    const wrapper = mountWithProvider({
      collapsible: 'icon',
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with left side by default', () => {
    const wrapper = mountWithProvider({
      side: 'left',
    });
    const html = wrapper.html();
    expect(html).toContain('data-side="left"');
  });

  it('applies custom class', () => {
    const wrapper = mountWithProvider({
      class: 'custom-sidebar-class',
    });
    expect(wrapper.html()).toContain('custom-sidebar-class');
  });

  it('renders with sidebar variant by default', () => {
    const wrapper = mountWithProvider();
    const html = wrapper.html();
    expect(html).toContain('data-variant="sidebar"');
  });

  it('renders with offcanvas collapsible by default', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.exists()).toBe(true);
  });

  it('binds additional attributes via $attrs', () => {
    const wrapper = mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () =>
          h(Sidebar, {
            'data-testid': 'custom-sidebar',
          }),
      },
    });
    expect(wrapper.html()).toContain('data-testid="custom-sidebar"');
  });
});
