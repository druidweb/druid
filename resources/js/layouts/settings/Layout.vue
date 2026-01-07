<script setup lang="ts">
import SectionLayout from '@/layouts/SectionLayout.vue';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editPassword } from '@/routes/password';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { type NavItem } from '@/types';

const navItems: NavItem[] = [
  {
    title: 'Profile',
    href: editProfile(),
  },
  {
    title: 'Password',
    href: editPassword(),
  },
  {
    title: 'Two-Factor Auth',
    href: show(),
  },
  {
    title: 'Appearance',
    href: editAppearance(),
  },
];
</script>

<template>
  <SectionLayout title="Settings" description="Manage your profile and account settings" :nav-items="navItems">
    <slot />
  </SectionLayout>
</template>
