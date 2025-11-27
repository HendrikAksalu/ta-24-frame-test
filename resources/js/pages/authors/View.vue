<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link } from '@inertiajs/vue3';   
import { edit, index, show } from '@/routes/authors';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
  author: {
    id: number;
    first_name: string;
    last_name: string;
    date_of_birth: string;
    created_at: string;
    updated_at: string;
    created_at_formatted: string;
    updated_at_formatted: string;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Authors', href: index().url },
  { title: props.author.first_name + ' ' + props.author.last_name, href: show.url(props.author.id) },
];

</script>

<template>
  <Head :title="props.author.first_name + ' ' + props.author.last_name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-col gap-6 overflow-x-auto rounded-xl p-6">
      <div class="flex flex-col gap-4 rounded-xl border border-border/60 bg-muted/40 p-6 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <h1 class="text-3xl font-semibold tracking-tight">{{ props.author.first_name + ' ' + props.author.last_name }}</h1>
            <p class="text-sm text-muted-foreground">
              <span class="font-medium text-foreground">{{ props.author.first_name + ' ' + props.author.last_name }}</span>
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-3">
            <Button as-child variant="outline">
              <Link :href="edit.url(props.author.id)">Edit Author</Link>
            </Button>

            <Button as-child variant="ghost">
              <Link :href="index().url">Back to Authors</Link>
            </Button>
          </div>
        </div>

        <dl class="grid gap-4 rounded-lg border border-border/40 bg-background p-4 sm:grid-cols-3">
          <div>
            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Created</dt>
            <dd class="text-sm text-foreground">
              <div>{{ props.author.created_at_formatted }}</div>
              <div class="text-xs text-muted-foreground">{{ props.author.created_at }}</div>
            </dd>
          </div>

          <div>
            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Last updated</dt>
            <dd class="text-sm text-foreground">
              <div>{{ props.author.updated_at_formatted }}</div>
              <div class="text-xs text-muted-foreground">{{ props.author.updated_at }}</div>
            </dd>
          </div>

          <div>
            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Author ID</dt>
            <dd class="text-sm text-foreground">#{{ props.author.id }}</dd>
          </div>
        </dl>
      </div>

      <section class="rounded-xl border border-border/60 bg-background p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-foreground">Date of Birth</h2>
        <div class="prose max-w-none text-sm leading-relaxed text-foreground/90 dark:prose-invert">
          <p class="whitespace-pre-line">{{ props.author.date_of_birth }}</p>
        </div>
      </section>
    </div>
  </AppLayout>
</template>