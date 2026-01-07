<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { destroy as destroyInvitation } from '@/routes/team-invitations';
import { destroy as destroyMember, store as storeMember, update as updateMember } from '@/routes/team-members';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Role {
  key: string;
  name: string;
  description: string;
  permissions: string[];
}

interface TeamMember {
  id: number;
  name: string;
  email: string;
  profile_photo_url: string;
  membership: {
    role: string;
  };
}

interface TeamInvitation {
  id: number;
  email: string;
}

interface Team {
  id: number;
  name: string;
  users: TeamMember[];
  team_invitations: TeamInvitation[];
  [key: string]: any;
}

interface UserPermissions {
  canAddTeamMembers: boolean;
  canUpdateTeamMembers: boolean;
  canRemoveTeamMembers: boolean;
  [key: string]: any;
}

const props = defineProps<{
  team: Team;
  availableRoles: Role[];
  userPermissions: UserPermissions;
}>();

const page = usePage();

const addTeamMemberForm = useForm({
  email: '',
  role: null as string | null,
});

const updateRoleForm = useForm({
  role: null as string | null,
});

const leaveTeamForm = useForm({});
const removeTeamMemberForm = useForm({});

const currentlyManagingRole = ref(false);
const managingRoleFor = ref<TeamMember | null>(null);
const confirmingLeavingTeam = ref(false);
const teamMemberBeingRemoved = ref<TeamMember | null>(null);

const addTeamMember = () => {
  addTeamMemberForm.post(storeMember(props.team.id).url, {
    errorBag: 'addTeamMember',
    preserveScroll: true,
    onSuccess: () => addTeamMemberForm.reset(),
  });
};

const cancelTeamInvitation = (invitation: TeamInvitation) => {
  router.delete(destroyInvitation(invitation.id).url, {
    preserveScroll: true,
  });
};

const manageRole = (teamMember: TeamMember) => {
  managingRoleFor.value = teamMember;
  updateRoleForm.role = teamMember.membership.role;
  currentlyManagingRole.value = true;
};

const updateRole = () => {
  updateRoleForm.put(updateMember({ team: props.team.id, user: managingRoleFor.value!.id }).url, {
    preserveScroll: true,
    onSuccess: () => (currentlyManagingRole.value = false),
  });
};

const confirmLeavingTeam = () => {
  confirmingLeavingTeam.value = true;
};

const leaveTeam = () => {
  leaveTeamForm.delete(destroyMember({ team: props.team.id, user: page.props.auth.user.id }).url);
};

const confirmTeamMemberRemoval = (teamMember: TeamMember) => {
  teamMemberBeingRemoved.value = teamMember;
};

const removeTeamMember = () => {
  removeTeamMemberForm.delete(destroyMember({ team: props.team.id, user: teamMemberBeingRemoved.value!.id }).url, {
    errorBag: 'removeTeamMember',
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => (teamMemberBeingRemoved.value = null),
  });
};

const displayableRole = (role: string) => {
  return props.availableRoles.find((r) => r.key === role)?.name;
};
</script>

<template>
  <div class="space-y-10">
    <!-- Add Team Member -->
    <Card v-if="userPermissions.canAddTeamMembers">
      <form @submit.prevent="addTeamMember">
        <CardHeader>
          <CardTitle>Add Team Member</CardTitle>
          <CardDescription>Add a new team member to your team, allowing them to collaborate with you.</CardDescription>
        </CardHeader>

        <CardContent class="space-y-6">
          <div class="max-w-xl text-sm text-muted-foreground">Please provide the email address of the person you would like to add to this team.</div>

          <!-- Member Email -->
          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input id="email" v-model="addTeamMemberForm.email" type="email" />
            <InputError :message="addTeamMemberForm.errors.email" />
          </div>

          <!-- Role -->
          <div v-if="availableRoles.length > 0" class="space-y-2">
            <Label for="roles">Role</Label>
            <InputError :message="addTeamMemberForm.errors.role" />

            <div class="relative z-0 cursor-pointer rounded-lg border">
              <button
                v-for="(role, i) in availableRoles"
                :key="role.key"
                type="button"
                class="relative inline-flex w-full rounded-lg px-4 py-3 focus:z-10 focus:ring-2 focus:ring-ring focus:outline-none"
                :class="{
                  'rounded-t-none border-t': i > 0,
                  'rounded-b-none': i != Object.keys(availableRoles).length - 1,
                }"
                @click="addTeamMemberForm.role = role.key">
                <div :class="{ 'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.key }">
                  <!-- Role Name -->
                  <div class="flex items-center">
                    <div class="text-sm text-muted-foreground" :class="{ 'font-semibold': addTeamMemberForm.role == role.key }">
                      {{ role.name }}
                    </div>

                    <CheckCircle2 v-if="addTeamMemberForm.role == role.key" class="ms-2 size-5 text-green-500" />
                  </div>

                  <!-- Role Description -->
                  <div class="mt-2 text-start text-xs text-muted-foreground">
                    {{ role.description }}
                  </div>
                </div>
              </button>
            </div>
          </div>
        </CardContent>

        <CardFooter class="flex items-center justify-end gap-3">
          <Transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="addTeamMemberForm.recentlySuccessful" class="text-sm text-muted-foreground">Added.</div>
          </Transition>

          <Button :class="{ 'opacity-25': addTeamMemberForm.processing }" :disabled="addTeamMemberForm.processing"> Add </Button>
        </CardFooter>
      </form>
    </Card>

    <!-- Team Member Invitations -->
    <Card v-if="team.team_invitations.length > 0 && userPermissions.canAddTeamMembers">
      <CardHeader>
        <CardTitle>Pending Team Invitations</CardTitle>
        <CardDescription>
          These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email
          invitation.
        </CardDescription>
      </CardHeader>

      <CardContent>
        <div class="space-y-6">
          <div v-for="invitation in team.team_invitations" :key="invitation.id" class="flex items-center justify-between">
            <div class="text-muted-foreground">
              {{ invitation.email }}
            </div>

            <div class="flex items-center">
              <!-- Cancel Team Invitation -->
              <Button
                v-if="userPermissions.canRemoveTeamMembers"
                variant="ghost"
                size="sm"
                class="text-destructive hover:text-destructive"
                @click="cancelTeamInvitation(invitation)">
                Cancel
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Manage Team Members -->
    <Card v-if="team.users.length > 0">
      <CardHeader>
        <CardTitle>Team Members</CardTitle>
        <CardDescription>All of the people that are part of this team.</CardDescription>
      </CardHeader>

      <CardContent>
        <div class="space-y-6">
          <div v-for="user in team.users" :key="user.id" class="flex items-center justify-between">
            <div class="flex items-center">
              <img class="size-8 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name" />
              <div class="ms-4 text-foreground">
                {{ user.name }}
              </div>
            </div>

            <div class="flex items-center gap-2">
              <!-- Manage Team Member Role -->
              <Button
                v-if="userPermissions.canUpdateTeamMembers && availableRoles.length"
                variant="link"
                size="sm"
                class="text-muted-foreground"
                @click="manageRole(user)">
                {{ displayableRole(user.membership.role) }}
              </Button>

              <div v-else-if="availableRoles.length" class="text-sm text-muted-foreground">
                {{ displayableRole(user.membership.role) }}
              </div>

              <!-- Leave Team -->
              <Button
                v-if="$page.props.auth.user.id === user.id"
                variant="ghost"
                size="sm"
                class="text-destructive hover:text-destructive"
                @click="confirmLeavingTeam">
                Leave
              </Button>

              <!-- Remove Team Member -->
              <Button
                v-else-if="userPermissions.canRemoveTeamMembers"
                variant="ghost"
                size="sm"
                class="text-destructive hover:text-destructive"
                @click="confirmTeamMemberRemoval(user)">
                Remove
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Role Management Modal -->
    <Dialog :open="currentlyManagingRole" @update:open="currentlyManagingRole = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Manage Role</DialogTitle>
        </DialogHeader>

        <div v-if="managingRoleFor">
          <div class="relative z-0 cursor-pointer rounded-lg border">
            <button
              v-for="(role, i) in availableRoles"
              :key="role.key"
              type="button"
              class="relative inline-flex w-full rounded-lg px-4 py-3 focus:z-10 focus:ring-2 focus:ring-ring focus:outline-none"
              :class="{
                'rounded-t-none border-t': i > 0,
                'rounded-b-none': i !== Object.keys(availableRoles).length - 1,
              }"
              @click="updateRoleForm.role = role.key">
              <div :class="{ 'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.key }">
                <!-- Role Name -->
                <div class="flex items-center">
                  <div class="text-sm text-muted-foreground" :class="{ 'font-semibold': updateRoleForm.role === role.key }">
                    {{ role.name }}
                  </div>

                  <CheckCircle2 v-if="updateRoleForm.role == role.key" class="ms-2 size-5 text-green-500" />
                </div>

                <!-- Role Description -->
                <div class="mt-2 text-xs text-muted-foreground">
                  {{ role.description }}
                </div>
              </div>
            </button>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="currentlyManagingRole = false"> Cancel </Button>

          <Button :class="{ 'opacity-25': updateRoleForm.processing }" :disabled="updateRoleForm.processing" @click="updateRole"> Save </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Leave Team Confirmation Modal -->
    <Dialog :open="confirmingLeavingTeam" @update:open="confirmingLeavingTeam = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Leave Team</DialogTitle>
          <DialogDescription>Are you sure you would like to leave this team?</DialogDescription>
        </DialogHeader>

        <DialogFooter>
          <Button variant="outline" @click="confirmingLeavingTeam = false"> Cancel </Button>

          <Button variant="destructive" :class="{ 'opacity-25': leaveTeamForm.processing }" :disabled="leaveTeamForm.processing" @click="leaveTeam">
            Leave
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Remove Team Member Confirmation Modal -->
    <Dialog :open="!!teamMemberBeingRemoved" @update:open="teamMemberBeingRemoved = $event ? teamMemberBeingRemoved : null">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Remove Team Member</DialogTitle>
          <DialogDescription>Are you sure you would like to remove this person from the team?</DialogDescription>
        </DialogHeader>

        <DialogFooter>
          <Button variant="outline" @click="teamMemberBeingRemoved = null"> Cancel </Button>

          <Button
            variant="destructive"
            :class="{ 'opacity-25': removeTeamMemberForm.processing }"
            :disabled="removeTeamMemberForm.processing"
            @click="removeTeamMember">
            Remove
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
