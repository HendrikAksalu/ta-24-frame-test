<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => {
    const raw = String((page.props.name as string | undefined) ?? '').trim();
    if (raw === '' || raw === 'Laravel') {
        return 'Ülesanded';
    }
    return raw;
});
</script>

<template>
    <div class="flex items-center">
        <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
            <AppLogoIcon class="size-5 text-white dark:text-black" />
        </div>
        <div class="ml-1.5 grid max-w-[10rem] flex-1 text-left text-sm leading-tight sm:max-w-xs">
            <span class="truncate font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">{{ appName }}</span>
        </div>
    </div>
</template>
