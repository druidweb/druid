<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const hasTeamFeatures = computed(() => page.props.teams?.hasTeamFeatures);
const user = computed(() => page.props.auth?.user);
const currentTeam = computed(() => user.value?.current_team);

// Show banner if teams are enabled AND current team is NOT a personal team
const shouldShowBanner = computed(() => {
  return hasTeamFeatures.value && currentTeam.value && !currentTeam.value.personal_team;
});
</script>

<template>
  <div class="h-0.5 w-full transition-colors duration-300" :class="shouldShowBanner ? 'bg-primary' : 'bg-transparent'"></div>
</template>
