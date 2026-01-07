<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SectionLayout from '@/layouts/SectionLayout.vue';
import ApiTokenManager from '@/pages/api/Partials/ApiTokenManager.vue';
import { index } from '@/routes/api-tokens';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

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
    title: 'API Tokens',
    href: index().url,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="API Tokens" />

    <SectionLayout title="API Tokens" description="Manage your API tokens for third-party access">
      <ApiTokenManager :tokens="tokens" :available-permissions="availablePermissions" :default-permissions="defaultPermissions" />
    </SectionLayout>
  </AppLayout>
</template>
