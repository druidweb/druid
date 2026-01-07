<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { destroy } from '@/routes/other-browser-sessions';
import { useForm } from '@inertiajs/vue3';
import { Monitor, Smartphone } from 'lucide-vue-next';
import { ref, Transition } from 'vue';

interface SessionAgent {
  is_desktop: boolean;
  platform?: string;
  browser?: string;
}

interface Session {
  agent: SessionAgent;
  ip_address: string;
  is_current_device: boolean;
  last_active: string;
}

defineProps<{
  sessions: Session[];
}>();

const confirmingLogout = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  password: '',
});

const confirmLogout = () => {
  confirmingLogout.value = true;

  setTimeout(() => passwordInput.value?.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
  form.delete(destroy(), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingLogout.value = false;

  form.reset();
};
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Browser Sessions</CardTitle>
      <CardDescription>Manage and log out your active sessions on other browsers and devices.</CardDescription>
    </CardHeader>

    <CardContent class="space-y-5">
      <div class="max-w-xl text-sm text-muted-foreground">
        If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below;
        however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.
      </div>

      <!-- Other Browser Sessions -->
      <div v-if="sessions.length > 0" class="space-y-6">
        <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
          <div>
            <Monitor v-if="session.agent.is_desktop" class="size-8 text-muted-foreground" />
            <Smartphone v-else class="size-8 text-muted-foreground" />
          </div>

          <div class="ms-3">
            <div class="text-sm text-muted-foreground">
              {{ session.agent.platform ? session.agent.platform : 'Unknown' }} - {{ session.agent.browser ? session.agent.browser : 'Unknown' }}
            </div>

            <div>
              <div class="text-xs text-muted-foreground">
                {{ session.ip_address }},

                <span v-if="session.is_current_device" class="font-semibold text-green-500">This device</span>
                <span v-else>Last active {{ session.last_active }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <Button @click="confirmLogout"> Log Out Other Browser Sessions </Button>

        <Transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
          <div v-show="form.recentlySuccessful" class="text-sm text-muted-foreground">Done.</div>
        </Transition>
      </div>

      <!-- Log Out Other Devices Confirmation Modal -->
      <Dialog :open="confirmingLogout" @update:open="confirmingLogout = $event">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Log Out Other Browser Sessions</DialogTitle>
            <DialogDescription>
              Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.
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
              @keyup.enter="logoutOtherBrowserSessions" />

            <InputError :message="form.errors.password" class="mt-2" />
          </div>

          <DialogFooter>
            <Button variant="outline" @click="closeModal"> Cancel </Button>

            <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="logoutOtherBrowserSessions">
              Log Out Other Browser Sessions
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </CardContent>
  </Card>
</template>
