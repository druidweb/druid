import { Sheet } from '@/components/ui/sheet';
import SheetTitle from '@/components/ui/sheet/SheetTitle.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('SheetTitle', () => {
  const mountWithSheet = (props: any = {}) => {
    return mount(Sheet, {
      props: {
        open: true,
      },
      slots: {
        default: () => h(SheetTitle, props, () => 'Sheet Title'),
      },
    });
  };

  it('renders within Sheet', () => {
    const wrapper = mountWithSheet();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mountWithSheet();
    expect(wrapper.text()).toContain('Sheet Title');
  });

  it('has correct data-slot attribute', () => {
    const wrapper = mountWithSheet();
    const title = wrapper.find('[data-slot="sheet-title"]');
    expect(title.exists()).toBe(true);
  });

  it('applies custom class', () => {
    const wrapper = mountWithSheet({ class: 'custom-title' });
    const title = wrapper.find('[data-slot="sheet-title"]');
    expect(title.classes()).toContain('custom-title');
  });
});

