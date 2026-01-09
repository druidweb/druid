import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { mount } from '@vue/test-utils';

vi.mock('@inertiajs/vue3', () => ({
  usePage: () => ({
    props: {
      auth: { user: { id: 1, name: 'Test' } },
      teams: { hasTeamFeatures: false },
    },
  }),
}));

describe('AppSidebarLayout', () => {
  it('renders component', () => {
    const wrapper = mount(AppSidebarLayout, {
      global: {
        stubs: {
          AppShell: true,
          AppSidebar: true,
          AppContent: true,
          AppSidebarHeader: true,
          TeamIndicatorBanner: true,
          SidebarProvider: true,
          SidebarInset: true,
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('passes breadcrumbs to AppSidebarHeader', () => {
    const breadcrumbs = [{ label: 'Dashboard', href: '/dashboard' }];
    const wrapper = mount(AppSidebarLayout, {
      props: {
        breadcrumbs,
      },
      global: {
        stubs: {
          AppShell: { template: '<div><slot /></div>' },
          AppSidebar: { template: '<div></div>' },
          AppContent: { template: '<div><slot /></div>' },
          AppSidebarHeader: {
            template: '<div></div>',
            props: ['breadcrumbs'],
          },
          TeamIndicatorBanner: { template: '<div></div>' },
          SidebarProvider: { template: '<div><slot /></div>' },
          SidebarInset: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.exists()).toBe(true);
  });

  it('renders slot content', () => {
    const wrapper = mount(AppSidebarLayout, {
      slots: {
        default: '<div>Dashboard Content</div>',
      },
      global: {
        stubs: {
          AppShell: { template: '<div><slot /></div>' },
          AppSidebar: { template: '<div></div>' },
          AppContent: { template: '<div><slot /></div>' },
          AppSidebarHeader: { template: '<div></div>' },
          TeamIndicatorBanner: { template: '<div></div>' },
          SidebarProvider: { template: '<div><slot /></div>' },
          SidebarInset: { template: '<div><slot /></div>' },
        },
      },
    });
    expect(wrapper.html()).toContain('Dashboard Content');
  });
});
