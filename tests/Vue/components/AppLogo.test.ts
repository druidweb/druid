import AppLogo from '@/components/AppLogo.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

vi.mock('@inertiajs/vue3', () => ({
  usePage: () => ({
    props: {
      name: 'Druid',
    },
  }),
}));

describe('AppLogo', () => {
  it('renders the app name when in sidebar layout', () => {
    const wrapper = mount(AppLogo, {
      global: {
        provide: {
          layoutVariant: 'sidebar',
        },
      },
    });

    expect(wrapper.text()).toContain('Druid');
  });

  it('renders the logo icon', () => {
    const wrapper = mount(AppLogo);

    const svg = wrapper.find('svg');
    expect(svg.exists()).toBe(true);
  });

  it('applies correct styling to logo container', () => {
    const wrapper = mount(AppLogo);

    const logoContainer = wrapper.find('div.flex.aspect-square');
    expect(logoContainer.exists()).toBe(true);
    expect(logoContainer.classes()).toContain('size-8');
    expect(logoContainer.classes()).toContain('rounded');
    expect(logoContainer.classes()).toContain('bg-black');
  });

  it('hides text container in default layout', () => {
    const wrapper = mount(AppLogo);

    const textContainer = wrapper.find('div.grid');
    expect(textContainer.exists()).toBe(false);
  });

  it('shows text container in sidebar layout', () => {
    const wrapper = mount(AppLogo, {
      global: {
        provide: {
          layoutVariant: 'sidebar',
        },
      },
    });

    const textContainer = wrapper.find('div.grid');
    expect(textContainer.exists()).toBe(true);
    expect(textContainer.classes()).toContain('ml-1');
    expect(textContainer.classes()).toContain('flex-1');
  });
});
