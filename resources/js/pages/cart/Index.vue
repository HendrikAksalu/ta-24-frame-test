<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

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
    if (!image) {
        return 'https://placehold.co/600x400/png?text=Product';
    }
    if (image.startsWith('http://') || image.startsWith('https://')) {
        return image;
    }
    return image;
}

const total = computed(() => {
    return props.cart.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0);
});

function updateQuantity(productId: number, quantity: number) {
    router.post(
        `/cart/update/${productId}`,
        { quantity },
        { preserveScroll: true, preserveState: true },
    );
}

function removeFromCart(productId: number) {
    router.post(
        `/cart/remove/${productId}`,
        {},
        { preserveScroll: true, preserveState: true },
    );
}

function clearCart() {
    const ok = confirm('Kas oled kindel, et tahad kogu ostukorvi tühjendada?');
    if (!ok) return;
    router.delete('/cart/clear', { preserveScroll: true, preserveState: true });
}
</script>

<template>
    <Head title="Ostukorv" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Ostukorv</h1>
                <button type="button" class="rounded-md border border-border/80 bg-card px-4 py-2 text-sm hover:bg-muted/30" @click="clearCart">
                    Tühjenda
                </button>
            </div>

            <div v-if="!props.cart.length" class="rounded-md border border-border/80 bg-card p-4 text-sm text-muted-foreground">
                Ostukorv on tühi.
            </div>

            <div v-else class="grid gap-4">
                <div
                    v-for="item in props.cart"
                    :key="item.id"
                    class="flex flex-col gap-4 rounded-xl border border-border/80 bg-card p-4 md:flex-row md:items-center"
                >
                    <img class="size-24 rounded-lg object-cover" :src="imageUrl(item.image)" :alt="item.name" />

                    <div class="flex-1">
                        <div class="font-semibold">{{ item.name }}</div>
                        <div class="text-sm text-muted-foreground">{{ Number(item.price).toFixed(2) }} €</div>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-sm text-muted-foreground">Kogus</label>
                        <input
                            class="w-20 rounded-md border border-input bg-transparent px-2 py-1 text-sm outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            type="number"
                            min="1"
                            :value="item.quantity"
                            @input="(e) => updateQuantity(item.id, Number((e.target as HTMLInputElement).value))"
                        />
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-muted-foreground">
                            Kokku: {{ (Number(item.price) * item.quantity).toFixed(2) }} €
                        </div>
                        <button
                            type="button"
                            class="mt-2 text-sm font-medium text-destructive hover:underline"
                            @click="removeFromCart(item.id)"
                        >
                            Eemalda
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-muted-foreground">Kokku</div>
                <div class="text-xl font-semibold">{{ total.toFixed(2) }} €</div>
            </div>

            <div class="flex justify-end">
                <a href="/checkout" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Edasi maksma
                </a>
            </div>
        </div>
    </AppLayout>
</template>

