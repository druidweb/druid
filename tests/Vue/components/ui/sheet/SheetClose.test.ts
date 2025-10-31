import { Sheet } from '@/components/ui/sheet';
import SheetClose from '@/components/ui/sheet/SheetClose.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('SheetClose', () => {
  const mountWithSheet = (props: any = {}) => {
    return mount(Sheet, {
      props: {
        open: true,
      },
      slots: {
        default: () => h(SheetClose, props, () => 'Close'),
      },
    });
  };

  it('renders within Sheet', () => {
    const wrapper = mountWithSheet();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mountWithSheet();
    expect(wrapper.text()).toContain('Close');
  });

  it('has correct data-slot attribute', () => {
    const wrapper = mountWithSheet();
    const closeButton = wrapper.find('[data-slot="sheet-close"]');
    expect(closeButton.exists()).toBe(true);
  });
});

