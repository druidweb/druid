import SidebarMenuSubItem from '@/components/ui/sidebar/SidebarMenuSubItem.vue';
import { mount } from '@vue/test-utils';

describe('SidebarMenuSubItem', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarMenuSubItem);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SidebarMenuSubItem, {
      slots: {
        default: '<div>Sub Item</div>',
      },
    });
    expect(wrapper.html()).toContain('Sub Item');
  });
});

