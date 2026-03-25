<script setup lang="ts">
import MapView from '@/components/MapView.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { WeatherData, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

defineProps<{
    weather: WeatherData | null;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div v-if="weather" class="flex h-full justify-between p-4">
                        <div>
                            <h2 class="text-5xl font-semibold">{{ weather.main.temp }}°C</h2>
                            <ul class="mt-4 grid list-inside list-disc gap-1 text-muted-foreground">
                                <li>{{ weather.weather[0].description }}</li>
                                <li>Humidity: {{ weather.main.humidity }}%</li>
                                <li>Wind: {{ weather.wind.speed }} m/s</li>
                            </ul>
                        </div>
                        <img class="size-20" :src="`https://openweathermap.org/img/wn/${weather.weather[0].icon}@4x.png`" alt="" />
                    </div>
                    <div v-else class="flex h-full items-center justify-center p-4 text-muted-foreground">Weather data unavailable</div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <MapView />
            </div>
        </div>
    </AppLayout>
</template>
