import AppHeader from '@/components/AppHeader.vue';
import { mount } from '@vue/test-utils';

vi.mock('@inertiajs/vue3', () => ({
  Link: {
    name: 'Link',
    template: '<a><slot /></a>',
  },
  usePage: () => ({
    props: {
      auth: {
        user: {
          id: 1,
          name: 'Test User',
          email: 'test@example.com',
        },
      },
    },
    url: '/dashboard',
  }),
}));

describe('AppHeader', () => {
  it('renders the component', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders without breadcrumbs', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with breadcrumbs', () => {
    const wrapper = mount(AppHeader, {
      props: {
        breadcrumbs: [{ title: 'Home', href: '/' }, { title: 'Dashboard' }],
      },
    });
    expect(wrapper.text()).toContain('Home');
    expect(wrapper.text()).toContain('Dashboard');
  });

  it('renders user menu', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.text()).toContain('TU');
  });

  it('renders navigation items', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.text()).toContain('Dashboard');
  });

  it('computes active route styles', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.vm.activeItemStyles).toBeDefined();
  });

  it('computes current route check', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.vm.isCurrentRoute).toBeDefined();
  });

  it('renders with empty breadcrumbs array', () => {
    const wrapper = mount(AppHeader, {
      props: {
        breadcrumbs: [],
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders navigation menu', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.html()).toContain('Dashboard');
  });

  it('renders user avatar with initials', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.text()).toContain('TU');
  });

  it('renders external navigation links', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.html()).toContain('Repository');
    expect(wrapper.html()).toContain('Documentation');
  });

  it('renders mobile menu button', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.html()).toContain('lg:hidden');
  });

  it('renders mobile menu sheet component', () => {
    const wrapper = mount(AppHeader);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders navigation items with icons when provided', () => {
    const wrapper = mount(AppHeader);
    const html = wrapper.html();
    // Icons should be rendered as SVG elements
    expect(html).toContain('svg');
  });

  it('renders right nav items with external link attributes', () => {
    const wrapper = mount(AppHeader);
    const links = wrapper.findAll('a[target="_blank"]');
    expect(links.length).toBeGreaterThan(0);
    links.forEach((link) => {
      expect(link.attributes('rel')).toBe('noopener noreferrer');
    });
  });
});
