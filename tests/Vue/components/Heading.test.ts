import Heading from '@/components/Heading.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Heading', () => {
  it('renders title', () => {
    const wrapper = mount(Heading, {
      props: {
        title: 'Test Title',
      },
    });

    expect(wrapper.text()).toContain('Test Title');
  });

  it('renders description when provided', () => {
    const wrapper = mount(Heading, {
      props: {
        title: 'Test Title',
        description: 'Test description',
      },
    });

    expect(wrapper.text()).toContain('Test description');
  });

  it('does not render description when not provided', () => {
    const wrapper = mount(Heading, {
      props: {
        title: 'Test Title',
      },
    });

    const p = wrapper.find('p');
    expect(p.exists()).toBe(false);
  });

  it('applies correct title styling', () => {
    const wrapper = mount(Heading, {
      props: {
        title: 'Test Title',
      },
    });

    const h2 = wrapper.find('h2');
    expect(h2.classes()).toContain('text-xl');
    expect(h2.classes()).toContain('font-semibold');
    expect(h2.classes()).toContain('tracking-tight');
  });

  it('applies correct description styling', () => {
    const wrapper = mount(Heading, {
      props: {
        title: 'Test Title',
        description: 'Test description',
      },
    });

    const p = wrapper.find('p');
    expect(p.classes()).toContain('text-sm');
    expect(p.classes()).toContain('text-muted-foreground');
  });

  it('applies correct container styling', () => {
    const wrapper = mount(Heading, {
      props: {
        title: 'Test Title',
      },
    });

    const div = wrapper.find('div');
    expect(div.classes()).toContain('mb-8');
    expect(div.classes()).toContain('space-y-0.5');
  });
});

