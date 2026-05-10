<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { appearance } from '@/routes';
import { dashboard } from '@/routes';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Töölaud',
        href: dashboard().url,
    },
    {
        title: 'Välimus',
        href: appearance().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Välimus — sätted" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Välimus" description="Vali kas hele, tume või süsteemi järgi automaatne kujundus." />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
