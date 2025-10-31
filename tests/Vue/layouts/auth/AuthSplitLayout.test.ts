import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';
import { mount } from '@vue/test-utils';

vi.mock('@inertiajs/vue3', async () => {
  const actual = await vi.importActual('@inertiajs/vue3');
  return {
    ...actual,
    usePage: () => ({
      props: {
        name: 'Test App',
        quote: {
          message: 'Test quote',
          author: 'Test Author',
        },
      },
    }),
  };
});

describe('AuthSplitLayout', () => {
  it('renders component', () => {
    const wrapper = mount(AuthSplitLayout, {
      global: {
        stubs: {
          Link: true,
          AppLogoIcon: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays title', () => {
    const wrapper = mount(AuthSplitLayout, {
      props: {
        title: 'Sign In',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
        },
      },
    });
    expect(wrapper.text()).toContain('Sign In');
  });

  it('displays description', () => {
    const wrapper = mount(AuthSplitLayout, {
      props: {
        description: 'Enter your credentials',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
        },
      },
    });
    expect(wrapper.text()).toContain('Enter your credentials');
  });

  it('renders slot content', () => {
    const wrapper = mount(AuthSplitLayout, {
      slots: {
        default: '<div>Login Form</div>',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
        },
      },
    });
    expect(wrapper.html()).toContain('Login Form');
  });
});

