<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import TablePagination from "@/components/admin/TablePagination.vue";
import type { Paginated } from "@/types/admin";
import type { InstitutionStudent } from "@/types/institution";
const props = defineProps<{
    institution: { name: string; code: string };
    stats: {
        students: number;
        profiles: number;
        withSkills: number;
        careers: number;
    };
    skills: { name: string; count: number }[];
    students: Paginated<InstitutionStudent>;
    filters: { search: string };
}>();
const search = ref(props.filters.search);
function submit() {
    router.get(
        "/institution",
        { search: search.value },
        { preserveState: true, replace: true },
    );
}
</script>
<template>
    <Head title="Institution overview" />
    <p class="text-xs font-bold tracking-[0.18em] text-teal-700 uppercase">
        {{ institution.code }} · Your learning community
    </p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">
        {{ institution.name }}
    </h1>
    <p class="mt-3 text-slate-500">
        Understand your students’ strengths. Help them find their next step.
    </p>
    <section class="my-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article
            v-for="item in [
                { label: 'Linked students', value: stats.students },
                { label: 'Profiles started', value: stats.profiles },
                { label: 'Students with skills', value: stats.withSkills },
                { label: 'Published career paths', value: stats.careers },
            ]"
            :key="item.label"
            class="rounded-xl border border-slate-200 bg-white p-6"
        >
            <p class="text-sm text-slate-500">{{ item.label }}</p>
            <p class="mt-4 text-4xl font-semibold text-[#123c59]">
                {{ item.value }}
            </p>
        </article>
    </section>
    <section class="mb-10 grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        <div class="rounded-2xl bg-[#092c4f] p-8 text-white">
            <p
                class="text-xs font-semibold tracking-widest text-cyan-200 uppercase"
            >
                Guidance with context
            </p>
            <h2 class="mt-4 text-2xl font-semibold">
                Make the next conversation count.
            </h2>
            <p class="mt-4 max-w-xl text-sm leading-7 text-blue-100">
                Open a student profile to review their skills, career interests,
                and matching career requirements. Use the gaps as a starting
                point for a counselling conversation.
            </p>
            <a
                href="#students"
                class="mt-6 inline-block rounded-lg bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-900"
                >Explore student profiles ↓</a
            >
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-semibold">Skills in your community</h2>
            <p class="mt-2 mb-5 text-xs leading-5 text-slate-500">
                Self-reported skills across linked students. Counts are students
                listing each skill.
            </p>
            <div
                v-for="skill in skills"
                :key="skill.name"
                class="flex justify-between gap-4 border-b border-slate-100 py-2 text-sm"
            >
                <span>{{ skill.name }}</span
                ><span class="font-semibold text-teal-700">{{
                    skill.count
                }}</span>
            </div>
            <p v-if="!skills.length" class="py-5 text-sm text-slate-400">
                Skill insights will appear as students complete their profiles.
            </p>
        </div>
    </section>
    <section id="students">
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div>
                <h2 class="text-2xl font-semibold">Your students</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Only students formally linked to your institution appear
                    here.
                </p>
            </div>
            <form class="flex gap-3" @submit.prevent="submit">
                <input
                    v-model="search"
                    aria-label="Search linked students"
                    placeholder="Search name or email"
                    class="min-w-0 rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm"
                /><button
                    class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-semibold text-white"
                >
                    Search
                </button>
            </form>
        </div>
        <div
            class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-slate-100 text-xs tracking-wider text-slate-500 uppercase"
                    >
                        <tr>
                            <th class="p-5">Student</th>
                            <th class="p-5">Programme</th>
                            <th class="p-5">Skills</th>
                            <th class="p-5">Guidance</th>
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
                            <td class="p-5 text-slate-500">
                                {{ student.programme || "Not provided" }}
                            </td>
                            <td class="p-5">{{ student.skillsCount }}</td>
                            <td class="p-5">
                                <Link
                                    :href="`/institution/students/${student.id}`"
                                    class="font-semibold text-teal-700"
                                    >View profile →</Link
                                >
                            </td>
                        </tr>
                        <tr v-if="!students.data.length">
                            <td
                                colspan="4"
                                class="p-12 text-center text-slate-500"
                            >
                                No linked students found.
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
        </div>
    </section>
</template>
