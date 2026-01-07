<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import TeamIndicatorBanner from '@/components/TeamIndicatorBanner.vue';
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
    <TeamIndicatorBanner />
    <AppHeader :breadcrumbs="breadcrumbs" />
    <AppContent>
      <slot />
    </AppContent>
  </AppShell>
</template>
