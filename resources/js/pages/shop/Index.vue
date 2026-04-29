<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
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
                message.value = 'Toote lisamine ebaõnnestus.';
            },
        },
    );
}

function imageUrl(image: string | null) {
    if (!image) {
        return 'https://placehold.co/600x400/png?text=Product';
    }
    if (image.startsWith('http://') || image.startsWith('https://')) {
        return image;
    }
    return image;
}
</script>

<template>
    <Head title="E-pood" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">E-pood</h1>
                <div v-if="props.cartCount" class="text-sm text-muted-foreground">Ostukorv: {{ props.cartCount }}</div>
            </div>

            <div v-if="message" class="rounded-md border border-border/80 bg-card p-3 text-sm text-foreground">
                {{ message }}
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="p in products"
                    :key="p.id"
                    class="rounded-xl border border-border/80 bg-card p-4 shadow-sm"
                >
                    <img class="aspect-video w-full rounded-lg object-cover" :src="imageUrl(p.image)" :alt="p.name" />
                    <h2 class="mt-3 text-lg font-semibold">{{ p.name }}</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        SKU: {{ p.sku }} · Laos: {{ p.stock_quantity }}
                    </p>
                    <p v-if="p.description" class="mt-2 text-sm text-foreground/90">
                        {{ p.description }}
                    </p>
                    <div class="mt-3 text-base font-semibold">{{ Number(p.price).toFixed(2) }} €</div>

                    <div class="mt-4 grid gap-2">
                        <label class="text-sm text-muted-foreground">Kogus</label>
                        <input
                            v-model.number="quantities[p.id]"
                            type="number"
                            min="1"
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        />
                    </div>

                    <div class="mt-4">
                        <button
                            type="button"
                            class="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                            @click="addToCart(p.id)"
                        >
                            Lisa ostukorvi
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="/cart" class="rounded-md border border-border/80 bg-card px-4 py-2 text-sm hover:bg-muted/30">Vaata ostukorvi</a>
            </div>
        </div>
    </AppLayout>
</template>

