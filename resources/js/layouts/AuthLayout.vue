<script setup lang="ts">
import AuthGateLayout from '@/layouts/auth/AuthGateLayout.vue';

withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        showHeader?: boolean;
    }>(),
    {
        showHeader: true,
    },
);
</script>

<template>
    <AuthGateLayout :title="title" :description="description" :show-header="showHeader">
        <slot />
    </AuthGateLayout>
</template>
