<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { show as privacyPolicyShow } from '@/routes/policy';
import { store } from '@/routes/register';
import { show as termsShow } from '@/routes/terms';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const page = usePage();

const termsUrl = termsShow.url();
const privacyPolicyUrl = privacyPolicyShow.url();
</script>

<template>
  <AuthBase title="Create an account" description="Enter your details below to create your account">
    <Head title="Register" />

    <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']" v-slot="{ errors, processing }" class="flex flex-col gap-6">
      <div class="grid gap-6">
        <div class="grid gap-2">
          <Label for="name">Name</Label>
          <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name" placeholder="Full name" />
          <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
          <Label for="email">Email address</Label>
          <Input id="email" type="email" required :tabindex="2" autocomplete="email" name="email" placeholder="email@example.com" />
          <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
          <Label for="password">Password</Label>
          <Input id="password" type="password" required :tabindex="3" autocomplete="new-password" name="password" placeholder="Password" />
          <InputError :message="errors.password" />
        </div>

        <div class="grid gap-2">
          <Label for="password_confirmation">Confirm password</Label>
          <Input
            id="password_confirmation"
            type="password"
            required
            :tabindex="4"
            autocomplete="new-password"
            name="password_confirmation"
            placeholder="Confirm password" />
          <InputError :message="errors.password_confirmation" />
        </div>

        <div v-if="page.props.teams.hasTermsAndPrivacyPolicyFeature" class="flex items-start gap-2">
          <Checkbox id="terms" name="terms" value="true" :tabindex="5" class="mt-0.5" />
          <div class="grid gap-1.5 leading-none">
            <label for="terms" class="cursor-pointer text-sm leading-normal font-normal text-muted-foreground">
              I agree to the
              <a :href="termsUrl" target="_blank" class="text-foreground underline underline-offset-2 hover:text-foreground/80">Terms of Service</a>
              and
              <a :href="privacyPolicyUrl" target="_blank" class="text-foreground underline underline-offset-2 hover:text-foreground/80"
                >Privacy Policy</a
              >
            </label>
            <InputError :message="errors.terms" />
          </div>
        </div>

        <Button type="submit" class="mt-2 w-full" :tabindex="6" :disabled="processing" data-test="register-user-button">
          <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
          Create account
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Already have an account?
        <TextLink :href="login()" class="underline underline-offset-4" :tabindex="7">Log in</TextLink>
      </div>
    </Form>
  </AuthBase>
</template>
