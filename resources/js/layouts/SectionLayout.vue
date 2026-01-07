<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';

interface Props {
  title: string;
  description?: string;
  navItems?: NavItem[];
}

withDefaults(defineProps<Props>(), {
  description: undefined,
  navItems: () => [],
});

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
const layoutVariant = inject<'sidebar' | 'default'>('layoutVariant', 'default');
</script>

<template>
  <div class="px-4 py-6">
    <!-- Sidebar variant: Title above, then two-column layout with nav on left -->
    <template v-if="layoutVariant === 'sidebar'">
      <div class="mb-6 space-y-1">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">{{ description }}</p>
      </div>

      <div class="flex flex-col gap-8 md:flex-row md:gap-8">
        <!-- Left column: Nav only -->
        <aside v-if="navItems.length > 0" class="w-full shrink-0 md:w-44">
          <nav class="flex flex-col space-y-1">
            <Button
              v-for="item in navItems"
              :key="toUrl(item.href)"
              variant="ghost"
              :class="['w-full justify-start', { 'bg-muted': urlIsActive(item.href, currentPath) }]"
              as-child>
              <Link :href="item.href">
                <component :is="item.icon" v-if="item.icon" class="mr-2 h-4 w-4" />
                {{ item.title }}
              </Link>
            </Button>
          </nav>
        </aside>

        <Separator v-if="navItems.length > 0" class="md:hidden" />

        <!-- Right column: Content -->
        <div class="min-w-0 flex-1">
          <slot />
        </div>
      </div>
    </template>

    <!-- Default variant: Two-column layout with title, description, and nav on left -->
    <div v-else class="flex flex-col gap-8 md:flex-row md:gap-16">
      <!-- Left column: Title, description, and nav -->
      <aside class="w-full shrink-0 md:w-72">
        <div class="space-y-1">
          <h2 class="text-lg font-semibold">{{ title }}</h2>
          <p v-if="description" class="text-sm text-muted-foreground">{{ description }}</p>
        </div>

        <nav v-if="navItems.length > 0" class="mt-6 flex flex-col space-y-1">
          <Button
            v-for="item in navItems"
            :key="toUrl(item.href)"
            variant="ghost"
            :class="['w-full justify-start', { 'bg-muted': urlIsActive(item.href, currentPath) }]"
            as-child>
            <Link :href="item.href">
              <component :is="item.icon" v-if="item.icon" class="mr-2 h-4 w-4" />
              {{ item.title }}
            </Link>
          </Button>
        </nav>
      </aside>

      <Separator class="md:hidden" />

      <!-- Right column: Content -->
      <div class="min-w-0 flex-1">
        <slot />
      </div>
    </div>
  </div>
</template>
