<script setup lang="ts">
import { toast } from '@/composables/useToast';
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import {
  ArrowRight,
  Bot,
  Camera,
  Code2,
  Component,
  FileText,
  GitBranch,
  Globe,
  Key,
  Layers,
  Lock,
  Mail,
  Monitor,
  Palette,
  Server,
  Shield,
  ShieldCheck,
  Tag,
  TestTube2,
  Trash2,
  Type,
  Users,
  Zap,
} from 'lucide-vue-next';

withDefaults(
  defineProps<{
    canRegister: boolean;
  }>(),
  {
    canRegister: true,
  },
);

const installCommand = 'laravel new my_app --using=druidweb/druid';

const { copy } = useClipboard();

const copyInstallCommand = (): void => {
  void copy(installCommand);
  toast.success('Copied to clipboard');
};

const stackItems = [
  {
    icon: Server,
    name: 'Laravel 13',
    description: 'PHP 8.5+ with the latest framework features, AI-native tooling, and first-class agent support.',
    href: 'https://laravel.com',
  },
  {
    icon: Component,
    name: 'Vue 3 + Vite',
    description: 'Composition API with script setup, full TypeScript support, and SSR out of the box.',
    href: 'https://vuejs.org',
  },
  {
    icon: Layers,
    name: 'Inertia v3',
    description: 'Modern SPA experience using the new v3 adapter and resolver API — no separate API needed.',
    href: 'https://inertiajs.com',
  },
  {
    icon: Type,
    name: 'TypeScript',
    description: 'End-to-end type safety across PHP and JavaScript using Wayfinder for typed route generation.',
    href: 'https://www.typescriptlang.org',
  },
  {
    icon: Palette,
    name: 'Tailwind 4',
    description: 'Next-generation CSS engine with CSS-first configuration, dark mode, and a tiny runtime.',
    href: 'https://tailwindcss.com',
  },
  {
    icon: Code2,
    name: 'shadcn-vue',
    description: 'Beautiful, accessible UI components built on Reka UI primitives — fully owned in your codebase.',
    href: 'https://www.shadcn-vue.com',
  },
];

const authFeatures = [
  { icon: Shield, label: 'Two-Factor Authentication' },
  { icon: Key, label: 'API Token Management' },
  { icon: Users, label: 'Team Management' },
  { icon: Mail, label: 'Team Invitations' },
  { icon: Camera, label: 'Profile Photos' },
  { icon: Monitor, label: 'Browser Sessions' },
  { icon: Trash2, label: 'Account Deletion' },
  { icon: FileText, label: 'Terms & Privacy Policy' },
  { icon: Lock, label: 'Password Reset' },
  { icon: ShieldCheck, label: 'Email Verification' },
  { icon: Lock, label: 'Password Confirmation' },
  { icon: Globe, label: 'Sanctum API Auth' },
];

const devFeatures = [
  {
    icon: TestTube2,
    name: 'Pest PHP',
    description: '100% code coverage enforced in CI. Parallel test execution with Vitest covering the full Vue component tree.',
    accent: '#FF2D20',
  },
  {
    icon: Bot,
    name: 'Laravel PAO',
    description:
      'Agent-optimized output ships by default. AI agents receive compact JSON instead of decorated terminal noise — cutting token usage by up to 99%.',
    accent: '#8b5cf6',
  },
  {
    icon: Zap,
    name: 'Laravel Octane',
    description: 'FrankenPHP-powered production server with sub-millisecond boot times. Configured and ready for Forge deployment.',
    accent: '#f59e0b',
  },
  {
    icon: GitBranch,
    name: 'CI/CD Pipeline',
    description: 'GitHub Actions running parallel Pest suites on a self-hosted runner. Zero free compute consumed for your nightly suite.',
    accent: '#10b981',
  },
  {
    icon: Tag,
    name: 'Semantic Releases',
    description: 'Conventional commits drive fully automated versioning, changelogs, and GitHub releases via semantic-release.',
    accent: '#3b82f6',
  },
  {
    icon: Code2,
    name: 'Static Analysis',
    description: 'Larastan at max level, Rector for automated refactoring, ESLint and Prettier enforced across the entire stack.',
    accent: '#ec4899',
  },
];

const statsItems = ['100% Test Coverage', 'PHP 8.5+', 'Fully Typed', 'Dark Mode', 'SSR Ready', 'PAO Ready', 'Octane Ready'];
</script>

<template>
  <Head title="Druid — The Laravel Starter Kit">
    <link rel="preconnect" href="https://fonts.bunny.net/" />
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800,900" />
  </Head>

  <div class="welcome-wrap">
    <div class="welcome-bg-grid" />
    <div class="welcome-bg-glow" />

    <!-- Navigation -->
    <header class="relative z-10 flex items-center justify-between px-6 py-5 lg:px-12">
      <div class="flex items-center gap-2">
        <svg class="text-[#FF2D20]" width="12" height="12" viewBox="0 0 10 10" fill="currentColor">
          <polygon points="5,0 10,5 5,10 0,5" />
        </svg>
        <span class="welcome-logo">druid</span>
      </div>
      <nav class="flex items-center gap-3">
        <template v-if="$page.props.auth.user">
          <Link :href="dashboard()" class="nav-btn nav-btn--solid">
            Dashboard
            <ArrowRight :size="13" class="ml-1 inline-block" />
          </Link>
        </template>
        <template v-else>
          <Link :href="login()" class="nav-btn nav-btn--ghost">Sign in</Link>
          <Link v-if="canRegister" :href="register()" class="nav-btn nav-btn--solid"> Get started </Link>
        </template>
      </nav>
    </header>

    <!-- Hero -->
    <section class="relative z-10 flex flex-col items-center px-6 pt-16 pb-24 text-center lg:pt-24 lg:pb-32">
      <div class="hero-badge translate-y-0 opacity-100 transition-all duration-500 starting:translate-y-2 starting:opacity-0">
        Laravel 13 · Inertia v3 · Vue 3
      </div>

      <h1 class="hero-title translate-y-0 opacity-100 transition-all delay-100 duration-700 starting:translate-y-4 starting:opacity-0">Druid</h1>

      <p class="hero-subtitle translate-y-0 opacity-100 transition-all delay-150 duration-700 starting:translate-y-4 starting:opacity-0">
        The most comprehensive and battle-tested Laravel&nbsp;+&nbsp;Vue starter kit. Production-ready from day one. Every feature tested. Every
        workflow automated.
      </p>

      <div
        class="mt-8 flex translate-y-0 flex-wrap items-center justify-center gap-3 opacity-100 transition-all delay-300 duration-700 starting:translate-y-4 starting:opacity-0">
        <Link v-if="canRegister" :href="register()" class="cta-btn cta-btn--primary">
          Get started <ArrowRight :size="15" class="ml-1.5 inline-block" />
        </Link>
        <a href="https://github.com/druidweb/druid" target="_blank" class="cta-btn cta-btn--ghost">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" class="-mt-px mr-1.5 inline-block">
            <path
              d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z" />
          </svg>
          View on GitHub
        </a>
      </div>

      <div class="install-snippet mt-10 translate-y-0 opacity-100 transition-all delay-500 duration-700 starting:translate-y-4 starting:opacity-0">
        <span class="install-prompt">$</span>
        <span class="install-cmd">laravel new my_app --using=druidweb/druid</span>
      </div>

      <div
        class="mt-8 flex translate-y-0 flex-wrap justify-center gap-2 opacity-100 transition-all delay-700 duration-700 starting:translate-y-4 starting:opacity-0">
        <span
          v-for="pill in ['PHP 8.5+', 'Pest 4', 'Vitest', 'Wayfinder', 'Fortify', 'Sanctum', 'Larastan', 'Rector']"
          :key="pill"
          class="stack-pill">
          {{ pill }}
        </span>
      </div>
    </section>

    <!-- Stats bar -->
    <div class="welcome-stats-bar">
      <div class="mx-auto flex max-w-5xl flex-wrap justify-center gap-x-8 gap-y-2 px-6 py-4">
        <span v-for="stat in statsItems" :key="stat" class="stats-item">
          <span class="stats-dot" />
          {{ stat }}
        </span>
      </div>
    </div>

    <!-- Core Stack -->
    <section class="welcome-section">
      <div class="mx-auto max-w-5xl px-6">
        <div class="section-label">Core Stack</div>
        <h2 class="section-title">
          Built on the Modern<br class="hidden lg:block" />
          Laravel Ecosystem
        </h2>
        <p class="section-subtitle">Every technology chosen for production quality, not trend-chasing.</p>

        <div class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <a v-for="item in stackItems" :key="item.name" :href="item.href" target="_blank" class="feature-card group">
            <component :is="item.icon" :size="20" class="mb-4 text-[#FF2D20] transition-transform duration-200 group-hover:scale-110" />
            <h3 class="feature-card-title">{{ item.name }}</h3>
            <p class="feature-card-desc">{{ item.description }}</p>
          </a>
        </div>
      </div>
    </section>

    <!-- Auth & Security -->
    <section class="welcome-section welcome-section--alt">
      <div class="mx-auto max-w-5xl px-6">
        <div class="section-label">Auth &amp; Security</div>
        <h2 class="section-title">
          Every Auth Feature<br class="hidden lg:block" />
          You'll Ever Need
        </h2>
        <p class="section-subtitle">
          All Laravel Jetstream and Fortify features — rebuilt from the ground up with Vue&nbsp;3, TypeScript, and 100% test coverage.
        </p>

        <div class="mt-12 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
          <div v-for="feature in authFeatures" :key="feature.label" class="auth-card group">
            <component :is="feature.icon" :size="15" class="shrink-0 text-[#FF2D20] transition-transform duration-200 group-hover:scale-110" />
            <span class="auth-card-label">{{ feature.label }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Developer Experience -->
    <section class="welcome-section">
      <div class="mx-auto max-w-5xl px-6">
        <div class="section-label">Developer Experience</div>
        <h2 class="section-title">
          The Complete<br class="hidden lg:block" />
          Development Ecosystem
        </h2>
        <p class="section-subtitle">Not just a starter kit — a complete foundation with every tool your team needs, already configured and tested.</p>

        <div class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="item in devFeatures" :key="item.name" class="dev-card">
            <div class="dev-card-icon" :style="{ background: `${item.accent}18`, color: item.accent }">
              <component :is="item.icon" :size="18" />
            </div>
            <h3 class="feature-card-title mt-4">{{ item.name }}</h3>
            <p class="feature-card-desc">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Installation CTA -->
    <section class="welcome-section welcome-section--alt">
      <div class="mx-auto max-w-2xl px-6 text-center">
        <div class="section-label">Get Started</div>
        <h2 class="section-title">Start building<br />in seconds.</h2>
        <p class="section-subtitle">Everything configured. Nothing to set up.</p>

        <div class="install-block mt-10">
          <div class="install-block-content">
            <span class="font-mono text-[#6b7280] select-none">$</span>
            <span class="install-block-cmd">{{ installCommand }}</span>
          </div>
          <button class="install-copy-btn" type="button" title="Copy to clipboard" data-test="copy-install" @click="copyInstallCommand">
            <svg
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round">
              <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>
          </button>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
          <Link v-if="canRegister" :href="register()" class="cta-btn cta-btn--primary">
            Create account <ArrowRight :size="15" class="ml-1.5 inline-block" />
          </Link>
          <Link :href="login()" class="cta-btn cta-btn--ghost">Sign in</Link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="welcome-footer">
      <div class="mx-auto flex max-w-5xl flex-col items-center justify-between gap-4 px-6 sm:flex-row">
        <div class="flex items-center gap-2">
          <svg class="text-[#FF2D20]" width="10" height="10" viewBox="0 0 10 10" fill="currentColor">
            <polygon points="5,0 10,5 5,10 0,5" />
          </svg>
          <span class="text-[13px] font-semibold text-[#111827] dark:text-[#f9fafb]">druid</span>
          <span class="text-[13px] text-[#9ca3af]">
            by
            <a href="https://jetstreamlabs.com" target="_blank" class="transition-colors hover:text-[#FF2D20]"> Jetstream Labs </a>
          </span>
        </div>
        <div class="flex items-center gap-6">
          <a href="https://github.com/druidweb/druid" target="_blank" class="footer-link"> GitHub </a>
          <a href="https://laravel.com/docs/13.x/starter-kits" target="_blank" class="footer-link"> Docs </a>
          <a href="https://github.com/druidweb/druid/blob/main/LICENSE.md" target="_blank" class="footer-link"> MIT License </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.welcome-wrap {
  position: relative;
  min-height: 100vh;
  font-family: 'Poppins', system-ui, sans-serif;
  background: #fafaf8;
  color: #111827;
}

.dark .welcome-wrap {
  background: #0a0a0a;
  color: #f9fafb;
}

.welcome-bg-grid {
  position: fixed;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  background-image: radial-gradient(circle, rgba(0, 0, 0, 0.06) 1px, transparent 1px);
  background-size: 28px 28px;
}

.dark .welcome-bg-grid {
  background-image: radial-gradient(circle, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
}

.welcome-bg-glow {
  position: fixed;
  top: -250px;
  left: 50%;
  transform: translateX(-50%);
  width: 900px;
  height: 700px;
  background: radial-gradient(ellipse, rgba(255, 45, 32, 0.06) 0%, transparent 65%);
  pointer-events: none;
  z-index: 0;
}

.dark .welcome-bg-glow {
  background: radial-gradient(ellipse, rgba(255, 45, 32, 0.1) 0%, transparent 65%);
}

.welcome-logo {
  font-size: 15px;
  font-weight: 700;
  letter-spacing: -0.01em;
  color: #111827;
}

.dark .welcome-logo {
  color: #f9fafb;
}

.nav-btn {
  display: inline-flex;
  align-items: center;
  font-size: 13px;
  font-weight: 500;
  padding: 7px 16px;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.15s ease;
  border: 1px solid transparent;
}

.nav-btn--ghost {
  color: #6b7280;
  border-color: rgba(0, 0, 0, 0.1);
}

.dark .nav-btn--ghost {
  color: rgba(255, 255, 255, 0.5);
  border-color: rgba(255, 255, 255, 0.1);
}

.nav-btn--ghost:hover {
  color: #111827;
  border-color: rgba(0, 0, 0, 0.2);
}

.dark .nav-btn--ghost:hover {
  color: #f9fafb;
  border-color: rgba(255, 255, 255, 0.2);
}

.nav-btn--solid {
  background: #ff2d20;
  color: #ffffff;
}

.nav-btn--solid:hover {
  background: #e02419;
}

.hero-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #ff2d20;
  border: 1px solid rgba(255, 45, 32, 0.25);
  background: rgba(255, 45, 32, 0.05);
  padding: 5px 16px;
  border-radius: 100px;
  margin-bottom: 28px;
}

.hero-title {
  font-size: clamp(72px, 14vw, 116px);
  font-weight: 800;
  letter-spacing: -0.045em;
  line-height: 0.92;
  margin: 0 0 20px;
  color: #111827;
}

.dark .hero-title {
  color: #f9fafb;
}

.hero-subtitle {
  font-size: 16px;
  line-height: 1.75;
  color: #6b7280;
  max-width: 500px;
}

.dark .hero-subtitle {
  color: rgba(255, 255, 255, 0.5);
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  font-size: 14px;
  font-weight: 600;
  padding: 12px 24px;
  border-radius: 10px;
  text-decoration: none;
  transition: all 0.15s ease;
  border: 1px solid transparent;
  letter-spacing: -0.01em;
}

.cta-btn--primary {
  background: #ff2d20;
  color: #ffffff;
}

.cta-btn--primary:hover {
  background: #e02419;
  transform: translateY(-1px);
  box-shadow: 0 6px 24px rgba(255, 45, 32, 0.28);
}

.cta-btn--ghost {
  color: #6b7280;
  border-color: rgba(0, 0, 0, 0.1);
}

.dark .cta-btn--ghost {
  color: rgba(255, 255, 255, 0.55);
  border-color: rgba(255, 255, 255, 0.1);
}

.cta-btn--ghost:hover {
  color: #111827;
  border-color: rgba(0, 0, 0, 0.2);
}

.dark .cta-btn--ghost:hover {
  color: #f9fafb;
  border-color: rgba(255, 255, 255, 0.2);
}

.install-snippet {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: 'Courier New', 'Consolas', monospace;
  font-size: 13px;
  padding: 10px 18px;
  background: rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 9px;
}

.dark .install-snippet {
  background: rgba(255, 255, 255, 0.04);
  border-color: rgba(255, 255, 255, 0.08);
}

.install-prompt {
  color: #9ca3af;
  user-select: none;
}

.install-cmd {
  color: #111827;
}

.dark .install-cmd {
  color: #f9fafb;
}

.stack-pill {
  font-size: 11px;
  font-family: 'Courier New', monospace;
  color: #6b7280;
  border: 1px solid rgba(0, 0, 0, 0.12);
  padding: 4px 12px;
  border-radius: 6px;
  transition:
    color 0.15s,
    border-color 0.15s;
  cursor: default;
}

.dark .stack-pill {
  color: rgba(255, 255, 255, 0.55);
  border-color: rgba(255, 255, 255, 0.14);
}

.stack-pill:hover {
  color: #ff2d20;
  border-color: rgba(255, 45, 32, 0.3);
}

.welcome-stats-bar {
  position: relative;
  z-index: 10;
  border-top: 1px solid rgba(0, 0, 0, 0.07);
  border-bottom: 1px solid rgba(0, 0, 0, 0.07);
  background: rgba(0, 0, 0, 0.015);
}

.dark .welcome-stats-bar {
  border-color: rgba(255, 255, 255, 0.06);
  background: rgba(255, 255, 255, 0.015);
}

.stats-item {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 12px;
  font-weight: 500;
  color: #9ca3af;
}

.dark .stats-item {
  color: rgba(255, 255, 255, 0.35);
}

.stats-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #ff2d20;
  flex-shrink: 0;
}

.welcome-section {
  position: relative;
  z-index: 10;
  padding: 80px 0;
}

@media (min-width: 1024px) {
  .welcome-section {
    padding: 112px 0;
  }
}

.welcome-section--alt {
  background: rgba(0, 0, 0, 0.015);
  border-top: 1px solid rgba(0, 0, 0, 0.06);
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.dark .welcome-section--alt {
  background: rgba(255, 255, 255, 0.015);
  border-color: rgba(255, 255, 255, 0.055);
}

.section-label {
  display: inline-block;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #ff2d20;
  margin-bottom: 14px;
}

.section-title {
  font-size: clamp(28px, 4.5vw, 44px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.05;
  margin-bottom: 14px;
  color: #111827;
}

.dark .section-title {
  color: #f9fafb;
}

.section-subtitle {
  font-size: 15px;
  color: #6b7280;
  line-height: 1.75;
}

.dark .section-subtitle {
  color: rgba(255, 255, 255, 0.45);
}

.feature-card {
  display: block;
  padding: 22px;
  border-radius: 12px;
  border: 1px solid rgba(0, 0, 0, 0.07);
  background: #ffffff;
  text-decoration: none;
  transition:
    border-color 0.2s ease,
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.dark .feature-card {
  background: rgba(255, 255, 255, 0.03);
  border-color: rgba(255, 255, 255, 0.07);
}

.feature-card:hover {
  border-color: rgba(255, 45, 32, 0.3);
  transform: translateY(-2px);
  box-shadow: 0 10px 32px rgba(0, 0, 0, 0.05);
}

.dark .feature-card:hover {
  box-shadow: 0 10px 32px rgba(0, 0, 0, 0.3);
}

.feature-card-title {
  font-size: 15px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 6px;
  letter-spacing: -0.01em;
}

.dark .feature-card-title {
  color: #f9fafb;
}

.feature-card-desc {
  font-size: 13px;
  line-height: 1.65;
  color: #6b7280;
}

.dark .feature-card-desc {
  color: rgba(255, 255, 255, 0.4);
}

.auth-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid rgba(0, 0, 0, 0.07);
  background: #ffffff;
  transition: border-color 0.2s ease;
  cursor: default;
}

.dark .auth-card {
  background: rgba(255, 255, 255, 0.03);
  border-color: rgba(255, 255, 255, 0.07);
}

.auth-card:hover {
  border-color: rgba(255, 45, 32, 0.25);
}

.auth-card-label {
  font-size: 13px;
  font-weight: 500;
  color: #374151;
}

.dark .auth-card-label {
  color: rgba(255, 255, 255, 0.65);
}

.dev-card {
  padding: 24px;
  border-radius: 12px;
  border: 1px solid rgba(0, 0, 0, 0.07);
  background: #ffffff;
  transition:
    border-color 0.2s ease,
    transform 0.2s ease;
}

.dark .dev-card {
  background: rgba(255, 255, 255, 0.03);
  border-color: rgba(255, 255, 255, 0.07);
}

.dev-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 32px rgba(0, 0, 0, 0.04);
}

.dark .dev-card:hover {
  box-shadow: 0 10px 32px rgba(0, 0, 0, 0.3);
}

.dev-card-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 9px;
}

.install-block {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 20px;
  background: #111111;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.install-block-content {
  display: flex;
  align-items: center;
  gap: 12px;
  overflow: hidden;
  min-width: 0;
}

.install-block-cmd {
  font-family: 'Courier New', 'Consolas', monospace;
  font-size: 14px;
  color: #f9fafb;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.install-copy-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: transparent;
  color: rgba(255, 255, 255, 0.35);
  cursor: pointer;
  transition: all 0.15s ease;
  flex-shrink: 0;
}

.install-copy-btn:hover {
  border-color: rgba(255, 45, 32, 0.4);
  color: #ff2d20;
  background: rgba(255, 45, 32, 0.06);
}

.welcome-footer {
  position: relative;
  z-index: 10;
  padding: 28px 0;
  border-top: 1px solid rgba(0, 0, 0, 0.07);
}

.dark .welcome-footer {
  border-color: rgba(255, 255, 255, 0.06);
}

.footer-link {
  font-size: 13px;
  color: #9ca3af;
  text-decoration: none;
  transition: color 0.15s;
}

.footer-link:hover {
  color: #ff2d20;
}
</style>
