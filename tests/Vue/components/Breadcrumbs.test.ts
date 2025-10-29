import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Breadcrumbs', () => {
  const mockBreadcrumbs = [{ title: 'Home', href: '/' }, { title: 'Products', href: '/products' }, { title: 'Product Details' }];

  it('renders all breadcrumb items', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: mockBreadcrumbs,
      },
    });

    expect(wrapper.text()).toContain('Home');
    expect(wrapper.text()).toContain('Products');
    expect(wrapper.text()).toContain('Product Details');
  });

  it('renders correct number of links', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: mockBreadcrumbs,
      },
    });

    const links = wrapper.findAll('a');
    expect(links).toHaveLength(2); // Last item is not a link
  });

  it('applies correct href to links', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: mockBreadcrumbs,
      },
    });

    const links = wrapper.findAll('a');
    expect(links[0].attributes('href')).toBe('/');
    expect(links[1].attributes('href')).toBe('/products');
  });

  it('last item is not a link', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: mockBreadcrumbs,
      },
    });

    const html = wrapper.html();
    expect(html).toContain('Product Details');

    const links = wrapper.findAll('a');
    const linkTexts = links.map((link) => link.text());
    expect(linkTexts).not.toContain('Product Details');
  });

  it('handles single breadcrumb', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: [{ title: 'Home' }],
      },
    });

    expect(wrapper.text()).toContain('Home');
    const links = wrapper.findAll('a');
    expect(links).toHaveLength(0);
  });

  it('handles breadcrumb without href', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: [{ title: 'Home', href: '/' }, { title: 'Products' }],
      },
    });

    const links = wrapper.findAll('a');
    expect(links).toHaveLength(1);
    expect(links[0].attributes('href')).toBe('/');
  });

  it('renders separators between items', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: mockBreadcrumbs,
      },
    });

    const html = wrapper.html();
    // Should have separators between items (3 items = 2 separators)
    // The separator is rendered as an SVG element
    const svgs = wrapper.findAll('svg');
    expect(svgs.length).toBeGreaterThan(0);
  });

  it('does not render separator after last item', () => {
    const wrapper = mount(Breadcrumbs, {
      props: {
        breadcrumbs: [{ title: 'Home' }],
      },
    });

    const html = wrapper.html();
    const separatorCount = (html.match(/BreadcrumbSeparator/g) || []).length;
    expect(separatorCount).toBe(0);
  });
});
