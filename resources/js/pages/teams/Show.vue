<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import SectionLayout from '@/layouts/SectionLayout.vue';
import DeleteTeamForm from '@/pages/teams/Partials/DeleteTeamForm.vue';
import TeamMemberManager from '@/pages/teams/Partials/TeamMemberManager.vue';
import UpdateTeamNameForm from '@/pages/teams/Partials/UpdateTeamNameForm.vue';
import { show } from '@/routes/teams';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { __ } from 'zorah-js';

interface TeamMember {
  id: number;
  name: string;
  email: string;
  profile_photo_url: string;
  membership: {
    role: string;
  };
}

interface TeamInvitation {
  id: number;
  email: string;
}

interface Team {
  id: number;
  name: string;
  personal_team: boolean;
  owner: {
    name: string;
    email: string;
    profile_photo_url: string;
  };
  users: TeamMember[];
  team_invitations: TeamInvitation[];
  [key: string]: any;
}

interface Role {
  key: string;
  name: string;
  description: string;
  permissions: string[];
}

interface Permissions {
  canAddTeamMembers: boolean;
  canDeleteTeam: boolean;
  canRemoveTeamMembers: boolean;
  canUpdateTeam: boolean;
  canUpdateTeamMembers: boolean;
  [key: string]: any;
}

const props = defineProps<{
  team: Team;
  availableRoles: Role[];
  permissions: Permissions;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: __('base.teams.settings'),
    href: show(props.team.id).url,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="__('base.teams.settings')" />

    <SectionLayout :title="__('base.teams.settings')" :description="__('base.teams.settings_description')">
      <div class="space-y-10">
        <UpdateTeamNameForm :team="team" :permissions="permissions" />

        <TeamMemberManager :team="team" :available-roles="availableRoles" :user-permissions="permissions" />

        <template v-if="permissions.canDeleteTeam && !team.personal_team">
          <Separator class="my-10" />

          <DeleteTeamForm :team="team" />
        </template>
      </div>
    </SectionLayout>
  </AppLayout>
</template>
