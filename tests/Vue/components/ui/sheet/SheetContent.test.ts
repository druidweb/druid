import SheetContent from '@/components/ui/sheet/SheetContent.vue';
import { Sheet } from '@/components/ui/sheet';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('SheetContent', () => {
  const mountWithSheet = (props: any = {}) => {
    return mount(Sheet, {
      props: {
        open: true,
      },
      slots: {
        default: () => h(SheetContent, props),
      },
    });
  };

  it('renders component', () => {
    const wrapper = mountWithSheet();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with right side', () => {
    const wrapper = mountWithSheet({
      side: 'right',
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with left side', () => {
    const wrapper = mountWithSheet({
      side: 'left',
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with top side', () => {
    const wrapper = mountWithSheet({
      side: 'top',
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with bottom side', () => {
    const wrapper = mountWithSheet({
      side: 'bottom',
    });
    expect(wrapper.exists()).toBe(true);
  });
});

