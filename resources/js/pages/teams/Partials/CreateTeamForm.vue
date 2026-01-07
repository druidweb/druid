<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/teams';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
});

const createTeam = () => {
  form.post(store().url, {
    errorBag: 'createTeam',
    preserveScroll: true,
  });
};
</script>

<template>
  <Card>
    <form @submit.prevent="createTeam">
      <CardHeader>
        <CardTitle>Team Details</CardTitle>
        <CardDescription>Create a new team to collaborate with others on projects.</CardDescription>
      </CardHeader>

      <CardContent class="space-y-6">
        <div class="space-y-2">
          <Label>Team Owner</Label>

          <div class="flex items-center">
            <img class="size-12 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />

            <div class="ms-4 leading-tight">
              <div class="text-foreground">{{ $page.props.auth.user.name }}</div>
              <div class="text-sm text-muted-foreground">
                {{ $page.props.auth.user.email }}
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-2">
          <Label for="name">Team Name</Label>
          <Input id="name" v-model="form.name" type="text" autofocus />
          <InputError :message="form.errors.name" />
        </div>
      </CardContent>

      <CardFooter class="flex items-center justify-end">
        <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Create </Button>
      </CardFooter>
    </form>
  </Card>
</template>
