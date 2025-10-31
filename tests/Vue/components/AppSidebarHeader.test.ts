import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';

vi.mock('@inertiajs/vue3', () => ({
  Link: {
    name: 'Link',
    template: '<a><slot /></a>',
  },
}));

describe('AppSidebarHeader', () => {
  const mountWithProvider = (props: any = {}) => {
    return mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () => h(AppSidebarHeader, props),
      },
      global: {
        components: {
          Link,
        },
      },
    });
  };

  it('renders the component', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders header element', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.find('header').exists()).toBe(true);
  });

  it('renders SidebarTrigger button', () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('button[data-slot="sidebar-trigger"]');
    expect(button.exists()).toBe(true);
  });

  it('renders breadcrumbs when provided', () => {
    const breadcrumbs: BreadcrumbItemType[] = [
      { label: 'Home', href: '/' },
      { label: 'Dashboard', href: '/dashboard' },
    ];

    const wrapper = mountWithProvider({ breadcrumbs });
    const nav = wrapper.find('nav[aria-label="breadcrumb"]');
    expect(nav.exists()).toBe(true);
  });

  it('does not render breadcrumbs when empty', () => {
    const wrapper = mountWithProvider({ breadcrumbs: [] });
    const nav = wrapper.find('nav[aria-label="breadcrumb"]');
    expect(nav.exists()).toBe(false);
  });
});
