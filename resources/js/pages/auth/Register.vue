<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    error?: string;
}>();

const inputClass =
    'h-11 border-neutral-300 bg-white shadow-none transition-colors focus-visible:border-indigo-500 focus-visible:ring-indigo-500/25 dark:border-neutral-600 dark:bg-neutral-900';
</script>

<template>
    <AuthBase :show-header="false">
        <Head title="Registreeri" />

        <Form
            v-bind="RegisteredUserController.store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div v-if="error" class="text-center text-sm font-medium text-destructive">
                {{ error }}
            </div>

            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label for="name" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Nimi</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Sinu nimi"
                        :class="inputClass"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">E-post</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" name="email" placeholder="sinu@epost.ee" :class="inputClass" />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Parool</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="••••••••"
                        :class="inputClass"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Kinnita parool</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="••••••••"
                        :class="inputClass"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    :tabindex="5"
                    :disabled="processing"
                    class="mt-1 h-11 w-full rounded-md bg-neutral-900 text-sm font-semibold uppercase tracking-wide text-white shadow-none hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                >
                    <LoaderCircle v-if="processing" class="size-4 animate-spin" />
                    <span v-else>Registreeri</span>
                </Button>
            </div>

            <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">
                Sul on juba konto?
                <TextLink :href="login()" class="font-medium text-neutral-900 underline-offset-4 hover:underline dark:text-white" :tabindex="6">
                    Logi sisse
                </TextLink>
            </p>
        </Form>
    </AuthBase>
</template>
