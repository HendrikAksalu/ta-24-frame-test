<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" :class="className" fill="none" v-bind="$attrs">
        <ellipse cx="20" cy="21" rx="17" ry="10.5" transform="rotate(-38 20 21)" stroke="currentColor" stroke-width="2.2" />
        <path d="M11 13.5l18 15M29 13.5 11 28.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M14.5 15h11M13 21h14M14.5 27h11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
    </svg>
</template>
