import NavMain from '@/components/NavMain.vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { mount } from '@vue/test-utils';
import { LayoutGrid } from 'lucide-vue-next';

vi.mock('@inertiajs/vue3', () => ({
  Link: {
    name: 'Link',
    template: '<a><slot /></a>',
  },
  usePage: () => ({
    url: '/dashboard',
  }),
}));

describe('NavMain', () => {
  const mockItems: NavItem[] = [
    {
      title: 'Dashboard',
      href: '/dashboard',
      icon: LayoutGrid,
    },
  ];

  const mountWithProvider = (props: any) => {
    return mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () => h(NavMain, props),
      },
      global: {
        components: {
          Link,
        },
      },
    });
  };

  it('renders all nav items', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    expect(wrapper.text()).toContain('Dashboard');
  });

  it('renders group label', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    expect(wrapper.text()).toContain('Platform');
  });

  it('renders item titles', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    expect(wrapper.text()).toContain('Dashboard');
  });
});
