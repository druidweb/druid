<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';

// Components
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const passwordInput = ref<InstanceType<typeof Input> | null>(null);
</script>

<template>
  <Card class="border-red-100 bg-red-50 dark:border-red-200/10 dark:bg-red-700/10">
    <CardHeader>
      <CardTitle class="text-red-600 dark:text-red-100">Delete Account</CardTitle>
      <CardDescription class="text-red-600/80 dark:text-red-100/80">Delete your account and all of its resources</CardDescription>
    </CardHeader>

    <CardContent class="space-y-4">
      <div class="max-w-xl text-sm text-red-600 dark:text-red-100">
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any
        data or information that you wish to retain.
      </div>

      <div>
        <Dialog>
          <DialogTrigger as-child>
            <Button variant="destructive" data-test="delete-user-button">Delete Account</Button>
          </DialogTrigger>
          <DialogContent>
            <Form
              v-bind="ProfileController.destroy.form()"
              reset-on-success
              @error="() => passwordInput?.$el?.focus()"
              :options="{
                preserveScroll: true,
              }"
              class="space-y-6"
              v-slot="{ errors, processing, reset, clearErrors }">
              <DialogHeader class="space-y-3">
                <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
                <DialogDescription>
                  Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm
                  you would like to permanently delete your account.
                </DialogDescription>
              </DialogHeader>

              <div class="grid gap-2">
                <Label for="password" class="sr-only">Password</Label>
                <Input id="password" type="password" name="password" ref="passwordInput" placeholder="Password" />
                <InputError :message="errors.password" />
              </div>

              <DialogFooter class="gap-2">
                <DialogClose as-child>
                  <Button
                    variant="secondary"
                    @click="
                      () => {
                        clearErrors();
                        reset();
                      }
                    ">
                    Cancel
                  </Button>
                </DialogClose>

                <Button type="submit" variant="destructive" :disabled="processing" data-test="confirm-delete-user-button"> Delete Account </Button>
              </DialogFooter>
            </Form>
          </DialogContent>
        </Dialog>
      </div>
    </CardContent>
  </Card>
</template>
