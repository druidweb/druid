import SidebarMenuBadge from '@/components/ui/sidebar/SidebarMenuBadge.vue';
import { mount } from '@vue/test-utils';

describe('SidebarMenuBadge', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarMenuBadge);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SidebarMenuBadge, {
      slots: {
        default: '5',
      },
    });
    expect(wrapper.text()).toContain('5');
  });

  it('applies custom class', () => {
    const wrapper = mount(SidebarMenuBadge, {
      props: {
        class: 'custom-badge',
      },
    });
    expect(wrapper.classes()).toContain('custom-badge');
  });
});

