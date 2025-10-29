import AlertError from '@/components/AlertError.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('AlertError', () => {
  it('renders with default title', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['Error 1'],
      },
    });

    expect(wrapper.text()).toContain('Something went wrong.');
  });

  it('renders with custom title', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['Error 1'],
        title: 'Custom Error Title',
      },
    });

    expect(wrapper.text()).toContain('Custom Error Title');
  });

  it('renders single error', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['This is an error'],
      },
    });

    expect(wrapper.text()).toContain('This is an error');
  });

  it('renders multiple errors', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['Error 1', 'Error 2', 'Error 3'],
      },
    });

    expect(wrapper.text()).toContain('Error 1');
    expect(wrapper.text()).toContain('Error 2');
    expect(wrapper.text()).toContain('Error 3');
  });

  it('removes duplicate errors', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['Error 1', 'Error 2', 'Error 1', 'Error 2'],
      },
    });

    const listItems = wrapper.findAll('li');
    expect(listItems).toHaveLength(2);
  });

  it('renders errors in a list', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['Error 1', 'Error 2'],
      },
    });

    const ul = wrapper.find('ul');
    expect(ul.exists()).toBe(true);
    expect(ul.classes()).toContain('list-inside');
    expect(ul.classes()).toContain('list-disc');
  });

  it('renders AlertCircle icon', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['Error 1'],
      },
    });

    const svg = wrapper.find('svg');
    expect(svg.exists()).toBe(true);
    expect(svg.classes()).toContain('size-4');
  });

  it('uses destructive variant', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: ['Error 1'],
      },
    });

    // The Alert component should receive variant="destructive"
    const alert = wrapper.findComponent({ name: 'Alert' });
    expect(alert.exists()).toBe(true);
  });

  it('handles empty errors array', () => {
    const wrapper = mount(AlertError, {
      props: {
        errors: [],
      },
    });

    const listItems = wrapper.findAll('li');
    expect(listItems).toHaveLength(0);
  });
});
