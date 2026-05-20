<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
/* @chisel-teams */
import TeamIndicatorBanner from '@/components/TeamIndicatorBanner.vue';
/* @end-chisel-teams */
import type { BreadcrumbItemType } from '@/types';
import { provide } from 'vue';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

provide('layoutVariant', 'default');
</script>

<template>
  <AppShell class="flex-col">
    <!-- @chisel-teams -->
    <TeamIndicatorBanner />
    <!-- @end-chisel-teams -->
    <AppHeader :breadcrumbs="breadcrumbs" />
    <AppContent>
      <slot />
    </AppContent>
  </AppShell>
</template>
