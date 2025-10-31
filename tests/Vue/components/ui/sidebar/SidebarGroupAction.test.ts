import SidebarGroupAction from '@/components/ui/sidebar/SidebarGroupAction.vue';
import { mount } from '@vue/test-utils';

describe('SidebarGroupAction', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarGroupAction);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SidebarGroupAction, {
      slots: {
        default: 'Action',
      },
    });
    expect(wrapper.text()).toContain('Action');
  });

  it('applies custom class', () => {
    const wrapper = mount(SidebarGroupAction, {
      props: {
        class: 'custom-action',
      },
    });
    expect(wrapper.classes()).toContain('custom-action');
  });
});

