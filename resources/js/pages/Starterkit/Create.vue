<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
  url: '',
});

const handleSubmit = () => {
  form.post(route('starterkit.store'));
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Starterkit',
    href: '/app/starterkit',
  },
  {
    title: 'Create',
    href: '/app/starterkit/create',
  },
];
</script>

<template>
  <Head title="Create Starterkit" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle>Create New Starterkit</CardTitle>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="space-y-2">
              <Label for="url">GitHub URL</Label>
              <Input id="url" v-model="form.url" placeholder="https://github.com/username/repository" />
              <div v-if="form.errors.url" class="text-red-500 text-sm mt-1">
                {{ form.errors.url }}
              </div>
            </div>

            <div>
              <Button type="submit" :disabled="form.processing">Submit</Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
