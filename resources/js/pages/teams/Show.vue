<script setup>
import SectionBorder from '@/components/teams/SectionBorder.vue';
import AppLayout from '@/layouts/teams/AppLayout.vue';
import DeleteTeamForm from '@/pages/teams/Partials/DeleteTeamForm.vue';
import TeamMemberManager from '@/pages/teams/Partials/TeamMemberManager.vue';
import UpdateTeamNameForm from '@/pages/teams/Partials/UpdateTeamNameForm.vue';

defineProps({
  team: Object,
  availableRoles: Array,
  permissions: Object,
});
</script>

<template>
  <AppLayout title="Team Settings">
    <template #header>
      <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">Team Settings</h2>
    </template>

    <div>
      <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
        <UpdateTeamNameForm :team="team" :permissions="permissions" />

        <TeamMemberManager class="mt-10 sm:mt-0" :team="team" :available-roles="availableRoles" :user-permissions="permissions" />

        <template v-if="permissions.canDeleteTeam && !team.personal_team">
          <SectionBorder />

          <DeleteTeamForm class="mt-10 sm:mt-0" :team="team" />
        </template>
      </div>
    </div>
  </AppLayout>
</template>
