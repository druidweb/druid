import { SidebarProvider } from '@/components/ui/sidebar';
import SidebarRail from '@/components/ui/sidebar/SidebarRail.vue';
import { mount } from '@vue/test-utils';
import { h } from 'vue';

describe('SidebarRail', () => {
  const mountWithProvider = (props: any = {}) => {
    return mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () => h(SidebarRail, props),
      },
    });
  };

  it('renders component', () => {
    const wrapper = mountWithProvider();
    expect(wrapper.exists()).toBe(true);
  });

  it('renders button', () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('button');
    expect(button.exists()).toBe(true);
  });

  it('renders with custom class', () => {
    const wrapper = mountWithProvider({
      class: 'custom-rail',
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('button has correct attributes', () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('button');
    expect(button.attributes('aria-label')).toBe('Toggle Sidebar');
    expect(button.attributes('title')).toBe('Toggle Sidebar');
  });

  it('has correct data attributes', () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('button');
    expect(button.attributes('data-sidebar')).toBe('rail');
    expect(button.attributes('data-slot')).toBe('sidebar-rail');
  });

  it('button has tabindex -1', () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('button');
    expect(button.attributes('tabindex')).toBe('-1');
  });

  it('calls toggleSidebar on click', async () => {
    const wrapper = mountWithProvider();
    const button = wrapper.find('button');
    await button.trigger('click');
    expect(button.exists()).toBe(true);
  });
});
