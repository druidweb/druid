<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
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

provide('layoutVariant', 'sidebar');
</script>

<template>
  <AppShell variant="sidebar">
    <!-- @chisel-teams -->
    <TeamIndicatorBanner />
    <!-- @end-chisel-teams -->
    <AppSidebar />
    <AppContent variant="sidebar" class="overflow-x-hidden">
      <AppSidebarHeader :breadcrumbs="breadcrumbs" />
      <slot />
    </AppContent>
  </AppShell>
</template>
