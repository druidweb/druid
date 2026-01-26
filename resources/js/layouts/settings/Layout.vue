<script setup lang="ts">
import SectionLayout from '@/layouts/SectionLayout.vue';
import { edit as editAppearance } from '@/routes/appearance';
import { index as browserSessions } from '@/routes/other-browser-sessions';
import { edit as editPassword } from '@/routes/password';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { type NavItem } from '@/types';
import { __ } from 'zorah-js';

const navItems: NavItem[] = [
  {
    title: __('base.nav.profile'),
    href: editProfile(),
  },
  {
    title: __('base.fields.password'),
    href: editPassword(),
  },
  {
    title: __('base.nav.two_factor'),
    href: show(),
  },
  {
    title: __('base.nav.browser_sessions'),
    href: browserSessions(),
  },
  {
    title: __('base.nav.appearance'),
    href: editAppearance(),
  },
];
</script>

<template>
  <SectionLayout :title="__('base.nav.settings')" :description="__('base.nav.settings_description')" :nav-items="navItems">
    <slot />
  </SectionLayout>
</template>
