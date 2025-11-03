<script setup>
import AppLayout from '@/layouts/teams/AppLayout.vue';
import CreateTeamForm from '@/pages/teams/Partials/CreateTeamForm.vue';
</script>

<template>
  <AppLayout title="Create Team">
    <template #header>
      <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">Create Team</h2>
    </template>

    <div>
      <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
        <CreateTeamForm />
      </div>
    </div>
  </AppLayout>
</template>
