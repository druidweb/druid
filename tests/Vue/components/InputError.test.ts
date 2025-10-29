import InputError from '@/components/InputError.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('InputError', () => {
  it('renders error message when provided', () => {
    const wrapper = mount(InputError, {
      props: {
        message: 'This field is required',
      },
    });

    expect(wrapper.text()).toContain('This field is required');
  });

  it('does not render when message is empty', () => {
    const wrapper = mount(InputError, {
      props: {
        message: '',
      },
    });

    const div = wrapper.find('div');
    expect(div.attributes('style')).toContain('display: none');
  });

  it('does not render when message is undefined', () => {
    const wrapper = mount(InputError, {
      props: {
        message: undefined,
      },
    });

    const div = wrapper.find('div');
    expect(div.attributes('style')).toContain('display: none');
  });

  it('applies correct error styling', () => {
    const wrapper = mount(InputError, {
      props: {
        message: 'Error message',
      },
    });

    const p = wrapper.find('p');
    expect(p.classes()).toContain('text-sm');
    expect(p.classes()).toContain('text-red-600');
    expect(p.classes()).toContain('dark:text-red-500');
  });

  it('updates when message prop changes', async () => {
    const wrapper = mount(InputError, {
      props: {
        message: 'First error',
      },
    });

    expect(wrapper.text()).toContain('First error');

    await wrapper.setProps({ message: 'Second error' });

    expect(wrapper.text()).toContain('Second error');
  });

  it('hides when message is cleared', async () => {
    const wrapper = mount(InputError, {
      props: {
        message: 'Error message',
      },
    });

    const div = wrapper.find('div');
    expect(div.attributes('style')).toBeUndefined();

    await wrapper.setProps({ message: '' });

    expect(div.attributes('style')).toContain('display: none');
  });
});
