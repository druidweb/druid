import { Dialog } from '@/components/ui/dialog';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('DialogContent', () => {
  const mountWithDialog = (props: any = {}) => {
    return mount(Dialog, {
      props: {
        open: true,
      },
      slots: {
        default: () => h(DialogContent, props),
      },
    });
  };

  it('renders component', () => {
    const wrapper = mountWithDialog();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with custom class', () => {
    const wrapper = mountWithDialog({
      class: 'custom-class',
    });
    expect(wrapper.exists()).toBe(true);
  });
});
