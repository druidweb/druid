import UserMenuContent from '@/components/UserMenuContent.vue';
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
      teams: {
        hasTeamFeatures: false,
        hasApiFeatures: false,
        canCreateTeams: false,
        managesProfilePhotos: false,
      },
    },
  }),
}));

describe('UserMenuContent', () => {
  it('renders component', () => {
    const wrapper = mount(UserMenuContent, {
      global: {
        stubs: {
          UserInfo: true,
          DropdownMenuLabel: true,
          DropdownMenuSeparator: true,
          DropdownMenuGroup: true,
          DropdownMenuItem: true,
          Link: true,
          Settings: true,
          LogOut: true,
          KeyRound: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays settings option', () => {
    const wrapper = mount(UserMenuContent, {
      global: {
        stubs: {
          UserInfo: true,
          DropdownMenuLabel: { template: '<div><slot /></div>' },
          DropdownMenuSeparator: { template: '<div />' },
          DropdownMenuGroup: { template: '<div><slot /></div>' },
          DropdownMenuItem: { template: '<div><slot /></div>' },
          Link: { template: '<a><slot /></a>' },
          Settings: { template: '<span>Settings Icon</span>' },
          LogOut: { template: '<span>Logout Icon</span>' },
          KeyRound: { template: '<span>Key Icon</span>' },
        },
      },
    });
    expect(wrapper.text()).toContain('base.nav.settings');
  });

  it('displays logout option', () => {
    const wrapper = mount(UserMenuContent, {
      global: {
        stubs: {
          UserInfo: true,
          DropdownMenuLabel: { template: '<div><slot /></div>' },
          DropdownMenuSeparator: { template: '<div />' },
          DropdownMenuGroup: { template: '<div><slot /></div>' },
          DropdownMenuItem: { template: '<div><slot /></div>' },
          Link: { template: '<a :data-test="$attrs[\'data-test\']"><slot /></a>' },
          Settings: { template: '<span>Settings Icon</span>' },
          LogOut: { template: '<span>Logout Icon</span>' },
          KeyRound: { template: '<span>Key Icon</span>' },
        },
      },
    });
    expect(wrapper.text()).toContain('base.auth.log_out');
    const logoutButton = wrapper.find('[data-test="logout-button"]');
    expect(logoutButton.exists()).toBe(true);
  });
});
