import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it } from 'vitest';

describe('AppearanceTabs', () => {
  beforeEach(() => {
    if (typeof localStorage !== 'undefined') {
      localStorage.clear();
    }
    if (typeof document !== 'undefined') {
      document.cookie = '';
    }
  });

  it('renders all three tabs', () => {
    const wrapper = mount(AppearanceTabs);

    expect(wrapper.text()).toContain('Light');
    expect(wrapper.text()).toContain('Dark');
    expect(wrapper.text()).toContain('System');
  });

  it('renders three buttons', () => {
    const wrapper = mount(AppearanceTabs);

    const buttons = wrapper.findAll('button');
    expect(buttons).toHaveLength(3);
  });

  it('renders icons for each tab', () => {
    const wrapper = mount(AppearanceTabs);

    const svgs = wrapper.findAll('svg');
    expect(svgs.length).toBeGreaterThanOrEqual(3);
  });

  it('updates appearance when light button is clicked', async () => {
    const wrapper = mount(AppearanceTabs);

    const buttons = wrapper.findAll('button');
    await buttons[0].trigger('click');

    expect(localStorage.getItem('appearance')).toBe('light');
  });

  it('updates appearance when dark button is clicked', async () => {
    const wrapper = mount(AppearanceTabs);

    const buttons = wrapper.findAll('button');
    await buttons[1].trigger('click');

    expect(localStorage.getItem('appearance')).toBe('dark');
  });

  it('updates appearance when system button is clicked', async () => {
    const wrapper = mount(AppearanceTabs);

    const buttons = wrapper.findAll('button');
    await buttons[2].trigger('click');

    expect(localStorage.getItem('appearance')).toBe('system');
  });

  it('applies active styling to selected tab', async () => {
    const wrapper = mount(AppearanceTabs);

    const buttons = wrapper.findAll('button');
    await buttons[0].trigger('click');

    await wrapper.vm.$nextTick();

    expect(buttons[0].classes()).toContain('bg-white');
    expect(buttons[0].classes()).toContain('shadow-xs');
  });

  it('applies inactive styling to non-selected tabs', async () => {
    const wrapper = mount(AppearanceTabs);

    const buttons = wrapper.findAll('button');
    await buttons[0].trigger('click');

    await wrapper.vm.$nextTick();

    expect(buttons[1].classes()).toContain('text-neutral-500');
  });
});

