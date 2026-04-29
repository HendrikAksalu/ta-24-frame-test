<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Makse katkestatud" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4">
            <h1 class="text-2xl font-semibold">Makse katkestati</h1>

            <div class="rounded-xl border border-border/80 bg-card p-5 text-sm">
                Midagi hullu ei juhtunud. Sinu tooted jäid ostukorvi alles ja saad uuesti proovida.
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="/cart" class="rounded-md border border-border/80 bg-card px-4 py-2 text-sm hover:bg-muted/30">Tagasi ostukorvi</a>
                <a href="/shop" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tagasi poodi</a>
            </div>
        </div>
    </AppLayout>
</template>

