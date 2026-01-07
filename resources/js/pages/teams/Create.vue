<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SectionLayout from '@/layouts/SectionLayout.vue';
import CreateTeamForm from '@/pages/teams/Partials/CreateTeamForm.vue';
import { create } from '@/routes/teams';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Create Team',
    href: create().url,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Create Team" />

    <SectionLayout title="Create Team" description="Create a new team to collaborate with others">
      <CreateTeamForm />
    </SectionLayout>
  </AppLayout>
</template>
