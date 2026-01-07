<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { destroy } from '@/routes/current-user';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;

  setTimeout(() => passwordInput.value?.focus(), 250);
};

const deleteUser = () => {
  form.delete(destroy(), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;

  form.reset();
};
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Delete Account</CardTitle>
      <CardDescription>Permanently delete your account.</CardDescription>
    </CardHeader>

    <CardContent class="space-y-5">
      <div class="max-w-xl text-sm text-muted-foreground">
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any
        data or information that you wish to retain.
      </div>

      <div>
        <Button variant="destructive" @click="confirmUserDeletion"> Delete Account </Button>
      </div>

      <!-- Delete Account Confirmation Modal -->
      <Dialog :open="confirmingUserDeletion" @update:open="confirmingUserDeletion = $event">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Account</DialogTitle>
            <DialogDescription>
              Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted.
              Please enter your password to confirm you would like to permanently delete your account.
            </DialogDescription>
          </DialogHeader>

          <div class="mt-4">
            <Input
              ref="passwordInput"
              v-model="form.password"
              type="password"
              class="w-3/4"
              placeholder="Password"
              autocomplete="current-password"
              @keyup.enter="deleteUser" />

            <InputError :message="form.errors.password" class="mt-2" />
          </div>

          <DialogFooter>
            <Button variant="outline" @click="closeModal"> Cancel </Button>

            <Button variant="destructive" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteUser">
              Delete Account
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </CardContent>
  </Card>
</template>
