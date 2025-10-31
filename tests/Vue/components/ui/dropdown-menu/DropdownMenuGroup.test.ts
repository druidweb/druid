import DropdownMenuGroup from '@/components/ui/dropdown-menu/DropdownMenuGroup.vue';
import { mount } from '@vue/test-utils';

describe('DropdownMenuGroup', () => {
  it('renders component', () => {
    const wrapper = mount(DropdownMenuGroup);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(DropdownMenuGroup, {
      slots: {
        default: '<div>Group Content</div>',
      },
    });
    expect(wrapper.html()).toContain('Group Content');
  });
});

