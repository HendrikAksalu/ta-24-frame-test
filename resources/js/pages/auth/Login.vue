<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    error?: string;
    canResetPassword: boolean;
}>();

const inputClass =
    'h-11 border-neutral-300 bg-white shadow-none transition-colors focus-visible:border-indigo-500 focus-visible:ring-indigo-500/25 dark:border-neutral-600 dark:bg-neutral-900';
</script>

<template>
    <AuthBase :show-header="false">
        <Head title="Logi sisse" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-emerald-600 dark:text-emerald-400">
            {{ status }}
        </div>

        <div v-if="error" class="mb-4 text-center text-sm font-medium text-destructive">
            {{ error }}
        </div>

        <Form
            v-bind="AuthenticatedSessionController.store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label for="email" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">E-post</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="sinu@epost.ee"
                        :class="inputClass"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Parool</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        :class="inputClass"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <Label for="remember" class="cursor-pointer text-sm font-normal text-neutral-600 dark:text-neutral-400">
                        Jäta mind meelde
                    </Label>
                </div>

                <div class="flex flex-col-reverse gap-4 pt-1 sm:flex-row sm:items-center sm:justify-between">
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm text-neutral-600 underline-offset-4 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100"
                        :tabindex="5"
                    >
                        Unustasid parooli?
                    </TextLink>
                    <Button
                        type="submit"
                        :tabindex="4"
                        :disabled="processing"
                        class="h-11 shrink-0 rounded-md bg-neutral-900 px-8 text-sm font-semibold uppercase tracking-wide text-white shadow-none hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200 sm:ml-auto"
                    >
                        <LoaderCircle v-if="processing" class="size-4 animate-spin" />
                        <span v-else>Logi sisse</span>
                    </Button>
                </div>
            </div>

            <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">
                Pole veel kontot?
                <TextLink :href="register()" class="font-medium text-neutral-900 underline-offset-4 hover:underline dark:text-white" :tabindex="6">
                    Registreeri
                </TextLink>
            </p>
        </Form>
    </AuthBase>
</template>
