<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { EllipsisVertical, Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps<{
  starterkits: Array<{
    id: string;
    url: string;
    created_at: string;
  }>;
}>();

const starterkitToDelete = ref<string | null>(null);
const showDeleteDialog = ref(false);

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

// Delete function
const confirmDelete = (id: string) => {
  starterkitToDelete.value = id;
  showDeleteDialog.value = true;
};

const deleteStarterkit = () => {
  if (starterkitToDelete.value) {
    router.delete(route('starterkit.destroy', starterkitToDelete.value));
    closeDialog();
  }
};

const closeDialog = () => {
  showDeleteDialog.value = false;
  starterkitToDelete.value = null;
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
            <CardHeader class="flex flex-row items-start justify-between space-y-0 pb-2">
              <CardTitle class="truncate max-w-[80%]">
                <a :href="kit.url" target="_blank" class="hover:underline text-blue-600 dark:text-blue-400">
                  {{ kit.url.replace('https://github.com/', '') }}
                </a>
              </CardTitle>
              <DropdownMenu>
                <DropdownMenuTrigger as="button" class="h-8 w-8 inline-flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                  <EllipsisVertical class="h-4 w-4" />
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuItem as="div">
                    <Link :href="route('starterkit.edit', kit.id)" class="flex w-full items-center">
                      <Pencil class="mr-2 h-4 w-4" />
                      Edit
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem @click="confirmDelete(kit.id)" class="text-red-600 dark:text-red-400 focus:text-red-700 dark:focus:text-red-300">
                    <Trash2 class="mr-2 h-4 w-4" />
                    Delete
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
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

  <!-- Delete Confirmation Dialog -->
  <Dialog :open="showDeleteDialog" @update:open="closeDialog">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Are you sure?</DialogTitle>
        <DialogDescription>
          This action cannot be undone. This will permanently delete your starterkit.
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <Button variant="outline" @click="closeDialog">Cancel</Button>
        <Button variant="destructive" @click="deleteStarterkit">Delete</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
