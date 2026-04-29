<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
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

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
});

const total = computed(() => {
    return props.cart.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0);
});

function imageUrl(image: string | null) {
    if (!image) return 'https://placehold.co/600x400/png?text=Product';
    if (image.startsWith('http://') || image.startsWith('https://')) return image;
    return image;
}
</script>

<template>
    <Head title="Kassa" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4">
            <h1 class="text-2xl font-semibold">Kassa</h1>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-border/80 bg-card p-5">
                    <h2 class="mb-4 text-lg font-semibold">Kliendi andmed</h2>

                    <div class="grid gap-3">
                        <div class="grid gap-2">
                            <label class="text-sm text-muted-foreground">Eesnimi</label>
                            <input v-model="form.first_name" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                            <p v-if="form.errors.first_name" class="text-sm text-destructive">{{ form.errors.first_name }}</p>
                        </div>

                        <div class="grid gap-2">
                            <label class="text-sm text-muted-foreground">Perekonnanimi</label>
                            <input v-model="form.last_name" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                            <p v-if="form.errors.last_name" class="text-sm text-destructive">{{ form.errors.last_name }}</p>
                        </div>

                        <div class="grid gap-2">
                            <label class="text-sm text-muted-foreground">Email</label>
                            <input v-model="form.email" type="email" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                            <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                        </div>

                        <div class="grid gap-2">
                            <label class="text-sm text-muted-foreground">Telefon</label>
                            <input v-model="form.phone" type="tel" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                            <p v-if="form.errors.phone" class="text-sm text-destructive">{{ form.errors.phone }}</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button
                            type="button"
                            class="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                            :disabled="form.processing"
                            @click="form.post('/checkout/stripe')"
                        >
                            Maksa Stripe'iga
                        </button>
                    </div>
                </div>

                <div class="rounded-xl border border-border/80 bg-card p-5">
                    <h2 class="mb-4 text-lg font-semibold">Tellimuse kokkuvõte</h2>

                    <div class="flex flex-col gap-3">
                        <div
                            v-for="item in props.cart"
                            :key="item.id"
                            class="flex items-center gap-3 rounded-lg border border-border/60 p-3"
                        >
                            <img class="size-16 rounded-md object-cover" :src="imageUrl(item.image)" :alt="item.name" />
                            <div class="flex-1">
                                <div class="font-semibold">{{ item.name }}</div>
                                <div class="text-sm text-muted-foreground">
                                    {{ item.quantity }} x {{ Number(item.price).toFixed(2) }} €
                                </div>
                            </div>
                            <div class="text-sm font-medium">
                                {{ (Number(item.price) * item.quantity).toFixed(2) }} €
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-border/60 pt-4">
                        <div class="text-sm text-muted-foreground">Kokku</div>
                        <div class="text-xl font-semibold">{{ total.toFixed(2) }} €</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

