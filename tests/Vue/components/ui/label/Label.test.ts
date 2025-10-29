import { Label } from '@/components/ui/label';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Label', () => {
  it('renders slot content', () => {
    const wrapper = mount(Label, {
      slots: {
        default: 'Username',
      },
    });

    expect(wrapper.text()).toBe('Username');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(Label, {
      slots: {
        default: 'Label',
      },
    });

    expect(wrapper.attributes('data-slot')).toBe('label');
  });

  it('applies custom class', () => {
    const wrapper = mount(Label, {
      props: {
        class: 'custom-label-class',
      },
      slots: {
        default: 'Label',
      },
    });

    expect(wrapper.classes()).toContain('custom-label-class');
  });

  it('applies for attribute', () => {
    const wrapper = mount(Label, {
      props: {
        for: 'username',
      },
      slots: {
        default: 'Username',
      },
    });

    expect(wrapper.attributes('for')).toBe('username');
  });

  it('renders as label element', () => {
    const wrapper = mount(Label, {
      slots: {
        default: 'Label',
      },
    });

    expect(wrapper.element.tagName).toBe('LABEL');
  });
});

