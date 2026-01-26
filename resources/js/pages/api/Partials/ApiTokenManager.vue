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
          <CardTitle>{{ __('base.api_tokens.create') }}</CardTitle>
          <CardDescription>{{ __('base.api_tokens.description') }}</CardDescription>
        </CardHeader>

        <CardContent class="space-y-6">
          <!-- Token Name -->
          <div class="space-y-2">
            <Label for="name">{{ __('base.fields.name') }}</Label>
            <Input id="name" v-model="createApiTokenForm.name" type="text" autofocus />
            <InputError :message="createApiTokenForm.errors.name" />
          </div>

          <!-- Token Permissions -->
          <div v-if="availablePermissions.length > 0" class="space-y-2">
            <Label for="permissions">{{ __('base.fields.permissions') }}</Label>

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
            <div v-show="createApiTokenForm.recentlySuccessful" class="text-sm text-muted-foreground">{{ __('base.status.created') }}</div>
          </Transition>

          <Button :class="{ 'opacity-25': createApiTokenForm.processing }" :disabled="createApiTokenForm.processing">{{
            __('base.actions.create')
          }}</Button>
        </CardFooter>
      </form>
    </Card>

    <!-- Manage API Tokens -->
    <Card v-if="tokens.length > 0">
      <CardHeader>
        <CardTitle>{{ __('base.api_tokens.manage') }}</CardTitle>
        <CardDescription>{{ __('base.api_tokens.delete_existing') }}</CardDescription>
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
                {{ __('base.fields.permissions') }}
              </Button>

              <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive" @click="confirmApiTokenDeletion(token)">
                {{ __('base.actions.delete') }}
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
          <DialogTitle>{{ __('base.api_tokens.created') }}</DialogTitle>
          <DialogDescription>{{ __('base.api_tokens.created_description') }}</DialogDescription>
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
                  <p>{{ copied ? __('base.status.copied') : __('base.status.copy_to_clipboard') }}</p>
                </TooltipContent>
              </Tooltip>
            </TooltipProvider>
          </div>
        </div>

        <DialogFooter>
          <Button @click="displayingToken = false">{{ __('base.actions.done') }}</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- API Token Permissions Modal -->
    <Dialog :open="managingPermissionsFor != null" @update:open="managingPermissionsFor = $event ? managingPermissionsFor : null">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ __('base.api_tokens.permissions') }}</DialogTitle>
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
          <Button variant="outline" @click="managingPermissionsFor = null">{{ __('base.actions.cancel') }}</Button>

          <Button :class="{ 'opacity-25': updateApiTokenForm.processing }" :disabled="updateApiTokenForm.processing" @click="updateApiToken">{{
            __('base.actions.save')
          }}</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Delete Token Confirmation Modal -->
    <Dialog :open="apiTokenBeingDeleted != null" @update:open="apiTokenBeingDeleted = $event ? apiTokenBeingDeleted : null">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ __('base.api_tokens.delete') }}</DialogTitle>
          <DialogDescription>{{ __('base.api_tokens.delete_confirm') }}</DialogDescription>
        </DialogHeader>

        <DialogFooter>
          <Button variant="outline" @click="apiTokenBeingDeleted = null">{{ __('base.actions.cancel') }}</Button>

          <Button
            variant="destructive"
            :class="{ 'opacity-25': deleteApiTokenForm.processing }"
            :disabled="deleteApiTokenForm.processing"
            @click="deleteApiToken">
            {{ __('base.actions.delete') }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
