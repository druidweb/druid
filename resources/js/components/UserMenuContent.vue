<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { index as apiTokensIndex } from '@/routes/api-tokens';
import { edit } from '@/routes/profile';
import { Link, router, usePage } from '@inertiajs/vue3';
import { KeyRound, LogOut, Settings } from 'lucide-vue-next';

const page = usePage();

const handleLogout = () => {
  router.flushAll();
};
</script>

<template>
  <DropdownMenuLabel class="p-0 font-normal">
    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
      <UserInfo :show-email="true" />
    </div>
  </DropdownMenuLabel>
  <DropdownMenuSeparator />
  <DropdownMenuGroup>
    <DropdownMenuItem :as-child="true">
      <Link class="block w-full" :href="edit()" prefetch as="button">
        <Settings class="mr-2 h-4 w-4" />
        {{ __('base.nav.settings') }}
      </Link>
    </DropdownMenuItem>
    <DropdownMenuItem v-if="page.props.teams.hasApiFeatures" :as-child="true">
      <Link class="block w-full" :href="apiTokensIndex()" prefetch as="button">
        <KeyRound class="mr-2 h-4 w-4" />
        {{ __('base.api_tokens.title') }}
      </Link>
    </DropdownMenuItem>
  </DropdownMenuGroup>
  <DropdownMenuSeparator />
  <DropdownMenuItem :as-child="true">
    <Link class="block w-full" :href="logout()" @click="handleLogout" as="button" data-test="logout-button">
      <LogOut class="mr-2 h-4 w-4" />
      {{ __('base.auth.log_out') }}
    </Link>
  </DropdownMenuItem>
</template>
