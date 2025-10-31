import DropdownMenuShortcut from '@/components/ui/dropdown-menu/DropdownMenuShortcut.vue';
import { mount } from '@vue/test-utils';

describe('DropdownMenuShortcut', () => {
  it('renders component', () => {
    const wrapper = mount(DropdownMenuShortcut);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(DropdownMenuShortcut, {
      slots: {
        default: '⌘K',
      },
    });
    expect(wrapper.text()).toContain('⌘K');
  });

  it('applies custom class', () => {
    const wrapper = mount(DropdownMenuShortcut, {
      props: {
        class: 'custom-shortcut',
      },
    });
    expect(wrapper.classes()).toContain('custom-shortcut');
  });
});

