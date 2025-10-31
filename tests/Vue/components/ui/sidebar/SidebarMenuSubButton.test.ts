import SidebarMenuSubButton from '@/components/ui/sidebar/SidebarMenuSubButton.vue';
import { mount } from '@vue/test-utils';

describe('SidebarMenuSubButton', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarMenuSubButton);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SidebarMenuSubButton, {
      slots: {
        default: 'Sub Button',
      },
    });
    expect(wrapper.text()).toContain('Sub Button');
  });

  it('applies custom class', () => {
    const wrapper = mount(SidebarMenuSubButton, {
      props: {
        class: 'custom-sub-button',
      },
    });
    expect(wrapper.classes()).toContain('custom-sub-button');
  });

  it('accepts asChild prop', () => {
    const wrapper = mount(SidebarMenuSubButton, {
      props: {
        asChild: true,
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});

