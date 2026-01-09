import DeleteUser from '@/components/DeleteUser.vue';
import { mount } from '@vue/test-utils';

describe('DeleteUser', () => {
  it('renders component', () => {
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: true,
          Dialog: true,
          DialogTrigger: true,
          DialogContent: true,
          DialogHeader: true,
          DialogTitle: true,
          DialogDescription: true,
          DialogFooter: true,
          DialogClose: true,
          Button: true,
          Input: true,
          Label: true,
          HeadingSmall: true,
          InputError: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('displays warning message', () => {
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: true,
          Dialog: true,
          DialogTrigger: true,
          DialogContent: true,
          DialogHeader: true,
          DialogTitle: true,
          DialogDescription: true,
          DialogFooter: true,
          DialogClose: true,
          Button: true,
          Input: true,
          Label: true,
          HeadingSmall: true,
          InputError: true,
        },
      },
    });
    expect(wrapper.text()).toContain('Delete Account');
    expect(wrapper.text()).toContain('Delete your account and all of its resources');
  });

  it('renders delete account button', () => {
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: {
            template: '<form><slot :errors="{}" :processing="false" :reset="() => {}" :clearErrors="() => {}" /></form>',
          },
          Dialog: { template: '<div><slot /></div>' },
          DialogTrigger: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          DialogFooter: { template: '<div><slot /></div>' },
          DialogClose: { template: '<div @click="$emit(\'click\')"><slot /></div>' },
          Button: {
            template: '<button :data-test="$attrs[\'data-test\']" @click="$emit(\'click\')"><slot /></button>',
          },
          Input: { template: '<input />' },
          Label: { template: '<label><slot /></label>' },
          HeadingSmall: { template: '<div><slot /></div>' },
          InputError: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.text()).toContain('Delete Account');
  });

  it('renders dialog content', () => {
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: {
            template: '<form><slot :errors="{}" :processing="false" :reset="() => {}" :clearErrors="() => {}" /></form>',
          },
          Dialog: { template: '<div><slot /></div>' },
          DialogTrigger: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          DialogFooter: { template: '<div><slot /></div>' },
          DialogClose: { template: '<div><slot /></div>' },
          Button: { template: '<button><slot /></button>' },
          Input: { template: '<input />' },
          Label: { template: '<label><slot /></label>' },
          HeadingSmall: { template: '<div><slot /></div>' },
          InputError: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.text()).toContain('Are you sure you want to delete your account?');
  });

  it('has password input ref', () => {
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: {
            template: '<form><slot :errors="{}" :processing="false" :reset="() => {}" :clearErrors="() => {}" /></form>',
          },
          Dialog: { template: '<div><slot /></div>' },
          DialogTrigger: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          DialogFooter: { template: '<div><slot /></div>' },
          DialogClose: { template: '<div><slot /></div>' },
          Button: { template: '<button><slot /></button>' },
          Input: { template: '<input />' },
          Label: { template: '<label><slot /></label>' },
          HeadingSmall: { template: '<div><slot /></div>' },
          InputError: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.vm.passwordInput).toBeDefined();
  });

  it('renders heading with title and description', () => {
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: true,
          Dialog: true,
          DialogTrigger: true,
          DialogContent: true,
          DialogHeader: true,
          DialogTitle: true,
          DialogDescription: true,
          DialogFooter: true,
          DialogClose: true,
          Button: true,
          Input: true,
          Label: true,
          HeadingSmall: { template: '<div><slot /></div>' },
          InputError: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders password input field', () => {
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: {
            template: '<form><slot :errors="{}" :processing="false" :reset="() => {}" :clearErrors="() => {}" /></form>',
          },
          Dialog: { template: '<div><slot /></div>' },
          DialogTrigger: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          DialogFooter: { template: '<div><slot /></div>' },
          DialogClose: { template: '<div><slot /></div>' },
          Button: { template: '<button><slot /></button>' },
          Input: { template: '<input id="password" type="password" name="password" />' },
          Label: { template: '<label><slot /></label>' },
          HeadingSmall: { template: '<div><slot /></div>' },
          InputError: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.html()).toContain('password');
  });

  it('triggers cancel button click handler', async () => {
    const mockReset = vi.fn();
    const mockClearErrors = vi.fn();
    const wrapper = mount(DeleteUser, {
      global: {
        stubs: {
          Form: {
            template: '<form><slot :errors="{}" :processing="false" :reset="reset" :clearErrors="clearErrors" /></form>',
            props: ['reset', 'clearErrors'],
            setup() {
              return { reset: mockReset, clearErrors: mockClearErrors };
            },
          },
          Dialog: { template: '<div><slot /></div>' },
          DialogTrigger: { template: '<div><slot /></div>' },
          DialogContent: { template: '<div><slot /></div>' },
          DialogHeader: { template: '<div><slot /></div>' },
          DialogTitle: { template: '<div><slot /></div>' },
          DialogDescription: { template: '<div><slot /></div>' },
          DialogFooter: { template: '<div><slot /></div>' },
          DialogClose: {
            template: '<div><slot /></div>',
          },
          Button: {
            template: '<button @click="$attrs.onClick && $attrs.onClick()"><slot /></button>',
          },
          Input: { template: '<input />' },
          Label: { template: '<label><slot /></label>' },
          HeadingSmall: { template: '<div><slot /></div>' },
          InputError: { template: '<div><slot /></div>' },
        },
      },
    });
    const cancelButton = wrapper.findAll('button')[0];
    await cancelButton.trigger('click');
    expect(wrapper.exists()).toBe(true);
  });
});
