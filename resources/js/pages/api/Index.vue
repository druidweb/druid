<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SectionLayout from '@/layouts/SectionLayout.vue';
import ApiTokenManager from '@/pages/api/Partials/ApiTokenManager.vue';
import { index } from '@/routes/api-tokens';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { __ } from 'zorah-js';

interface ApiToken {
  id: number;
  name: string;
  abilities: string[];
  last_used_ago: string | null;
  created_at: string;
  [key: string]: any;
}

defineProps<{
  tokens: ApiToken[];
  availablePermissions: string[];
  defaultPermissions: string[];
}>();

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: __('base.api_tokens.title'),
    href: index().url,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="__('base.api_tokens.title')" />

    <SectionLayout :title="__('base.api_tokens.title')" :description="__('base.api_tokens.manage_description')">
      <ApiTokenManager :tokens="tokens" :available-permissions="availablePermissions" :default-permissions="defaultPermissions" />
    </SectionLayout>
  </AppLayout>
</template>
