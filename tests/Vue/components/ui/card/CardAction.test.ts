import CardAction from '@/components/ui/card/CardAction.vue';
import { mount } from '@vue/test-utils';

describe('CardAction', () => {
  it('renders the component', () => {
    const wrapper = mount(CardAction);
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(CardAction, {
      slots: {
        default: 'Action content',
      },
    });
    expect(wrapper.text()).toBe('Action content');
  });
});

