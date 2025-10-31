import NavFooter from '@/components/NavFooter.vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { mount } from '@vue/test-utils';
import { BookOpen, Folder, Github } from 'lucide-vue-next';
import { describe, expect, it, vi } from 'vitest';
import { h } from 'vue';

// Mock the toUrl utility
vi.mock('@/lib/utils', () => ({
  toUrl: vi.fn((url) => url),
  cn: vi.fn((...args) => args.filter(Boolean).join(' ')),
}));

describe('NavFooter', () => {
  const mockItems: NavItem[] = [
    {
      title: 'Github Repo',
      href: 'https://github.com/druidweb/druid',
      icon: Folder,
    },
    {
      title: 'Documentation',
      href: 'https://laravel.com/docs',
      icon: BookOpen,
    },
  ];

  const mountWithProvider = (props: any) => {
    return mount(SidebarProvider, {
      props: {
        defaultOpen: true,
      },
      slots: {
        default: () => h(NavFooter, props),
      },
    });
  };

  it('renders component', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    expect(wrapper.exists()).toBe(true);
  });

  it('renders all nav items', async () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    await wrapper.vm.$nextTick();
    const links = wrapper.findAll('a');
    expect(links).toHaveLength(2);
  });

  it('renders item titles and icons', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    expect(wrapper.text()).toContain('Github Repo');
    expect(wrapper.text()).toContain('Documentation');

    // Icons should be rendered
    const svgs = wrapper.findAll('svg');
    expect(svgs.length).toBeGreaterThan(0);
  });

  it('renders correct href attributes', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    const links = wrapper.findAll('a');
    expect(links[0].attributes('href')).toBe('https://github.com/druidweb/druid');
    expect(links[1].attributes('href')).toBe('https://laravel.com/docs');
  });

  it('opens links in new tab with security attributes', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
    });

    const links = wrapper.findAll('a');
    links.forEach((link) => {
      expect(link.attributes('target')).toBe('_blank');
      expect(link.attributes('rel')).toBe('noopener noreferrer');
    });
  });

  it('applies custom class prop', () => {
    const wrapper = mountWithProvider({
      items: mockItems,
      class: 'custom-class',
    });

    expect(wrapper.html()).toContain('custom-class');
  });

  it('renders with single item', () => {
    const singleItem: NavItem[] = [
      {
        title: 'Single Link',
        href: 'https://example.com',
        icon: Github,
      },
    ];

    const wrapper = mountWithProvider({
      items: singleItem,
    });

    expect(wrapper.text()).toContain('Single Link');
    const links = wrapper.findAll('a');
    expect(links).toHaveLength(1);
  });

  it('renders with empty items array', () => {
    const wrapper = mountWithProvider({
      items: [],
    });

    expect(wrapper.exists()).toBe(true);
    const links = wrapper.findAll('a');
    expect(links).toHaveLength(0);
  });

  it('renders multiple items with different icons', () => {
    const multipleItems: NavItem[] = [
      { title: 'Item 1', href: 'https://one.com', icon: Folder },
      { title: 'Item 2', href: 'https://two.com', icon: BookOpen },
      { title: 'Item 3', href: 'https://three.com', icon: Github },
    ];

    const wrapper = mountWithProvider({
      items: multipleItems,
    });

    const links = wrapper.findAll('a');
    expect(links).toHaveLength(3);
    expect(wrapper.text()).toContain('Item 1');
    expect(wrapper.text()).toContain('Item 2');
    expect(wrapper.text()).toContain('Item 3');
  });
});
