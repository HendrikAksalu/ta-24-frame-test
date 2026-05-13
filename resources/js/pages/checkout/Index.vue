<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    stripeConfigured: boolean;
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
    if (!image) return '/img/veg/tomato.jpg';
    if (image.startsWith('/')) return image;
    if (image.startsWith('http://') || image.startsWith('https://')) return image;
    return image;
}

function submitStripe() {
    form.post('/checkout/stripe');
}
</script>

<template>
    <Head title="Maksmine" />

    <AppLayout :breadcrumbs="[]">
        <div class="w-full rounded-xl bg-emerald-50/80 px-4 py-10 dark:bg-neutral-950/50 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl">
                <Link href="/cart" class="mb-6 inline-block text-sm font-medium text-emerald-700 hover:underline dark:text-emerald-400">
                    ← Tagasi ostukorvi
                </Link>

                <h1 class="mb-8 text-3xl font-bold text-gray-800 dark:text-neutral-100">Maksmine</h1>

                <div
                    v-if="form.errors.payment"
                    class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-900 dark:bg-red-950/40 dark:text-red-100"
                >
                    {{ form.errors.payment }}
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-neutral-100">Kliendi andmed</h2>

                        <div class="grid gap-3">
                            <div class="grid gap-2">
                                <label class="text-sm text-gray-500 dark:text-neutral-400">Eesnimi</label>
                                <input
                                    v-model="form.first_name"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-neutral-600 dark:bg-neutral-950 dark:text-neutral-100"
                                    autocomplete="given-name"
                                />
                                <p v-if="form.errors.first_name" class="text-sm text-red-600 dark:text-red-400">{{ form.errors.first_name }}</p>
                            </div>

                            <div class="grid gap-2">
                                <label class="text-sm text-gray-500 dark:text-neutral-400">Perekonnanimi</label>
                                <input
                                    v-model="form.last_name"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-neutral-600 dark:bg-neutral-950 dark:text-neutral-100"
                                    autocomplete="family-name"
                                />
                                <p v-if="form.errors.last_name" class="text-sm text-red-600 dark:text-red-400">{{ form.errors.last_name }}</p>
                            </div>

                            <div class="grid gap-2">
                                <label class="text-sm text-gray-500 dark:text-neutral-400">E-post</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-neutral-600 dark:bg-neutral-950 dark:text-neutral-100"
                                    autocomplete="email"
                                />
                                <p v-if="form.errors.email" class="text-sm text-red-600 dark:text-red-400">{{ form.errors.email }}</p>
                            </div>

                            <div class="grid gap-2">
                                <label class="text-sm text-gray-500 dark:text-neutral-400">Telefon</label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-neutral-600 dark:bg-neutral-950 dark:text-neutral-100"
                                    autocomplete="tel"
                                />
                                <p v-if="form.errors.phone" class="text-sm text-red-600 dark:text-red-400">{{ form.errors.phone }}</p>
                            </div>
                        </div>

                        <p
                            v-if="!props.stripeConfigured"
                            class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100"
                        >
                            Stripe makselahenduse kasutamiseks lisa keskkonda <code class="rounded bg-amber-100/80 px-1 dark:bg-amber-900/60">STRIPE_SECRET_KEY</code>.
                        </p>

                        <button
                            type="button"
                            class="mt-6 w-full rounded-lg bg-[#635BFF] py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#544bc9] disabled:opacity-50"
                            :disabled="form.processing || !props.stripeConfigured"
                            @click="submitStripe()"
                        >
                            Maksa kaardiga (Stripe)
                        </button>
                    </div>

                    <div class="h-fit rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-neutral-100">Tellimuse kokkuvõte</h2>

                        <div class="flex flex-col gap-3">
                            <div
                                v-for="item in props.cart"
                                :key="item.id"
                                class="flex items-center gap-3 rounded-lg border border-gray-100 p-3 dark:border-neutral-800"
                            >
                                <img
                                    class="size-16 shrink-0 rounded-md object-cover"
                                    :src="imageUrl(item.image)"
                                    :alt="item.name"
                                    referrerpolicy="no-referrer"
                                    loading="lazy"
                                />
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-gray-800 dark:text-neutral-100">{{ item.name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-neutral-400">
                                        {{ item.quantity }} tk × {{ Number(item.price).toFixed(2) }} €
                                    </div>
                                </div>
                                <div class="text-sm font-semibold text-gray-800 dark:text-neutral-100">
                                    {{ (Number(item.price) * item.quantity).toFixed(2) }} €
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-neutral-800">
                            <span class="text-gray-600 dark:text-neutral-400">Kokku</span>
                            <span class="text-xl font-bold text-emerald-700 dark:text-emerald-400">{{ total.toFixed(2) }} €</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
