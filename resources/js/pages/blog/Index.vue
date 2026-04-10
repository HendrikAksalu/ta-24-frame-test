<script setup lang="ts">
import BlogLayout from '@/layouts/BlogLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface PostRow {
    id: number;
    title: string;
    description: string | null;
    created_at_formatted: string;
    author?: { first_name: string; last_name: string } | null;
}

interface Paginated {
    data: PostRow[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

defineProps<{
    posts: Paginated;
}>();
</script>

<template>
    <Head title="Blogi" />

    <BlogLayout>
        <h1 class="mb-2 text-3xl font-semibold tracking-tight">Blogi</h1>
        <p class="mb-8 text-muted-foreground">Avaldatud postitused. Sisu pärineb andmebaasist (sh seederid).</p>

        <ul v-if="posts.data.length" class="space-y-6">
            <li v-for="post in posts.data" :key="post.id" class="rounded-xl border border-border/80 bg-card p-5 shadow-sm">
                <Link :href="`/blog/${post.id}`" class="text-xl font-medium text-foreground underline-offset-4 hover:underline">
                    {{ post.title }}
                </Link>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ post.author ? `${post.author.first_name} ${post.author.last_name}` : 'Autor teadmata' }}
                    · {{ post.created_at_formatted }}
                </p>
                <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-foreground/90">
                    {{ post.description || '—' }}
                </p>
                <Link :href="`/blog/${post.id}`" class="mt-3 inline-block text-sm font-medium text-primary hover:underline"> Loe edasi </Link>
            </li>
        </ul>
        <p v-else class="text-muted-foreground">Ühtegi avaldatud postitust pole. Lisa neid sisse logides jaotises Posts või käivita <code class="rounded bg-muted px-1 py-0.5 text-xs">php artisan db:seed</code>.</p>

        <div v-if="posts.last_page > 1" class="mt-10 flex justify-between gap-4 text-sm">
            <component
                :is="posts.prev_page_url ? Link : 'span'"
                :href="posts.prev_page_url || undefined"
                class="rounded-md border border-border px-4 py-2"
                :class="!posts.prev_page_url && 'pointer-events-none opacity-40'"
            >
                Eelmine
            </component>
            <span class="self-center text-muted-foreground"> Lehekülg {{ posts.current_page }} / {{ posts.last_page }} </span>
            <component
                :is="posts.next_page_url ? Link : 'span'"
                :href="posts.next_page_url || undefined"
                class="rounded-md border border-border px-4 py-2"
                :class="!posts.next_page_url && 'pointer-events-none opacity-40'"
            >
                Järgmine
            </component>
        </div>
    </BlogLayout>
</template>
