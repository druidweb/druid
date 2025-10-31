import Dialog from '@/components/ui/dialog/Dialog.vue';
import { mount } from '@vue/test-utils';

describe('Dialog', () => {
  it('renders component', () => {
    const wrapper = mount(Dialog, {
      props: {
        open: false,
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(Dialog, {
      props: {
        open: true,
      },
      slots: {
        default: '<div>Dialog Content</div>',
      },
    });
    expect(wrapper.html()).toContain('Dialog Content');
  });
});

