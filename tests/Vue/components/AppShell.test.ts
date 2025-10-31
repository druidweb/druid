import AppShell from '@/components/AppShell.vue';
import { mount } from '@vue/test-utils';

vi.mock('@inertiajs/vue3', () => ({
  usePage: () => ({
    props: {
      sidebarOpen: true,
    },
  }),
}));

describe('AppShell', () => {
  it('renders header variant', () => {
    const wrapper = mount(AppShell, {
      props: {
        variant: 'header',
      },
      slots: {
        default: '<div class="test-content">Content</div>',
      },
    });

    expect(wrapper.html()).toContain('test-content');
    expect(wrapper.find('.flex.min-h-screen').exists()).toBe(true);
  });

  it('renders sidebar variant', () => {
    const wrapper = mount(AppShell, {
      props: {
        variant: 'sidebar',
      },
      slots: {
        default: '<div class="test-content">Content</div>',
      },
    });

    expect(wrapper.html()).toContain('test-content');
  });

  it('renders slot content', () => {
    const wrapper = mount(AppShell, {
      props: {
        variant: 'header',
      },
      slots: {
        default: '<div>Test Content</div>',
      },
    });

    expect(wrapper.text()).toContain('Test Content');
  });
});

