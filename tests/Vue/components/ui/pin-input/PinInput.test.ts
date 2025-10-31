import PinInput from '@/components/ui/pin-input/PinInput.vue';
import { mount } from '@vue/test-utils';

describe('PinInput', () => {
  it('renders component', () => {
    const wrapper = mount(PinInput, {
      props: {
        modelValue: [],
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(PinInput, {
      props: {
        modelValue: [],
      },
      slots: {
        default: '<div>Pin Input</div>',
      },
    });
    expect(wrapper.html()).toContain('Pin Input');
  });

  it('accepts type prop', () => {
    const wrapper = mount(PinInput, {
      props: {
        modelValue: [],
        type: 'number',
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});

