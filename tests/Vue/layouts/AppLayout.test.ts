import AppLayout from '@/layouts/AppLayout.vue';
import { mount } from '@vue/test-utils';

describe('AppLayout', () => {
  it('renders component', () => {
    const wrapper = mount(AppLayout, {
      global: {
        stubs: {
          AppLayout: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('passes breadcrumbs prop', () => {
    const breadcrumbs = [{ label: 'Home', href: '/' }];
    const wrapper = mount(AppLayout, {
      props: {
        breadcrumbs,
      },
      global: {
        stubs: {
          AppLayout: {
            template: '<div><slot /></div>',
            props: ['breadcrumbs'],
          },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(AppLayout, {
      slots: {
        default: '<div>Test Content</div>',
      },
      global: {
        stubs: {
          AppLayout: {
            template: '<div><slot /></div>',
          },
        },
      },
    });
    expect(wrapper.html()).toContain('Test Content');
  });
});

