import { showFlashBanner } from '@/composables/useFlashBanner';
import { useToast } from '@/composables/useToast';
import { beforeEach, describe, expect, it } from 'vitest';

describe('showFlashBanner', () => {
  beforeEach(() => {
    useToast().toasts.value = [];
  });

  it('does nothing without a banner', () => {
    showFlashBanner(undefined);
    showFlashBanner({});
    showFlashBanner({ token: 'abc' });

    expect(useToast().toasts.value).toHaveLength(0);
  });

  it('shows a success toast by default', () => {
    showFlashBanner({ banner: 'Joined the team' });

    expect(useToast().toasts.value[0]).toMatchObject({ title: 'Joined the team', variant: 'success' });
  });

  it('maps danger to a destructive toast and warning to the default toast', () => {
    showFlashBanner({ banner: 'Gone', bannerStyle: 'danger' });
    showFlashBanner({ banner: 'Careful', bannerStyle: 'warning' });

    expect(useToast().toasts.value.map(({ title, variant }) => ({ title, variant }))).toEqual([
      { title: 'Gone', variant: 'destructive' },
      { title: 'Careful', variant: 'default' },
    ]);
  });
});
