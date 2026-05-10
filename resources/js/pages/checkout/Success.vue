<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    order: {
        id: number;
        order_number: number | string;
        order_short?: string;
        first_name?: string;
        email?: string;
        total: string;
        payment_status: string;
        payment_provider?: string | null;
        payment_id?: string | null;
        paid_at: string | null;
        items?: Array<{
            product_name: string;
            price: string;
            quantity: number;
            image?: string | null;
        }>;
    };
}>();

const methodLabel = computed(() => {
    if (props.order.payment_provider === 'paypal') {
        return 'PayPal (demo)';
    }
    if (props.order.payment_provider === 'stripe') {
        return 'Stripe';
    }
    return props.order.payment_provider ?? '—';
});

function itemImage(src: string | null | undefined) {
    if (src && src.startsWith('/')) {
        return src;
    }
    if (src && (src.startsWith('http://') || src.startsWith('https://'))) {
        return src;
    }
    return '/img/veg/tomato.jpg';
}
</script>

<template>
    <Head title="Makse õnnestus" />

    <AppLayout :breadcrumbs="[]">
        <div class="flex flex-1 justify-center px-4 py-10 md:py-14">
            <div class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex justify-center">
                    <div class="flex size-16 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-md">
                        <CheckCircle2 class="size-9" stroke-width="2.5" />
                    </div>
                </div>

                <h1 class="mt-6 text-center text-2xl font-bold text-gray-900 dark:text-neutral-50">Makse õnnestus!</h1>

                <p class="mt-3 text-center text-sm leading-relaxed text-gray-600 dark:text-neutral-400">
                    Täname tellimuse eest<span v-if="order.first_name">, {{ order.first_name }}</span>! Kinnitus saadetakse aadressile
                    <strong class="text-gray-900 dark:text-neutral-200">{{ order.email }}</strong>
                    .
                </p>

                <div class="mt-8 overflow-hidden rounded-xl border border-gray-100 dark:border-neutral-800">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-neutral-950 dark:text-neutral-400">Tellimuse nr</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-neutral-100">
                                    {{ order.order_short ?? order.order_number }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-neutral-950 dark:text-neutral-400">Makse ID</td>
                                <td class="max-w-[12rem] break-all px-4 py-3 text-right font-mono text-xs text-gray-800 dark:text-neutral-200">
                                    {{ order.payment_id ?? '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-neutral-950 dark:text-neutral-400">Makseviis</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-neutral-100">{{ methodLabel }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray-50 px-4 py-3 font-medium text-gray-600 dark:bg-neutral-950 dark:text-neutral-400">Kokku makstud</td>
                                <td class="px-4 py-3 text-right text-lg font-bold text-emerald-700 dark:text-emerald-400">{{ order.total }} €</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="order.items?.length" class="mt-8">
                    <p class="mb-3 text-sm font-semibold text-gray-800 dark:text-neutral-200">Tellitud tooted:</p>
                    <ul class="space-y-3">
                        <li
                            v-for="(i, idx) in order.items"
                            :key="`${i.product_name}-${idx}`"
                            class="flex items-center gap-3 rounded-xl border border-gray-100 p-3 dark:border-neutral-800"
                        >
                            <img
                                class="size-14 shrink-0 rounded-lg object-cover"
                                :src="itemImage(i.image)"
                                :alt="i.product_name"
                                referrerpolicy="no-referrer"
                                loading="lazy"
                            />
                            <div class="min-w-0 flex-1">
                                <div class="font-medium text-gray-900 dark:text-neutral-100">{{ i.product_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-neutral-400">{{ i.quantity }} tk</div>
                            </div>
                            <div class="shrink-0 text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                {{ (Number(i.price) * i.quantity).toFixed(2) }} €
                            </div>
                        </li>
                    </ul>
                </div>

                <Link
                    href="/shop"
                    class="mt-8 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3.5 text-sm font-semibold text-white shadow transition hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500"
                >
                    Tagasi poodi
                    <span aria-hidden="true">→</span>
                </Link>

                <p v-if="order.paid_at" class="mt-4 text-center text-xs text-muted-foreground">
                    Tasutud: {{ new Date(order.paid_at).toLocaleString() }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
