import AppSidebar from '@/components/AppSidebar.vue';
import { SidebarProvider } from '@/components/ui/sidebar';
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
          current_team: { id: 1, name: 'Test Team' },
          all_teams: [{ id: 1, name: 'Test Team' }],
        },
      },
      teams: {
        hasTeamFeatures: false,
        hasApiFeatures: false,
        canCreateTeams: false,
        managesProfilePhotos: false,
      },
    },
  }),
  router: {
    flushAll: vi.fn(),
  },
}));

describe('AppSidebar', () => {
  const mountWithProvider = () => {
    return mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () => h(AppSidebar),
      },
    });
  };

  it('renders the component', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders sidebar header', () => {
    const wrapper = mountWithProvider();
    const sidebar = wrapper.find('[data-slot="sidebar"]');
    expect(sidebar.exists()).toBe(true);
  });

  it('renders NavMain with main nav items', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.text()).toContain('Dashboard');
  });

  it('renders NavFooter with footer nav items', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.text()).toContain('Github Repo');
    expect(wrapper.text()).toContain('Documentation');
  });

  it('renders NavUser', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.text()).toContain('Test User');
  });
});
