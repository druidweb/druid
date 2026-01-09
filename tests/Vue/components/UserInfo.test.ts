import UserInfo from '@/components/UserInfo.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

const mockUser = {
  id: 1,
  name: 'John Doe',
  email: 'john@example.com',
  profile_photo_url: 'https://example.com/avatar.jpg',
  profile_photo_path: '/path/to/avatar.jpg',
  email_verified_at: null,
  two_factor_enabled: false,
  created_at: '2024-01-01',
  updated_at: '2024-01-01',
};

vi.mock('@inertiajs/vue3', () => ({
  usePage: () => ({
    props: {
      auth: {
        user: mockUser,
      },
    },
  }),
}));

describe('UserInfo', () => {
  it('renders user name', () => {
    const wrapper = mount(UserInfo);

    expect(wrapper.text()).toContain('John Doe');
  });

  it('renders user email when showEmail is true', () => {
    const wrapper = mount(UserInfo, {
      props: {
        showEmail: true,
      },
    });

    expect(wrapper.text()).toContain('john@example.com');
  });

  it('does not render user email when showEmail is false', () => {
    const wrapper = mount(UserInfo, {
      props: {
        showEmail: false,
      },
    });

    expect(wrapper.text()).not.toContain('john@example.com');
  });

  it('does not render user email by default', () => {
    const wrapper = mount(UserInfo);

    expect(wrapper.text()).not.toContain('john@example.com');
  });

  it('renders avatar component', () => {
    const wrapper = mount(UserInfo);

    const avatar = wrapper.find('[data-slot="avatar"]');
    expect(avatar.exists()).toBe(true);
  });

  it('applies correct styling to name', () => {
    const wrapper = mount(UserInfo);

    const div = wrapper.find('div.grid');
    const nameSpan = div.findAll('span')[0];
    expect(nameSpan.classes()).toContain('truncate');
    expect(nameSpan.classes()).toContain('font-medium');
  });

  it('applies correct styling to email', () => {
    const wrapper = mount(UserInfo, {
      props: {
        showEmail: true,
      },
    });

    const div = wrapper.find('div.grid');
    const emailSpan = div.findAll('span')[1];
    expect(emailSpan.classes()).toContain('truncate');
    expect(emailSpan.classes()).toContain('text-xs');
    expect(emailSpan.classes()).toContain('text-muted-foreground');
  });
});
