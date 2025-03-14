<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { X } from 'lucide-vue-next';
import { ref, computed, nextTick } from 'vue';

const props = defineProps<{
  modelValue: number[];
  availableTags: Array<{ id: number; name: string }>;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: number[]];
}>();

const search = ref('');
const isOpen = ref(false);
const inputRef = ref<HTMLInputElement | null>(null);

const filteredTags = computed(() => {
  return props.availableTags
    .filter(tag => !props.modelValue.includes(tag.id))
    .filter(tag =>
      tag.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const removeTag = (tagId: number) => {
  emit('update:modelValue', props.modelValue.filter(id => id !== tagId));
};

const selectTag = (tagId: number) => {
  if (!props.modelValue.includes(tagId)) {
    emit('update:modelValue', [...props.modelValue, tagId]);
    search.value = '';
    // Keep focus and keep the menu open
    nextTick(() => {
      inputRef.value?.focus();
    });
  }
};

const getSelectedTags = () => {
  return props.availableTags.filter(tag => props.modelValue.includes(tag.id));
};

const handleFocusOut = (event: FocusEvent) => {
  const target = event.relatedTarget as HTMLElement;
  if (!target?.closest('[data-tag-selector]')) {
    isOpen.value = false;
  }
};
</script>

<template>
  <div class="space-y-3" data-tag-selector>
    <div class="flex flex-wrap gap-2">
      <Badge v-for="tag in getSelectedTags()" :key="tag.id" variant="secondary" class="flex items-center gap-1">
        {{ tag.name }}
        <button type="button" class="hover:text-destructive" @click="removeTag(tag.id)">
          <X class="h-3 w-3" />
        </button>
      </Badge>
    </div>

    <div @click="isOpen = true" @focusout="handleFocusOut">
      <Command v-if="isOpen" class="rounded-lg border shadow-md">
        <CommandInput ref="inputRef" v-model="search" placeholder="Search tags..." :value="search" />
        <CommandList>
          <CommandEmpty>No tags found.</CommandEmpty>
          <CommandGroup>
            <CommandItem v-for="tag in filteredTags" :key="tag.id" :value="tag.name" @select="selectTag(tag.id)">
              {{ tag.name }}
            </CommandItem>
          </CommandGroup>
        </CommandList>
      </Command>
      <div v-else
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background cursor-pointer">
        Click to add tags...
      </div>
    </div>
  </div>
</template>
