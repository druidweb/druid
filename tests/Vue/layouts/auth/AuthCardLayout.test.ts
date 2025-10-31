import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { mount } from '@vue/test-utils';

describe('AuthCardLayout', () => {
  it('renders component', () => {
    const wrapper = mount(AuthCardLayout, {
      global: {
        stubs: {
          Link: true,
          AppLogoIcon: true,
          Card: true,
          CardHeader: true,
          CardTitle: true,
          CardDescription: true,
          CardContent: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays title', () => {
    const wrapper = mount(AuthCardLayout, {
      props: {
        title: 'Login',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
          Card: { template: '<div><slot /></div>' },
          CardHeader: { template: '<div><slot /></div>' },
          CardTitle: { template: '<div><slot /></div>' },
          CardDescription: { template: '<div><slot /></div>' },
          CardContent: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.text()).toContain('Login');
  });

  it('displays description', () => {
    const wrapper = mount(AuthCardLayout, {
      props: {
        description: 'Please login to continue',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
          Card: { template: '<div><slot /></div>' },
          CardHeader: { template: '<div><slot /></div>' },
          CardTitle: { template: '<div><slot /></div>' },
          CardDescription: { template: '<div><slot /></div>' },
          CardContent: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.text()).toContain('Please login to continue');
  });

  it('renders slot content', () => {
    const wrapper = mount(AuthCardLayout, {
      slots: {
        default: '<div>Form Content</div>',
      },
      global: {
        stubs: {
          Link: { template: '<a><slot /></a>' },
          AppLogoIcon: { template: '<div />' },
          Card: { template: '<div><slot /></div>' },
          CardHeader: { template: '<div><slot /></div>' },
          CardTitle: { template: '<div><slot /></div>' },
          CardDescription: { template: '<div><slot /></div>' },
          CardContent: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.html()).toContain('Form Content');
  });
});

