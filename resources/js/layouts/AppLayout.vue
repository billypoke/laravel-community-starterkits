<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});
</script>

<template>
  <div :data-auth="$page.props.auth.user ? '' : null">
    <AppLayout :breadcrumbs="breadcrumbs">
      <slot />
    </AppLayout>
  </div>
</template>
