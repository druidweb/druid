import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';

describe('Card', () => {
  it('renders slot content', () => {
    const wrapper = mount(Card, {
      slots: {
        default: 'Card Content',
      },
    });

    expect(wrapper.text()).toBe('Card Content');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(Card);

    expect(wrapper.attributes('data-slot')).toBe('card');
  });

  it('applies custom class', () => {
    const wrapper = mount(Card, {
      props: {
        class: 'custom-card-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-card-class');
  });

  it('renders as div element', () => {
    const wrapper = mount(Card);

    expect(wrapper.element.tagName).toBe('DIV');
  });

  it('has default styling classes', () => {
    const wrapper = mount(Card);

    expect(wrapper.classes()).toContain('rounded');
    expect(wrapper.classes()).toContain('border');
    expect(wrapper.classes()).toContain('bg-card');
  });
});

describe('CardTitle', () => {
  it('renders slot content', () => {
    const wrapper = mount(CardTitle, {
      slots: {
        default: 'Card Title',
      },
    });

    expect(wrapper.text()).toBe('Card Title');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(CardTitle);

    expect(wrapper.attributes('data-slot')).toBe('card-title');
  });

  it('applies custom class', () => {
    const wrapper = mount(CardTitle, {
      props: {
        class: 'custom-title-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-title-class');
  });

  it('renders as h3 element', () => {
    const wrapper = mount(CardTitle);

    expect(wrapper.element.tagName).toBe('H3');
  });

  it('has font-semibold class', () => {
    const wrapper = mount(CardTitle);

    expect(wrapper.classes()).toContain('font-semibold');
  });
});

describe('CardDescription', () => {
  it('renders slot content', () => {
    const wrapper = mount(CardDescription, {
      slots: {
        default: 'Card Description',
      },
    });

    expect(wrapper.text()).toBe('Card Description');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(CardDescription);

    expect(wrapper.attributes('data-slot')).toBe('card-description');
  });

  it('applies custom class', () => {
    const wrapper = mount(CardDescription, {
      props: {
        class: 'custom-description-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-description-class');
  });

  it('renders as p element', () => {
    const wrapper = mount(CardDescription);

    expect(wrapper.element.tagName).toBe('P');
  });

  it('has text-muted-foreground class', () => {
    const wrapper = mount(CardDescription);

    expect(wrapper.classes()).toContain('text-muted-foreground');
  });
});

describe('CardContent', () => {
  it('renders slot content', () => {
    const wrapper = mount(CardContent, {
      slots: {
        default: 'Content',
      },
    });

    expect(wrapper.text()).toBe('Content');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(CardContent);

    expect(wrapper.attributes('data-slot')).toBe('card-content');
  });

  it('applies custom class', () => {
    const wrapper = mount(CardContent, {
      props: {
        class: 'custom-content-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-content-class');
  });

  it('renders as div element', () => {
    const wrapper = mount(CardContent);

    expect(wrapper.element.tagName).toBe('DIV');
  });

  it('has px-6 class', () => {
    const wrapper = mount(CardContent);

    expect(wrapper.classes()).toContain('px-6');
  });
});

describe('CardHeader', () => {
  it('renders slot content', () => {
    const wrapper = mount(CardHeader, {
      slots: {
        default: 'Header',
      },
    });

    expect(wrapper.text()).toBe('Header');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(CardHeader);

    expect(wrapper.attributes('data-slot')).toBe('card-header');
  });

  it('applies custom class', () => {
    const wrapper = mount(CardHeader, {
      props: {
        class: 'custom-header-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-header-class');
  });

  it('renders as div element', () => {
    const wrapper = mount(CardHeader);

    expect(wrapper.element.tagName).toBe('DIV');
  });
});

describe('CardFooter', () => {
  it('renders slot content', () => {
    const wrapper = mount(CardFooter, {
      slots: {
        default: 'Footer',
      },
    });

    expect(wrapper.text()).toBe('Footer');
  });

  it('has data-slot attribute', () => {
    const wrapper = mount(CardFooter);

    expect(wrapper.attributes('data-slot')).toBe('card-footer');
  });

  it('applies custom class', () => {
    const wrapper = mount(CardFooter, {
      props: {
        class: 'custom-footer-class',
      },
    });

    expect(wrapper.classes()).toContain('custom-footer-class');
  });

  it('renders as div element', () => {
    const wrapper = mount(CardFooter);

    expect(wrapper.element.tagName).toBe('DIV');
  });
});

