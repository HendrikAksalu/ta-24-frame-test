<script setup lang="ts">
import MapView from '@/components/MapView.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Kaart', href: '/kaart' }];
</script>

<template>
    <Head title="Kaart" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl bg-muted/30 p-4 md:p-6">
            <div class="min-h-[70vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-card shadow-sm dark:border-sidebar-border md:min-h-[min(85vh,900px)]">
                <MapView />
            </div>
        </div>
    </AppLayout>
</template>
