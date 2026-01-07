import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavItem {
  title: string;
  href: NonNullable<InertiaLinkProps['href']>;
  icon?: LucideIcon;
  isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
  name: string;
  quote: { message: string; author: string };
  auth: Auth;
  sidebarOpen: boolean;
};

export interface Team {
  id: number;
  name: string;
  personal_team: boolean;
  owner_id: number;
}

export interface User {
  id: number;
  name: string;
  email: string;
  profile_photo_url: string;
  profile_photo_path?: string;
  email_verified_at: string | null;
  current_team_id?: number;
  current_team?: Team;
  all_teams?: Team[];
  created_at: string;
  updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
