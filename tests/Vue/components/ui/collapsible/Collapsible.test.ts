import Collapsible from '@/components/ui/collapsible/Collapsible.vue';
import { mount } from '@vue/test-utils';

describe('Collapsible', () => {
  it('renders component', () => {
    const wrapper = mount(Collapsible);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(Collapsible, {
      slots: {
        default: '<div>Collapsible Content</div>',
      },
    });
    expect(wrapper.html()).toContain('Collapsible Content');
  });

  it('passes open prop', () => {
    const wrapper = mount(Collapsible, {
      props: {
        open: true,
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});

