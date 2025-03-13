<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
  starterkits: Array<{
    id: string;
    url: string;
    created_at: string;
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

// Format date function
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date);
};
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- My Starterkits Section -->
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold">My Starterkits</h2>
          <Link :href="route('starterkit.create')">
            <Button>Add Starterkit</Button>
          </Link>
        </div>

        <div v-if="starterkits.length === 0" class="p-4 text-center">
          <p class="text-gray-500 mb-4">You haven't added any starterkits yet.</p>
          <Link :href="route('starterkit.create')">
            <Button>Add Your First Starterkit</Button>
          </Link>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <Card v-for="kit in starterkits" :key="kit.id" class="h-full">
            <CardHeader>
              <CardTitle class="truncate">
                <a :href="kit.url" target="_blank" class="hover:underline text-blue-600 dark:text-blue-400">
                  {{ kit.url.replace('https://github.com/', '') }}
                </a>
              </CardTitle>
            </CardHeader>

            <CardContent>
              <p class="text-sm text-gray-500">
                Added on {{ formatDate(kit.created_at) }}
              </p>
            </CardContent>

            <CardFooter class="flex justify-between">
              <Button variant="outline" as="a" :href="kit.url" target="_blank">
                View Repository
              </Button>
            </CardFooter>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
