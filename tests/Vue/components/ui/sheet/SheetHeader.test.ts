import SheetHeader from '@/components/ui/sheet/SheetHeader.vue';
import { mount } from '@vue/test-utils';

describe('SheetHeader', () => {
  it('renders component', () => {
    const wrapper = mount(SheetHeader);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(SheetHeader, {
      slots: {
        default: '<div>Header</div>',
      },
    });
    expect(wrapper.html()).toContain('Header');
  });

  it('applies custom class', () => {
    const wrapper = mount(SheetHeader, {
      props: {
        class: 'custom-header',
      },
    });
    expect(wrapper.classes()).toContain('custom-header');
  });
});

