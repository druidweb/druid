import { Dialog } from '@/components/ui/dialog';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('DialogTitle', () => {
  const mountWithDialog = (props: any = {}) => {
    return mount(Dialog, {
      props: {
        open: true,
      },
      slots: {
        default: () => h(DialogTitle, props, () => 'Dialog Title'),
      },
    });
  };

  it('renders within Dialog', () => {
    const wrapper = mountWithDialog();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mountWithDialog();
    expect(wrapper.text()).toContain('Dialog Title');
  });

  it('has correct data-slot attribute', () => {
    const wrapper = mountWithDialog();
    const title = wrapper.find('[data-slot="dialog-title"]');
    expect(title.exists()).toBe(true);
  });

  it('applies custom class', () => {
    const wrapper = mountWithDialog({ class: 'custom-title' });
    const title = wrapper.find('[data-slot="dialog-title"]');
    expect(title.classes()).toContain('custom-title');
  });
});

