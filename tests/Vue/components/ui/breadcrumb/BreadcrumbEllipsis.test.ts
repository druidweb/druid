import BreadcrumbEllipsis from '@/components/ui/breadcrumb/BreadcrumbEllipsis.vue';
import { mount } from '@vue/test-utils';

describe('BreadcrumbEllipsis', () => {
  it('renders the component', () => {
    const wrapper = mount(BreadcrumbEllipsis);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders MoreHorizontal icon', () => {
    const wrapper = mount(BreadcrumbEllipsis);
    expect(wrapper.find('svg').exists()).toBe(true);
  });

  it('has sr-only text', () => {
    const wrapper = mount(BreadcrumbEllipsis);
    expect(wrapper.text()).toContain('More');
  });
});

