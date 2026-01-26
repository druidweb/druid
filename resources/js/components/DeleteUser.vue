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
      <CardTitle class="text-red-600 dark:text-red-100">{{ __('base.delete_account.title') }}</CardTitle>
      <CardDescription class="text-red-600/80 dark:text-red-100/80">{{ __('base.delete_account.description') }}</CardDescription>
    </CardHeader>

    <CardContent class="space-y-4">
      <div class="max-w-xl text-sm text-red-600 dark:text-red-100">
        {{ __('base.delete_account.warning') }}
      </div>

      <div>
        <Dialog>
          <DialogTrigger as-child>
            <Button variant="destructive" data-test="delete-user-button">{{ __('base.delete_account.title') }}</Button>
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
                <DialogTitle>{{ __('base.delete_account.confirm_title') }}</DialogTitle>
                <DialogDescription>
                  {{ __('base.delete_account.confirm_description') }}
                </DialogDescription>
              </DialogHeader>

              <div class="grid gap-2">
                <Label for="password" class="sr-only">{{ __('base.fields.password') }}</Label>
                <Input id="password" type="password" name="password" ref="passwordInput" :placeholder="__('base.fields.password')" />
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
                    {{ __('base.actions.cancel') }}
                  </Button>
                </DialogClose>

                <Button type="submit" variant="destructive" :disabled="processing" data-test="confirm-delete-user-button">{{
                  __('base.delete_account.title')
                }}</Button>
              </DialogFooter>
            </Form>
          </DialogContent>
        </Dialog>
      </div>
    </CardContent>
  </Card>
</template>
