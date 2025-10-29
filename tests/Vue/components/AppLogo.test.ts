import AppLogo from '@/components/AppLogo.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('AppLogo', () => {
  it('renders the logo text', () => {
    const wrapper = mount(AppLogo);

    expect(wrapper.text()).toContain('Laravel Starter Kit');
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

  it('applies correct styling to text container', () => {
    const wrapper = mount(AppLogo);

    const textContainer = wrapper.find('div.grid');
    expect(textContainer.exists()).toBe(true);
    expect(textContainer.classes()).toContain('ml-1');
    expect(textContainer.classes()).toContain('flex-1');
  });

  it('applies correct styling to text', () => {
    const wrapper = mount(AppLogo);

    const span = wrapper.find('span');
    expect(span.classes()).toContain('truncate');
    expect(span.classes()).toContain('font-semibold');
  });
});

