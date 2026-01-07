<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
  size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const { getInitials } = useInitials();

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'size-6';
    case 'lg':
      return 'size-20';
    default:
      return 'size-8';
  }
});
</script>

<template>
  <Avatar :class="['overflow-hidden rounded-full', sizeClasses]">
    <AvatarImage v-if="user.profile_photo_path" :src="user.profile_photo_url" :alt="user.name" />
    <AvatarFallback class="rounded-full bg-muted text-foreground">
      {{ getInitials(user.name) }}
    </AvatarFallback>
  </Avatar>
</template>

