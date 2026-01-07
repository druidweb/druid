<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { destroy as destroyPhoto } from '@/routes/current-user-photo';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
import UserAvatar from '@/components/UserAvatar.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { computed, ref } from 'vue';

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Profile settings',
    href: edit().url,
  },
];

const page = usePage<{
  auth: { user: { name: string; email: string; profile_photo_url: string; profile_photo_path?: string; email_verified_at: string | null } };
  teams: { managesProfilePhotos: boolean };
}>();
const user = computed(() => page.props.auth.user);

const photoInput = ref<HTMLInputElement | null>(null);
const photoPreview = ref<string | null>(null);

const selectNewPhoto = () => {
  photoInput.value?.click();
};

const updatePhotoPreview = () => {
  const photo = photoInput.value?.files?.[0];
  if (!photo) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    photoPreview.value = e.target?.result as string;
  };
  reader.readAsDataURL(photo);
};

const deletePhoto = () => {
  router.delete(destroyPhoto(), {
    preserveScroll: true,
    onSuccess: () => {
      photoPreview.value = null;
      clearPhotoFileInput();
    },
  });
};

const clearPhotoFileInput = () => {
  if (photoInput.value?.value) {
    photoInput.value.value = '';
  }
  photoPreview.value = null;
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Profile settings" />

    <SettingsLayout>
      <div class="space-y-10">
        <Card>
          <Form
            v-bind="ProfileController.update.form()"
            enctype="multipart/form-data"
            @success="clearPhotoFileInput"
            v-slot="{ errors, processing, recentlySuccessful }">
            <CardHeader>
              <CardTitle>Profile Information</CardTitle>
              <CardDescription>Update your name and email address</CardDescription>
            </CardHeader>

            <CardContent class="space-y-6">
              <!-- Profile Photo -->
              <div v-if="page.props.teams?.managesProfilePhotos" class="grid gap-2">
                <input id="photo" ref="photoInput" type="file" name="photo" accept="image/*" class="hidden" @change="updatePhotoPreview" />

                <Label>Avatar</Label>

                <!-- Current Profile Photo -->
                <div v-show="!photoPreview" class="mt-2">
                  <UserAvatar size="lg" />
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                  <span
                    class="block size-20 rounded-full bg-cover bg-center bg-no-repeat"
                    :style="'background-image: url(\'' + photoPreview + '\');'" />
                </div>

                <div class="flex gap-2">
                  <Button variant="outline" type="button" @click.prevent="selectNewPhoto">Select New Photo</Button>
                  <Button v-if="user.profile_photo_path" variant="outline" type="button" @click.prevent="deletePhoto">Remove Photo</Button>
                </div>

                <InputError class="mt-2" :message="errors.photo" />
              </div>

              <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                  id="name"
                  class="mt-1 block w-full"
                  name="name"
                  :default-value="user.name"
                  required
                  autocomplete="name"
                  placeholder="Full name" />
                <InputError class="mt-2" :message="errors.name" />
              </div>

              <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                  id="email"
                  type="email"
                  class="mt-1 block w-full"
                  name="email"
                  :default-value="user.email"
                  required
                  autocomplete="username"
                  placeholder="Email address" />
                <InputError class="mt-2" :message="errors.email" />
              </div>

              <div v-if="mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                  Your email address is unverified.
                  <Link
                    :href="send()"
                    as="button"
                    class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
                    Click here to resend the verification email.
                  </Link>
                </p>

                <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                  A new verification link has been sent to your email address.
                </div>
              </div>
            </CardContent>

            <CardFooter class="flex items-center justify-end gap-3">
              <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0">
                <p v-show="recentlySuccessful" class="text-sm text-muted-foreground">Saved.</p>
              </Transition>

              <Button :disabled="processing" data-test="update-profile-button">Save</Button>
            </CardFooter>
          </Form>
        </Card>

        <DeleteUser />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
