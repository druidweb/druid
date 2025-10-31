import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import { mount } from '@vue/test-utils';

describe('DropdownMenuLabel', () => {
  it('renders component', () => {
    const wrapper = mount(DropdownMenuLabel);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(DropdownMenuLabel, {
      slots: {
        default: 'Label',
      },
    });
    expect(wrapper.text()).toContain('Label');
  });

  it('applies inset prop', () => {
    const wrapper = mount(DropdownMenuLabel, {
      props: {
        inset: true,
      },
    });
    expect(wrapper.attributes('data-inset')).toBe('');
  });

  it('applies custom class', () => {
    const wrapper = mount(DropdownMenuLabel, {
      props: {
        class: 'custom-label',
      },
    });
    expect(wrapper.classes()).toContain('custom-label');
  });
});

