<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
  starterkits: {
    data: Array<{
      id: string;
      url: string;
      user: {
        id: number;
        name: string;
      };
      created_at: string;
    }>;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  auth: {
    user: any | null;
  };
}>();

const isLoggedIn = !!props.auth.user;
const isLoading = ref(false);
const loadedStarterkits = ref(props.starterkits.data);
const currentPage = ref(props.starterkits.current_page);
const hasMorePages = ref(currentPage.value < props.starterkits.last_page);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Starterkits',
    href: '/app/starterkits',
  },
];

// Format date helper
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date);
};

// Load more starterkits for infinite scrolling
const loadMoreStarterkits = async () => {
  if (isLoading.value || !hasMorePages.value) return;

  isLoading.value = true;
  try {
    const nextPage = currentPage.value + 1;
    const response = await axios.get(`/api/starterkits/load-more?page=${nextPage}`);

    const newStarterkits = response.data.data;
    loadedStarterkits.value = [...loadedStarterkits.value, ...newStarterkits];

    currentPage.value = response.data.current_page;
    hasMorePages.value = currentPage.value < response.data.last_page;
  } catch (error) {
    console.error('Error loading more starterkits:', error);
  } finally {
    isLoading.value = false;
  }
};

// Infinite scroll handler
const handleScroll = () => {
  if (isLoading.value) return;

  const scrollPosition = window.innerHeight + window.scrollY;
  const bodyHeight = document.body.offsetHeight;

  // Load more when user scrolls to 80% of the page
  if (scrollPosition >= bodyHeight * 0.8 && hasMorePages.value) {
    loadMoreStarterkits();
  }
};

// Setup scroll event listener
onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

// Cleanup
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
  <Head title="Starterkits Gallery" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Sign Up Banner for guests -->
      <div v-if="!isLoggedIn" class="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
        <div class="flex items-start">
          <div class="flex-1">
            <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300">Welcome to Laravel Starterkits!</h3>
            <p class="text-blue-700 dark:text-blue-400 mt-1">
              Sign up to access all features including submitting your own starterkits.
            </p>
          </div>
          <div class="ml-4">
            <Link :href="route('register')">
              <Button size="sm" variant="outline" class="border-blue-500 text-blue-600 hover:bg-blue-100 dark:border-blue-400 dark:text-blue-300">
                Sign Up
              </Button>
            </Link>
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Starterkits Gallery</h1>
        <Link :href="isLoggedIn ? route('starterkit.create') : route('login', { redirect: route('starterkit.create') })">
          <Button>Add Starterkit</Button>
        </Link>
      </div>

      <!-- Empty state -->
      <div v-if="loadedStarterkits.length === 0" class="p-8 text-center">
        <p class="text-gray-500 mb-4">No starterkits have been added yet.</p>
        <Link :href="route('starterkit.create')">
          <Button>Be the first to add one!</Button>
        </Link>
      </div>

      <!-- List view instead of grid -->
      <div v-else class="space-y-4">
        <Card v-for="kit in loadedStarterkits" :key="kit.id" class="w-full">
          <div class="flex flex-col md:flex-row md:items-center p-4">
            <div class="flex-1">
              <h3 class="font-medium text-lg">
                <a :href="kit.url" target="_blank" class="hover:underline text-blue-600 dark:text-blue-400">
                  {{ kit.url.replace('https://github.com/', '') }}
                </a>
              </h3>
              <p class="text-sm text-gray-500 mt-1">
                Added on {{ formatDate(kit.created_at) }}
              </p>
            </div>
            <div class="mt-4 md:mt-0">
              <Button variant="outline" as="a" :href="kit.url" target="_blank" class="w-full md:w-auto">
                View Repository
              </Button>
            </div>
          </div>
        </Card>

        <!-- Loading indicator -->
        <div v-if="isLoading" class="py-4 text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <!-- End of list message -->
        <div v-if="!hasMorePages && loadedStarterkits.length > 0" class="py-4 text-center text-gray-500">
          You've reached the end of the list.
        </div>
      </div>
    </div>
  </AppLayout>
</template>
