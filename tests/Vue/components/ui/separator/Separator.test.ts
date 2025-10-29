import { Separator } from '@/components/ui/separator';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Separator', () => {
  it('renders separator element', () => {
    const wrapper = mount(Separator);

    expect(wrapper.exists()).toBe(true);
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(Separator);

    expect(wrapper.attributes('data-slot')).toBe('separator-root');
  });

  it('has horizontal orientation by default', () => {
    const wrapper = mount(Separator);

    expect(wrapper.attributes('data-orientation')).toBe('horizontal');
  });

  it('renders with vertical orientation', () => {
    const wrapper = mount(Separator, {
      props: {
        orientation: 'vertical',
      },
    });

    expect(wrapper.attributes('data-orientation')).toBe('vertical');
  });

  it('is decorative by default', () => {
    const wrapper = mount(Separator);

    expect(wrapper.attributes('role')).toBe('none');
  });

  it('applies custom class', () => {
    const wrapper = mount(Separator, {
      props: {
        class: 'custom-separator-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-separator-class');
  });

  it('applies bg-border class', () => {
    const wrapper = mount(Separator);

    expect(wrapper.classes()).toContain('bg-border');
  });
});
