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

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Browser Sessions',
    href: index.url(),
  },
];

const showConfirmModal = ref(false);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Browser Sessions" />
    <SettingsLayout>
      <Card>
        <CardHeader>
          <CardTitle>Browser Sessions</CardTitle>
          <CardDescription>Manage and log out your active sessions on other browsers and devices</CardDescription>
        </CardHeader>

        <CardContent class="space-y-6">
          <p class="text-muted-foreground">
            If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed
            below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.
          </p>

          <div class="flex items-center space-x-4 rounded-lg border p-4">
            <Monitor class="size-8 text-muted-foreground" />
            <div>
              <p class="font-medium">This device</p>
              <p class="text-sm text-muted-foreground">Your current browser session</p>
            </div>
          </div>

          <Button @click="showConfirmModal = true">Log Out Other Browser Sessions</Button>
        </CardContent>
      </Card>
    </SettingsLayout>

    <Dialog v-model:open="showConfirmModal">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Log Out Other Browser Sessions</DialogTitle>
          <DialogDescription>Please enter your password to confirm you would like to log out of your other browser sessions.</DialogDescription>
        </DialogHeader>

        <Form v-bind="destroy.form()" @success="showConfirmModal = false" v-slot="{ errors, processing }">
          <div class="space-y-6">
            <div class="grid gap-2">
              <Label for="password">Password</Label>
              <Input id="password" type="password" name="password" class="mt-1 block w-full" required autocomplete="current-password" autofocus />

              <InputError :message="errors.password" />
            </div>

            <DialogFooter>
              <Button type="button" variant="outline" @click="showConfirmModal = false">Cancel</Button>
              <Button type="submit" :disabled="processing">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Log Out Other Sessions
              </Button>
            </DialogFooter>
          </div>
        </Form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
