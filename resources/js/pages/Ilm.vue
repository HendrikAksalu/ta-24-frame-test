<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, WeatherData } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Ilm', href: '/ilm' }];

const props = defineProps<{
    weather: WeatherData | null;
    city: string;
}>();

const cityInput = ref(props.city);

function searchCity() {
    router.get('/ilm', { city: cityInput.value });
}
</script>

<template>
    <Head title="Ilm" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 rounded-xl bg-muted/30 p-6">
            <div class="mx-auto w-full max-w-xl rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                    <div class="flex-1">
                        <template v-if="weather">
                            <h2 class="text-4xl font-semibold tracking-tight md:text-5xl">{{ weather.main.temp }}°C</h2>
                            <ul class="mt-4 grid list-inside list-disc gap-1 text-muted-foreground">
                                <li>{{ weather.weather[0].description }}</li>
                                <li>Niiskus: {{ weather.main.humidity }}%</li>
                                <li>Tuul: {{ weather.wind.speed }} m/s</li>
                            </ul>
                        </template>
                        <template v-else>
                            <p class="text-muted-foreground">Ilmaandmed ei ole saadaval. Kontrolli linna nime või API võtit.</p>
                        </template>
                    </div>
                    <img v-if="weather" class="mx-auto size-24 md:mx-0" :src="`https://openweathermap.org/img/wn/${weather.weather[0].icon}@4x.png`" alt="" />
                </div>
                <form class="mt-6 flex flex-wrap items-center gap-2" @submit.prevent="searchCity">
                    <input
                        v-model="cityInput"
                        type="text"
                        placeholder="Linna nimi (nt Tallinn)"
                        class="min-w-[12rem] flex-1 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    />
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Otsi</button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
