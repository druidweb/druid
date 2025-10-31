import SidebarMenuSkeleton from '@/components/ui/sidebar/SidebarMenuSkeleton.vue';
import { mount } from '@vue/test-utils';

describe('SidebarMenuSkeleton', () => {
  it('renders component', () => {
    const wrapper = mount(SidebarMenuSkeleton, {
      global: {
        stubs: {
          Skeleton: { template: '<div class="skeleton"></div>' },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders with showIcon prop', () => {
    const wrapper = mount(SidebarMenuSkeleton, {
      props: {
        showIcon: true,
      },
      global: {
        stubs: {
          Skeleton: { template: '<div class="skeleton"></div>' },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });
});

