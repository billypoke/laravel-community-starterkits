<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const githubUrl = ref('');

const handleSubmit = () => {
  console.log('GitHub URL submitted:', githubUrl.value);
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
          <CardTitle>Add New Starterkit</CardTitle>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="space-y-2">
              <Label for="githubUrl">GitHub URL</Label>
              <Input id="githubUrl" v-model="githubUrl" placeholder="https://github.com/username/repository" />
            </div>

            <div>
              <Button type="submit">Submit</Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
