<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ShoppingCart } from 'lucide-vue-next';

type CartItem = {
    id: number;
    name: string;
    price: number | string;
    image: string | null;
    quantity: number;
};

const props = defineProps<{
    cart: CartItem[];
}>();

function imageUrl(image: string | null) {
    if (!image || !String(image).trim()) {
        return '/img/veg/tomato.jpg';
    }
    const s = String(image).trim();
    if (s.startsWith('http://') || s.startsWith('https://')) {
        return s;
    }
    if (s.startsWith('/')) {
        return s;
    }
    return `/${s.replace(/^\/+/, '')}`;
}

const total = computed(() => {
    return props.cart.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0);
});

function updateQuantity(productId: number, quantity: number) {
    router.post(`/cart/update/${productId}`, { quantity }, { preserveScroll: true });
}

function removeFromCart(productId: number) {
    router.post(`/cart/remove/${productId}`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Ostukorv" />

    <AppLayout :breadcrumbs="[]">
        <div class="w-full rounded-xl bg-emerald-50/80 px-4 py-10 dark:bg-neutral-950/50 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <Link href="/shop" class="mb-6 inline-block text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                    ← Tagasi poodi
                </Link>

                <h1 class="mb-8 text-3xl font-bold text-gray-800 dark:text-neutral-100">Ostukorv</h1>

                <div v-if="!props.cart.length" class="rounded-xl border border-gray-100 bg-white py-20 text-center shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <ShoppingCart class="mx-auto mb-4 size-16 text-gray-300 dark:text-neutral-600" stroke-width="1" />
                    <p class="text-xl text-gray-400 dark:text-neutral-500">Ostukorv on tühi.</p>
                    <Link href="/shop" class="mt-3 inline-block text-indigo-600 hover:underline dark:text-indigo-400"> Mine poodi → </Link>
                </div>

                <template v-else>
                    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <div
                            v-for="item in props.cart"
                            :key="item.id"
                            class="flex flex-col gap-4 border-b border-gray-100 p-5 last:border-b-0 sm:flex-row sm:items-center dark:border-neutral-800"
                        >
                            <img
                                class="h-20 w-20 shrink-0 rounded-lg object-cover"
                                :src="imageUrl(item.image)"
                                :alt="item.name"
                                referrerpolicy="no-referrer"
                                loading="lazy"
                            />
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-800 dark:text-neutral-100">{{ item.name }}</h3>
                                <p class="font-bold text-emerald-700 dark:text-emerald-400">{{ Number(item.price).toFixed(2) }} €</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                <select
                                    :value="item.quantity"
                                    class="rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-neutral-600 dark:bg-neutral-950 dark:text-neutral-100"
                                    @change="updateQuantity(item.id, Number(($event.target as HTMLSelectElement).value))"
                                >
                                    <option v-for="n in 99" :key="n" :value="n">{{ n }}</option>
                                </select>
                                <p class="w-24 text-right font-semibold text-gray-800 dark:text-neutral-100">
                                    {{ (Number(item.price) * item.quantity).toFixed(2) }} €
                                </p>
                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                    title="Eemalda"
                                    @click="removeFromCart(item.id)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-semibold text-gray-800 dark:text-neutral-100">Kokku:</span>
                            <span class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ total.toFixed(2) }} €</span>
                        </div>
                        <Link
                            href="/checkout"
                            class="mt-4 block w-full rounded-lg bg-green-600 py-3 text-center text-base font-semibold text-white transition hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-500"
                        >
                            Edasi maksma →
                        </Link>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
