<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { dashboard, home, login, register } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <div class="min-h-svh bg-background text-foreground">
        <header class="flex items-center justify-between border-b border-border px-4 py-3 md:px-8">
            <Link :href="home()" class="flex items-center gap-2 text-sm font-semibold">
                <AppLogoIcon class="size-8 fill-current" />
                <span>{{ $page.props.name }}</span>
            </Link>
            <nav class="flex flex-wrap items-center gap-4 text-sm">
                <Link href="/blog" class="text-muted-foreground transition hover:text-foreground"> Blogi </Link>
                <template v-if="user">
                    <Link :href="dashboard()" class="text-muted-foreground transition hover:text-foreground"> Töölaud </Link>
                </template>
                <template v-else>
                    <Link :href="login()" class="text-muted-foreground transition hover:text-foreground"> Logi sisse </Link>
                    <Link :href="register()" class="rounded-md bg-primary px-3 py-1.5 text-primary-foreground"> Registreeru </Link>
                </template>
            </nav>
        </header>
        <main class="mx-auto max-w-3xl px-4 py-10 md:px-6">
            <slot />
        </main>
    </div>
</template>
