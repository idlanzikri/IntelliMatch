<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import type { StudentProfile } from "@/types/career";
import type { Vacancy } from "@/types/industry";
defineProps<{
    vacancy: Vacancy;
    applicants: {
        id: number;
        name: string;
        profile: StudentProfile | null;
        status: string;
        date: string;
        match: {
            score: number;
            skills_score: number;
            qualification_score: number | null;
            matched: string[];
            missing: string[];
        };
    }[];
}>();
</script>
<template>
    <Head title="Vacancy applicants" /><Link
        href="/industry"
        class="text-sm text-teal-700"
        >← My vacancies</Link
    >
    <h1 class="mt-4 text-3xl font-semibold">Applicants: {{ vacancy.title }}</h1>
    <p class="my-4 text-sm text-slate-500">
        Ranked by current profile: 70% required skill coverage + 30% minimum
        qualification level. Missing qualification data earns no qualification
        points. Scores are guidance based on self-reported information;
        programme relevance and certificates require your review.
    </p>
    <div class="overflow-x-auto rounded-xl border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="p-4">Applicant</th>
                    <th class="p-4">Match</th>
                    <th class="p-4">Applied</th>
                    <th class="p-4">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="a in applicants" :key="a.id" class="border-t">
                    <td class="p-4">
                        <strong>{{ a.name }}</strong>
                        <p class="mt-1 text-slate-500">
                            {{ a.profile?.programme }} ·
                            {{ a.profile?.institution }}
                        </p>
                        <details class="mt-3">
                            <summary class="cursor-pointer text-teal-700">
                                Applicant details
                            </summary>
                            <p class="mt-2">
                                Qualification:
                                {{
                                    a.profile?.qualification_level ||
                                    "Not provided"
                                }}
                            </p>
                            <p>
                                Interests:
                                {{ a.profile?.interests || "Not provided" }}
                            </p>
                            <p>
                                Matched skills:
                                {{ a.match.matched.join(", ") || "None" }}
                            </p>
                            <p>
                                Missing skills:
                                {{ a.match.missing.join(", ") || "None" }}
                            </p>
                        </details>
                    </td>
                    <td class="p-4">
                        <strong class="text-xl text-teal-700"
                            >{{ a.match.score }}%</strong
                        >
                        <p>Skills: {{ a.match.skills_score }}%</p>
                        <p>
                            Qualification:
                            {{
                                a.match.qualification_score === null
                                    ? "Insufficient data"
                                    : `${a.match.qualification_score}%`
                            }}
                        </p>
                    </td>
                    <td class="p-4">{{ a.date }}</td>
                    <td class="p-4">
                        <select
                            :value="a.status"
                            aria-label="Application status"
                            @change="
                                router.put(
                                    `/industry/vacancies/${vacancy.id}/applicants/${a.id}`,
                                    {
                                        status: (
                                            $event.target as HTMLSelectElement
                                        ).value,
                                    },
                                    { preserveScroll: true },
                                )
                            "
                            class="rounded-lg border p-2"
                        >
                            <option
                                v-for="s in [
                                    'submitted',
                                    'shortlisted',
                                    'rejected',
                                ]"
                                :key="s"
                            >
                                {{ s }}
                            </option>
                        </select>
                    </td>
                </tr>
                <tr v-if="!applicants.length">
                    <td colspan="4" class="p-10 text-center text-slate-500">
                        No applications yet.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
