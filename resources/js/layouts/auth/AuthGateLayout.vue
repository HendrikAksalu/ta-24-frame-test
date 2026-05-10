<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { dashboard } from '@/routes';
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        /** Peida kaardi ülemine pealkirjakast (login/register minimalistlik režiim). */
        showHeader?: boolean;
    }>(),
    {
        showHeader: true,
    },
);
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center bg-[#f4f4f5] p-6 dark:bg-neutral-950">
        <div class="w-full max-w-[420px]">
            <Link
                :href="dashboard()"
                class="mb-8 flex justify-center transition-opacity hover:opacity-85 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400 focus-visible:ring-offset-2 rounded-lg"
            >
                <span class="flex scale-[1.45] items-center gap-1 origin-center">
                    <AppLogo />
                </span>
                <span class="sr-only">Töölaud</span>
            </Link>

            <div
                class="rounded-xl border border-neutral-200/90 bg-white px-8 py-9 shadow-[0_1px_3px_rgba(0,0,0,0.08)] dark:border-neutral-800 dark:bg-neutral-900 dark:shadow-none"
            >
                <div v-if="showHeader && (title || description)" class="mb-8 space-y-1.5 text-center">
                    <h1 v-if="title" class="text-lg font-semibold tracking-tight text-neutral-900 dark:text-neutral-50">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm leading-relaxed text-neutral-500 dark:text-neutral-400">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
