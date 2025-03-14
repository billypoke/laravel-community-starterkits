<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import BookmarkButton from '@/components/BookmarkButton.vue';

const props = defineProps<{
  starterkit: {
    id: string;
    url: string;
    user?: {
      id: number;
      name: string;
    };
    tags: Array<{
      id: number;
      name: string;
    }>;
    created_at: string;
    is_bookmarked?: boolean;
  };
  showBookmark?: boolean;
}>();

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};
</script>

<template>
  <Card class="w-full">
    <div class="flex flex-col md:flex-row md:items-center p-4">
      <div class="flex-1">
        <h3 class="font-medium text-lg">
          <a :href="starterkit.url" target="_blank" class="hover:underline text-blue-600 dark:text-blue-400">
            {{ starterkit.url.replace('https://github.com/', '') }}
          </a>
        </h3>
        <div class="flex flex-wrap gap-2 mt-2">
          <Badge v-for="tag in starterkit.tags" :key="tag.id" variant="secondary" class="text-xs">
            {{ tag.name }}
          </Badge>
        </div>
        <p class="text-sm text-gray-500 mt-2">
          <template v-if="starterkit.user">
            Added by {{ starterkit.user.name }} on {{ formatDate(starterkit.created_at) }}
          </template>
          <template v-else>
            Added on {{ formatDate(starterkit.created_at) }}
          </template>
        </p>
      </div>
      <div class="mt-4 md:mt-0 flex items-center gap-2">
        <BookmarkButton 
          v-if="showBookmark" 
          :starterkit-id="starterkit.id" 
          :is-bookmarked="starterkit.is_bookmarked" 
        />
        <Button variant="outline" as="a" :href="starterkit.url" target="_blank" class="w-full md:w-auto">
          View Repository
        </Button>
      </div>
    </div>
  </Card>
</template>