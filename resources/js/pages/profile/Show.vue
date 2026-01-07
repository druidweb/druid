<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DeleteUserForm from '@/pages/profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/pages/profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import UpdateProfileInformationForm from '@/pages/profile/Partials/UpdateProfileInformationForm.vue';

interface Session {
  agent: {
    is_desktop: boolean;
    platform: string;
    browser: string;
  };
  ip_address: string;
  is_current_device: boolean;
  last_active: string;
}

defineProps<{
  sessions: Session[];
}>();
</script>

<template>
  <AppLayout title="Profile">
    <template #header>
      <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">Profile</h2>
    </template>

    <div>
      <div class="mx-auto max-w-7xl space-y-10 py-10 sm:px-6 lg:px-8">
        <div v-if="$page.props.teams.canUpdateProfileInformation">
          <UpdateProfileInformationForm :user="$page.props.auth.user" />
        </div>

        <Separator v-if="$page.props.teams.canUpdateProfileInformation" />

        <LogoutOtherBrowserSessionsForm :sessions="sessions" />

        <template v-if="$page.props.teams.hasAccountDeletionFeatures">
          <Separator />

          <DeleteUserForm />
        </template>
      </div>
    </div>
  </AppLayout>
</template>
