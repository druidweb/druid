<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { update as updateCurrentTeam } from '@/routes/current-team';
import { create as createTeam, show as showTeam } from '@/routes/teams';
import { Link, router, usePage } from '@inertiajs/vue3';
import { CheckCircle, ChevronsUpDown, Users } from 'lucide-vue-next';
import { computed, inject } from 'vue';

const page = usePage();
const layoutVariant = inject<'sidebar' | 'default'>('layoutVariant', 'default');

const user = computed(() => page.props.auth.user);
const currentTeam = computed(() => user.value.current_team);
const allTeams = computed(() => user.value.all_teams ?? []);
const canCreateTeams = computed(() => page.props.teams.canCreateTeams);
const hasTeamFeatures = computed(() => page.props.teams.hasTeamFeatures);

// Use current team or fall back to first team
const activeTeam = computed(() => currentTeam.value ?? allTeams.value[0]);
const hasMultipleTeams = computed(() => allTeams.value.length > 1);

const switchTeam = (teamId: number) => {
  if (teamId === currentTeam.value?.id) return;
  router.put(updateCurrentTeam().url, { team_id: teamId }, { preserveState: false });
};
</script>

<template>
  <DropdownMenu v-if="hasTeamFeatures && activeTeam">
    <DropdownMenuTrigger as-child>
      <Button
        variant="ghost"
        :class="['gap-2 text-muted-foreground hover:text-foreground', layoutVariant === 'sidebar' ? 'w-full justify-start' : '']">
        <Users v-if="layoutVariant === 'sidebar'" class="size-4 shrink-0" />
        <span class="max-w-32 truncate">{{ activeTeam.name }}</span>
        <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end" class="w-56">
      <DropdownMenuLabel class="text-xs font-normal text-muted-foreground">{{ __('base.teams.manage_team') }}</DropdownMenuLabel>
      <DropdownMenuItem as-child>
        <Link :href="showTeam(activeTeam.id)" class="cursor-pointer font-medium">{{ __('base.teams.settings') }}</Link>
      </DropdownMenuItem>
      <DropdownMenuItem v-if="canCreateTeams" as-child>
        <Link :href="createTeam()" class="cursor-pointer font-medium">{{ __('base.teams.create_new') }}</Link>
      </DropdownMenuItem>
      <template v-if="hasMultipleTeams">
        <DropdownMenuSeparator />
        <DropdownMenuLabel class="text-xs font-normal text-muted-foreground">{{ __('base.teams.switch_teams') }}</DropdownMenuLabel>
        <DropdownMenuItem v-for="team in allTeams" :key="team.id" class="cursor-pointer" @click="switchTeam(team.id)">
          <CheckCircle v-if="team.id === currentTeam?.id" class="mr-2 size-4 text-green-500" />
          <span :class="{ 'ml-6': team.id !== currentTeam?.id }">{{ team.name }}</span>
        </DropdownMenuItem>
      </template>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
