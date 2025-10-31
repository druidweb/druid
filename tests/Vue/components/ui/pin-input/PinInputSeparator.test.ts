import PinInputSeparator from '@/components/ui/pin-input/PinInputSeparator.vue';
import { mount } from '@vue/test-utils';

describe('PinInputSeparator', () => {
  it('renders component', () => {
    const wrapper = mount(PinInputSeparator);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(PinInputSeparator, {
      slots: {
        default: '-',
      },
    });
    expect(wrapper.text()).toContain('-');
  });

  it('applies custom class', () => {
    const wrapper = mount(PinInputSeparator, {
      props: {
        class: 'custom-separator',
      },
    });
    expect(wrapper.classes()).toContain('custom-separator');
  });
});

