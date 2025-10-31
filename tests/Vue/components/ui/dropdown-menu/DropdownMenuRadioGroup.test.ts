import DropdownMenuRadioGroup from '@/components/ui/dropdown-menu/DropdownMenuRadioGroup.vue';
import { mount } from '@vue/test-utils';

describe('DropdownMenuRadioGroup', () => {
  it('renders component', () => {
    const wrapper = mount(DropdownMenuRadioGroup);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(DropdownMenuRadioGroup, {
      slots: {
        default: '<div>Radio Group</div>',
      },
    });
    expect(wrapper.html()).toContain('Radio Group');
  });

  it('accepts modelValue prop', () => {
    const wrapper = mount(DropdownMenuRadioGroup, {
      props: {
        modelValue: 'option1',
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});

