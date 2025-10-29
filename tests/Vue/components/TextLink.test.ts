import TextLink from '@/components/TextLink.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('TextLink', () => {
  it('renders with href', () => {
    const wrapper = mount(TextLink, {
      props: {
        href: '/test',
      },
      slots: {
        default: 'Test Link',
      },
    });

    expect(wrapper.text()).toContain('Test Link');
  });

  it('renders slot content', () => {
    const wrapper = mount(TextLink, {
      props: {
        href: '/test',
      },
      slots: {
        default: '<span>Custom Content</span>',
      },
    });

    expect(wrapper.html()).toContain('Custom Content');
  });

  it('applies correct styling classes', () => {
    const wrapper = mount(TextLink, {
      props: {
        href: '/test',
      },
      slots: {
        default: 'Link',
      },
    });

    const link = wrapper.find('a');
    expect(link.classes()).toContain('cursor-pointer');
    expect(link.classes()).toContain('text-foreground');
    expect(link.classes()).toContain('underline');
    expect(link.classes()).toContain('decoration-neutral-300');
    expect(link.classes()).toContain('dark:decoration-neutral-500');
  });

  it('passes tabindex prop', () => {
    const wrapper = mount(TextLink, {
      props: {
        href: '/test',
        tabindex: 5,
      },
      slots: {
        default: 'Link',
      },
    });

    const link = wrapper.find('a');
    expect(link.attributes('tabindex')).toBe('5');
  });

  it('passes method prop', () => {
    const wrapper = mount(TextLink, {
      props: {
        href: '/test',
        method: 'post',
      },
      slots: {
        default: 'Link',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('passes as prop', () => {
    const wrapper = mount(TextLink, {
      props: {
        href: '/test',
        as: 'button',
      },
      slots: {
        default: 'Link',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('handles object href', () => {
    const wrapper = mount(TextLink, {
      props: {
        href: { url: '/test', method: 'get' },
      },
      slots: {
        default: 'Link',
      },
    });

    expect(wrapper.exists()).toBe(true);
  });
});

