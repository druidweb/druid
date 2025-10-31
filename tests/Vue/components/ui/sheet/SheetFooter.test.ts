import SheetFooter from '@/components/ui/sheet/SheetFooter.vue';
import { mount } from '@vue/test-utils';

describe('SheetFooter', () => {
  it('renders component', () => {
    const wrapper = mount(SheetFooter);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SheetFooter, {
      slots: {
        default: '<div>Footer</div>',
      },
    });
    expect(wrapper.html()).toContain('Footer');
  });

  it('applies custom class', () => {
    const wrapper = mount(SheetFooter, {
      props: {
        class: 'custom-footer',
      },
    });
    expect(wrapper.classes()).toContain('custom-footer');
  });
});

