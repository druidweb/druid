import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('PlaceholderPattern', () => {
  it('renders an svg element', () => {
    const wrapper = mount(PlaceholderPattern);

    const svg = wrapper.find('svg');
    expect(svg.exists()).toBe(true);
  });

  it('applies correct svg classes', () => {
    const wrapper = mount(PlaceholderPattern);

    const svg = wrapper.find('svg');
    expect(svg.classes()).toContain('absolute');
    expect(svg.classes()).toContain('inset-0');
    expect(svg.classes()).toContain('size-full');
  });

  it('contains a defs element with pattern', () => {
    const wrapper = mount(PlaceholderPattern);

    const defs = wrapper.find('defs');
    expect(defs.exists()).toBe(true);

    const pattern = defs.find('pattern');
    expect(pattern.exists()).toBe(true);
  });

  it('contains a rect element', () => {
    const wrapper = mount(PlaceholderPattern);

    const rect = wrapper.find('rect');
    expect(rect.exists()).toBe(true);
    expect(rect.attributes('width')).toBe('100%');
    expect(rect.attributes('height')).toBe('100%');
  });

  it('generates unique pattern id', () => {
    const wrapper1 = mount(PlaceholderPattern);
    const wrapper2 = mount(PlaceholderPattern);

    const pattern1 = wrapper1.find('pattern');
    const pattern2 = wrapper2.find('pattern');

    const id1 = pattern1.attributes('id');
    const id2 = pattern2.attributes('id');

    expect(id1).toBeDefined();
    expect(id2).toBeDefined();
    expect(id1).not.toBe(id2);
  });

  it('pattern id starts with "pattern-"', () => {
    const wrapper = mount(PlaceholderPattern);

    const pattern = wrapper.find('pattern');
    const id = pattern.attributes('id');

    expect(id).toMatch(/^pattern-/);
  });

  it('rect fill references pattern id', () => {
    const wrapper = mount(PlaceholderPattern);

    const pattern = wrapper.find('pattern');
    const rect = wrapper.find('rect');

    const patternId = pattern.attributes('id');
    const rectFill = rect.attributes('fill');

    expect(rectFill).toBe(`url(#${patternId})`);
  });
});

