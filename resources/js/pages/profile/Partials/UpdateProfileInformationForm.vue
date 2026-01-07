<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { destroy as destroyPhoto } from '@/routes/current-user-photo';
import { update } from '@/routes/user-profile-information';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref, Transition } from 'vue';

interface User {
  name: string;
  email: string;
  profile_photo_url: string;
  profile_photo_path?: string;
  email_verified_at: string | null;
  [key: string]: any;
}

const props = defineProps<{
  user: User;
}>();

const form = useForm({
  _method: 'PUT',
  name: props.user.name,
  email: props.user.email,
  photo: null as File | null,
});

const verificationLinkSent = ref(false);
const photoPreview = ref<string | null>(null);
const photoInput = ref<HTMLInputElement | null>(null);

const updateProfileInformation = () => {
  if (photoInput.value?.files?.[0]) {
    form.photo = photoInput.value.files[0];
  }

  form.post(update().url, {
    errorBag: 'updateProfileInformation',
    preserveScroll: true,
    onSuccess: () => clearPhotoFileInput(),
  });
};

const sendEmailVerification = () => {
  verificationLinkSent.value = true;
};

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
};
</script>

<template>
  <Card>
    <form @submit.prevent="updateProfileInformation">
      <CardHeader>
        <CardTitle>Profile Information</CardTitle>
        <CardDescription>Update your account's profile information and email address.</CardDescription>
      </CardHeader>

      <CardContent class="space-y-6">
        <!-- Profile Photo -->
        <div v-if="$page.props.teams.managesProfilePhotos" class="space-y-2">
          <!-- Profile Photo File Input -->
          <input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview" />

          <Label for="photo">Photo</Label>

          <!-- Current Profile Photo -->
          <div v-show="!photoPreview" class="mt-2">
            <img :src="user.profile_photo_url" :alt="user.name" class="size-20 rounded-full object-cover" />
          </div>

          <!-- New Profile Photo Preview -->
          <div v-show="photoPreview" class="mt-2">
            <span class="block size-20 rounded-full bg-cover bg-center bg-no-repeat" :style="'background-image: url(\'' + photoPreview + '\');'" />
          </div>

          <div class="flex gap-2">
            <Button variant="outline" type="button" @click.prevent="selectNewPhoto"> Select A New Photo </Button>

            <Button v-if="user.profile_photo_path" variant="outline" type="button" @click.prevent="deletePhoto"> Remove Photo </Button>
          </div>

          <InputError :message="form.errors.photo" />
        </div>

        <!-- Name -->
        <div class="space-y-2">
          <Label for="name">Name</Label>
          <Input id="name" v-model="form.name" type="text" required autocomplete="name" />
          <InputError :message="form.errors.name" />
        </div>

        <!-- Email -->
        <div class="space-y-2">
          <Label for="email">Email</Label>
          <Input id="email" v-model="form.email" type="email" required autocomplete="username" />
          <InputError :message="form.errors.email" />

          <div v-if="$page.props.teams.hasEmailVerification && user.email_verified_at === null">
            <p class="mt-2 text-sm text-foreground">
              Your email address is unverified.

              <Link
                :href="sendVerificationEmail().url"
                method="post"
                as="button"
                class="rounded-md text-sm text-muted-foreground underline hover:text-foreground focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                @click.prevent="sendEmailVerification">
                Click here to re-send the verification email.
              </Link>
            </p>

            <div v-show="verificationLinkSent" class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
              A new verification link has been sent to your email address.
            </div>
          </div>
        </div>
      </CardContent>

      <CardFooter class="flex items-center justify-end gap-3">
        <Transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
          <div v-show="form.recentlySuccessful" class="text-sm text-muted-foreground">Saved.</div>
        </Transition>

        <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Save </Button>
      </CardFooter>
    </form>
  </Card>
</template>
