<script setup lang="ts">
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';
import BookmarkButton from './BookmarkButton.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
        <div v-if="showBookmark" class="flex flex-col items-center">
          <BookmarkButton :starterkit-id="starterkit.id" :is-bookmarked="isBookmarked"
            v-model:bookmark-count="bookmarkCount" v-model:is-bookmarked="isBookmarked" />
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
