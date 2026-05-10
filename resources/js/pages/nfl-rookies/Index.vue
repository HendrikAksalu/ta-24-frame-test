<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Star, User, Users } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

type Source = 'mine' | 'friend';

type NflRookie = {
    id: number | string;
    title: string;
    image: string | null;
    description: string;
    team: string;
    position: string;
    draft_round: number;
    season_year: number;
    rating?: string;
};

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

const sortField = ref<'created_at' | 'title' | 'team' | 'position' | 'draft_round' | 'season_year'>('created_at');
const direction = ref<'asc' | 'desc'>('desc');
const limit = ref(24);
const seasonYear = ref(2026);
const search = ref('');
/** Valitud filter: minu API-s positsioon, sõbra API-s žanr */
const genre = ref('Kõik');

const sortLabels: Record<string, string> = {
    created_at: 'Lisamise aeg',
    title: 'Nimi',
    team: 'Meeskond',
    position: 'Positsioon',
    draft_round: 'Draft ring',
    season_year: 'Hooaasta',
};

const friendSortMap: Record<string, string> = {
    created_at: 'created_at',
    title: 'title',
    team: 'director',
    position: 'genre',
    draft_round: 'rating',
    season_year: 'release_year',
};

const source = ref<Source>('mine');

const subjects = ref<NflRookie[]>([]);
const error = ref<string | null>(null);
const loading = ref(false);

const positionPresets = ['QB', 'RB', 'WR', 'TE', 'OT', 'IOL', 'EDGE', 'DL', 'LB', 'CB', 'S'];
const movieGenrePresets = ['Sci-Fi', 'Action', 'Thriller', 'Drama', 'Animation', 'Fantasy', 'Crime'];

const filterLabel = computed(() => (source.value === 'friend' ? 'Žanr' : 'Positsioon'));

const genrePresetsActive = computed(() => (source.value === 'friend' ? movieGenrePresets : positionPresets));

/** Kõik → ainult positsioonide / ainult žanrite loend (presetid järjekorras, siis API-st tulnud täiendused) */
const genreOptions = computed(() => {
    const fromData = new Set<string>();
    for (const s of subjects.value) {
        const v = s.position?.trim();
        if (v) {
            fromData.add(v);
        }
    }
    const presetList = genrePresetsActive.value;
    const seen = new Set<string>();
    const opts = ['Kõik'];
    for (const p of presetList) {
        if (!seen.has(p)) {
            opts.push(p);
            seen.add(p);
        }
    }
    for (const x of Array.from(fromData).sort((a, b) => a.localeCompare(b, 'et'))) {
        if (!seen.has(x)) {
            opts.push(x);
            seen.add(x);
        }
    }
    return opts;
});

const searchPlaceholder = computed(() =>
    source.value === 'friend' ? 'Otsi pealkirja järgi...' : 'Otsi nime järgi...',
);

/** Õppe ülesandes kasutatav sõbra avalik endpoint — sama näidata kui päris päringu allikat */
const friendPublicApiUrl = 'https://raamistikud.ta24armus.itmajakas.ee/api/my-favorite-subjects';

function buildMineQuery(): string {
    const params = new URLSearchParams();
    if (search.value.trim() !== '') {
        params.set('search', search.value.trim());
    }
    if (genre.value !== 'Kõik') {
        params.set('position', genre.value);
    }
    params.set('sort', sortField.value);
    params.set('direction', direction.value);
    params.set('limit', String(limit.value));
    params.set('season_year', String(seasonYear.value));
    return params.toString();
}

function buildFriendQuery(): string {
    const params = new URLSearchParams();
    if (search.value.trim() !== '') {
        params.set('search', search.value.trim());
    }
    if (genre.value !== 'Kõik') {
        params.set('genre', genre.value);
    }
    params.set('sort', friendSortMap[sortField.value] ?? 'created_at');
    params.set('direction', direction.value);
    params.set('limit', String(limit.value));
    return params.toString();
}

/** Sõbra avaliku API (`my-favorite-subjects`) rida → sama kaardivorming mis proxy normeeris */
function normalizeFriendSubjectRow(row: Record<string, unknown>, idx: number): NflRookie {
    const rawId = row.id;
    const fid = rawId !== undefined && rawId !== null ? rawId : idx + 1;
    const idStr = `friend-${fid}`;

    const hasMovieShape = row.director !== undefined || row.release_year !== undefined;

    if (hasMovieShape) {
        const ratingRaw = row.rating;
        let ratingNum: number | null = null;
        if (typeof ratingRaw === 'number' && !Number.isNaN(ratingRaw)) {
            ratingNum = ratingRaw;
        } else if (typeof ratingRaw === 'string' && ratingRaw !== '' && !Number.isNaN(Number(ratingRaw))) {
            ratingNum = Number(ratingRaw);
        }

        let ratingStr: string;
        if (ratingNum !== null) {
            ratingStr = ratingNum.toFixed(1);
        } else if (ratingRaw !== undefined && ratingRaw !== null && String(ratingRaw) !== '') {
            ratingStr = String(ratingRaw);
        } else {
            ratingStr = (7.4 + (idx % 6) * 0.15).toFixed(1);
        }

        const ry = row.release_year;
        const seasonYearVal = typeof ry === 'number' ? ry : new Date().getFullYear();

        const draftRound =
            typeof row.draft_round === 'number'
                ? row.draft_round
                : ratingNum !== null
                  ? Math.max(1, Math.min(7, Math.round(12 - ratingNum)))
                  : 3;

        return {
            id: idStr,
            title: String(row.title ?? ''),
            team: String(row.director ?? ''),
            position: String(row.genre ?? ''),
            season_year: seasonYearVal,
            draft_round: draftRound,
            image: typeof row.image === 'string' ? row.image : null,
            description: String(row.description ?? ''),
            rating: ratingStr,
        };
    }

    const draftRound = typeof row.draft_round === 'number' ? row.draft_round : 4;
    const ratingRaw = row.rating;
    let ratingStr: string;
    if (ratingRaw !== undefined && ratingRaw !== null && String(ratingRaw) !== '') {
        ratingStr = typeof ratingRaw === 'number' ? ratingRaw.toFixed(1) : String(ratingRaw);
    } else {
        ratingStr = Math.max(6.0, Math.min(9.9, 9.2 - (draftRound - 1) * 0.42)).toFixed(1);
    }

    const sy = row.season_year;
    return {
        id: idStr,
        title: String(row.title ?? ''),
        team: String(row.team ?? ''),
        position: String(row.position ?? ''),
        season_year: typeof sy === 'number' ? sy : new Date().getFullYear(),
        draft_round: draftRound,
        image: typeof row.image === 'string' ? row.image : null,
        description: String(row.description ?? ''),
        rating: ratingStr,
    };
}

async function load() {
    loading.value = true;
    error.value = null;

    try {
        if (source.value === 'mine') {
            const endpoint = `/api/rookies?${buildMineQuery()}`;
            const res = await api<{ success: boolean; data: NflRookie[]; message?: string }>(endpoint);
            subjects.value = Array.isArray(res.data) ? res.data : [];
            if (res.success === false && subjects.value.length === 0) {
                error.value = 'Andmeid ei laaditud.';
            }
        } else {
            const url = `${friendPublicApiUrl}?${buildFriendQuery()}`;
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            const payload = (await res.json().catch(() => ({}))) as { data?: unknown; message?: string };
            if (!res.ok) {
                throw new Error(typeof payload.message === 'string' ? payload.message : 'Sõbra API vastust ei saadud.');
            }
            const rows = payload.data;
            const list = Array.isArray(rows) ? rows : [];
            subjects.value = list.map((r, i) =>
                normalizeFriendSubjectRow(r !== null && typeof r === 'object' ? (r as Record<string, unknown>) : {}, i),
            );
        }
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Failed to load';
        subjects.value = [];
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    load();
});

watch(source, () => {
    genre.value = 'Kõik';
    load();
});

function resetFilters() {
    search.value = '';
    genre.value = 'Kõik';
    sortField.value = 'created_at';
    direction.value = 'desc';
    limit.value = 24;
    load();
}

function displayRating(s: NflRookie): string {
    if (s.rating !== undefined && s.rating !== '') {
        return String(s.rating);
    }
    const v = 9.2 - (s.draft_round - 1) * 0.42;

    return Math.max(6, Math.min(9.9, v)).toFixed(1);
}

function canManage(s: NflRookie): boolean {
    return source.value === 'mine' && typeof s.id === 'number';
}

async function removeSubject(s: NflRookie) {
    if (!canManage(s)) {
        return;
    }
    const ok = confirm(`Kustuta: ${s.title}?`);
    if (!ok) {
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        await api<unknown>(`/api/rookies/${s.id}`, { method: 'DELETE' });
        await load();
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Delete failed';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="NFL'i rookied" />

    <AppLayout>
        <div class="min-h-[calc(100vh-4rem)] bg-neutral-50 pb-10 pt-6 dark:bg-neutral-950">
            <div class="mx-auto max-w-6xl space-y-6 px-4 md:px-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <h1 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">NFL'i rookied</h1>
                            <a
                                :href="source === 'mine' ? '/api/rookies' : friendPublicApiUrl"
                                target="_blank"
                                rel="noreferrer"
                                class="text-sm font-medium text-violet-600 hover:underline dark:text-violet-400"
                            >
                                API →
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Allikas:</span>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-medium transition"
                                :class="
                                    source === 'mine'
                                        ? 'bg-violet-600 text-white shadow-sm dark:bg-violet-500'
                                        : 'border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200'
                                "
                                @click="source = 'mine'"
                            >
                                <User class="size-4 shrink-0" aria-hidden="true" />
                                Minu rookied
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-medium transition"
                                :class="
                                    source === 'friend'
                                        ? 'bg-violet-600 text-white shadow-sm dark:bg-violet-500'
                                        : 'border border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200'
                                "
                                @click="source = 'friend'"
                            >
                                <Users class="size-4 shrink-0" aria-hidden="true" />
                                Sõbra filmid
                            </button>
                            <code
                                class="ml-1 inline-block max-w-full break-all rounded bg-neutral-200/80 px-2 py-0.5 text-left text-xs text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 sm:max-w-xl"
                            >
                                <template v-if="source === 'mine'">/api/rookies</template>
                                <template v-else>{{ friendPublicApiUrl }}</template>
                            </code>
                        </div>

                    </div>
                </div>

                <section class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-12 lg:items-end">
                        <div class="grid gap-2 lg:col-span-4">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Otsi</label>
                            <input
                                v-model="search"
                                class="rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none ring-violet-500/30 focus:ring-2 dark:border-neutral-700 dark:bg-neutral-950"
                                :placeholder="searchPlaceholder"
                                @keydown.enter.prevent="load"
                            />
                        </div>

                        <div class="grid gap-2 lg:col-span-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ filterLabel }}</label>
                            <select
                                v-model="genre"
                                class="rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none ring-violet-500/30 focus:ring-2 dark:border-neutral-700 dark:bg-neutral-950"
                            >
                                <option v-for="g in genreOptions" :key="g" :value="g">{{ g }}</option>
                            </select>
                        </div>

                        <div class="grid gap-2 lg:col-span-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Sorteeri</label>
                            <select
                                v-model="sortField"
                                class="rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none ring-violet-500/30 focus:ring-2 dark:border-neutral-700 dark:bg-neutral-950"
                            >
                                <option v-for="(label, key) in sortLabels" :key="key" :value="key">{{ label }}</option>
                            </select>
                        </div>

                        <div class="grid gap-2 lg:col-span-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Suund</label>
                            <select
                                v-model="direction"
                                class="rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none ring-violet-500/30 focus:ring-2 dark:border-neutral-700 dark:bg-neutral-950"
                            >
                                <option value="desc">Kahanev</option>
                                <option value="asc">Kasvav</option>
                            </select>
                        </div>

                        <div class="flex flex-wrap gap-2 lg:col-span-2 lg:justify-end">
                            <button
                                type="button"
                                class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-violet-700 disabled:opacity-60 dark:bg-violet-500 dark:hover:bg-violet-600"
                                :disabled="loading"
                                @click="load"
                            >
                                Otsi
                            </button>
                            <button
                                type="button"
                                class="rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-2 text-sm font-medium text-neutral-800 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:bg-neutral-700"
                                :disabled="loading"
                                @click="resetFilters"
                            >
                                Tühjenda
                            </button>
                        </div>
                    </div>
                </section>

                <div v-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200">
                    {{ error }}
                </div>

                <div v-if="loading" class="text-sm text-neutral-500 dark:text-neutral-400">Laen...</div>

                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="s in subjects"
                        :key="String(s.id)"
                        class="flex flex-col overflow-hidden rounded-2xl border border-neutral-200/90 bg-white shadow-[0_2px_12px_rgba(0,0,0,0.06)] dark:border-neutral-800 dark:bg-neutral-900 dark:shadow-none"
                    >
                        <img
                            :src="s.image || 'https://placehold.co/600x400/png?text=Rookie'"
                            class="aspect-video w-full object-cover"
                            :alt="s.title"
                        />
                        <div class="flex flex-1 flex-col gap-2 p-4">
                            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-50">{{ s.title }}</h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ s.team }} · {{ s.season_year }}</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-semibold text-violet-800 dark:bg-violet-950 dark:text-violet-200"
                                >
                                    {{ s.position }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-neutral-800 dark:text-neutral-100">
                                    <Star class="size-4 fill-amber-400 text-amber-400" aria-hidden="true" />
                                    {{ displayRating(s) }}/10
                                </span>
                            </div>
                            <p class="line-clamp-3 flex-1 text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">
                                {{ s.description }}
                            </p>
                            <button
                                v-if="canManage(s)"
                                type="button"
                                class="mt-2 self-end text-sm font-medium text-red-600 hover:underline dark:text-red-400"
                                @click="removeSubject(s)"
                            >
                                Kustuta
                            </button>
                        </div>
                    </article>
                </div>

                <section class="rounded-2xl border border-neutral-200/90 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h2 class="text-xl font-bold text-neutral-900 dark:text-neutral-50">API dokumentatsioon</h2>

                    <div class="mt-5 space-y-8 text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">
                        <div>
                            <p class="mb-2 rounded bg-sky-100 px-2 py-1 font-mono text-xs font-semibold text-sky-900 dark:bg-sky-950 dark:text-sky-100">
                                GET /api/rookies
                            </p>
                            <p class="mb-3">Tagastab kõik rookie'd. Toetab järgmisi parameetreid:</p>
                            <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <table class="w-full min-w-[28rem] border-collapse text-left text-xs">
                                    <thead class="bg-neutral-100 dark:bg-neutral-800/80">
                                        <tr>
                                            <th class="border-b border-neutral-200 px-3 py-2 font-semibold text-neutral-900 dark:border-neutral-700 dark:text-neutral-100">
                                                Parameeter
                                            </th>
                                            <th class="border-b border-neutral-200 px-3 py-2 font-semibold text-neutral-900 dark:border-neutral-700 dark:text-neutral-100">
                                                Kirjeldus
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-neutral-700 dark:text-neutral-300">
                                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">search</td>
                                            <td class="px-3 py-2">otsing nime järgi</td>
                                        </tr>
                                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">position</td>
                                            <td class="px-3 py-2">
                                                filtreeri positsiooni järgi (QB, RB, WR, TE, OT, DE, CB, S, ILB)
                                            </td>
                                        </tr>
                                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">team</td>
                                            <td class="px-3 py-2">filtreeri meeskonna järgi</td>
                                        </tr>
                                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">draft_round</td>
                                            <td class="px-3 py-2">filtreeri drafti ringi järgi (1–7)</td>
                                        </tr>
                                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">season_year</td>
                                            <td class="px-3 py-2">filtreeri hooaja järgi</td>
                                        </tr>
                                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">sort</td>
                                            <td class="px-3 py-2">
                                                sorteeri (title, rating, draft_round, season_year, created_at)
                                            </td>
                                        </tr>
                                        <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">direction</td>
                                            <td class="px-3 py-2">suund (asc, desc)</td>
                                        </tr>
                                        <tr>
                                            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]">limit</td>
                                            <td class="px-3 py-2">piira tagastatavate kirjete arvu</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-4 mb-2 rounded bg-sky-100 px-2 py-1 font-mono text-xs font-semibold text-sky-900 dark:bg-sky-950 dark:text-sky-100">
                                GET /api/rookies/{id}
                            </p>
                            <p class="mb-6">Tagastab ühe rookie andmed.</p>

                            <p class="mb-2 rounded bg-sky-100 px-2 py-1 font-mono text-xs font-semibold text-sky-900 dark:bg-sky-950 dark:text-sky-100 break-all">
                                GET {{ friendPublicApiUrl }}
                            </p>
                            <p class="mb-6">Sõbra API — välise allikaga integratsioon.</p>

                            <p class="mb-2 font-semibold text-neutral-900 dark:text-neutral-50">Näidispäringud</p>
                            <pre class="overflow-x-auto rounded-lg bg-neutral-100 p-3 text-xs dark:bg-neutral-950"><code>GET /api/rookies?search=mason&amp;sort=rating&amp;direction=desc&amp;limit=5
GET /api/rookies?position=QB&amp;draft_round=1</code></pre>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
