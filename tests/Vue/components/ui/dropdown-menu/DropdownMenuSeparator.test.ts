import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import { mount } from '@vue/test-utils';

describe('DropdownMenuSeparator', () => {
  it('renders component', () => {
    const wrapper = mount(DropdownMenuSeparator);
    expect(wrapper.exists()).toBe(true);
  });

  it('applies custom class', () => {
    const wrapper = mount(DropdownMenuSeparator, {
      props: {
        class: 'custom-separator',
      },
    });
    expect(wrapper.classes()).toContain('custom-separator');
  });
});

