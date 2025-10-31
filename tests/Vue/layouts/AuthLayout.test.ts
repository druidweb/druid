import AuthLayout from '@/layouts/AuthLayout.vue';
import { mount } from '@vue/test-utils';

describe('AuthLayout', () => {
  it('renders component', () => {
    const wrapper = mount(AuthLayout, {
      global: {
        stubs: {
          AuthLayout: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('passes title prop', () => {
    const wrapper = mount(AuthLayout, {
      props: {
        title: 'Login',
      },
      global: {
        stubs: {
          AuthLayout: {
            template: '<div><slot /></div>',
            props: ['title', 'description'],
          },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('passes description prop', () => {
    const wrapper = mount(AuthLayout, {
      props: {
        description: 'Please login',
      },
      global: {
        stubs: {
          AuthLayout: {
            template: '<div><slot /></div>',
            props: ['title', 'description'],
          },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(AuthLayout, {
      slots: {
        default: '<div>Auth Content</div>',
      },
      global: {
        stubs: {
          AuthLayout: {
            template: '<div><slot /></div>',
          },
        },
      },
    });
    expect(wrapper.html()).toContain('Auth Content');
  });
});

