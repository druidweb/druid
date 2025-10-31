import SidebarMenuSub from '@/components/ui/sidebar/SidebarMenuSub.vue';
import { mount } from '@vue/test-utils';

describe('SidebarMenuSub', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarMenuSub);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SidebarMenuSub, {
      slots: {
        default: '<div>Sub Menu</div>',
      },
    });
    expect(wrapper.html()).toContain('Sub Menu');
  });

  it('applies custom class', () => {
    const wrapper = mount(SidebarMenuSub, {
      props: {
        class: 'custom-sub',
      },
    });
    expect(wrapper.classes()).toContain('custom-sub');
  });
});

