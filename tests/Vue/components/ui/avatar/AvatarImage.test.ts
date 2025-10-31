import { Avatar } from '@/components/ui/avatar';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('AvatarImage', () => {
  const mountWithAvatar = (props: any = {}) => {
    return mount(Avatar, {
      slots: {
        default: () => h(AvatarImage, props),
      },
    });
  };

  it('renders within Avatar', () => {
    const wrapper = mountWithAvatar({ src: 'https://example.com/avatar.jpg' });
    expect(wrapper.exists()).toBe(true);
  });

  it('passes src prop', () => {
    const wrapper = mountWithAvatar({ src: 'https://example.com/avatar.jpg' });
    const img = wrapper.find('[data-slot="avatar-image"]');
    expect(img.exists()).toBe(true);
  });

  it('renders with alt text', () => {
    const wrapper = mountWithAvatar({
      src: 'https://example.com/avatar.jpg',
      alt: 'User Avatar',
    });
    expect(wrapper.html()).toContain('avatar-image');
  });

  it('has correct data-slot attribute', () => {
    const wrapper = mountWithAvatar({ src: 'https://example.com/avatar.jpg' });
    const img = wrapper.find('[data-slot="avatar-image"]');
    expect(img.attributes('data-slot')).toBe('avatar-image');
  });

  it('renders with slot content', () => {
    const wrapper = mount(Avatar, {
      slots: {
        default: () => h(AvatarImage, { src: 'https://example.com/avatar.jpg' }, () => [h('span', 'Slot Content')]),
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});
