<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { store } from '@/routes/password/confirm';
import { disable, enable, show } from '@/routes/two-factor';
import { BreadcrumbItem } from '@/types';
import { Form, Head, router } from '@inertiajs/vue3';
import { KeyRound, LoaderCircle, ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';

interface Props {
  requiresConfirmation?: boolean;
  twoFactorEnabled?: boolean;
  passwordConfirmed?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  requiresConfirmation: false,
  twoFactorEnabled: false,
  passwordConfirmed: false,
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Two-Factor Authentication',
    href: show.url(),
  },
];

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);
const showPasswordModal = ref<boolean>(false);

onMounted(() => {
  // Show password confirmation modal if not confirmed
  if (!props.passwordConfirmed) {
    showPasswordModal.value = true;
  }
});

onUnmounted(() => {
  clearTwoFactorAuthData();
});

const handlePasswordModalClose = (open: boolean) => {
  if (!open && !props.passwordConfirmed) {
    // User cancelled - go back to previous page
    window.history.back();
  }
  showPasswordModal.value = open;
};

const handlePasswordConfirmed = () => {
  showPasswordModal.value = false;
  // The backend will redirect to the intended URL
  // Just reload to get the updated passwordConfirmed state
  router.reload({ only: ['passwordConfirmed'] });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Two-Factor Authentication" />
    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall title="Two-Factor Authentication" description="Manage your two-factor authentication settings" />

        <div v-if="!twoFactorEnabled" class="flex flex-col items-start justify-start space-y-4">
          <Badge variant="destructive">Disabled</Badge>

          <p class="text-muted-foreground">
            When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a
            TOTP-supported application on your phone.
          </p>

          <div>
            <Button v-if="hasSetupData" @click="showSetupModal = true"> <ShieldCheck />Continue Setup </Button>
            <Form v-else v-bind="enable.form()" @success="showSetupModal = true" #default="{ processing }">
              <Button type="submit" :disabled="processing"> <ShieldCheck />Enable 2FA</Button></Form
            >
          </div>
        </div>

        <div v-else class="flex flex-col items-start justify-start space-y-4">
          <Badge variant="default">Enabled</Badge>

          <p class="text-muted-foreground">
            With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the
            TOTP-supported application on your phone.
          </p>

          <TwoFactorRecoveryCodes />

          <div class="relative inline">
            <Form v-bind="disable.form()" #default="{ processing }">
              <Button variant="destructive" type="submit" :disabled="processing">
                <ShieldBan />
                Disable 2FA
              </Button>
            </Form>
          </div>
        </div>

        <TwoFactorSetupModal v-model:isOpen="showSetupModal" :requiresConfirmation="requiresConfirmation" :twoFactorEnabled="twoFactorEnabled" />
      </div>
    </SettingsLayout>

    <!-- Password Confirmation Modal -->
    <Dialog v-model:open="showPasswordModal" @update:open="handlePasswordModalClose">
      <DialogContent class="sm:max-w-md">
        <DialogHeader class="flex items-center justify-center">
          <div class="mb-3 w-auto">
            <div class="relative overflow-hidden rounded-full bg-muted p-2.5">
              <div class="absolute inset-0 grid grid-cols-5 opacity-50">
                <div v-for="i in 5" :key="`col-${i}`" class="border-r border-border last:border-r-0" />
              </div>
              <div class="absolute inset-0 grid grid-rows-5 opacity-50">
                <div v-for="i in 5" :key="`row-${i}`" class="border-b border-border last:border-b-0" />
              </div>
              <KeyRound class="relative z-20 size-6 text-foreground" />
            </div>
          </div>
          <DialogTitle>Confirm your password</DialogTitle>
          <DialogDescription>This is a secure area of the application. Please confirm your password before continuing.</DialogDescription>
        </DialogHeader>

        <Form v-bind="store.form()" reset-on-success @success="handlePasswordConfirmed" v-slot="{ errors, processing }">
          <div class="space-y-6">
            <div class="grid gap-2">
              <Label htmlFor="password">Password</Label>
              <Input id="password" type="password" name="password" class="mt-1 block w-full" required autocomplete="current-password" autofocus />

              <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-end">
              <Button class="w-full" :disabled="processing" data-test="confirm-password-button">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Confirm Password
              </Button>
            </div>
          </div>
        </Form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
