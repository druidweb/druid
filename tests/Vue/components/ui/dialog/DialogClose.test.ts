import { Dialog } from '@/components/ui/dialog';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('DialogClose', () => {
  const mountWithDialog = (props: any = {}) => {
    return mount(Dialog, {
      props: {
        open: true,
      },
      slots: {
        default: () => h(DialogClose, props, () => 'Close'),
      },
    });
  };

  it('renders within Dialog', () => {
    const wrapper = mountWithDialog();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mountWithDialog();
    expect(wrapper.text()).toContain('Close');
  });

  it('has correct data-slot attribute', () => {
    const wrapper = mountWithDialog();
    const closeButton = wrapper.find('[data-slot="dialog-close"]');
    expect(closeButton.exists()).toBe(true);
  });
});

