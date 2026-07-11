<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { cn } from '@/lib/utils';
import { ToastDescription, ToastProvider, ToastRoot, ToastTitle, ToastViewport } from 'reka-ui';

const { toasts, dismiss } = useToast();

const toastClass = (variant: string): string =>
  cn(
    'pointer-events-auto flex items-center justify-between gap-3 rounded-lg border px-4 py-3 shadow-lg',
    variant === 'success' && 'border-green-600/30 bg-background text-foreground',
    variant === 'destructive' && 'border-destructive bg-destructive text-destructive-foreground',
    variant === 'default' && 'border-border bg-background text-foreground',
  );
</script>

<template>
  <ToastProvider>
    <ToastRoot
      v-for="item in toasts"
      :key="item.id"
      :open="true"
      :duration="item.duration"
      data-test="toast"
      :class="toastClass(item.variant)"
      @update:open="(open: boolean) => open || dismiss(item.id)">
      <div class="grid gap-0.5">
        <ToastTitle class="text-sm font-medium">{{ item.title }}</ToastTitle>
        <ToastDescription v-if="item.description" class="text-xs opacity-90">{{ item.description }}</ToastDescription>
      </div>
    </ToastRoot>
    <ToastViewport class="fixed right-0 bottom-0 z-100 m-0 flex w-full max-w-sm list-none flex-col gap-2 p-4 outline-none" />
  </ToastProvider>
</template>
