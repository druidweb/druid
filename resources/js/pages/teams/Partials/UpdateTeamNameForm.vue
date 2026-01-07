<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/teams';
import { useForm } from '@inertiajs/vue3';
import { Transition } from 'vue';

interface Team {
  id: number;
  name: string;
  owner: {
    name: string;
    email: string;
    profile_photo_url: string;
  };
  [key: string]: any;
}

interface Permissions {
  canUpdateTeam: boolean;
  [key: string]: any;
}

const props = defineProps<{
  team: Team;
  permissions: Permissions;
}>();

const form = useForm({
  name: props.team.name,
});

const updateTeamName = () => {
  form.put(update(props.team.id).url, {
    errorBag: 'updateTeamName',
    preserveScroll: true,
  });
};
</script>

<template>
  <Card>
    <form @submit.prevent="updateTeamName">
      <CardHeader>
        <CardTitle>Team Name</CardTitle>
        <CardDescription>The team's name and owner information.</CardDescription>
      </CardHeader>

      <CardContent class="space-y-6">
        <!-- Team Owner Information -->
        <div class="space-y-2">
          <Label>Team Owner</Label>

          <div class="flex items-center">
            <img class="size-12 rounded-full object-cover" :src="team.owner.profile_photo_url" :alt="team.owner.name" />

            <div class="ms-4 leading-tight">
              <div class="text-foreground">{{ team.owner.name }}</div>
              <div class="text-sm text-muted-foreground">
                {{ team.owner.email }}
              </div>
            </div>
          </div>
        </div>

        <!-- Team Name -->
        <div class="space-y-2">
          <Label for="name">Team Name</Label>

          <Input id="name" v-model="form.name" type="text" :disabled="!permissions.canUpdateTeam" />

          <InputError :message="form.errors.name" />
        </div>
      </CardContent>

      <CardFooter v-if="permissions.canUpdateTeam" class="flex items-center justify-end gap-3">
        <Transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
          <div v-show="form.recentlySuccessful" class="text-sm text-muted-foreground">Saved.</div>
        </Transition>

        <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Save </Button>
      </CardFooter>
    </form>
  </Card>
</template>
