<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pencil, Trash2, Plus } from 'lucide-vue-next';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { AlertCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  tags: Array<{ id: number; name: string }>;
}>();

const form = useForm({
  name: '',
});

const editForm = useForm({
  name: '',
});

const editingTagId = ref<number | null>(null);

const submit = () => {
  form.post(route('tags.store'), {
    onSuccess: () => form.reset(),
  });
};

const startEdit = (tag: { id: number; name: string }) => {
  editingTagId.value = tag.id;
  editForm.name = tag.name;
};

const updateTag = (tagId: number) => {
  editForm.put(route('tags.update', tagId), {
    onSuccess: () => {
      editingTagId.value = null;
      editForm.reset();
    },
  });
};

const deleteTag = (tagId: number) => {
  if (confirm('Are you sure you want to delete this tag?')) {
    router.delete(route('tags.destroy', tagId), {
      preserveScroll: true,
      onSuccess: () => {
      },
    });
  }
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tags Management',
    href: route('tags.index'),
  },
];
</script>

<template>

  <Head title="Tags Management" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle>Tags Management</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="mb-6 space-y-4">
            <div class="flex gap-2">
              <Input v-model="form.name" placeholder="New tag name" required />
              <Button type="submit" :disabled="form.processing">
                <Plus class="mr-2 h-4 w-4" />
                Add Tag
              </Button>
            </div>

            <!-- Add validation error alert -->
            <Alert v-if="form.errors.name" variant="destructive">
              <AlertCircle class="h-4 w-4" />
              <AlertDescription>{{ form.errors.name }}</AlertDescription>
            </Alert>
          </form>

          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead class="w-[100px]">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="tag in tags" :key="tag.id">
                <TableCell>
                  <div v-if="editingTagId === tag.id" class="flex gap-2">
                    <Input v-model="editForm.name" class="max-w-[200px]" />
                    <Button @click="updateTag(tag.id)" size="sm" :disabled="editForm.processing">Save</Button>
                    <Button @click="editingTagId = null" size="sm" variant="outline">Cancel</Button>

                    <!-- Add edit validation error alert -->
                    <Alert v-if="editForm.errors.name" variant="destructive" class="mt-2">
                      <AlertCircle class="h-4 w-4" />
                      <AlertDescription>{{ editForm.errors.name }}</AlertDescription>
                    </Alert>
                  </div>
                  <span v-else>{{ tag.name }}</span>
                </TableCell>
                <TableCell>
                  <div class="flex gap-2">
                    <Button v-if="editingTagId !== tag.id" @click="startEdit(tag)" size="sm" variant="outline">
                      <Pencil class="h-4 w-4" />
                    </Button>
                    <Button @click="deleteTag(tag.id)" size="sm" variant="destructive">
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
