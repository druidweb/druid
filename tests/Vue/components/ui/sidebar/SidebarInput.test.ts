import SidebarInput from '@/components/ui/sidebar/SidebarInput.vue';
import { mount } from '@vue/test-utils';

describe('SidebarInput', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarInput);
    expect(wrapper.exists()).toBe(true);
  });

  it('applies custom class', () => {
    const wrapper = mount(SidebarInput, {
      props: {
        class: 'custom-input',
      },
    });
    expect(wrapper.classes()).toContain('custom-input');
  });

  it('accepts placeholder prop', () => {
    const wrapper = mount(SidebarInput, {
      props: {
        placeholder: 'Search...',
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('has correct data attributes', () => {
    const wrapper = mount(SidebarInput);
    expect(wrapper.attributes('data-slot')).toBe('sidebar-input');
    expect(wrapper.attributes('data-sidebar')).toBe('input');
  });

  it('renders without props', () => {
    const wrapper = mount(SidebarInput);
    expect(wrapper.exists()).toBe(true);
  });
});
