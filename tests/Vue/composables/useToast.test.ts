import { toast, useToast } from '@/composables/useToast';
import { beforeEach, describe, expect, it } from 'vitest';

describe('useToast', () => {
  beforeEach(() => {
    const { toasts } = useToast();
    toasts.value = [];
  });

  it('pushes a toast with default variant and duration', () => {
    const { toasts } = useToast();

    const id = toast('Hello');

    expect(toasts.value).toHaveLength(1);
    expect(toasts.value[0]).toMatchObject({ id, title: 'Hello', variant: 'default', duration: 3000 });
  });

  it('applies provided options', () => {
    const { toasts } = useToast();

    toast('With options', { description: 'details', duration: 5000 });

    expect(toasts.value[0]).toMatchObject({ description: 'details', duration: 5000 });
  });

  it('creates success and error toasts', () => {
    const { toasts } = useToast();

    toast.success('Yay');
    toast.error('Nope', { description: 'bad' });

    expect(toasts.value[0].variant).toBe('success');
    expect(toasts.value[1]).toMatchObject({ variant: 'destructive', description: 'bad' });
  });

  it('assigns unique incrementing ids', () => {
    const first = toast('one');
    const second = toast('two');

    expect(second).toBeGreaterThan(first);
  });

  it('dismisses a toast by id', () => {
    const { toasts, dismiss } = useToast();

    const id = toast('Bye');
    expect(toasts.value).toHaveLength(1);

    dismiss(id);

    expect(toasts.value).toHaveLength(0);
  });
});
