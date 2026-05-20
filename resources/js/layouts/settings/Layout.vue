<script setup lang="ts">
import SectionLayout from '@/layouts/SectionLayout.vue';
import { edit as editAppearance } from '@/routes/appearance';
import { index as browserSessions } from '@/routes/other-browser-sessions';
/* @chisel-update-passwords */
import { edit as editPassword } from '@/routes/password';
/* @end-chisel-update-passwords */
import { edit as editProfile } from '@/routes/profile';
/* @chisel-two-factor */
import { show } from '@/routes/two-factor';
/* @end-chisel-two-factor */
import { type NavItem } from '@/types';
import { __ } from 'zorah-js';

const navItems: NavItem[] = [
  {
    title: __('base.nav.profile'),
    href: editProfile(),
  },
  /* @chisel-update-passwords */
  {
    title: __('base.fields.password'),
    href: editPassword(),
  },
  /* @end-chisel-update-passwords */
  /* @chisel-two-factor */
  {
    title: __('base.nav.two_factor'),
    href: show(),
  },
  /* @end-chisel-two-factor */
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
