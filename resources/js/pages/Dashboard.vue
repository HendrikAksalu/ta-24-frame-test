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

const props = defineProps<{
    weather: WeatherData | null;
    city: string;
}>();

import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
const cityInput = ref(props.city);

function searchCity() {
    router.get('/dashboard', { city: cityInput.value });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex h-full flex-col justify-between p-4">
                        <div class="flex flex-1 flex-row justify-between">
                            <div>
                                <template v-if="weather">
                                    <h2 class="text-5xl font-semibold">{{ weather.main.temp }}°C</h2>
                                    <ul class="mt-4 grid list-inside list-disc gap-1 text-muted-foreground">
                                        <li>{{ weather.weather[0].description }}</li>
                                        <li>Humidity: {{ weather.main.humidity }}%</li>
                                        <li>Wind: {{ weather.wind.speed }} m/s</li>
                                    </ul>
                                </template>
                                <template v-else>
                                    <div class="flex h-full items-center justify-center text-muted-foreground">Weather data unavailable</div>
                                </template>
                            </div>
                            <img v-if="weather" class="size-20" :src="`https://openweathermap.org/img/wn/${weather.weather[0].icon}@4x.png`" alt="" />
                        </div>
                        <form @submit.prevent="searchCity" class="mt-4 flex items-center gap-2">
                            <input v-model="cityInput" type="text" placeholder="Enter city (e.g. Tallinn)" class="rounded border px-2 py-1" />
                            <button type="submit" class="rounded bg-blue-600 px-3 py-1 text-white">Search</button>
                        </form>
                    </div>
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
