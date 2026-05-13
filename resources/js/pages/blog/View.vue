<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    post: {
        id: number;
        title: string;
        description: string;
        author?: { first_name: string; last_name: string } | null;
        created_at_formatted: string;
        comments?: Array<{ id: number; author_name: string; content: string; created_at: string }> | null;
    };
}>();

const commentForm = useForm({
    author_name: '',
    content: '',
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => page.props.auth?.user?.email === 'test@example.com');

const submitComment = () => {
    commentForm.post(`/blog/${props.post.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
};

const deleteComment = (commentId: number) => {
    if (!confirm('Kustuta see kommentaar?')) {
        return;
    }
    router.delete(`/blog/${props.post.id}/comments/${commentId}`, {
        preserveScroll: true,
    });
};

function deletePost() {
    if (!confirm('Kustuta see postitus koos kommentaaridega?')) {
        return;
    }
    router.delete(`/blog/${props.post.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="post.title" />

    <AppLayout :breadcrumbs="[]">
        <div class="mx-auto w-full max-w-3xl flex-1 px-4 py-6 md:px-6 md:py-8">
            <nav class="mb-6 text-sm text-muted-foreground">
                <Link href="/blog" class="text-indigo-600 hover:text-indigo-800 hover:underline dark:text-indigo-400">← NFL blogi</Link>
            </nav>

            <article class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <h1 class="text-3xl font-semibold tracking-tight">{{ post.title }}</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    {{ post.author ? `${post.author.first_name} ${post.author.last_name}` : 'Autor teadmata' }} · {{ post.created_at_formatted }}
                </p>
                <div class="prose prose-sm mt-6 max-w-none whitespace-pre-line text-foreground dark:prose-invert">
                    {{ post.description }}
                </div>
            </article>

            <section class="mt-10 rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 text-lg font-semibold">Kommentaarid</h2>

                <form class="mb-8 space-y-3" @submit.prevent="submitComment">
                    <div v-if="!user" class="grid gap-2">
                        <Label for="guest-name">Sinu nimi</Label>
                        <Input id="guest-name" v-model="commentForm.author_name" required autocomplete="name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="comment-body">Kommentaar</Label>
                        <textarea
                            id="comment-body"
                            v-model="commentForm.content"
                            rows="4"
                            required
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            placeholder="Kirjuta siia…"
                        />
                    </div>
                    <div class="flex justify-end">
                        <Button type="submit" :disabled="commentForm.processing">Saada</Button>
                    </div>
                </form>

                <ul v-if="post.comments?.length" class="space-y-4">
                    <li v-for="c in post.comments" :key="c.id" class="rounded-lg border border-border/60 p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium">{{ c.author_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ new Date(c.created_at).toLocaleString() }}</p>
                            </div>
                            <Button v-if="isAdmin" type="button" size="sm" variant="destructive" @click="deleteComment(c.id)"> Kustuta </Button>
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm">{{ c.content }}</p>
                    </li>
                </ul>
                <p v-else class="text-sm text-muted-foreground">Kommentaare veel pole.</p>
            </section>
        </div>
    </AppLayout>
</template>
