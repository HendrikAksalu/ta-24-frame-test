<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        authors?: Record<number, string>;
    }>(),
    {
        authors: () => ({}),
    },
);

const authorOptions = computed(() =>
    Object.entries(props.authors).map(([id, name]) => ({ id: Number(id), name: String(name) })),
);

const form = useForm({
    title: '',
    description: '',
    author_id: null as number | null,
});

watch(
    authorOptions,
    (opts) => {
        if (form.author_id !== null) {
            return;
        }
        if (opts.length === 1) {
            form.author_id = opts[0].id;
        }
    },
    { immediate: true },
);

function submit() {
    form.post('/blog');
}
</script>

<template>
    <Head title="Uus postitus" />

    <AppLayout :breadcrumbs="[]">
        <div class="mx-auto w-full max-w-3xl flex-1 px-4 py-6 md:px-6 md:py-8">
            <nav class="mb-6 text-sm">
                <Link href="/blog" class="text-indigo-600 hover:text-indigo-800 hover:underline dark:text-indigo-400">← Tagasi blogisse</Link>
            </nav>

            <div class="rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 md:p-8">
                <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-50">Uus postitus</h1>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div v-if="authorOptions.length !== 1" class="grid gap-2">
                        <Label for="create-author">Autor</Label>
                        <select
                            id="create-author"
                            v-model.number="form.author_id"
                            class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 dark:bg-neutral-950"
                            required
                        >
                            <option disabled :value="null">Vali autor</option>
                            <option v-for="a in authorOptions" :key="a.id" :value="a.id">{{ a.name }}</option>
                        </select>
                        <InputError :message="form.errors.author_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-title">Pealkiri</Label>
                        <Input id="create-title" v-model="form.title" required autocomplete="off" />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-body">Sisu</Label>
                        <Textarea id="create-body" v-model="form.description" rows="10" required class="min-h-[200px]" />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="flex justify-start pt-2">
                        <Button
                            type="submit"
                            class="bg-violet-600 text-white hover:bg-violet-700 dark:bg-violet-600 dark:hover:bg-violet-500"
                            :disabled="form.processing || authorOptions.length === 0"
                        >
                            Avalda postitus
                        </Button>
                    </div>

                    <p v-if="authorOptions.length === 0" class="text-sm text-amber-700 dark:text-amber-400">
                        Autoreid pole — käivita näidis:
                        <code class="rounded bg-muted px-1 py-0.5 text-xs">php artisan db:seed --class=AuthorSeeder</code>
                    </p>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
