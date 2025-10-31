import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue';
import { mount } from '@vue/test-utils';

describe('AuthSimpleLayout', () => {
  it('renders component', () => {
    const wrapper = mount(AuthSimpleLayout, {
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
    const wrapper = mount(AuthSimpleLayout, {
      props: {
        title: 'Register',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
        },
      },
    });
    expect(wrapper.text()).toContain('Register');
  });

  it('displays description', () => {
    const wrapper = mount(AuthSimpleLayout, {
      props: {
        description: 'Create your account',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
        },
      },
    });
    expect(wrapper.text()).toContain('Create your account');
  });

  it('renders slot content', () => {
    const wrapper = mount(AuthSimpleLayout, {
      slots: {
        default: '<div>Registration Form</div>',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
        },
      },
    });
    expect(wrapper.html()).toContain('Registration Form');
  });
});

