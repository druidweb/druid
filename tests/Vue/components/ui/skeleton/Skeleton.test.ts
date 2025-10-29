import { Skeleton } from '@/components/ui/skeleton';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Skeleton', () => {
  it('renders skeleton element', () => {
    const wrapper = mount(Skeleton);

    expect(wrapper.exists()).toBe(true);
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(Skeleton);

    expect(wrapper.attributes('data-slot')).toBe('skeleton');
  });

  it('applies custom class', () => {
    const wrapper = mount(Skeleton, {
      props: {
        class: 'custom-skeleton-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-skeleton-class');
  });

  it('renders as div element', () => {
    const wrapper = mount(Skeleton);

    expect(wrapper.element.tagName).toBe('DIV');
  });

  it('has animate-pulse class', () => {
    const wrapper = mount(Skeleton);

    expect(wrapper.classes()).toContain('animate-pulse');
  });

  it('has rounded class', () => {
    const wrapper = mount(Skeleton);

    expect(wrapper.classes()).toContain('rounded');
  });

  it('has bg-primary/10 class', () => {
    const wrapper = mount(Skeleton);

    expect(wrapper.classes()).toContain('bg-primary/10');
  });
});

