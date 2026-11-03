<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import TablePagination from "@/components/admin/TablePagination.vue";
import type { Paginated } from "@/types/admin";
import type { Vacancy } from "@/types/industry";
defineProps<{
    applications: Paginated<{
        id: number;
        status: string;
        created_at: string;
        vacancy: Vacancy;
    }>;
}>();
</script>
<template>
    <Head title="My applications" />
    <h1 class="text-3xl font-semibold">My applications</h1>
    <p class="my-4 text-slate-500">
        Employers review your current profile. Keep it up to date.
    </p>
    <div class="space-y-4">
        <article
            v-for="a in applications.data"
            :key="a.id"
            class="flex flex-wrap items-center justify-between gap-4 rounded-xl border bg-white p-6"
        >
            <div>
                <h2 class="font-semibold">{{ a.vacancy.title }}</h2>
                <p class="text-sm text-slate-500">
                    {{ a.vacancy.company?.name }} · Applied
                    {{ a.created_at.slice(0, 10) }}
                </p>
            </div>
            <span
                class="rounded-full bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-800 capitalize"
                >{{ a.status }}</span
            >
        </article>
        <p v-if="!applications.data.length">
            No applications yet.
            <Link href="/opportunities" class="text-teal-700"
                >Browse opportunities</Link
            >
        </p>
    </div>
    <TablePagination
        :current="applications.current_page"
        :last="applications.last_page"
        :total="applications.total"
        :previous="applications.prev_page_url"
        :next="applications.next_page_url"
    />
</template>
