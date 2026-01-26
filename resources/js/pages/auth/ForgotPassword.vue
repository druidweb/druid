<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
  status?: string;
}>();
</script>

<template>
  <AuthLayout :title="__('base.auth.forgot_password')" :description="__('base.auth.forgot_password_description')">
    <Head :title="__('base.auth.forgot_password')" />

    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <div class="space-y-6">
      <Form v-bind="email.form()" v-slot="{ errors, processing }">
        <div class="grid gap-2">
          <Label for="email">{{ __('base.fields.email_address') }}</Label>
          <Input id="email" type="email" name="email" autocomplete="off" autofocus :placeholder="__('base.fields.email_placeholder')" />
          <InputError :message="errors.email" />
        </div>

        <div class="my-6 flex items-center justify-start">
          <Button class="w-full" :disabled="processing" data-test="email-password-reset-link-button">
            <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
            Email password reset link
          </Button>
        </div>
      </Form>

      <div class="space-x-1 text-center text-sm text-muted-foreground">
        <span>{{ __('base.auth.or_return_to') }}</span>
        <TextLink :href="login()">{{ __('base.auth.log_in') }}</TextLink>
      </div>
    </div>
  </AuthLayout>
</template>
