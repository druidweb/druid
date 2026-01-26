<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { destroy } from '@/routes/teams';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Team {
  id: number;
  name: string;
  [key: string]: any;
}

const props = defineProps<{
  team: Team;
}>();

const confirmingTeamDeletion = ref(false);
const form = useForm({});

const confirmTeamDeletion = () => {
  confirmingTeamDeletion.value = true;
};

const deleteTeam = () => {
  form.delete(destroy(props.team.id).url, {
    errorBag: 'deleteTeam',
  });
};
</script>

<template>
  <Card class="border-red-100 bg-red-50 dark:border-red-200/10 dark:bg-red-700/10">
    <CardHeader>
      <CardTitle class="text-red-600 dark:text-red-100">{{ __('base.teams.delete') }}</CardTitle>
      <CardDescription class="text-red-600/80 dark:text-red-100/80">{{ __('base.teams.delete_description') }}</CardDescription>
    </CardHeader>

    <CardContent class="space-y-4">
      <div class="max-w-xl text-sm text-red-600 dark:text-red-100">
        {{ __('base.teams.delete_warning') }}
      </div>

      <div>
        <Button variant="destructive" @click="confirmTeamDeletion">{{ __('base.teams.delete') }}</Button>
      </div>

      <!-- Delete Team Confirmation Modal -->
      <Dialog :open="confirmingTeamDeletion" @update:open="confirmingTeamDeletion = $event">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{{ __('base.teams.delete') }}</DialogTitle>
            <DialogDescription>
              {{ __('base.teams.delete_confirm') }}
            </DialogDescription>
          </DialogHeader>

          <DialogFooter>
            <Button variant="outline" @click="confirmingTeamDeletion = false">{{ __('base.actions.cancel') }}</Button>

            <Button variant="destructive" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteTeam">
              {{ __('base.teams.delete') }}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </CardContent>
  </Card>
</template>
