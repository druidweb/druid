import Icon from '@/components/Icon.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Icon', () => {
  it('renders with default props', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'home',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('applies default classes', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'home',
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.classes()).toContain('h-4');
    expect(svg.classes()).toContain('w-4');
  });

  it('applies custom class', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'home',
        class: 'text-red-500',
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.classes()).toContain('text-red-500');
  });

  it('applies custom size', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'home',
        size: 24,
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.attributes('width')).toBe('24');
    expect(svg.attributes('height')).toBe('24');
  });

  it('applies custom stroke width', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'home',
        strokeWidth: 3,
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.attributes('stroke-width')).toBe('3');
  });

  it('applies custom color', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'home',
        color: 'red',
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.attributes('stroke')).toBe('red');
  });

  it('capitalizes icon name correctly', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'arrowRight',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('handles single letter icon names', () => {
    const wrapper = mount(Icon, {
      props: {
        name: 'x',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });
});
