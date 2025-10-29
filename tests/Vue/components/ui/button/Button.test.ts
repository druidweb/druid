import { Button } from '@/components/ui/button';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Button', () => {
  it('renders slot content', () => {
    const wrapper = mount(Button, {
      slots: {
        default: 'Click Me',
      },
    });

    expect(wrapper.text()).toBe('Click Me');
  });

  it('renders as button by default', () => {
    const wrapper = mount(Button, {
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.element.tagName).toBe('BUTTON');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(Button, {
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.attributes('data-slot')).toBe('button');
  });

  it('renders with default variant', () => {
    const wrapper = mount(Button, {
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with secondary variant', () => {
    const wrapper = mount(Button, {
      props: {
        variant: 'secondary',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with destructive variant', () => {
    const wrapper = mount(Button, {
      props: {
        variant: 'destructive',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with outline variant', () => {
    const wrapper = mount(Button, {
      props: {
        variant: 'outline',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with ghost variant', () => {
    const wrapper = mount(Button, {
      props: {
        variant: 'ghost',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with link variant', () => {
    const wrapper = mount(Button, {
      props: {
        variant: 'link',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with sm size', () => {
    const wrapper = mount(Button, {
      props: {
        size: 'sm',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with lg size', () => {
    const wrapper = mount(Button, {
      props: {
        size: 'lg',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.html()).toContain('Button');
  });

  it('renders with icon size', () => {
    const wrapper = mount(Button, {
      props: {
        size: 'icon',
      },
      slots: {
        default: 'X',
      },
    });

    expect(wrapper.html()).toContain('X');
  });

  it('applies custom class', () => {
    const wrapper = mount(Button, {
      props: {
        class: 'custom-button-class',
      },
      slots: {
        default: 'Button',
      },
    });

    expect(wrapper.classes()).toContain('custom-button-class');
  });

  it('renders as different element with as prop', () => {
    const wrapper = mount(Button, {
      props: {
        as: 'a',
      },
      slots: {
        default: 'Link Button',
      },
    });

    expect(wrapper.element.tagName).toBe('A');
  });
});

