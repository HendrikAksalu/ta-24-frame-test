<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index, update } from '@/routes/authors';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    author: {
        id: number;
        first_name: string;
        last_name: string;
        date_of_birth: string;
        created_at_formatted?: string;
        updated_at_formatted?: string;
    };
}>();



const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Authors', href: index().url },
    { title: `Edit Author #${props.author.id}`, href: `/authors/${props.author.id}/edit` },
];

const form = useForm({
                    first_name: props.author.first_name,
    last_name: props.author.last_name,
    date_of_birth: props.author.date_of_birth,
});

const submit = () => {
    form.put(update.url(props.author.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Edit Author #${props.author.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6 flex flex-col gap-6">
            <h1 class="text-2xl font-semibold">Edit Author</h1>

            <form @submit.prevent="submit" class="flex flex-col gap-4">
                <div>
                    <Label for="first_name">First Name</Label>
                    <Input id="first_name" v-model="form.first_name" />
                    <p v-if="form.errors.first_name" class="text-red-600 text-sm">
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <div>
                                        <Label for="last_name">Last Name</Label>
                    <Input id="last_name" v-model="form.last_name" />
                    <p v-if="form.errors.last_name" class="text-red-600 text-sm">
                        {{ form.errors.last_name }}
                    </p>
                </div>

                <div>
                    <Label for="date_of_birth">Date of Birth</Label>
                    <Textarea id="date_of_birth" rows="6" v-model="form.date_of_birth" />
                    <p v-if="form.errors.date_of_birth" class="text-red-600 text-sm">
                        {{ form.errors.date_of_birth }}
                    </p>
                </div>

                <div class="flex gap-3 mt-4">
                    <Button type="submit" :disabled="form.processing">
                        Save Changes
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit(index().url)"
                    >
                        Cancel
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 