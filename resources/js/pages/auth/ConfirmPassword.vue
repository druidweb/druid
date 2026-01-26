<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/vue3';
import { KeyRound, LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const isOpen = ref(true);

const handleOpenChange = (open: boolean) => {
  if (!open) {
    // User cancelled - go back to previous page
    window.history.back();
  }
  isOpen.value = open;
};
</script>

<template>
  <AppLayout>
    <Head :title="__('base.auth.confirm_password')" />

    <Dialog :open="isOpen" @update:open="handleOpenChange">
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

        <Form v-bind="store.form()" reset-on-success v-slot="{ errors, processing }">
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
