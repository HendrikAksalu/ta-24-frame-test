<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { login } from '@/routes';
import { create } from '@/routes/posts';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed, watch } from 'vue';

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

const page = usePage();

const props = withDefaults(
    defineProps<{
        posts: Paginated;
        authors?: Record<number, string>;
    }>(),
    {
        authors: () => ({}),
    },
);

const authorOptions = computed(() =>
    Object.entries(props.authors).map(([id, name]) => ({ id: Number(id), name: String(name) })),
);

const composeForm = useForm({
    title: '',
    description: '',
    author_id: null as number | null,
});

watch(
    authorOptions,
    (opts) => {
        if (composeForm.author_id !== null) {
            return;
        }
        if (opts.length === 1) {
            composeForm.author_id = opts[0].id;
        }
    },
    { immediate: true },
);

function submitCompose() {
    composeForm.post('/blog', {
        preserveScroll: true,
        onSuccess: () => {
            composeForm.reset('title', 'description');
            if (authorOptions.value.length === 1) {
                composeForm.author_id = authorOptions.value[0].id;
            }
        },
    });
}
</script>

<template>
    <Head title="NFL blogi" />

    <AppLayout :breadcrumbs="[]">
        <div class="mx-auto w-full max-w-3xl flex-1 px-4 py-6 md:px-6 md:py-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-50">NFL blogi</h1>
                    <p class="mt-2 max-w-xl text-neutral-600 dark:text-neutral-400">
                        Lühikesed artiklid National Football League’i, mängude ja fantasy näpunäidete teemal.
                    </p>
                </div>
                <Button v-if="page.props.auth?.user" as-child class="shrink-0 gap-2">
                    <Link :href="create().url">
                        <Plus class="size-4" />
                        Uus postitus
                    </Link>
                </Button>
            </div>

            <ul v-if="posts.data.length" class="space-y-6">
                <li v-for="post in posts.data" :key="post.id" class="rounded-xl border border-neutral-200/90 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
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
                    <Link :href="`/blog/${post.id}`" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                        Loe edasi
                    </Link>
                </li>
            </ul>
            <p v-else class="text-muted-foreground">
                Ühtegi avaldatud postitust pole. Loo esimene nupuga „Uus postitus“ või laadi NFL näidised käsuga
                <code class="rounded bg-muted px-1 py-0.5 text-xs">php artisan db:seed --class=NflBlogReseedSeeder</code>
                .
            </p>

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

            <section
                v-if="page.props.auth?.user"
                class="mt-12 rounded-xl border border-neutral-200/90 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
            >
                <h2 class="text-xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-50">Uus postitus</h2>
                <p class="mt-1 text-sm text-muted-foreground">Avalda otse blogisse — sama voog nagu eraldi loomislehel.</p>

                <form class="mt-6 space-y-4" @submit.prevent="submitCompose">
                    <div v-if="authorOptions.length !== 1" class="grid gap-2">
                        <Label for="compose-author">Autor</Label>
                        <select
                            id="compose-author"
                            v-model.number="composeForm.author_id"
                            class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 dark:bg-neutral-950"
                            required
                        >
                            <option disabled :value="null">Vali autor</option>
                            <option v-for="a in authorOptions" :key="a.id" :value="a.id">{{ a.name }}</option>
                        </select>
                        <InputError :message="composeForm.errors.author_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="compose-title">Pealkiri</Label>
                        <Input id="compose-title" v-model="composeForm.title" required autocomplete="off" />
                        <InputError :message="composeForm.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="compose-body">Sisu</Label>
                        <Textarea id="compose-body" v-model="composeForm.description" rows="8" required class="min-h-[180px]" />
                        <InputError :message="composeForm.errors.description" />
                    </div>

                    <div class="flex justify-start pt-2">
                        <Button
                            type="submit"
                            class="bg-violet-600 text-white hover:bg-violet-700 dark:bg-violet-600 dark:hover:bg-violet-500"
                            :disabled="composeForm.processing || authorOptions.length === 0"
                        >
                            Avalda postitus
                        </Button>
                    </div>

                    <p v-if="authorOptions.length === 0" class="text-sm text-amber-700 dark:text-amber-400">
                        Autoreid pole — käivita näidis:
                        <code class="rounded bg-muted px-1 py-0.5 text-xs">php artisan db:seed --class=AuthorSeeder</code>
                    </p>
                </form>
            </section>

            <section v-else class="mt-12 rounded-xl border border-neutral-200/90 bg-muted/40 p-6 dark:border-neutral-800 dark:bg-neutral-900/80">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">Uus postitus</h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Postitamiseks pead olema sisse loginud. See vorm on nähtav ainult autenditud kasutajale.
                </p>
                <Button as-child class="mt-4">
                    <Link :href="login().url">Logi sisse</Link>
                </Button>
            </section>
        </div>
    </AppLayout>
</template>
