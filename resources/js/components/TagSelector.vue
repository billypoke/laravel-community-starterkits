<script setup lang="ts">
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { X } from 'lucide-vue-next';

const props = defineProps<{
  modelValue: number[];
  availableTags: Array<{ id: number; name: string }>;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: number[]];
}>();

const removeTag = (tagId: number) => {
  emit('update:modelValue', props.modelValue.filter(id => id !== tagId));
};

const addTag = (tagId: number) => {
  if (!props.modelValue.includes(tagId)) {
    emit('update:modelValue', [...props.modelValue, tagId]);
  }
};

const getSelectedTags = () => {
  return props.availableTags.filter(tag => props.modelValue.includes(tag.id));
};

const getUnselectedTags = () => {
  return props.availableTags.filter(tag => !props.modelValue.includes(tag.id));
};
</script>

<template>
  <div class="space-y-3">
    <!-- Selected tags -->
    <div class="flex flex-wrap gap-2">
      <Badge v-for="tag in getSelectedTags()" :key="tag.id" variant="secondary" class="flex items-center gap-1">
        {{ tag.name }}
        <button type="button" class="hover:text-destructive" @click="removeTag(tag.id)">
          <X class="h-3 w-3" />
        </button>
      </Badge>
    </div>

    <!-- Tag selector -->
    <Select @update:model-value="addTag">
      <SelectTrigger>
        <SelectValue placeholder="Add a tag..." />
      </SelectTrigger>
      <SelectContent>
        <SelectGroup>
          <SelectItem v-for="tag in getUnselectedTags()" :key="tag.id" :value="tag.id">
            {{ tag.name }}
          </SelectItem>
        </SelectGroup>
      </SelectContent>
    </Select>
  </div>
</template>
