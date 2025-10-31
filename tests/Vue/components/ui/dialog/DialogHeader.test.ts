import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import { mount } from '@vue/test-utils';

describe('DialogHeader', () => {
  it('renders component', () => {
    const wrapper = mount(DialogHeader);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(DialogHeader, {
      slots: {
        default: '<div>Header Content</div>',
      },
    });
    expect(wrapper.html()).toContain('Header Content');
  });

  it('applies custom class', () => {
    const wrapper = mount(DialogHeader, {
      props: {
        class: 'custom-header',
      },
    });
    expect(wrapper.classes()).toContain('custom-header');
  });
});

