<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, inject } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name || 'Laravel');
const layoutVariant = inject<'sidebar' | 'default'>('layoutVariant', 'default');
</script>

<template>
  <div class="flex aspect-square size-8 items-center justify-center rounded bg-black text-sidebar-primary-foreground dark:bg-white">
    <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
  </div>
  <div v-if="layoutVariant === 'sidebar'" class="ml-1 grid flex-1 text-left text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">{{ appName }}</span>
  </div>
</template>
