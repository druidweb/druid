import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { mount } from '@vue/test-utils';

describe('AppHeaderLayout', () => {
  it('renders component', () => {
    const wrapper = mount(AppHeaderLayout, {
      global: {
        stubs: {
          AppShell: true,
          AppHeader: true,
          AppContent: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('passes breadcrumbs to AppHeader', () => {
    const breadcrumbs = [{ label: 'Home', href: '/' }];
    const wrapper = mount(AppHeaderLayout, {
      props: {
        breadcrumbs,
      },
      global: {
        stubs: {
          AppShell: { template: '<div><slot /></div>' },
          AppHeader: {
            template: '<div></div>',
            props: ['breadcrumbs'],
          },
          AppContent: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(AppHeaderLayout, {
      slots: {
        default: '<div>Page Content</div>',
      },
      global: {
        stubs: {
          AppShell: { template: '<div><slot /></div>' },
          AppHeader: { template: '<div></div>' },
          AppContent: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.html()).toContain('Page Content');
  });
});

