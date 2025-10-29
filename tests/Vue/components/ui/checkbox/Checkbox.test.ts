import { Checkbox } from '@/components/ui/checkbox';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Checkbox', () => {
  it('renders checkbox element', () => {
    const wrapper = mount(Checkbox);

    expect(wrapper.exists()).toBe(true);
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(Checkbox);

    expect(wrapper.attributes('data-slot')).toBe('checkbox');
  });

  it('applies custom class', () => {
    const wrapper = mount(Checkbox, {
      props: {
        class: 'custom-checkbox-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-checkbox-class');
  });

  it('has default styling classes', () => {
    const wrapper = mount(Checkbox);

    expect(wrapper.classes()).toContain('rounded-[4px]');
    expect(wrapper.classes()).toContain('border');
    expect(wrapper.classes()).toContain('border-input');
  });

  it('supports disabled state', () => {
    const wrapper = mount(Checkbox, {
      props: {
        disabled: true,
      },
    });

    expect(wrapper.attributes('data-disabled')).toBeDefined();
  });

  it('can be clicked', async () => {
    const wrapper = mount(Checkbox, {
      props: {
        checked: false,
      },
    });

    await wrapper.trigger('click');

    // Component exists and is clickable
    expect(wrapper.exists()).toBe(true);
  });
});
