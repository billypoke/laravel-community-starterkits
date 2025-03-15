<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Bookmark } from 'lucide-vue-next';
import { ref } from 'vue';
import axios from 'axios';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();

const props = defineProps<{
  starterkitId: string;
  isBookmarked: boolean;
  bookmarkCount: number;
}>();

const emit = defineEmits(['update:bookmarkCount', 'update:isBookmarked']);
const isBookmarked = ref(props.isBookmarked);
const isLoading = ref(false);

const toggleBookmark = async () => {
  if (!page.props.auth.user.email) {
    // Redirect to register page with the current URL as redirect parameter
    router.visit(route('register'), {
      data: {
        redirect: window.location.pathname
      }
    });
    return;
  }

  try {
    isLoading.value = true;
    // Just increment/decrement by 1 locally based on action
    const newCount = props.bookmarkCount + (isBookmarked.value ? -1 : 1);
    isBookmarked.value = !isBookmarked.value;
    emit('update:bookmarkCount', newCount);
    emit('update:isBookmarked', isBookmarked.value);

    // Still make the server request but don't use its count
    const response = await axios.post(route('starterkit.bookmark', props.starterkitId));

    // Only use server response for bookmark state validation
    if (response.data.is_bookmarked !== isBookmarked.value) {
      isBookmarked.value = response.data.is_bookmarked;
      emit('update:isBookmarked', response.data.is_bookmarked);
    }
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
