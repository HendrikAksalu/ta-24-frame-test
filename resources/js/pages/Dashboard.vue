<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import FootballIcon from '@/components/icons/FootballIcon.vue';
import { ArrowRight, CloudSun, CreditCard, Map, Newspaper, ShoppingCart } from 'lucide-vue-next';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage<AppPageProps>();
const greetingName = computed(() => {
    const raw = page.props.auth?.user?.name?.trim();
    if (!raw) return '';
    return raw.split(/\s+/)[0] ?? '';
});

const modules = [
    {
        title: 'Ilm ja ennustus',
        description: 'Kiire pilk ilmastiku järgmistesse päevadesse — vali linn ja vaata näitu.',
        href: '/ilm',
        icon: CloudSun,
        iconBg: 'from-fuchsia-500 via-purple-600 to-pink-900',
    },
    {
        title: 'Kaart ja markerid',
        description: 'Interaktiivne kaart: lisa punkte, kirjeldusi ning halda neid ühest kohast.',
        href: '/kaart',
        icon: Map,
        iconBg: 'from-lime-500 via-emerald-600 to-teal-900',
    },
    {
        title: 'Blogi',
        description: 'Postitused ja kommentaarid — loe või kirjuta, kui oled sisse loginud.',
        href: '/blog',
        icon: Newspaper,
        iconBg: 'from-amber-500 via-orange-600 to-rose-950',
    },
    {
        title: 'E-pood',
        description: 'Köögiviljad ostukorvis — maksmine Stripe hosted checkout kaudu.',
        href: '/shop',
        icon: ShoppingCart,
        iconBg: 'from-cyan-600 via-blue-700 to-slate-900',
    },
    {
        title: 'Maksmine Stripe’iga',
        description: 'Ava e-pood, mine kassasse ja tasuta Stripe kaardimaksevooga.',
        href: '/shop',
        icon: CreditCard,
        iconBg: 'from-indigo-600 via-blue-700 to-slate-900',
    },
    {
        title: "NFL'i rookied",
        description: 'Minu ja sõbra API kaudu — otsing, filtrid ja kaardid nagu filmide demos.',
        href: '/nfl-rookies',
        icon: FootballIcon,
        iconBg: 'from-violet-700 via-indigo-800 to-neutral-950',
    },
];
</script>

<template>
    <Head title="Töölaud" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div
                class="relative mx-auto w-full max-w-6xl overflow-hidden rounded-2xl border border-neutral-200/90 bg-gradient-to-b from-white via-white to-neutral-50 p-6 shadow-sm md:p-10 dark:border-neutral-800 dark:from-neutral-900 dark:via-neutral-900 dark:to-neutral-950"
            >
                <div
                    class="pointer-events-none absolute -right-24 -top-28 size-72 rounded-full bg-fuchsia-500/[0.14] blur-3xl dark:bg-fuchsia-400/[0.09]"
                    aria-hidden="true"
                />
                <div
                    class="pointer-events-none absolute -bottom-32 -left-16 size-80 rounded-full bg-lime-500/[0.09] blur-3xl dark:bg-lime-400/[0.06]"
                    aria-hidden="true"
                />

                <div class="relative max-w-xl space-y-3">
                    <h1 class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl dark:text-neutral-50">
                        <span v-if="greetingName">Tere, {{ greetingName }}!</span>
                        <span v-else>Tere tulemast!</span>
                    </h1>
                    <p class="text-base leading-relaxed text-neutral-600 dark:text-neutral-400">
                        Siit saad hüpata kõikidesse moodulitesse — iga kaart viib otse õige rakenduseni.
                    </p>
                </div>

                <div class="relative mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <Link
                        v-for="item in modules"
                        :key="item.href"
                        :href="item.href"
                        class="group flex min-h-[168px] flex-col justify-between rounded-xl border border-neutral-200/90 bg-white p-5 shadow-[0_1px_2px_rgba(0,0,0,0.04)] transition hover:border-fuchsia-400/50 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900 dark:hover:border-fuchsia-500/35 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-fuchsia-500/35 focus-visible:ring-offset-2"
                    >
                        <div>
                            <div
                                class="mb-4 flex size-11 items-center justify-center rounded-xl bg-gradient-to-br shadow-inner ring-1 ring-black/5 dark:ring-white/10"
                                :class="item.iconBg"
                            >
                                <component :is="item.icon" class="size-5 text-white" stroke-width="2" aria-hidden="true" />
                            </div>
                            <h2 class="text-lg font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">{{ item.title }}</h2>
                            <p class="mt-2 text-sm leading-snug text-neutral-600 dark:text-neutral-400">{{ item.description }}</p>
                        </div>
                        <span
                            class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-fuchsia-700 group-hover:gap-2 dark:text-fuchsia-400"
                        >
                            Ava moodul
                            <ArrowRight class="size-4 transition-transform group-hover:translate-x-0.5" aria-hidden="true" />
                        </span>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
