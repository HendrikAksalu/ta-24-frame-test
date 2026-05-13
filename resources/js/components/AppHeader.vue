<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { NavigationMenu, NavigationMenuItem, NavigationMenuList, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu';
import { Separator } from '@/components/ui/separator';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { getInitials } from '@/composables/useInitials';
import { toUrl, urlIsActive } from '@/lib/utils';
import { appearance, dashboard, login, logout, register } from '@/routes';
import { edit as editPassword } from '@/routes/password';
import { edit as editProfile } from '@/routes/profile';
import type { BreadcrumbItem, NavItem } from '@/types';
import { InertiaLinkProps, Link, router, usePage } from '@inertiajs/vue3';
import FootballIcon from '@/components/icons/FootballIcon.vue';
import {
    CloudSun,
    KeyRound,
    LayoutGrid,
    LogOut,
    Map,
    Menu,
    Newspaper,
    Palette,
    ShoppingCart,
    User,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

const isCurrentRoute = computed(() => (url: NonNullable<InertiaLinkProps['href']>) => urlIsActive(url, page.url));

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(toUrl(url)) ? 'font-semibold text-neutral-900 dark:text-neutral-100' : 'text-neutral-600 dark:text-neutral-400',
);

const underlineActive = computed(() => (url: NonNullable<InertiaLinkProps['href']>) => isCurrentRoute.value(toUrl(url)));

const mainNavItems: NavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Ilm', href: '/ilm', icon: CloudSun },
    { title: 'Kaart', href: '/kaart', icon: Map },
    { title: 'Blogi', href: '/blog', icon: Newspaper },
    { title: 'Pood', href: '/shop', icon: ShoppingCart },
    { title: "NFL'i rookied", href: '/nfl-rookies', icon: FootballIcon },
];

const settingsMobileItems: NavItem[] = [
    { title: 'Profiil', href: editProfile(), icon: User },
    { title: 'Parool', href: editPassword(), icon: KeyRound },
    { title: 'Välimus', href: appearance(), icon: Palette },
];

const handleLogoutFlush = () => {
    router.flushAll();
};
</script>

<template>
    <div class="border-b border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950">
        <div class="mx-auto flex h-14 items-center px-4 md:h-16 md:max-w-7xl md:px-6">
            <div class="lg:hidden">
                <Sheet>
                    <SheetTrigger :as-child="true">
                        <Button variant="ghost" size="icon" class="mr-2 h-9 w-9 shrink-0">
                            <Menu class="h-5 w-5" />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="flex w-[280px] flex-col p-6">
                        <SheetTitle class="sr-only">Menüü</SheetTitle>
                        <SheetHeader class="flex justify-start text-left">
                            <Link :href="auth.user ? dashboard().url : '/'" class="inline-flex items-center">
                                <AppLogo />
                                <span class="sr-only">{{ auth.user ? 'Töölaud' : 'Avaleht' }}</span>
                            </Link>
                        </SheetHeader>
                        <nav class="mt-6 flex flex-1 flex-col gap-1 overflow-y-auto">
                            <Link
                                v-for="item in mainNavItems"
                                :key="item.title"
                                :href="item.href"
                                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-accent"
                                :class="activeItemStyles(item.href)"
                            >
                                <component :is="item.icon" class="h-5 w-5 shrink-0" />
                                {{ item.title }}
                            </Link>

                            <template v-if="auth.user">
                                <Separator class="my-3" />

                                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Sätted</p>
                                <Link
                                    v-for="item in settingsMobileItems"
                                    :key="`st-${item.title}`"
                                    :href="item.href"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-accent"
                                    :class="activeItemStyles(item.href)"
                                >
                                    <component :is="item.icon" class="h-5 w-5 shrink-0" />
                                    {{ item.title }}
                                </Link>

                                <Separator class="my-3" />

                                <Link
                                    :href="logout()"
                                    method="post"
                                    as="button"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                                    @click="handleLogoutFlush"
                                >
                                    <LogOut class="h-5 w-5 shrink-0" />
                                    Logi välja
                                </Link>
                            </template>
                            <template v-else>
                                <Separator class="my-3" />
                                <Link
                                    :href="login().url"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-accent"
                                >
                                    Logi sisse
                                </Link>
                                <Link
                                    :href="register().url"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-accent"
                                >
                                    Registreeri
                                </Link>
                            </template>
                        </nav>
                    </SheetContent>
                </Sheet>
            </div>

            <Link :href="auth.user ? dashboard().url : '/'" class="flex shrink-0 items-center gap-x-2">
                <AppLogo />
            </Link>

            <div class="hidden min-w-0 flex-1 lg:ml-8 lg:flex lg:items-center lg:justify-center">
                <NavigationMenu class="flex max-w-full justify-center">
                    <NavigationMenuList class="flex flex-wrap items-center gap-x-1 gap-y-1">
                        <NavigationMenuItem v-for="(item, index) in mainNavItems" :key="index" class="relative flex items-center">
                            <Link
                                :class="[navigationMenuTriggerStyle(), activeItemStyles(item.href), 'h-9 cursor-pointer whitespace-nowrap px-2.5 text-sm']"
                                :href="item.href"
                            >
                                {{ item.title }}
                            </Link>
                            <div
                                v-if="underlineActive(item.href)"
                                class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px rounded-full bg-neutral-900 dark:bg-white"
                            />
                        </NavigationMenuItem>
                    </NavigationMenuList>
                </NavigationMenu>
            </div>

            <div class="ml-auto flex min-w-0 shrink-0 items-center gap-2 pl-2">
                <template v-if="auth.user">
                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button variant="ghost" class="h-auto max-w-[14rem] gap-2 rounded-full px-2 py-1.5 hover:bg-neutral-100 dark:hover:bg-neutral-900">
                                <span class="hidden truncate text-sm font-medium text-neutral-700 md:inline dark:text-neutral-200">
                                    {{ auth.user.email ?? auth.user.name }}
                                </span>
                                <Avatar class="size-8 shrink-0 overflow-hidden rounded-full">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="rounded-full bg-neutral-200 text-xs font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </template>
                <template v-else>
                    <Button variant="ghost" size="sm" class="hidden md:inline-flex" as-child>
                        <Link :href="login().url">Logi sisse</Link>
                    </Button>
                    <Button size="sm" class="hidden md:inline-flex" as-child>
                        <Link :href="register().url">Registreeri</Link>
                    </Button>
                </template>
            </div>
        </div>

        <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-t border-neutral-100 dark:border-neutral-800">
            <div class="mx-auto flex h-11 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl md:px-6">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
