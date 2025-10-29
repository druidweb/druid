import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('AppLogoIcon', () => {
  it('renders an SVG element', () => {
    const wrapper = mount(AppLogoIcon);

    const svg = wrapper.find('svg');
    expect(svg.exists()).toBe(true);
  });

  it('applies className prop', () => {
    const wrapper = mount(AppLogoIcon, {
      props: {
        className: 'custom-icon-class',
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.classes()).toContain('custom-icon-class');
  });

  it('has correct viewBox', () => {
    const wrapper = mount(AppLogoIcon);

    const svg = wrapper.find('svg');
    expect(svg.attributes('viewBox')).toBe('0 0 40 42');
  });

  it('has correct xmlns', () => {
    const wrapper = mount(AppLogoIcon);

    const svg = wrapper.find('svg');
    expect(svg.attributes('xmlns')).toBe('http://www.w3.org/2000/svg');
  });

  it('contains path element', () => {
    const wrapper = mount(AppLogoIcon);

    const path = wrapper.find('path');
    expect(path.exists()).toBe(true);
  });

  it('path has fill="currentColor"', () => {
    const wrapper = mount(AppLogoIcon);

    const path = wrapper.find('path');
    expect(path.attributes('fill')).toBe('currentColor');
  });

  it('passes through additional attributes', () => {
    const wrapper = mount(AppLogoIcon, {
      attrs: {
        'data-testid': 'logo-icon',
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.attributes('data-testid')).toBe('logo-icon');
  });
});

