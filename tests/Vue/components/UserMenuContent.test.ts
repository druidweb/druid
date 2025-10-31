import UserMenuContent from '@/components/UserMenuContent.vue';
import type { User } from '@/types';
import { mount } from '@vue/test-utils';

const mockUser: User = {
  id: 1,
  name: 'Test User',
  email: 'test@example.com',
  email_verified_at: '2024-01-01',
  created_at: '2024-01-01',
  updated_at: '2024-01-01',
};

describe('UserMenuContent', () => {
  it('renders component', () => {
    const wrapper = mount(UserMenuContent, {
      props: {
        user: mockUser,
      },
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
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays settings option', () => {
    const wrapper = mount(UserMenuContent, {
      props: {
        user: mockUser,
      },
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
        },
      },
    });
    expect(wrapper.text()).toContain('Settings');
  });

  it('displays logout option', () => {
    const wrapper = mount(UserMenuContent, {
      props: {
        user: mockUser,
      },
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
        },
      },
    });
    expect(wrapper.text()).toContain('Log out');
    const logoutButton = wrapper.find('[data-test="logout-button"]');
    expect(logoutButton.exists()).toBe(true);
  });
});

