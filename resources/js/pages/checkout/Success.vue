<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    order: {
        id: number;
        order_number: number | string;
        total: string;
        payment_status: string;
        paid_at: string | null;
        items?: Array<{
            product_name: string;
            price: string;
            quantity: number;
        }>;
    };
}>();
</script>

<template>
    <Head title="Maksmine õnnestus" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4">
            <h1 class="text-2xl font-semibold">Õnnestus</h1>

            <div class="rounded-xl border border-border/80 bg-card p-5">
                <p class="text-sm text-muted-foreground">Aitäh tellimuse eest!</p>
                <p class="mt-3 text-xl font-semibold">
                    Tellimuse number: {{ order.order_number }}
                </p>
                <p class="mt-1 text-sm text-muted-foreground">Summa: {{ order.total }} €</p>

                <div class="mt-3 text-sm">
                    <span class="font-medium">Staatus:</span> {{ order.payment_status }}
                </div>
                <div v-if="order.paid_at" class="mt-1 text-sm text-muted-foreground">
                    Tasutud: {{ new Date(order.paid_at).toLocaleString() }}
                </div>
            </div>

            <div v-if="order.items?.length" class="rounded-xl border border-border/80 bg-card p-5">
                <h2 class="mb-3 text-lg font-semibold">Tellimuse read</h2>
                <ul class="space-y-2">
                    <li v-for="i in order.items" :key="i.product_name" class="flex items-center justify-between">
                        <div class="text-sm">
                            {{ i.quantity }} x {{ i.product_name }}
                        </div>
                        <div class="text-sm font-medium">{{ (Number(i.price) * i.quantity).toFixed(2) }} €</div>
                    </li>
                </ul>
            </div>

            <div class="flex justify-end">
                <a href="/shop" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Tagasi poodi
                </a>
            </div>
        </div>
    </AppLayout>
</template>

