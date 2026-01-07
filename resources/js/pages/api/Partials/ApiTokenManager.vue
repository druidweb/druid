<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { destroy, store, update } from '@/routes/api-tokens';
import { useForm } from '@inertiajs/vue3';
import { Check, Copy } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const copied = ref(false);

const copyToken = async (token: string) => {
  await navigator.clipboard.writeText(token);
  copied.value = true;
  setTimeout(() => {
    copied.value = false;
  }, 2000);
};

interface ApiToken {
  id: number;
  name: string;
  abilities: string[];
  last_used_ago: string | null;
  created_at: string;
  [key: string]: any;
}

const props = defineProps<{
  tokens: ApiToken[];
  availablePermissions: string[];
  defaultPermissions: string[];
}>();

onMounted(() => {
  console.log('Tokens on mount:', props.tokens);
});

watch(
  () => props.tokens,
  (newTokens) => {
    console.log('Tokens updated:', newTokens);
  },
);

const createApiTokenForm = useForm({
  name: '',
  permissions: [] as string[],
});

const updateApiTokenForm = useForm({
  permissions: [] as string[],
});

const deleteApiTokenForm = useForm({});

const displayingToken = ref(false);
const managingPermissionsFor = ref<ApiToken | null>(null);
const apiTokenBeingDeleted = ref<ApiToken | null>(null);

const createApiToken = () => {
  console.log('Creating token with permissions:', createApiTokenForm.permissions);
  createApiTokenForm.post(store(), {
    preserveScroll: true,
    onSuccess: () => {
      displayingToken.value = true;
      createApiTokenForm.reset();
    },
  });
};

const manageApiTokenPermissions = (token: ApiToken) => {
  updateApiTokenForm.permissions = token.abilities;
  managingPermissionsFor.value = token;
};

const updateApiToken = () => {
  updateApiTokenForm.put(update(managingPermissionsFor.value!.id), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => (managingPermissionsFor.value = null),
  });
};

const confirmApiTokenDeletion = (token: ApiToken) => {
  apiTokenBeingDeleted.value = token;
};

const deleteApiToken = () => {
  deleteApiTokenForm.delete(destroy(apiTokenBeingDeleted.value!.id), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => (apiTokenBeingDeleted.value = null),
  });
};
</script>

<template>
  <div class="space-y-10">
    <!-- Generate API Token -->
    <Card>
      <form @submit.prevent="createApiToken">
        <CardHeader>
          <CardTitle>Create API Token</CardTitle>
          <CardDescription>API tokens allow third-party services to authenticate with our application on your behalf.</CardDescription>
        </CardHeader>

        <CardContent class="space-y-6">
          <!-- Token Name -->
          <div class="space-y-2">
            <Label for="name">Name</Label>
            <Input id="name" v-model="createApiTokenForm.name" type="text" autofocus />
            <InputError :message="createApiTokenForm.errors.name" />
          </div>

          <!-- Token Permissions -->
          <div v-if="availablePermissions.length > 0" class="space-y-2">
            <Label for="permissions">Permissions</Label>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div v-for="permission in availablePermissions" :key="permission" class="flex items-center gap-2">
                <Checkbox
                  :id="`create-${permission}`"
                  :model-value="createApiTokenForm.permissions.includes(permission)"
                  @update:model-value="
                    (checked: boolean | 'indeterminate') => {
                      if (checked === true) {
                        createApiTokenForm.permissions = [...createApiTokenForm.permissions, permission];
                      } else {
                        createApiTokenForm.permissions = createApiTokenForm.permissions.filter((p) => p !== permission);
                      }
                    }
                  " />
                <Label :for="`create-${permission}`" class="cursor-pointer text-sm font-normal text-muted-foreground">{{ permission }}</Label>
              </div>
            </div>
          </div>
        </CardContent>

        <CardFooter class="flex items-center justify-end gap-3">
          <Transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="createApiTokenForm.recentlySuccessful" class="text-sm text-muted-foreground">Created.</div>
          </Transition>

          <Button :class="{ 'opacity-25': createApiTokenForm.processing }" :disabled="createApiTokenForm.processing"> Create </Button>
        </CardFooter>
      </form>
    </Card>

    <!-- Manage API Tokens -->
    <Card v-if="tokens.length > 0">
      <CardHeader>
        <CardTitle>Manage API Tokens</CardTitle>
        <CardDescription>You may delete any of your existing tokens if they are no longer needed.</CardDescription>
      </CardHeader>

      <CardContent>
        <div class="space-y-6">
          <div v-for="token in tokens" :key="token.id" class="flex items-center justify-between">
            <div class="break-all text-foreground">
              {{ token.name }}
            </div>

            <div class="ms-2 flex items-center gap-2">
              <div v-if="token.last_used_ago" class="text-sm text-muted-foreground">Last used {{ token.last_used_ago }}</div>

              <Button
                v-if="availablePermissions.length > 0"
                variant="link"
                size="sm"
                class="text-muted-foreground"
                @click="manageApiTokenPermissions(token)">
                Permissions
              </Button>

              <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive" @click="confirmApiTokenDeletion(token)">
                Delete
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Token Value Modal -->
    <Dialog :open="displayingToken" @update:open="displayingToken = $event">
      <DialogContent class="sm:max-w-xl">
        <DialogHeader>
          <DialogTitle>API Token Created</DialogTitle>
          <DialogDescription>Please copy your new API token. For your security, it won't be shown again.</DialogDescription>
        </DialogHeader>

        <div v-if="$page.props.teams.flash.token" class="group relative">
          <div class="flex items-center gap-2 rounded-lg border bg-muted/50 p-4">
            <code class="flex-1 overflow-x-auto font-mono text-sm whitespace-nowrap text-foreground">
              {{ $page.props.teams.flash.token }}
            </code>
            <TooltipProvider :delay-duration="0">
              <Tooltip>
                <TooltipTrigger as-child>
                  <Button variant="ghost" size="icon" class="h-8 w-8 shrink-0" @click="copyToken($page.props.teams.flash.token)">
                    <Check v-if="copied" class="h-4 w-4 text-green-500" />
                    <Copy v-else class="h-4 w-4" />
                  </Button>
                </TooltipTrigger>
                <TooltipContent>
                  <p>{{ copied ? 'Copied!' : 'Copy to clipboard' }}</p>
                </TooltipContent>
              </Tooltip>
            </TooltipProvider>
          </div>
        </div>

        <DialogFooter>
          <Button @click="displayingToken = false">Done</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- API Token Permissions Modal -->
    <Dialog :open="managingPermissionsFor != null" @update:open="managingPermissionsFor = $event ? managingPermissionsFor : null">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>API Token Permissions</DialogTitle>
        </DialogHeader>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div v-for="permission in availablePermissions" :key="permission" class="flex items-center gap-2">
            <Checkbox
              :id="`update-${permission}`"
              :model-value="updateApiTokenForm.permissions.includes(permission)"
              @update:model-value="
                (checked: boolean | 'indeterminate') => {
                  if (checked === true) {
                    updateApiTokenForm.permissions = [...updateApiTokenForm.permissions, permission];
                  } else {
                    updateApiTokenForm.permissions = updateApiTokenForm.permissions.filter((p) => p !== permission);
                  }
                }
              " />
            <Label :for="`update-${permission}`" class="cursor-pointer text-sm font-normal text-muted-foreground">{{ permission }}</Label>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="managingPermissionsFor = null"> Cancel </Button>

          <Button :class="{ 'opacity-25': updateApiTokenForm.processing }" :disabled="updateApiTokenForm.processing" @click="updateApiToken">
            Save
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Delete Token Confirmation Modal -->
    <Dialog :open="apiTokenBeingDeleted != null" @update:open="apiTokenBeingDeleted = $event ? apiTokenBeingDeleted : null">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Delete API Token</DialogTitle>
          <DialogDescription>Are you sure you would like to delete this API token?</DialogDescription>
        </DialogHeader>

        <DialogFooter>
          <Button variant="outline" @click="apiTokenBeingDeleted = null"> Cancel </Button>

          <Button
            variant="destructive"
            :class="{ 'opacity-25': deleteApiTokenForm.processing }"
            :disabled="deleteApiTokenForm.processing"
            @click="deleteApiToken">
            Delete
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
