<script setup lang="ts">
interface Props {
  tabindex?: number | string;
}

defineProps<Props>();
</script>

<template>
  <a
    :tabindex="Number(tabindex)"
    class="hover:decoration-current! text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out dark:decoration-neutral-500"
  >
    <slot />
  </a>
</template>
