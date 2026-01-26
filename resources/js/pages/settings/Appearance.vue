<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { __ } from 'zorah-js';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: __('base.appearance.title'),
    href: edit().url,
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="__('base.appearance.title')" />

    <SettingsLayout>
      <Card>
        <CardHeader>
          <CardTitle>{{ __('base.appearance.title') }}</CardTitle>
          <CardDescription>{{ __('base.appearance.description') }}</CardDescription>
        </CardHeader>

        <CardContent>
          <AppearanceTabs />
        </CardContent>
      </Card>
    </SettingsLayout>
  </AppLayout>
</template>
