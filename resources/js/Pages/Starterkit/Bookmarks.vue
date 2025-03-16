<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import StarterkitCard from '@/components/StarterkitCard.vue';

const props = defineProps<{
  starterkits: Array<{
    id: string;
    url: string;
    user: {
      id: number;
      name: string;
    };
    tags: Array<{
      id: number;
      name: string;
    }>;
    created_at: string;
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bookmarks',
    href: route('starterkit.bookmarks'),
  },
];

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};
</script>

<template>

  <Head title="Bookmarks" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">My Bookmarks</h1>
      </div>

      <div v-if="starterkits.length === 0" class="p-8 text-center">
        <p class="text-gray-500 mb-4">You haven't bookmarked any starterkits yet.</p>
        <Link :href="route('starterkit.index')">
        <Button>Browse Starterkits</Button>
        </Link>
      </div>

      <div v-else class="space-y-4">
        <StarterkitCard v-for="kit in starterkits" :key="kit.id" :starterkit="kit" :show-bookmark="true" />
      </div>
    </div>
  </AppLayout>
</template>
