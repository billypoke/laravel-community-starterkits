<script setup lang="ts">
import StarterkitCard from '@/components/StarterkitCard.vue';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Starterkit } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
  starterkits: Array<Starterkit>;
  bookmarks: Array<Starterkit>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
];
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <Link :href="route('starterkit.create')">
          <Button>Add Starter Kit</Button>
        </Link>
      </div>

      <Tabs default-value="bookmarks" class="w-full">
        <TabsList class="grid w-full grid-cols-2">
          <TabsTrigger value="bookmarks">Bookmarks</TabsTrigger>
          <TabsTrigger value="my-kits">My Starter Kits</TabsTrigger>
        </TabsList>

        <TabsContent value="bookmarks">
          <div v-if="!bookmarks?.length" class="p-8 text-center">
            <p class="mb-4 text-gray-500">You haven't bookmarked any starterkits yet.</p>
            <Link :href="route('starterkit.index')">
              <Button>Browse Starter Kits</Button>
            </Link>
          </div>
          <div v-else class="space-y-4">
            <StarterkitCard v-for="kit in bookmarks" :key="kit.id" :starterkit="kit" :show-bookmark="true" />
          </div>
        </TabsContent>

        <TabsContent value="my-kits">
          <div v-if="!starterkits?.length" class="p-8 text-center">
            <p class="mb-4 text-gray-500">You haven't created any starter kits yet.</p>
            <Link :href="route('starterkit.create')">
              <Button>Create Your First Kit</Button>
            </Link>
          </div>
          <div v-else class="space-y-4">
            <StarterkitCard v-for="kit in starterkits" :key="kit.id" :starterkit="kit" :show-actions="true" />
          </div>
        </TabsContent>
      </Tabs>
    </div>
  </AppLayout>
</template>
