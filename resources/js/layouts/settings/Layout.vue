<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { appearance } from '@/routes';
import { edit as editPassword } from '@/routes/password';
import { edit } from '@/routes/profile';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen } from 'lucide-vue-next';

const page = usePage();
const currentPath = computed(() => page.url.split('?')[0]);

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profiil',
        href: edit(),
    },
    {
        title: 'Parool',
        href: editPassword(),
    },
    {
        title: 'Välimus',
        href: appearance(),
    },
];
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Sätted" description="Halda profiili, parooli ja kasutajaliidese välimust." />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-52">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': urlIsActive(item.href, currentPath) }]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                    <Separator class="my-2" />
                    <Button variant="ghost" class="w-full justify-start text-muted-foreground" as-child>
                        <Link href="/authors" class="flex w-full items-center gap-2">
                            <BookOpen class="size-4 shrink-0" />
                            Blogi autorid
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
