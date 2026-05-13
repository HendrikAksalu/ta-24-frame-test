<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import * as L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogDescription from './ui/dialog/DialogDescription.vue';
import DialogHeader from './ui/dialog/DialogHeader.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';

export type ApiMarker = {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    description: string | null;
    added: string;
    edited: string | null;
};

const mapEl = ref<HTMLElement | null>(null);
const markers = ref<ApiMarker[]>([]);
const loadError = ref<string | null>(null);
const saving = ref(false);
const formError = ref<string | null>(null);

const createOpen = ref(false);
const editOpen = ref(false);
const createLatLng = ref<{ lat: number; lng: number } | null>(null);
const editingMarker = ref<ApiMarker | null>(null);

const createForm = ref({ name: '', description: '' });
const editForm = ref({ name: '', description: '', latitude: 0, longitude: 0 });

const page = usePage();
/** Avalik vaade: markerid laadivad; lisamine/muutmine ainult sisse loginud */
const canEditMarkers = computed(() => Boolean(page.props.auth?.user));

let map: L.Map | null = null;
let markersLayer: L.LayerGroup | null = null;
let suppressNextMapClick = false;

/** Meta token on first paint; prop updates on every Inertia visit (avoids stale token after login/regenerate). */
const csrfHeader = computed(() => {
    const fromProps = page.props.csrf_token;
    if (typeof fromProps === 'string' && fromProps.length > 0) {
        return fromProps;
    }
    return document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
});

function formatApiError(data: Record<string, unknown>): string {
    if (typeof data.message === 'string') {
        return data.message;
    }
    const errs = data.errors;
    if (errs && typeof errs === 'object') {
        return Object.values(errs)
            .flatMap((v) => (Array.isArray(v) ? v.map(String) : [String(v)]))
            .join(' ');
    }
    return 'Request failed';
}

async function api<T>(url: string, options: RequestInit = {}): Promise<T> {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfHeader.value,
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers as Record<string, string>),
    };
    if (options.body && !(options.body instanceof FormData) && !headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }
    const res = await fetch(url, { ...options, headers, credentials: 'same-origin' });
    if (res.status === 204) {
        return undefined as T;
    }
    const data = (await res.json().catch(() => ({}))) as Record<string, unknown>;
    if (!res.ok) {
        throw new Error(formatApiError(data) || `Error ${res.status}`);
    }
    return data as T;
}

async function loadMarkers() {
    loadError.value = null;
    try {
        markers.value = await api<ApiMarker[]>('/markers');
        renderMarkers();
    } catch (e) {
        loadError.value = e instanceof Error ? e.message : 'Load failed';
    }
}

function renderMarkers() {
    if (!map || !markersLayer) {
        return;
    }
    markersLayer.clearLayers();
    for (const m of markers.value) {
        const cm = L.circleMarker([m.latitude, m.longitude], {
            radius: 8,
            color: '#1d4ed8',
            weight: 2,
            fillColor: '#3b82f6',
            fillOpacity: 0.9,
        });
        const safeName = m.name.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const safeDesc = (m.description ?? '—').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        cm.bindPopup(`<strong>${safeName}</strong><br/>${safeDesc}`);
        cm.on('click', () => {
            suppressNextMapClick = true;
            if (canEditMarkers.value) {
                openEdit(m);
            } else {
                cm.openPopup();
            }
            setTimeout(() => {
                suppressNextMapClick = false;
            }, 100);
        });
        cm.addTo(markersLayer);
    }
    if (markers.value.length > 0 && map) {
        const b = L.latLngBounds(markers.value.map((m) => [m.latitude, m.longitude] as L.LatLngExpression));
        map.fitBounds(b, { padding: [48, 48], maxZoom: 12 });
    }
}

function onMapClick(e: L.LeafletMouseEvent) {
    if (suppressNextMapClick) {
        return;
    }
    if (!canEditMarkers.value) {
        return;
    }
    createLatLng.value = { lat: e.latlng.lat, lng: e.latlng.lng };
    createForm.value = { name: '', description: '' };
    formError.value = null;
    createOpen.value = true;
}

function openEdit(m: ApiMarker) {
    editingMarker.value = m;
    editForm.value = {
        name: m.name,
        description: m.description ?? '',
        latitude: m.latitude,
        longitude: m.longitude,
    };
    formError.value = null;
    editOpen.value = true;
}

function closeCreate() {
    createOpen.value = false;
    createLatLng.value = null;
    formError.value = null;
}

function closeEdit() {
    editOpen.value = false;
    editingMarker.value = null;
    formError.value = null;
}

async function submitCreate() {
    if (!createLatLng.value) {
        return;
    }
    saving.value = true;
    formError.value = null;
    try {
        await api('/markers', {
            method: 'POST',
            body: JSON.stringify({
                name: createForm.value.name,
                description: createForm.value.description.trim() || null,
                latitude: createLatLng.value.lat,
                longitude: createLatLng.value.lng,
            }),
        });
        closeCreate();
        await loadMarkers();
    } catch (e) {
        formError.value = e instanceof Error ? e.message : 'Save failed';
    } finally {
        saving.value = false;
    }
}

async function submitEdit() {
    if (!editingMarker.value) {
        return;
    }
    saving.value = true;
    formError.value = null;
    try {
        await api(`/markers/${editingMarker.value.id}`, {
            method: 'PUT',
            body: JSON.stringify({
                name: editForm.value.name,
                description: editForm.value.description.trim() || null,
                latitude: Number(editForm.value.latitude),
                longitude: Number(editForm.value.longitude),
            }),
        });
        closeEdit();
        await loadMarkers();
    } catch (e) {
        formError.value = e instanceof Error ? e.message : 'Save failed';
    } finally {
        saving.value = false;
    }
}

async function removeMarker(m: ApiMarker) {
    if (!confirm(`Kustuta marker "${m.name}"?`)) {
        return;
    }
    try {
        await api(`/markers/${m.id}`, { method: 'DELETE' });
        if (editingMarker.value?.id === m.id) {
            closeEdit();
        }
        await loadMarkers();
    } catch (e) {
        alert(e instanceof Error ? e.message : 'Delete failed');
    }
}

onMounted(() => {
    if (!mapEl.value) {
        return;
    }
    map = L.map(mapEl.value, { zoomControl: true }).setView([59.4, 24.7], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);
    markersLayer = L.layerGroup().addTo(map);
    map.on('click', onMapClick);
    loadMarkers();
});

onBeforeUnmount(() => {
    map?.remove();
    map = null;
    markersLayer = null;
});
</script>

<template>
    <div class="flex min-h-[420px] flex-col gap-4 md:h-[min(70vh,720px)] md:flex-row">
        <div ref="mapEl" class="z-10 min-h-[320px] flex-1 rounded-lg border border-sidebar-border/70 md:min-h-0 dark:border-sidebar-border" />

        <aside
            class="flex w-full flex-col rounded-lg border border-sidebar-border/70 bg-card p-4 md:w-72 dark:border-sidebar-border"
        >
            <h3 class="mb-2 text-sm font-semibold">Markerid</h3>
            <p v-if="canEditMarkers" class="mb-3 text-xs text-muted-foreground">Klõpsa kaardil, et lisada uus koht.</p>
            <p v-else class="mb-3 text-xs text-muted-foreground">Logi sisse, et lisada või muuta markereid. Markeritel klõpsates näed nime ja kirjeldust.</p>
            <p v-if="loadError" class="mb-2 text-xs text-destructive">{{ loadError }}</p>
            <ul class="max-h-64 space-y-2 overflow-y-auto text-sm md:max-h-none md:flex-1">
                <li v-if="!markers.length && !loadError" class="text-muted-foreground">Ühtegi markerit pole.</li>
                <li
                    v-for="m in markers"
                    :key="m.id"
                    class="rounded-md border border-border/80 p-2 transition hover:bg-muted/40"
                >
                    <div class="font-medium">{{ m.name }}</div>
                    <p class="mt-1 text-xs leading-snug text-muted-foreground">
                        {{ m.description?.trim() ? m.description : 'Kirjeldus puudub.' }}
                    </p>
                    <div class="mt-1 text-xs text-muted-foreground">
                        {{ m.latitude.toFixed(5) }}, {{ m.longitude.toFixed(5) }}
                    </div>
                    <div v-if="canEditMarkers" class="mt-2 flex gap-2">
                        <Button type="button" variant="secondary" size="sm" class="h-7 text-xs" @click="openEdit(m)">Muuda</Button>
                        <Button type="button" variant="destructive" size="sm" class="h-7 text-xs" @click="removeMarker(m)">Kustuta</Button>
                    </div>
                </li>
            </ul>
        </aside>
    </div>

    <Dialog :open="createOpen" @update:open="(v) => !v && closeCreate()">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Uus marker</DialogTitle>
                <DialogDescription>Sisesta nimi ja kirjeldus. Koordinaadid tulevad kaardil klõpsust.</DialogDescription>
            </DialogHeader>
            <div v-if="createLatLng" class="grid gap-3">
                <p v-if="formError" class="text-sm text-destructive">{{ formError }}</p>
                <div class="grid gap-2">
                    <Label for="m-name">Nimi</Label>
                    <Input id="m-name" v-model="createForm.name" required />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="m-lat">Laiuskraad</Label>
                        <Input id="m-lat" :model-value="String(createLatLng.lat)" disabled />
                    </div>
                    <div>
                        <Label for="m-lng">Pikkuskraad</Label>
                        <Input id="m-lng" :model-value="String(createLatLng.lng)" disabled />
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="m-desc">Kirjeldus</Label>
                    <Textarea id="m-desc" v-model="createForm.description" rows="3" />
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" variant="secondary" @click="closeCreate">Tühista</Button>
                    <Button type="button" :disabled="saving || !createForm.name.trim()" @click="submitCreate">Salvesta</Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>

    <Dialog :open="editOpen" @update:open="(v) => !v && closeEdit()">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Muuda markerit</DialogTitle>
                <DialogDescription>Uuenda nime, kirjeldust või koordinaate.</DialogDescription>
            </DialogHeader>
            <div v-if="editingMarker" class="grid gap-3">
                <p v-if="formError" class="text-sm text-destructive">{{ formError }}</p>
                <div class="grid gap-2">
                    <Label for="e-name">Nimi</Label>
                    <Input id="e-name" v-model="editForm.name" required />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="e-lat">Laiuskraad</Label>
                        <Input id="e-lat" v-model="editForm.latitude" type="number" step="any" />
                    </div>
                    <div>
                        <Label for="e-lng">Pikkuskraad</Label>
                        <Input id="e-lng" v-model="editForm.longitude" type="number" step="any" />
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="e-desc">Kirjeldus</Label>
                    <Textarea id="e-desc" v-model="editForm.description" rows="3" />
                </div>
                <p class="text-xs text-muted-foreground">
                    Lisatud: {{ new Date(editingMarker.added).toLocaleString() }}
                    <span v-if="editingMarker.edited"> · Muudetud: {{ new Date(editingMarker.edited).toLocaleString() }}</span>
                </p>
                <div class="flex justify-end gap-2">
                    <Button type="button" variant="secondary" @click="closeEdit">Sulge</Button>
                    <Button type="button" :disabled="saving || !editForm.name.trim()" @click="submitEdit">Salvesta</Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
