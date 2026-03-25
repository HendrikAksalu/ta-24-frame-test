<script setup lang="ts">
import * as L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { onMounted, ref } from 'vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogDescription from './ui/dialog/DialogDescription.vue';
import DialogHeader from './ui/dialog/DialogHeader.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import { Input } from './ui/input';
import { Label } from './ui/label';
import { Textarea } from './ui/textarea';

const mapEl = ref<HTMLElement | null>(null);
const selectedLocation = ref<{ lat: number; lng: number }>();

const mapClick = (e: L.LeafletMouseEvent) => {
    selectedLocation.value = { lat: e.latlng.lat, lng: e.latlng.lng };
};

onMounted(() => {
    const map = L.map(mapEl.value!, {
        zoomControl: true,
    }).setView([59.4, 24.7], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    map.on('click', mapClick);
});
</script>

<template>
    <div ref="mapEl" class="z-10 h-full"></div>
    <Dialog :open="!!selectedLocation" @update:open="selectedLocation = undefined">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Salvesta uus marker</DialogTitle>
                <DialogDescription> Lisa nimi ja kirjeldus </DialogDescription>
                <form action="/" method="post">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <Label class="mb-1.5" for="name">Nimi</Label>
                            <Input id="name" name="name"></Input>
                        </div>
                    </div>

                    <div>
                        <Label class="mb-1.5" for="lat">Lat</Label>
                        <Input id="lat" name="lat" disabled :default-value="selectedLocation?.lat" />
                    </div>

                    <div>
                        <Label class="mb-1.5" for="lng">Lng</Label>
                        <Input id="lng" name="lng" disabled :default-value="selectedLocation?.lng" />
                    </div>
                    <Textarea name="description" class="col-span-2" />
                    <button class="col-span-2" type="submit">Salvesta</button>
                </form>
            </DialogHeader>
        </DialogContent>
    </Dialog>
</template>
