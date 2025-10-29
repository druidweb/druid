import { Badge } from '@/components/ui/badge';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Badge', () => {
  it('renders slot content', () => {
    const wrapper = mount(Badge, {
      slots: {
        default: 'Test Badge',
      },
    });

    expect(wrapper.text()).toBe('Test Badge');
  });

  it('renders with default variant', () => {
    const wrapper = mount(Badge, {
      slots: {
        default: 'Badge',
      },
    });

    expect(wrapper.attributes('data-slot')).toBe('badge');
  });

  it('renders with secondary variant', () => {
    const wrapper = mount(Badge, {
      props: {
        variant: 'secondary',
      },
      slots: {
        default: 'Badge',
      },
    });

    expect(wrapper.html()).toContain('Badge');
  });

  it('renders with destructive variant', () => {
    const wrapper = mount(Badge, {
      props: {
        variant: 'destructive',
      },
      slots: {
        default: 'Badge',
      },
    });

    expect(wrapper.html()).toContain('Badge');
  });

  it('renders with outline variant', () => {
    const wrapper = mount(Badge, {
      props: {
        variant: 'outline',
      },
      slots: {
        default: 'Badge',
      },
    });

    expect(wrapper.html()).toContain('Badge');
  });

  it('applies custom class', () => {
    const wrapper = mount(Badge, {
      props: {
        class: 'custom-badge-class',
      },
      slots: {
        default: 'Badge',
      },
    });

    expect(wrapper.classes()).toContain('custom-badge-class');
  });

  it('renders as different element with as prop', () => {
    const wrapper = mount(Badge, {
      props: {
        as: 'span',
      },
      slots: {
        default: 'Badge',
      },
    });

    expect(wrapper.element.tagName).toBe('SPAN');
  });
});

