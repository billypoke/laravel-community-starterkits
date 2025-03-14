<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Bookmark } from 'lucide-vue-next';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps<{
  starterkitId: string;
  isBookmarked: boolean;
}>();

const isBookmarked = ref(props.isBookmarked);
const isLoading = ref(false);

const toggleBookmark = async () => {
  try {
    isLoading.value = true;
    await axios.post(route('starterkit.bookmark', props.starterkitId));
    isBookmarked.value = !isBookmarked.value;
  } catch (error) {
    console.error('Error toggling bookmark:', error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <Button
    variant="ghost"
    size="icon"
    :disabled="isLoading"
    @click="toggleBookmark"
    :class="[isBookmarked ? 'text-yellow-500' : 'text-gray-500']"
  >
    <Bookmark :class="{ 'fill-current': isBookmarked }" class="h-5 w-5" />
  </Button>
</template>