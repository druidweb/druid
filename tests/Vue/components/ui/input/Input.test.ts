import { Input } from '@/components/ui/input';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Input', () => {
  it('renders input element', () => {
    const wrapper = mount(Input);

    expect(wrapper.find('input').exists()).toBe(true);
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(Input);

    expect(wrapper.find('input').attributes('data-slot')).toBe('input');
  });

  it('applies custom class', () => {
    const wrapper = mount(Input, {
      props: {
        class: 'custom-input-class',
      },
    });

    expect(wrapper.find('input').classes()).toContain('custom-input-class');
  });

  it('binds modelValue', async () => {
    const wrapper = mount(Input, {
      props: {
        modelValue: 'test value',
      },
    });

    const input = wrapper.find('input');
    expect((input.element as HTMLInputElement).value).toBe('test value');
  });

  it('emits update:modelValue on input', async () => {
    const wrapper = mount(Input, {
      props: {
        modelValue: '',
      },
    });

    const input = wrapper.find('input');
    await input.setValue('new value');

    expect(wrapper.emitted('update:modelValue')).toBeTruthy();
    expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['new value']);
  });

  it('uses defaultValue when modelValue is not provided', () => {
    const wrapper = mount(Input, {
      props: {
        defaultValue: 'default text',
      },
    });

    const input = wrapper.find('input');
    expect((input.element as HTMLInputElement).value).toBe('default text');
  });

  it('has default styling classes', () => {
    const wrapper = mount(Input);

    const input = wrapper.find('input');
    expect(input.classes()).toContain('rounded');
    expect(input.classes()).toContain('border');
    expect(input.classes()).toContain('border-input');
  });

  it('supports type attribute', () => {
    const wrapper = mount(Input, {
      attrs: {
        type: 'password',
      },
    });

    const input = wrapper.find('input');
    expect(input.attributes('type')).toBe('password');
  });

  it('supports placeholder attribute', () => {
    const wrapper = mount(Input, {
      attrs: {
        placeholder: 'Enter text',
      },
    });

    const input = wrapper.find('input');
    expect(input.attributes('placeholder')).toBe('Enter text');
  });

  it('supports disabled attribute', () => {
    const wrapper = mount(Input, {
      attrs: {
        disabled: true,
      },
    });

    const input = wrapper.find('input');
    expect(input.attributes('disabled')).toBeDefined();
  });
});

