<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { LayoutGrid, Plus, Home, Tag, Bookmark } from 'lucide-vue-next';

const page = usePage<SharedData>();
const isAdmin = computed(() => page.props.auth.user?.id === 1);

const mainNavItems = computed(() => {
  const items: NavItem[] = [
    {
      title: 'Dashboard',
      href: '/app/dashboard',
      icon: Home,
    },
    {
      title: 'All Starterkits',
      href: '/app/starterkits',
      icon: LayoutGrid,
    },
    {
      title: 'Add Starterkit',
      href: '/app/starterkit/create',
      icon: Plus,
    }
  ];

  // Add Tags menu item only for admin
  if (isAdmin.value) {
    items.push({
      title: 'Tags',
      href: '/app/tags',
      icon: Tag,
    });
  }

  return items;
});
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in mainNavItems" :key="item.title">
        <SidebarMenuButton as-child :is-active="item.href === page.url">
          <Link :href="item.href">
          <component :is="item.icon" />
          <span>{{ item.title }}</span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
