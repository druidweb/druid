<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  token: string;
  email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
  <AuthLayout :title="__('base.auth.reset_password')" :description="__('base.auth.reset_password_description')">
    <Head :title="__('base.auth.reset_password')" />

    <Form
      v-bind="update.form()"
      :transform="(data) => ({ ...data, token, email })"
      :reset-on-success="['password', 'password_confirmation']"
      v-slot="{ errors, processing }">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="email">{{ __('base.fields.email') }}</Label>
          <Input id="email" type="email" name="email" autocomplete="email" v-model="inputEmail" class="mt-1 block w-full" readonly />
          <InputError :message="errors.email" class="mt-2" />
        </div>

        <div class="grid gap-2">
          <Label for="password">{{ __('base.fields.password') }}</Label>
          <Input
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
            class="mt-1 block w-full"
            autofocus
            :placeholder="__('base.fields.password')" />
          <InputError :message="errors.password" />
        </div>

        <div class="grid gap-2">
          <Label for="password_confirmation">{{ __('base.auth.confirm_password') }}</Label>
          <Input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
            class="mt-1 block w-full"
            :placeholder="__('base.auth.confirm_password')" />
          <InputError :message="errors.password_confirmation" />
        </div>

        <Button type="submit" class="mt-4 w-full" :disabled="processing" data-test="reset-password-button">
          <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
          {{ __('base.auth.reset_password') }}
        </Button>
      </div>
    </Form>
  </AuthLayout>
</template>
