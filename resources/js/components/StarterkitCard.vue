<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import BookmarkButton from './BookmarkButton.vue';

const props = defineProps<{
  starterkit: {
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
    is_bookmarked?: boolean;
    bookmark_count: number;
  };
  showBookmark?: boolean;
  showActions?: boolean;
}>();

const bookmarkCount = ref(props.starterkit.bookmark_count);
const isBookmarked = ref(props.starterkit.is_bookmarked ?? false);

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const deleteStarterkit = () => {
  if (confirm('Are you sure you want to delete this starterkit?')) {
    router.delete(route('starterkit.destroy', props.starterkit.id));
  }
};
</script>

<template>
  <Card class="w-full">
    <div class="flex flex-col p-4 md:flex-row md:items-center">
      <div class="flex-1">
        <h3 class="text-lg font-medium">
          <a :href="starterkit.url" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">
            {{ starterkit.url.replace('https://github.com/', '') }}
          </a>
        </h3>
        <div class="mt-2 flex flex-wrap gap-2">
          <Badge v-for="tag in starterkit.tags" :key="tag.id" variant="secondary" class="text-xs">
            {{ tag.name }}
          </Badge>
        </div>
        <p class="mt-2 text-sm text-gray-500">Added on {{ formatDate(starterkit.created_at) }}</p>
      </div>
      <div class="mt-4 flex items-center gap-2 md:mt-0">
        <div v-if="showBookmark" class="flex flex-col items-center">
          <BookmarkButton
            :starterkit-id="starterkit.id"
            :is-bookmarked="isBookmarked"
            :bookmark-count="bookmarkCount"
            @update:bookmark-count="(newCount) => (bookmarkCount = newCount)"
          />
          <span class="text-xs text-gray-500">{{ bookmarkCount }}</span>
        </div>

        <template v-if="showActions">
          <Link :href="route('starterkit.edit', starterkit.id)">
            <Button variant="outline" size="sm">
              <Pencil class="h-4 w-4" />
            </Button>
          </Link>
          <Button @click="deleteStarterkit" variant="destructive" size="sm">
            <Trash2 class="h-4 w-4" />
          </Button>
        </template>
      </div>
    </div>
  </Card>
</template>
