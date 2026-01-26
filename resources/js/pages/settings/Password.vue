<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/password';
import { update } from '@/routes/user-password';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { __ } from 'zorah-js';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: __('base.password.settings'),
    href: edit().url,
  },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="__('base.password.settings')" />

    <SettingsLayout>
      <Card>
        <Form
          v-bind="update.form()"
          :options="{
            preserveScroll: true,
          }"
          reset-on-success
          :reset-on-error="['password', 'password_confirmation', 'current_password']"
          v-slot="{ errors, processing, recentlySuccessful }">
          <CardHeader>
            <CardTitle>{{ __('base.password.title') }}</CardTitle>
            <CardDescription>{{ __('base.password.description') }}</CardDescription>
          </CardHeader>

          <CardContent class="space-y-6">
            <div class="grid gap-2">
              <Label for="current_password">{{ __('base.password.current') }}</Label>
              <Input
                id="current_password"
                ref="currentPasswordInput"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
                :placeholder="__('base.password.current')" />
              <InputError :message="errors.current_password" />
            </div>

            <div class="grid gap-2">
              <Label for="password">{{ __('base.password.new') }}</Label>
              <Input
                id="password"
                ref="passwordInput"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                :placeholder="__('base.password.new')" />
              <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
              <Label for="password_confirmation">{{ __('base.auth.confirm_password') }}</Label>
              <Input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                :placeholder="__('base.auth.confirm_password')" />
              <InputError :message="errors.password_confirmation" />
            </div>
          </CardContent>

          <CardFooter class="flex items-center justify-end gap-3">
            <Transition
              enter-active-class="transition ease-in-out"
              enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out"
              leave-to-class="opacity-0">
              <p v-show="recentlySuccessful" class="text-sm text-muted-foreground">{{ __('base.status.saved') }}</p>
            </Transition>

            <Button :disabled="processing" data-test="update-password-button">{{ __('base.actions.save') }}</Button>
          </CardFooter>
        </Form>
      </Card>
    </SettingsLayout>
  </AppLayout>
</template>
