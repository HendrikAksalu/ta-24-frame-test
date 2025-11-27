<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { edit, index, show } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
  post: {
    id: number;
    title: string;
    content: string;
    author?: { id?: number; first_name: string; last_name: string } | null;
    published: boolean;
    created_at: string;
    updated_at: string;
    created_at_formatted: string;
    updated_at_formatted: string;
    comments?: Array<{ id: number; author_name: string; content: string; created_at: string }> | null;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Posts', href: index().url },
  { title: props.post.title, href: show.url(props.post.id) },
];

const statusLabel = computed(() => (props.post.published ? 'Published' : 'Draft'));
const statusClasses = computed(() =>
  props.post.published
    ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
    : 'bg-slate-100 text-slate-600 border border-slate-200',
);

const commentForm = useForm({
  author_name: '',
  content: '',
});

const submitComment = () => {
  commentForm.post(`/posts/${props.post.id}/comments`, {
    preserveScroll: true,
    onSuccess: () => commentForm.reset(),
  });
};

const deleteComment = (commentId: number) => {
  if (!confirm('Are you sure you want to delete this comment?')) return;
  router.delete(`/posts/${props.post.id}/comments/${commentId}`, {
    preserveScroll: true,
    onSuccess: () => window.location.reload(),
    onError: (err: any) => console.error(err),
  });
};
</script>

<template>
  <Head :title="props.post.title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-col gap-6 overflow-x-auto rounded-xl p-6">
      <div class="flex flex-col gap-4 rounded-xl border border-border/60 bg-muted/40 p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <h1 class="text-3xl font-semibold tracking-tight">{{ props.post.title }}</h1>
            <p class="text-sm text-muted-foreground">
              Written by <span class="font-medium text-foreground">{{ props.post.author ? `${props.post.author.first_name} ${props.post.author.last_name}` : 'Unknown' }}</span>
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium" :class="statusClasses">
              {{ statusLabel }}
            </span>

            <Button variant="outline" @click="() => router.get(edit.url(props.post.id))">
              Edit Post
            </Button>

            <Button as-child variant="ghost">
              <Link :href="index().url">Back to Posts</Link>
            </Button>
          </div>
        </div>

        <dl class="grid gap-4 rounded-lg border border-border/40 bg-background p-4 sm:grid-cols-3">
          <div>
            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Created</dt>
            <dd class="text-sm text-foreground">
              <div>{{ props.post.created_at_formatted }}</div>
              <div class="text-xs text-muted-foreground">{{ props.post.created_at }}</div>
            </dd>
          </div>

          <div>
            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Last updated</dt>
            <dd class="text-sm text-foreground">
              <div>{{ props.post.updated_at_formatted }}</div>
              <div class="text-xs text-muted-foreground">{{ props.post.updated_at }}</div>
            </dd>
          </div>

          <div>
            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Post ID</dt>
            <dd class="text-sm text-foreground">#{{ props.post.id }}</dd>
          </div>
        </dl>
      </div>

      <section class="rounded-xl border border-border/60 bg-background p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-foreground">Content</h2>
        <div class="prose max-w-none text-sm leading-relaxed text-foreground/90 dark:prose-invert">
          <p class="whitespace-pre-line">{{ props.post.content }}</p>
        </div>
      </section>

      <section class="rounded-xl border border-border/60 bg-background p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-foreground">Comments</h2>

        <div class="space-y-4">
          <div v-if="props.post.comments && props.post.comments.length">
            <div v-for="comment in (props.post.comments || []).slice(0, 3)" :key="comment.id" class="border rounded p-3">
              <div class="flex justify-between items-start">
                <div>
                  <div class="text-sm font-medium">{{ comment.author_name }}</div>
                  <div class="text-xs text-muted-foreground">{{ new Date(comment.created_at).toLocaleString() }}</div>
                </div>
                <div>
                  <Button size="icon" variant="ghost" @click="() => deleteComment(comment.id)">
                    Delete
                  </Button>
                </div>
              </div>
              <div class="mt-2 text-sm whitespace-pre-line">{{ comment.content }}</div>
            </div>
            <div v-if="(props.post.comments || []).length > 3" class="text-sm text-muted-foreground mt-2">
              <a :href="show.url(props.post.id) + '#comments'">View all comments ({{ props.post.comments.length }})</a>
            </div>
          </div>
          <div v-else class="text-sm text-muted-foreground">No comments yet.</div>
        </div>

        <form @submit.prevent="submitComment" class="mt-6">
          <div class="grid gap-3">
            <input v-model="commentForm.author_name" placeholder="Your name" class="w-full rounded border p-2" />
            <textarea v-model="commentForm.content" rows="4" placeholder="Add a comment" class="w-full rounded border p-2"></textarea>
            <div class="flex justify-end">
              <Button type="submit" :disabled="commentForm.processing">Add Comment</Button>
            </div>
          </div>
        </form>
      </section>
    </div>
  </AppLayout>
</template>