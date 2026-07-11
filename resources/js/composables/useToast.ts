import { ref } from 'vue';

export type ToastVariant = 'default' | 'success' | 'destructive';

export interface Toast {
  id: number;
  title: string;
  description?: string;
  variant: ToastVariant;
  duration: number;
}

export type ToastOptions = Partial<Pick<Toast, 'description' | 'variant' | 'duration'>>;

const toasts = ref<Toast[]>([]);
let counter = 0;

// Push a toast onto the stack and return its id.
const push = (title: string, options: ToastOptions = {}): number => {
  const id = ++counter;

  toasts.value.push({
    id,
    title,
    variant: 'default',
    duration: 3000,
    ...options,
  });

  return id;
};

// Remove a toast by id.
const dismiss = (id: number): void => {
  toasts.value = toasts.value.filter((item) => item.id !== id);
};

// Imperative, ergonomic API: toast('...'), toast.success('...'), toast.error('...').
export const toast = Object.assign(push, {
  success: (title: string, options: Omit<ToastOptions, 'variant'> = {}): number => push(title, { ...options, variant: 'success' }),
  error: (title: string, options: Omit<ToastOptions, 'variant'> = {}): number => push(title, { ...options, variant: 'destructive' }),
});

export function useToast() {
  return { toasts, toast, dismiss };
}
