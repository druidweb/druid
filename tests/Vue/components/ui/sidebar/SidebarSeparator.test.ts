import SidebarSeparator from '@/components/ui/sidebar/SidebarSeparator.vue';
import { mount } from '@vue/test-utils';

describe('SidebarSeparator', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarSeparator, {
      global: {
        stubs: {
          Separator: { template: '<div class="separator"></div>' },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('applies custom class', () => {
    const wrapper = mount(SidebarSeparator, {
      props: {
        class: 'custom-separator',
      },
      global: {
        stubs: {
          Separator: { template: '<div class="separator"></div>' },
        },
      },
    });
    expect(wrapper.classes()).toContain('custom-separator');
  });
});

