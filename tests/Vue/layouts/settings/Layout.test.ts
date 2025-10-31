import SettingsLayout from '@/layouts/settings/Layout.vue';
import { mount } from '@vue/test-utils';

describe('SettingsLayout', () => {
  it('renders component', () => {
    const wrapper = mount(SettingsLayout, {
      global: {
        stubs: {
          Heading: true,
          Button: true,
          Separator: true,
          Link: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays settings title', () => {
    const wrapper = mount(SettingsLayout, {
      global: {
        stubs: {
          Heading: {
            template: '<div>{{ title }}</div>',
            props: ['title', 'description'],
          },
          Button: { template: '<button><slot /></button>' },
          Separator: { template: '<div />' },
          Link: { template: '<a><slot /></a>' },
        },
      },
    });
    expect(wrapper.text()).toContain('Settings');
  });

  it('renders navigation items', () => {
    const wrapper = mount(SettingsLayout, {
      global: {
        stubs: {
          Heading: { template: '<div />' },
          Button: { template: '<button><slot /></button>' },
          Separator: { template: '<div />' },
          Link: { template: '<a><slot /></a>' },
        },
      },
    });
    expect(wrapper.text()).toContain('Profile');
    expect(wrapper.text()).toContain('Password');
    expect(wrapper.text()).toContain('Two-Factor Auth');
    expect(wrapper.text()).toContain('Appearance');
  });

  it('renders slot content', () => {
    const wrapper = mount(SettingsLayout, {
      slots: {
        default: '<div>Settings Content</div>',
      },
      global: {
        stubs: {
          Heading: { template: '<div />' },
          Button: { template: '<button><slot /></button>' },
          Separator: { template: '<div />' },
          Link: { template: '<a><slot /></a>' },
        },
      },
    });
    expect(wrapper.html()).toContain('Settings Content');
  });
});

