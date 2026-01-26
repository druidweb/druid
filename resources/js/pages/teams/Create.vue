<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SectionLayout from '@/layouts/SectionLayout.vue';
import CreateTeamForm from '@/pages/teams/Partials/CreateTeamForm.vue';
import { create } from '@/routes/teams';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { __ } from 'zorah-js';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: __('base.teams.create'),
    href: create().url,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="__('base.teams.create')" />

    <SectionLayout :title="__('base.teams.create')" :description="__('base.teams.create_description')">
      <CreateTeamForm />
    </SectionLayout>
  </AppLayout>
</template>
