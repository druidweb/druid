import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';

export function updateTheme(value: Appearance) {
  if (typeof window === 'undefined') {
    return;
  }

  if (value === 'system') {
    const mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');
    const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

    document.documentElement.classList.toggle('dark', systemTheme === 'dark');
  } else {
    document.documentElement.classList.toggle('dark', value === 'dark');
  }
}

const setCookie = (name: string, value: string) => {
  /* v8 ignore start */
  // SSR guard: setCookie is only reached via client-side updateAppearance
  if (typeof document === 'undefined') {
    return;
  }
  /* v8 ignore stop */

  const maxAge = 365 * 24 * 60 * 60;

  document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
  /* v8 ignore start */
  // SSR guard: initializeTheme already confirms window before calling this
  if (typeof window === 'undefined') {
    return null;
  }
  /* v8 ignore stop */

  return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
  /* v8 ignore start */
  // SSR guard: callers run only after window is confirmed defined
  if (typeof window === 'undefined') {
    return null;
  }
  /* v8 ignore stop */

  return localStorage.getItem('appearance') as Appearance | null;
};

const handleSystemThemeChange = () => {
  const currentAppearance = getStoredAppearance();

  updateTheme(currentAppearance || 'system');
};

export function initializeTheme() {
  if (typeof window === 'undefined') {
    return;
  }

  // Initialize theme from saved preference or default to system...
  const savedAppearance = getStoredAppearance();
  updateTheme(savedAppearance || 'system');

  // Set up system theme change listener...
  mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

const appearance = ref<Appearance>('system');

export function useAppearance() {
  onMounted(() => {
    const savedAppearance = localStorage.getItem('appearance') as Appearance | null;

    if (savedAppearance) {
      appearance.value = savedAppearance;
    }
  });

  function updateAppearance(value: Appearance) {
    appearance.value = value;

    // Store in localStorage for client-side persistence...
    localStorage.setItem('appearance', value);

    // Store in cookie for SSR...
    setCookie('appearance', value);

    updateTheme(value);
  }

  return {
    appearance,
    updateAppearance,
  };
}
