import AppContent from '@/components/AppContent.vue';
import { describe, expect, it } from 'vitest';
import { createMockProps, mountComponent } from '../setup';

describe('AppContent', () => {
  it('renders correctly with default props', () => {
    const wrapper = mountComponent(AppContent);

    expect(wrapper.exists()).toBe(true);
  });

  it('renders main element when variant is not sidebar', () => {
    const wrapper = mountComponent(AppContent, {
      props: createMockProps({ variant: 'header' }),
    });

    const mainElement = wrapper.find('main');
    expect(mainElement.exists()).toBe(true);
    expect(mainElement.classes()).toContain('mx-auto');
    expect(mainElement.classes()).toContain('flex');
    expect(mainElement.classes()).toContain('h-full');
    expect(mainElement.classes()).toContain('w-full');
    expect(mainElement.classes()).toContain('max-w-7xl');
    expect(mainElement.classes()).toContain('flex-1');
    expect(mainElement.classes()).toContain('flex-col');
    expect(mainElement.classes()).toContain('gap-4');
    expect(mainElement.classes()).toContain('rounded-xl');
  });

  it('renders main element when variant is undefined', () => {
    const wrapper = mountComponent(AppContent);

    const mainElement = wrapper.find('main');
    expect(mainElement.exists()).toBe(true);
  });

  it('applies custom class when provided', () => {
    const customClass = 'custom-test-class';
    const wrapper = mountComponent(AppContent, {
      props: createMockProps({ class: customClass }),
    });

    const mainElement = wrapper.find('main');
    expect(mainElement.classes()).toContain(customClass);
  });

  it('renders slot content in main element', () => {
    const slotContent = '<div data-testid="slot-content">Test Content</div>';
    const wrapper = mountComponent(AppContent, {
      slots: {
        default: slotContent,
      },
    });

    expect(wrapper.find('[data-testid="slot-content"]').exists()).toBe(true);
    expect(wrapper.find('[data-testid="slot-content"]').text()).toBe('Test Content');
  });

  it('computes className correctly from class prop', () => {
    const testClass = 'test-computed-class';
    const wrapper = mountComponent(AppContent, {
      props: createMockProps({ class: testClass }),
    });

    // The computed className should be applied to the main element
    const mainElement = wrapper.find('main');
    expect(mainElement.classes()).toContain(testClass);
  });
});
