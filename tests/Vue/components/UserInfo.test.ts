import UserInfo from '@/components/UserInfo.vue';
import type { User } from '@/types';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('UserInfo', () => {
  const mockUser: User = {
    id: 1,
    name: 'John Doe',
    email: 'john@example.com',
    avatar: 'https://example.com/avatar.jpg',
    email_verified_at: null,
    two_factor_enabled: false,
    created_at: '2024-01-01',
    updated_at: '2024-01-01',
  };

  it('renders user name', () => {
    const wrapper = mount(UserInfo, {
      props: {
        user: mockUser,
      },
    });

    expect(wrapper.text()).toContain('John Doe');
  });

  it('renders user email when showEmail is true', () => {
    const wrapper = mount(UserInfo, {
      props: {
        user: mockUser,
        showEmail: true,
      },
    });

    expect(wrapper.text()).toContain('john@example.com');
  });

  it('does not render user email when showEmail is false', () => {
    const wrapper = mount(UserInfo, {
      props: {
        user: mockUser,
        showEmail: false,
      },
    });

    expect(wrapper.text()).not.toContain('john@example.com');
  });

  it('does not render user email by default', () => {
    const wrapper = mount(UserInfo, {
      props: {
        user: mockUser,
      },
    });

    expect(wrapper.text()).not.toContain('john@example.com');
  });

  it('renders avatar image when user has avatar', () => {
    const wrapper = mount(UserInfo, {
      props: {
        user: mockUser,
      },
    });

    const img = wrapper.find('img');
    expect(img.exists()).toBe(true);
    expect(img.attributes('src')).toBe('https://example.com/avatar.jpg');
    expect(img.attributes('alt')).toBe('John Doe');
  });

  it('renders initials fallback when user has no avatar', () => {
    const userWithoutAvatar: User = {
      ...mockUser,
      avatar: null,
    };

    const wrapper = mount(UserInfo, {
      props: {
        user: userWithoutAvatar,
      },
    });

    const img = wrapper.find('img');
    expect(img.exists()).toBe(false);
    expect(wrapper.text()).toContain('JD');
  });

  it('renders initials fallback when avatar is empty string', () => {
    const userWithEmptyAvatar: User = {
      ...mockUser,
      avatar: '',
    };

    const wrapper = mount(UserInfo, {
      props: {
        user: userWithEmptyAvatar,
      },
    });

    const img = wrapper.find('img');
    expect(img.exists()).toBe(false);
    expect(wrapper.text()).toContain('JD');
  });

  it('applies correct styling to name', () => {
    const wrapper = mount(UserInfo, {
      props: {
        user: mockUser,
      },
    });

    const div = wrapper.find('div.grid');
    const nameSpan = div.findAll('span')[0];
    expect(nameSpan.classes()).toContain('truncate');
    expect(nameSpan.classes()).toContain('font-medium');
  });

  it('applies correct styling to email', () => {
    const wrapper = mount(UserInfo, {
      props: {
        user: mockUser,
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
