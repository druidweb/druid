import PinInputGroup from '@/components/ui/pin-input/PinInputGroup.vue';
import { mount } from '@vue/test-utils';

describe('PinInputGroup', () => {
  it('renders component', () => {
    const wrapper = mount(PinInputGroup);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(PinInputGroup, {
      slots: {
        default: '<div>Group</div>',
      },
    });
    expect(wrapper.html()).toContain('Group');
  });

  it('applies custom class', () => {
    const wrapper = mount(PinInputGroup, {
      props: {
        class: 'custom-group',
      },
    });
    expect(wrapper.classes()).toContain('custom-group');
  });
});

