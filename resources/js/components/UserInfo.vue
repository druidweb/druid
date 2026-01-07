<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
  showEmail?: boolean;
}

withDefaults(defineProps<Props>(), {
  showEmail: false,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
  <UserAvatar />

  <div class="grid flex-1 text-left text-sm leading-tight">
    <span class="truncate font-medium">{{ user.name }}</span>
    <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
  </div>
</template>
