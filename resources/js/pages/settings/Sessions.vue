<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { destroy, index } from '@/routes/other-browser-sessions';
import { BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, Monitor } from 'lucide-vue-next';
import { ref } from 'vue';
import { __ } from 'zorah-js';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: __('base.sessions.title'),
    href: index.url(),
  },
];

const showConfirmModal = ref(false);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="__('base.sessions.title')" />
    <SettingsLayout>
      <Card>
        <CardHeader>
          <CardTitle>{{ __('base.sessions.title') }}</CardTitle>
          <CardDescription>{{ __('base.sessions.description') }}</CardDescription>
        </CardHeader>

        <CardContent class="space-y-6">
          <p class="text-muted-foreground">
            {{ __('base.sessions.info') }}
          </p>

          <div class="flex items-center space-x-4 rounded-lg border p-4">
            <Monitor class="size-8 text-muted-foreground" />
            <div>
              <p class="font-medium">{{ __('base.sessions.this_device') }}</p>
              <p class="text-sm text-muted-foreground">{{ __('base.sessions.current_session') }}</p>
            </div>
          </div>

          <Button @click="showConfirmModal = true">{{ __('base.sessions.logout_other') }}</Button>
        </CardContent>
      </Card>
    </SettingsLayout>

    <Dialog v-model:open="showConfirmModal">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{{ __('base.sessions.logout_other') }}</DialogTitle>
          <DialogDescription>{{ __('base.sessions.confirm_logout') }}</DialogDescription>
        </DialogHeader>

        <Form v-bind="destroy.form()" @success="showConfirmModal = false" v-slot="{ errors, processing }">
          <div class="space-y-6">
            <div class="grid gap-2">
              <Label for="password">{{ __('base.fields.password') }}</Label>
              <Input id="password" type="password" name="password" class="mt-1 block w-full" required autocomplete="current-password" autofocus />

              <InputError :message="errors.password" />
            </div>

            <DialogFooter>
              <Button type="button" variant="outline" @click="showConfirmModal = false">{{ __('base.actions.cancel') }}</Button>
              <Button type="submit" :disabled="processing">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                {{ __('base.sessions.logout_sessions') }}
              </Button>
            </DialogFooter>
          </div>
        </Form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
