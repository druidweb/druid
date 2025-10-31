import { PinInput } from '@/components/ui/pin-input';
import PinInputSlot from '@/components/ui/pin-input/PinInputSlot.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('PinInputSlot', () => {
  const mountWithPinInput = (slotProps: any = {}) => {
    return mount(PinInput, {
      props: {
        modelValue: [],
      },
      slots: {
        default: () => h(PinInputSlot, { index: 0, ...slotProps }),
      },
    });
  };

  it('renders within PinInput', () => {
    const wrapper = mountWithPinInput();
    expect(wrapper.exists()).toBe(true);
  });

  it('has correct data-slot attribute', () => {
    const wrapper = mountWithPinInput();
    const slot = wrapper.find('[data-slot="pin-input-slot"]');
    expect(slot.exists()).toBe(true);
  });

  it('applies custom class', () => {
    const wrapper = mountWithPinInput({ class: 'custom-slot' });
    const slot = wrapper.find('[data-slot="pin-input-slot"]');
    expect(slot.classes()).toContain('custom-slot');
  });
});

