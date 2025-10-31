import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('AppLogoIcon', () => {
  it('renders default SVG snapshot', () => {
    const wrapper = mount(AppLogoIcon);
    expect(wrapper.html()).toMatchSnapshot();
  });

  it('renders SVG with className snapshot', () => {
    const wrapper = mount(AppLogoIcon, {
      props: { className: 'custom-icon-class' },
    });
    expect(wrapper.html()).toMatchSnapshot();
  });

  it('renders SVG with multiple classNames snapshot', () => {
    const wrapper = mount(AppLogoIcon, {
      props: { className: 'class-one class-two' },
    });
    expect(wrapper.html()).toMatchSnapshot();
  });

  it('renders SVG with custom attributes snapshot', () => {
    const wrapper = mount(AppLogoIcon, {
      attrs: {
        'data-testid': 'logo-icon',
        'aria-label': 'Company Logo',
      },
    });
    expect(wrapper.html()).toMatchSnapshot();
  });

  it('has correct SVG structure', () => {
    const wrapper = mount(AppLogoIcon);
    const svg = wrapper.find('svg');
    expect(svg.exists()).toBe(true);
    expect(svg.attributes('xmlns')).toBe('http://www.w3.org/2000/svg');
    expect(svg.attributes('viewBox')).toBe('0 0 40 42');
  });

  it('has path with currentColor fill', () => {
    const wrapper = mount(AppLogoIcon);
    const path = wrapper.find('path');
    expect(path.exists()).toBe(true);
    expect(path.attributes('fill')).toBe('currentColor');
  });
});
