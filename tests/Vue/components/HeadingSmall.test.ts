import HeadingSmall from '@/components/HeadingSmall.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('HeadingSmall', () => {
  it('renders title', () => {
    const wrapper = mount(HeadingSmall, {
      props: {
        title: 'Small Title',
      },
    });

    expect(wrapper.text()).toContain('Small Title');
  });

  it('renders description when provided', () => {
    const wrapper = mount(HeadingSmall, {
      props: {
        title: 'Small Title',
        description: 'Small description',
      },
    });

    expect(wrapper.text()).toContain('Small description');
  });

  it('does not render description when not provided', () => {
    const wrapper = mount(HeadingSmall, {
      props: {
        title: 'Small Title',
      },
    });

    const p = wrapper.find('p');
    expect(p.exists()).toBe(false);
  });

  it('applies correct title styling', () => {
    const wrapper = mount(HeadingSmall, {
      props: {
        title: 'Small Title',
      },
    });

    const h3 = wrapper.find('h3');
    expect(h3.classes()).toContain('mb-0.5');
    expect(h3.classes()).toContain('text-base');
    expect(h3.classes()).toContain('font-medium');
  });

  it('applies correct description styling', () => {
    const wrapper = mount(HeadingSmall, {
      props: {
        title: 'Small Title',
        description: 'Small description',
      },
    });

    const p = wrapper.find('p');
    expect(p.classes()).toContain('text-sm');
    expect(p.classes()).toContain('text-muted-foreground');
  });

  it('uses header element as container', () => {
    const wrapper = mount(HeadingSmall, {
      props: {
        title: 'Small Title',
      },
    });

    const header = wrapper.find('header');
    expect(header.exists()).toBe(true);
  });
});

