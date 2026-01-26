<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
import { __ } from 'zorah-js';

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
    title: __('base.two_factor.title'),
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
    <Head :title="__('base.two_factor.title')" />
    <SettingsLayout>
      <Card>
        <CardHeader>
          <CardTitle>{{ __('base.two_factor.title') }}</CardTitle>
          <CardDescription>{{ __('base.two_factor.description') }}</CardDescription>
        </CardHeader>

        <CardContent class="space-y-6">
          <div v-if="!twoFactorEnabled" class="flex flex-col items-start justify-start space-y-4">
            <Badge variant="destructive">{{ __('base.status.disabled') }}</Badge>

            <p class="text-muted-foreground">
              {{ __('base.two_factor.enable_description') }}
            </p>

            <div>
              <Button v-if="hasSetupData" @click="showSetupModal = true"> <ShieldCheck />{{ __('base.two_factor.continue_setup') }} </Button>
              <Form v-else v-bind="enable.form()" @success="showSetupModal = true" #default="{ processing }">
                <Button type="submit" :disabled="processing"> <ShieldCheck />{{ __('base.two_factor.enable') }}</Button></Form
              >
            </div>
          </div>

          <div v-else class="flex flex-col items-start justify-start space-y-4">
            <Badge variant="default">{{ __('base.status.enabled') }}</Badge>

            <p class="text-muted-foreground">
              {{ __('base.two_factor.enabled_description') }}
            </p>

            <TwoFactorRecoveryCodes />

            <div class="relative inline">
              <Form v-bind="disable.form()" #default="{ processing }">
                <Button variant="destructive" type="submit" :disabled="processing">
                  <ShieldBan />
                  {{ __('base.two_factor.disable') }}
                </Button>
              </Form>
            </div>
          </div>

          <TwoFactorSetupModal v-model:isOpen="showSetupModal" :requiresConfirmation="requiresConfirmation" :twoFactorEnabled="twoFactorEnabled" />
        </CardContent>
      </Card>
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
          <DialogTitle>{{ __('base.auth.confirm_your_password') }}</DialogTitle>
          <DialogDescription>{{ __('base.auth.secure_area') }}</DialogDescription>
        </DialogHeader>

        <Form v-bind="store.form()" reset-on-success @success="handlePasswordConfirmed" v-slot="{ errors, processing }">
          <div class="space-y-6">
            <div class="grid gap-2">
              <Label htmlFor="password">{{ __('base.fields.password') }}</Label>
              <Input id="password" type="password" name="password" class="mt-1 block w-full" required autocomplete="current-password" autofocus />

              <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-end">
              <Button class="w-full" :disabled="processing" data-test="confirm-password-button">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                {{ __('base.auth.confirm_password') }}
              </Button>
            </div>
          </div>
        </Form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
