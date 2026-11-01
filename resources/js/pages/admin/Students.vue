<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import TablePagination from "@/components/admin/TablePagination.vue";
import type { AdminStudent, Paginated } from "@/types/admin";
const props = defineProps<{
    students: Paginated<AdminStudent>;
    filters: { search: string };
}>();
const search = ref(props.filters.search);
function submit() {
    router.get(
        "/admin/students",
        { search: search.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Manage students" />
    <p class="text-xs font-bold tracking-widest text-teal-700 uppercase">
        Your community
    </p>
    <h1 class="mt-2 text-3xl font-semibold">Students</h1>
    <p class="mt-3 text-slate-500">
        Review registered students and their profile completion.
    </p>
    <form class="my-7 flex max-w-lg gap-3" @submit.prevent="submit">
        <input
            v-model="search"
            aria-label="Search students by name or email"
            placeholder="Search by name or email"
            class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm"
        /><button
            class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white"
        >
            Search
        </button>
    </form>
    <section
        class="overflow-hidden rounded-xl border border-slate-200 bg-white"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead
                    class="bg-slate-100 text-xs tracking-wider text-slate-500 uppercase"
                >
                    <tr>
                        <th class="p-5">Student</th>
                        <th class="p-5">Institution / programme</th>
                        <th class="p-5">Skills</th>
                        <th class="p-5">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="student in students.data"
                        :key="student.id"
                        class="border-t border-slate-100"
                    >
                        <td class="p-5">
                            <p class="font-semibold">{{ student.name }}</p>
                            <p class="mt-1 text-slate-500">
                                {{ student.email }}
                            </p>
                        </td>
                        <td class="p-5">
                            <p>{{ student.institution || "Not provided" }}</p>
                            <p class="mt-1 text-slate-500">
                                {{ student.programme || "No programme added" }}
                            </p>
                        </td>
                        <td class="p-5">{{ student.skillsCount }}</td>
                        <td class="p-5">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-medium"
                                :class="
                                    student.verified
                                        ? 'bg-teal-50 text-teal-700'
                                        : 'bg-amber-50 text-amber-700'
                                "
                                >{{
                                    student.verified ? "Verified" : "Unverified"
                                }}</span
                            >
                        </td>
                    </tr>
                    <tr v-if="!students.data.length">
                        <td colspan="4" class="p-12 text-center text-slate-500">
                            No students found. Try a different search.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <TablePagination
            :current="students.current_page"
            :last="students.last_page"
            :total="students.total"
            :previous="students.prev_page_url"
            :next="students.next_page_url"
        />
    </section>
</template>
