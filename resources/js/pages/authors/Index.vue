<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationEllipsis from '@/components/ui/pagination/PaginationEllipsis.vue';
import PaginationFirst from '@/components/ui/pagination/PaginationFirst.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationLast from '@/components/ui/pagination/PaginationLast.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, edit, show, index } from '@/routes/authors';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';

// Breadcrumbs for layout navigation
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Authors',
    href: index().url,
  },
];

// Type definitions
interface PaginationLink {
  url: string | null;
  label: string;
  page?: number | null;
  active: boolean;
}

interface PaginatedResponse {
  current_page: number;
  data: Author[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: PaginationLink[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

type Author = {
  id: number;
  first_name: string;
  last_name: string;
  date_of_birth: Date;
  created_at: string;
  updated_at: string;
  created_at_formatted: string;
  updated_at_formatted: string;
  date_of_birth_formatted: string;
};


defineProps<{
  authors: PaginatedResponse;
}>();

const deleteAuthor = (authorId: number) => {
  if (!confirm('Oled sa kindel?')) return;
  router.delete(destroy.url(authorId), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Autor kustutatud.');
    },
    onError: (err) => {
      console.error(err);
      alert('Ups, sa ei saanud eluga hakkama.');
    },
  });
}

</script>

<template>

  <Head title="Authors" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- <pre>{{ authors }}</pre> -->

      <Table>
        <TableCaption>A list of your recent blog authors.</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead class="w-[100px]">ID</TableHead>
            <TableHead>First Name</TableHead>
            <TableHead>Last Name</TableHead>
            <TableHead>Date of Birth</TableHead>
            <TableHead class="text-right">Created at</TableHead>
            <TableHead class="text-right">Updated At</TableHead>
            <TableHead>
              <span class="sr-only">Actions</span>
            </TableHead>
          </TableRow>
        </TableHeader>

        <TableBody>
            <TableRow v-for="author in authors.data" :key="author.id">
            <TableCell class="font-medium">{{ author.id }}</TableCell>
            <TableCell>{{ author.first_name }}</TableCell>
            <TableCell>{{ author.last_name }}</TableCell>
            <TableCell>{{ author.date_of_birth }}</TableCell>
            <TableCell class="text-right">{{ author.created_at_formatted }}</TableCell>
            <TableCell class="text-right">{{ author.updated_at_formatted }}</TableCell>
            <TableCell>
              <div class="flex justify-end">
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button size="icon" variant="ghost">
                      <MoreVertical />
                    </Button>
                  </DropdownMenuTrigger>

                  <DropdownMenuContent>
                    <DropdownMenuItem as-child>
                      <Link :href="show.url(author.id)">View</Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem as-child>
                      <Link :href="edit.url(author.id)">Edit</Link>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem class="text-destructive" @click="deleteAuthor(author.id)">
                      Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

        <Pagination class="w-full" :page="authors.current_page" v-slot="{ page }" :total="authors.total"
        :items-per-page="authors.per_page" @update:page="(page) => router.get(index().url, { page: page })">
        <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
          <PaginationFirst />
          <PaginationPrevious />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem v-if="item.type === 'page'" :value="item.value" as-child>
              <Button class="w-10 h-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                {{ item.value }}
              </Button>
            </PaginationItem>

            <PaginationEllipsis v-else :key="item.type" :index="index" />
          </template>

          <PaginationNext />
          <PaginationLast />
        </PaginationContent>
      </Pagination>
    </div>
  </AppLayout>
</template>