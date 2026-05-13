<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Head, Link, router } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import { ref } from 'vue';

type Product = {
    id: number;
    name: string;
    description: string | null;
    price: number | string;
    sku: string;
    stock_quantity: number;
    image: string | null;
};

const props = defineProps<{
    products: Product[];
    cartCount?: number;
}>();

const quantities = ref<Record<number, number>>({});

for (const p of props.products) {
    quantities.value[p.id] = 1;
}

const message = ref<string | null>(null);

function addToCart(productId: number) {
    message.value = null;
    const quantity = quantities.value[productId] ?? 1;

    router.post(
        `/cart/add/${productId}`,
        { quantity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                message.value = 'Toode lisati ostukorvi.';
            },
            onError: () => {
                message.value = 'Lisamine ebaõnnestus.';
            },
        },
    );
}

function imageUrl(image: string | null) {
    if (!image) {
        return '/img/veg/tomato.jpg';
    }
    if (image.startsWith('/')) {
        return image;
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

function priceNum(price: number | string) {
    return Number(price);
}
</script>

<template>
    <Head title="Köögiviljad — pood" />

    <AppLayout :breadcrumbs="[]">
        <div class="w-full rounded-xl bg-emerald-50/80 px-4 py-10 dark:bg-neutral-950/50 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-800 dark:text-neutral-100">Talupood — köögiviljad</h1>
                        <p class="mt-2 max-w-2xl text-sm text-gray-500 dark:text-neutral-400">
                            Värsked viljad ja mugavad pakendid — vali kogus ja lisa ostukorvi.
                        </p>
                    </div>
                    <Link
                        href="/cart"
                        class="relative inline-flex items-center gap-2 self-start rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800"
                    >
                        <ShoppingCart class="size-5 text-gray-600 dark:text-neutral-300" />
                        <span>Ostukorv</span>
                        <span
                            v-if="(cartCount ?? 0) > 0"
                            class="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-xs font-bold text-white"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>
                </div>

                <div v-if="message" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-100">
                    {{ message }}
                </div>

                <div v-if="products.length" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="p in products"
                        :key="p.id"
                        class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                    >
                        <img
                            :src="imageUrl(p.image)"
                            :alt="p.name"
                            class="h-48 w-full object-cover"
                            referrerpolicy="no-referrer"
                            loading="lazy"
                        />
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-100">{{ p.name }}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-gray-500 dark:text-neutral-400">{{ p.description }}</p>
                            <p class="mt-3 text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ priceNum(p.price).toFixed(2) }} €</p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-neutral-500">SKU {{ p.sku }} · Laos {{ p.stock_quantity }}</p>

                            <div class="mt-4 flex items-center gap-3">
                                <label class="text-sm text-gray-500 dark:text-neutral-400">Kogus</label>
                                <select
                                    v-model.number="quantities[p.id]"
                                    class="rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-neutral-600 dark:bg-neutral-950 dark:text-neutral-100"
                                >
                                    <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
                                </select>
                            </div>

                            <Button
                                type="button"
                                class="mt-4 w-full bg-emerald-600 py-2.5 font-medium text-white hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500"
                                @click="addToCart(p.id)"
                            >
                                Lisa ostukorvi
                            </Button>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-muted-foreground">
                    Tooteid pole. Laadi köögiviljad käsuga
                    <code class="rounded bg-muted px-1 py-0.5 text-xs">php artisan db:seed --class=ProductSeeder</code>
                    .
                </p>
            </div>
        </div>
    </AppLayout>
</template>
