<script setup lang="ts">
import { cn } from '@/lib/utils';
import { X } from 'lucide-vue-next';
import { DialogClose, DialogContent, type DialogContentEmits, type DialogContentProps, DialogPortal, useForwardPropsEmits } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<DialogContentProps & { class?: HTMLAttributes['class'] }>();
const emits = defineEmits<DialogContentEmits>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
  <DialogPortal>
    <DialogContent
      data-slot="dialog-content"
      v-bind="forwarded"
      :class="
        cn(
          'fixed top-[50%] left-[50%] z-50 max-h-full w-full max-w-[calc(100%-2rem)] min-w-0 translate-x-[-50%] translate-y-[-50%] rounded border border-zinc-300 bg-transparent p-2 shadow-lg backdrop-blur-[6px] duration-300 focus:outline-hidden data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[state=open]:animate-in data-[state=open]:fade-in-0 data-[state=open]:zoom-in-95 sm:max-w-lg dark:border-zinc-700/60',
          props.class,
        )
      ">
      <div
        class="relative grid gap-4 overflow-hidden rounded border border-zinc-300 bg-background p-6 shadow-[0_0_4px_rgba(0,0,0,0.1)] transition-all duration-300 dark:border-zinc-700/60 forced-colors:outline">
        <slot />

        <DialogClose
          class="absolute top-4 right-4 cursor-pointer rounded-md p-1 opacity-50 ring-offset-background transition-all hover:bg-muted hover:opacity-100 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4">
          <X />
          <span class="sr-only">Close</span>
        </DialogClose>
      </div>
    </DialogContent>
  </DialogPortal>
</template>
