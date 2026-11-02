<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import CareerCard from "@/components/career/CareerCard.vue";
import type { CareerMatch, StudentProfile } from "@/types/career";
defineProps<{
    student: { id: number; name: string; email: string };
    profile: StudentProfile | null;
    careers: CareerMatch[];
}>();
</script>
<template>
    <Head :title="`${student.name} — Student guidance`" /><Link
        href="/institution#students"
        class="text-sm font-semibold text-teal-700"
        >← Back to students</Link
    >
    <p class="mt-8 text-xs font-bold tracking-widest text-teal-700 uppercase">
        Student guidance
    </p>
    <h1 class="mt-2 text-3xl font-semibold">{{ student.name }}</h1>
    <p class="mt-2 text-slate-500">{{ student.email }}</p>
    <section class="my-8 rounded-xl border border-slate-200 bg-white p-7">
        <div class="grid gap-6 md:grid-cols-3">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Programme
                </p>
                <p class="mt-2 font-medium">
                    {{ profile?.programme || "Not provided" }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Preferred location
                </p>
                <p class="mt-2 font-medium">
                    {{ profile?.location || "Not provided" }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Skills recorded
                </p>
                <p class="mt-2 font-medium">
                    {{ profile?.skills?.length || 0 }}
                </p>
            </div>
        </div>
        <h2 class="mt-7 text-sm font-semibold">Career interests</h2>
        <p class="mt-2 text-sm leading-7 whitespace-pre-wrap text-slate-500">
            {{ profile?.interests || "No career interests added yet." }}
        </p>
        <h2 class="mt-6 text-sm font-semibold">Self-reported skills</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            <span
                v-for="skill in profile?.skills || []"
                :key="skill"
                class="rounded-md bg-teal-50 px-3 py-2 text-sm text-teal-800"
                >{{ skill }}</span
            >
            <p v-if="!profile?.skills?.length" class="text-sm text-slate-400">
                No skills added yet.
            </p>
        </div>
    </section>
    <h2 class="text-2xl font-semibold">Career alignment</h2>
    <p class="mt-3 mb-6 max-w-3xl text-sm leading-7 text-slate-500">
        Coverage compares recorded skills with published career requirements. It
        is guidance for discussion, not a verified assessment or prediction of
        employment. Profiles without skills show career examples without scores.
    </p>
    <div class="grid gap-5 md:grid-cols-2">
        <CareerCard
            v-for="career in careers"
            :key="career.id"
            :career="career"
            :has-skills="!!profile?.skills?.length"
        />
    </div>
    <p
        v-if="!careers.length"
        class="rounded-lg border border-slate-200 bg-white p-8 text-slate-500"
    >
        No career profiles are currently published.
    </p>
</template>
