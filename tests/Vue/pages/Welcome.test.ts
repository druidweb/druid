import { describe, expect, it, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Welcome from '@/pages/Welcome.vue';

const mockRoute = vi.fn();

vi.mock('@inertiajs/vue3', () => ({
  Head: { name: 'Head', props: ['title'], template: '<head data-testid="head" :data-title="title"></head>' },
  Link: { name: 'Link', props: ['href'], template: '<a data-testid="link" :href="href" :class="$attrs.class"><slot /></a>' },
}));

describe('Welcome', () => {
  beforeEach(() => {
    mockRoute.mockImplementation((name) => {
      const routes = {
        'dashboard': '/dashboard',
        'login': '/login',
        'register': '/register',
      };
      return routes[name] || '/';
    });
    global.route = mockRoute;
  });

  const createWrapper = (pageProps = {}) => {
    const defaultPageProps = {
      auth: { user: null },
    };
    
    return mount(Welcome, {
      global: {
        mocks: {
          route: mockRoute,
          $page: {
            props: { ...defaultPageProps, ...pageProps },
          },
        },
      },
    });
  };

  it('renders correctly', () => {
    const wrapper = createWrapper();
    expect(wrapper.exists()).toBe(true);
    expect(wrapper.find('[data-testid="head"]').exists()).toBe(true);
  });

  it('sets correct page title', () => {
    const wrapper = createWrapper();
    expect(wrapper.find('[data-testid="head"]').attributes('data-title')).toBe('Welcome');
  });

  it('shows login and register links when user is not authenticated', () => {
    const wrapper = createWrapper({ auth: { user: null } });
    
    const links = wrapper.findAll('[data-testid="link"]');
    const loginLink = links.find(link => link.text() === 'Log in');
    const registerLink = links.find(link => link.text() === 'Register');
    
    expect(loginLink).toBeDefined();
    expect(registerLink).toBeDefined();
    expect(loginLink?.attributes('href')).toBe('/login');
    expect(registerLink?.attributes('href')).toBe('/register');
  });

  it('shows dashboard link when user is authenticated', () => {
    const wrapper = createWrapper({ auth: { user: { id: 1, name: 'Test User' } } });
    
    const links = wrapper.findAll('[data-testid="link"]');
    const dashboardLink = links.find(link => link.text() === 'Dashboard');
    
    expect(dashboardLink).toBeDefined();
    expect(dashboardLink?.attributes('href')).toBe('/dashboard');
  });

  it('does not show login/register links when user is authenticated', () => {
    const wrapper = createWrapper({ auth: { user: { id: 1, name: 'Test User' } } });
    
    const links = wrapper.findAll('[data-testid="link"]');
    const loginLink = links.find(link => link.text() === 'Log in');
    const registerLink = links.find(link => link.text() === 'Register');
    
    expect(loginLink).toBeUndefined();
    expect(registerLink).toBeUndefined();
  });

  it('contains main content sections', () => {
    const wrapper = createWrapper();
    
    expect(wrapper.text()).toContain("Let's get started");
    expect(wrapper.text()).toContain('Laravel has an incredibly rich ecosystem');
    expect(wrapper.text()).toContain('Documentation');
    expect(wrapper.text()).toContain('Laracasts');
    expect(wrapper.text()).toContain('Deploy now');
  });

  it('has external links with correct attributes', () => {
    const wrapper = createWrapper();
    
    const docLink = wrapper.find('a[href="https://laravel.com/docs"]');
    const laracastsLink = wrapper.find('a[href="https://laracasts.com"]');
    const deployLink = wrapper.find('a[href="https://cloud.laravel.com"]');
    
    expect(docLink.exists()).toBe(true);
    expect(docLink.attributes('target')).toBe('_blank');
    
    expect(laracastsLink.exists()).toBe(true);
    expect(laracastsLink.attributes('target')).toBe('_blank');
    
    expect(deployLink.exists()).toBe(true);
    expect(deployLink.attributes('target')).toBe('_blank');
  });

  it('has correct layout structure', () => {
    const wrapper = createWrapper();
    
    expect(wrapper.find('header').exists()).toBe(true);
    expect(wrapper.find('nav').exists()).toBe(true);
    expect(wrapper.find('main').exists()).toBe(true);
  });

  it('contains SVG graphics', () => {
    const wrapper = createWrapper();
    const svgs = wrapper.findAll('svg');
    expect(svgs.length).toBeGreaterThan(0);
  });
});
