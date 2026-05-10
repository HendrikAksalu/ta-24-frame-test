<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="header" class="flex-col bg-muted/30">
        <AppHeader :breadcrumbs="breadcrumbs" />
        <AppContent class="flex-1 px-4 pb-8 pt-4 md:px-6">
            <slot />
        </AppContent>
    </AppShell>
</template>
