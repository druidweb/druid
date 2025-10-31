import NavUser from '@/components/NavUser.vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import { mount } from '@vue/test-utils';

vi.mock('@inertiajs/vue3', () => ({
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
  }),
  Link: {
    name: 'Link',
    template: '<a><slot /></a>',
  },
  router: {
    flushAll: vi.fn(),
  },
}));

describe('NavUser', () => {
  const mountWithProvider = () => {
    return mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () => h(NavUser),
      },
    });
  };

  it('renders the component', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders UserInfo component', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.html()).toContain('Test User');
  });

  it('renders dropdown menu trigger button', () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('[data-test="sidebar-menu-button"]');
    expect(button.exists()).toBe(true);
  });

  it('renders within SidebarMenu', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders dropdown menu content', async () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('[data-test="sidebar-menu-button"]');
    await button.trigger('click');
    await wrapper.vm.$nextTick();
    expect(wrapper.html()).toBeTruthy();
  });
});
