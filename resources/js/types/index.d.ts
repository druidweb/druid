import type { LucideIcon } from 'lucide-vue-next';
import { Config, RouteParams } from 'ziggy-js';
import { PageProps } from '@inertiajs/core';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavItem {
  title: string;
  href: string;
  icon?: LucideIcon;
  isActive?: boolean;
}

export interface SharedData extends PageProps {
  name: string;
  quote: { message: string; author: string };
  auth: Auth;
  ziggy: {
    location: string;
    url: string;
    port: null | number;
    defaults: Record<string, unknown>;
    routes: Record<string, string>;
  };
  sidebarOpen: boolean;
  [key: string]: unknown;
}

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

declare global {
  function route(): Config;
  function route<T extends keyof typeof Ziggy.routes>(name: T, params?: RouteParams<T>, absolute?: boolean): string;
}

// Vue component custom properties
declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    route: typeof route;
    $page: {
      props: SharedData;
    };
  }
}
