import AppContent from '@/components/AppContent.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('AppContent', () => {
  it('renders slot content', () => {
    const wrapper = mount(AppContent, {
      slots: {
        default: '<div>Test Content</div>',
      },
    });

    expect(wrapper.html()).toContain('Test Content');
  });

  it('renders main element by default', () => {
    const wrapper = mount(AppContent, {
      slots: {
        default: '<div>Content</div>',
      },
    });

    const main = wrapper.find('main');
    expect(main.exists()).toBe(true);
  });

  it('renders main element when variant is header', () => {
    const wrapper = mount(AppContent, {
      props: {
        variant: 'header',
      },
      slots: {
        default: '<div>Content</div>',
      },
    });

    const main = wrapper.find('main');
    expect(main.exists()).toBe(true);
  });

  it('renders SidebarInset when variant is sidebar', () => {
    const wrapper = mount(AppContent, {
      props: {
        variant: 'sidebar',
      },
      slots: {
        default: '<div>Content</div>',
      },
    });

    // SidebarInset renders as a div
    const div = wrapper.find('div');
    expect(div.exists()).toBe(true);
  });

  it('applies custom class to main element', () => {
    const wrapper = mount(AppContent, {
      props: {
        class: 'custom-class',
      },
      slots: {
        default: '<div>Content</div>',
      },
    });

    const main = wrapper.find('main');
    expect(main.classes()).toContain('custom-class');
  });

  it('applies custom class to SidebarInset', () => {
    const wrapper = mount(AppContent, {
      props: {
        variant: 'sidebar',
        class: 'custom-class',
      },
      slots: {
        default: '<div>Content</div>',
      },
    });

    expect(wrapper.html()).toContain('custom-class');
  });

  it('applies default styling to main element', () => {
    const wrapper = mount(AppContent, {
      slots: {
        default: '<div>Content</div>',
      },
    });

    const main = wrapper.find('main');
    expect(main.classes()).toContain('mx-auto');
    expect(main.classes()).toContain('flex');
    expect(main.classes()).toContain('h-full');
    expect(main.classes()).toContain('w-full');
    expect(main.classes()).toContain('max-w-7xl');
  });
});
