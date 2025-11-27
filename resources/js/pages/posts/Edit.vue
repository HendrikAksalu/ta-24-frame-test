<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { index } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';


const props = defineProps<{
    post: {
        id: number;
        title: string;
        content: string;
        author_id: number | null;
        published: boolean;
        created_at?: string;
        updated_at?: string;
    };
    authors: Record<number, string>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Posts', href: index().url },
    { title: `Edit Post #${props.post.id}`, href: `/posts/${props.post.id}/edit` },
];

const form = useForm({
    title: props.post.title,
    content: props.post.content,
    author_id: props.post.author_id ?? '',
    published: props.post.published,
});

const submit = () => {
    form.put(`/posts/${props.post.id}`, {
        preserveScroll: true,
        onSuccess: () => console.log('Post updated!'),
    });
};
</script>

<template>
    <Head :title="`Edit Post #${props.post.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6 flex flex-col gap-6">
            <h1 class="text-2xl font-semibold">Edit Post</h1>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" />
                    <p v-if="form.errors.title" class="text-red-600 text-sm">
                        {{ form.errors.title }}
                    </p>
                </div>

                <div>
                    <Label for="content">Content</Label>
                    <Textarea id="content" rows="6" v-model="form.content" />
                    <p v-if="form.errors.content" class="text-red-600 text-sm">
                        {{ form.errors.content }}
                    </p>
                </div>

                <div>
                    <Label for="author">Author</Label>
                    <select id="author" class="mt-1 w-full rounded border p-2" v-model="form.author_id">
                        <option value="" disabled>Select author</option>
                        <option v-for="(name, id) in props.authors" :key="id" :value="id">{{ name }}</option>
                    </select>
                    <p v-if="form.errors.author_id" class="text-red-600 text-sm">
                        {{ form.errors.author_id }}
                    </p>
                </div>

                <div class="flex items-center justify-between">
                    <Switch v-model:="form.published" id="published" />
                    <Label for="published">Published</Label>
                </div>

                <div class="text-sm text-gray-500 mt-2">
                    <p>Created at: {{ props.post.created_at }}</p>
                    <p>Last updated: {{ props.post.updated_at }}</p>
                </div>

                <div class="flex gap-3 mt-4">
                    <Button type="submit" :disabled="form.processing">
                        Save Changes
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit(index().url)"
                    >
                        Cancel
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>





