import SidebarMenuAction from '@/components/ui/sidebar/SidebarMenuAction.vue';
import { mount } from '@vue/test-utils';

describe('SidebarMenuAction', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarMenuAction);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SidebarMenuAction, {
      slots: {
        default: 'Action',
      },
    });
    expect(wrapper.text()).toContain('Action');
  });

  it('applies custom class', () => {
    const wrapper = mount(SidebarMenuAction, {
      props: {
        class: 'custom-action',
      },
    });
    expect(wrapper.classes()).toContain('custom-action');
  });

  it('accepts asChild prop', () => {
    const wrapper = mount(SidebarMenuAction, {
      props: {
        asChild: true,
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});

