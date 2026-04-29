<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

type NflRookie = {
    id: number;
    title: string;
    image: string | null;
    description: string;
    team: string;
    position: string;
    draft_round: number;
    season_year: number;
};

const page = usePage();
const user = computed(() => page.props.auth?.user);

function csrf(): string {
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
}

async function api<T>(url: string, options: RequestInit = {}): Promise<T> {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf(),
        ...(options.headers as Record<string, string>),
    };

    const res = await fetch(url, {
        ...options,
        headers,
        credentials: 'same-origin',
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const msg = typeof data?.message === 'string' ? data.message : 'Request failed';
        throw new Error(msg);
    }
    return data as T;
}

const subjects = ref<NflRookie[]>([]);
const error = ref<string | null>(null);
const loading = ref(false);

const search = ref('');
const team = ref('');
const position = ref('');

const sortBy = ref<'draft_round' | 'title' | 'team' | 'position' | 'season_year'>('draft_round');
const direction = ref<'asc' | 'desc'>('asc');
const limit = ref<number>(10);

const effectiveSeasonYear = computed(() => 2026);

const draft = ref({
    title: '',
    image: '',
    description: '',
    team: '',
    position: '',
    draft_round: 1,
});

function buildQuery() {
    const params = new URLSearchParams();
    if (search.value.trim() !== '') params.set('search', search.value.trim());
    if (team.value.trim() !== '') params.set('team', team.value.trim());
    if (position.value.trim() !== '') params.set('position', position.value.trim());
    params.set('sort_by', sortBy.value);
    params.set('direction', direction.value);
    params.set('limit', String(limit.value));
    params.set('season_year', String(effectiveSeasonYear.value));
    return params.toString();
}

async function load() {
    loading.value = true;
    error.value = null;

    try {
        const query = buildQuery();
        const res = await api<{ success: boolean; data: NflRookie[]; count: number }>(`/api/nfl-rookies?${query}`);
        subjects.value = res.data;
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Failed to load';
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    load();
});

async function submitCreate() {
    if (!user.value) {
        error.value = 'Logi sisse, et lisada sisu.';
        return;
    }

    error.value = null;
    loading.value = true;

    try {
        await api<{ success: boolean; data: NflRookie }>('/api/nfl-rookies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                title: draft.value.title,
                image: draft.value.image,
                description: draft.value.description,
                team: draft.value.team,
                position: draft.value.position,
                draft_round: draft.value.draft_round,
                season_year: effectiveSeasonYear.value,
            }),
        });

        // simple reset
        draft.value = {
            title: '',
            image: '',
            description: '',
            team: '',
            position: '',
            draft_round: 1,
        };

        await load();
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Create failed';
    } finally {
        loading.value = false;
    }
}

async function removeSubject(s: NflRookie) {
    if (!user.value) return;
    const ok = confirm(`Kustuta: ${s.title}?`);
    if (!ok) return;

    loading.value = true;
    error.value = null;

    try {
        await api<unknown>(`/api/nfl-rookies/${s.id}`, { method: 'DELETE' });
        await load();
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Delete failed';
    } finally {
        loading.value = false;
    }
}

function resetFilters() {
    search.value = '';
    team.value = '';
    position.value = '';
    sortBy.value = 'draft_round';
    direction.value = 'asc';
    limit.value = 10;
    load();
}
</script>

<template>
    <Head title="NFL rookied" />

    <AppLayout>
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">NFL rookied</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Sisu: hooaja {{ effectiveSeasonYear }}</p>
                </div>

                <button type="button" class="rounded-md border border-border/80 bg-card px-4 py-2 text-sm hover:bg-muted/30" @click="resetFilters">
                    Reset filtrid
                </button>
            </div>

            <section class="rounded-xl border border-border/80 bg-card p-5">
                <h2 class="mb-4 text-lg font-semibold">Otsing ja filtrid</h2>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Otsi (title)</label>
                        <input v-model="search" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" placeholder="Nt Mason" />
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Team</label>
                        <input v-model="team" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" placeholder="Nt Colts" />
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Position</label>
                        <input v-model="position" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" placeholder="Nt QB" />
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Limit</label>
                        <input v-model.number="limit" type="number" min="1" max="100" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Sorteeri</label>
                        <select v-model="sortBy" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm">
                            <option value="draft_round">draft_round</option>
                            <option value="title">title</option>
                            <option value="team">team</option>
                            <option value="position">position</option>
                            <option value="season_year">season_year</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Suund</label>
                        <select v-model="direction" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm">
                            <option value="asc">kasvav</option>
                            <option value="desc">kahanev</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button
                            type="button"
                            class="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                            :disabled="loading"
                            @click="load"
                        >
                            Otsi
                        </button>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-border/80 bg-card p-5">
                <h2 class="mb-4 text-lg font-semibold">
                    Leitud: {{ subjects.length }}
                </h2>

                <div v-if="error" class="mb-4 rounded-md border border-destructive/40 bg-destructive/10 p-3 text-sm text-destructive">
                    {{ error }}
                </div>

                <div v-if="loading" class="text-sm text-muted-foreground">Laen uuringuid...</div>

                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="s in subjects"
                        :key="s.id"
                        class="rounded-xl border border-border/80 bg-card p-4"
                    >
                        <img :src="s.image || 'https://placehold.co/600x400/png?text=Rookie'" class="aspect-video w-full rounded-lg object-cover" :alt="s.title" />
                        <h3 class="mt-3 text-lg font-semibold">{{ s.title }}</h3>
                        <div class="mt-1 text-sm text-muted-foreground">
                            {{ s.team }} · {{ s.position }}
                        </div>
                        <div class="mt-2 text-sm">
                            draft_round: <span class="font-medium">{{ s.draft_round }}</span>
                        </div>
                        <p class="mt-2 line-clamp-3 text-sm text-foreground/90">{{ s.description }}</p>

                        <div v-if="user" class="mt-4 flex justify-end">
                            <button type="button" class="text-sm font-medium text-destructive hover:underline" @click="removeSubject(s)">
                                Kustuta
                            </button>
                        </div>
                    </article>
                </div>
            </section>

            <section v-if="user" class="rounded-xl border border-border/80 bg-card p-5">
                <h2 class="mb-4 text-lg font-semibold">+ Lisa uus rookie</h2>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="grid gap-2 sm:col-span-2">
                        <label class="text-sm text-muted-foreground">Title</label>
                        <input v-model="draft.title" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                    </div>

                    <div class="grid gap-2 sm:col-span-2">
                        <label class="text-sm text-muted-foreground">Image URL</label>
                        <input v-model="draft.image" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" placeholder="https://..." />
                    </div>

                    <div class="grid gap-2 sm:col-span-2">
                        <label class="text-sm text-muted-foreground">Description</label>
                        <textarea v-model="draft.description" rows="4" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Team</label>
                        <input v-model="draft.team" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">Position</label>
                        <input v-model="draft.position" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                    </div>

                    <div class="grid gap-2">
                        <label class="text-sm text-muted-foreground">draft_round</label>
                        <input v-model.number="draft.draft_round" type="number" min="1" class="rounded-md border border-input bg-transparent px-3 py-2 text-sm" />
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                        :disabled="loading || !draft.title.trim() || !draft.description.trim()"
                        @click="submitCreate"
                    >
                        Lisa
                    </button>
                </div>
            </section>

            <section v-else class="rounded-xl border border-border/80 bg-card p-5 text-sm text-muted-foreground">
                Logi sisse, et saaksid lisada ja kustutada.
            </section>
        </div>
    </AppLayout>
</template>

