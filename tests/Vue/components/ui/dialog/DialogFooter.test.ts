import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import { mount } from '@vue/test-utils';

describe('DialogFooter', () => {
  it('renders component', () => {
    const wrapper = mount(DialogFooter);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(DialogFooter, {
      slots: {
        default: '<div>Footer Content</div>',
      },
    });
    expect(wrapper.html()).toContain('Footer Content');
  });

  it('applies custom class', () => {
    const wrapper = mount(DialogFooter, {
      props: {
        class: 'custom-footer',
      },
    });
    expect(wrapper.classes()).toContain('custom-footer');
  });
});

