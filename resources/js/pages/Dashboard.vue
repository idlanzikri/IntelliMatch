<script setup lang="ts">
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ArrowUpRight, Compass, GraduationCap, Sparkles } from "@lucide/vue";
import CareerCard from "@/components/career/CareerCard.vue";
import type { CareerMatch, StudentProfile } from "@/types/career";
defineProps<{ profile: StudentProfile | null; careers: CareerMatch[] }>();
const page = usePage();
defineOptions({
    layout: {
        breadcrumbs: [{ title: "Your career dashboard", href: "/dashboard" }],
    },
});
</script>
<template>
    <Head title="Your career dashboard" />
    <div class="min-h-full bg-slate-50 p-5 text-slate-900 md:p-9">
        <p
            class="mb-2 text-xs font-semibold tracking-[0.2em] text-teal-700 uppercase"
        >
            Your next chapter
        </p>
        <h1 class="text-3xl font-semibold tracking-tight">
            Welcome, {{ page.props.auth.user.name }}.
        </h1>
        <p class="mt-2 text-slate-500">
            Discover where your skills can take you.
        </p>
        <section
            class="my-8 flex flex-col justify-between gap-6 rounded-2xl bg-[#082b50] p-7 text-white md:flex-row md:items-center"
        >
            <div>
                <div class="mb-3 flex items-center gap-2 text-sm text-cyan-200">
                    <Sparkles :size="17" /> A direction that fits you
                </div>
                <h2 class="text-2xl font-semibold">
                    {{
                        profile?.skills?.length
                            ? "Keep building your career profile."
                            : "Your skills are the starting point."
                    }}
                </h2>
                <p class="mt-2 max-w-xl text-sm leading-6 text-blue-100">
                    Add your programme and practical skills to see how they
                    align with different careers.
                </p>
            </div>
            <Link
                href="/student/profile"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-900"
                >{{ profile ? "Update my profile" : "Build my profile" }}
                <ArrowUpRight :size="17"
            /></Link>
        </section>
        <div class="mb-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <GraduationCap class="mb-3 text-teal-600" :size="22" />
                <p class="text-sm text-slate-500">Your programme</p>
                <p class="mt-1 font-semibold">
                    {{ profile?.programme || "Add your programme" }}
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <Sparkles class="mb-3 text-teal-600" :size="22" />
                <p class="text-sm text-slate-500">Skills in your profile</p>
                <p class="mt-1 text-2xl font-semibold">
                    {{ profile?.skills?.length || 0 }}
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <Compass class="mb-3 text-teal-600" :size="22" />
                <p class="text-sm text-slate-500">Career paths to explore</p>
                <p class="mt-1 text-2xl font-semibold">{{ careers.length }}</p>
            </div>
        </div>
        <section id="careers">
            <h2 class="text-2xl font-semibold">
                {{
                    profile?.skills?.length
                        ? "Your career matches"
                        : "Explore career possibilities"
                }}
            </h2>
            <p class="mt-2 mb-6 text-sm leading-6 text-slate-500">
                {{
                    profile?.skills?.length
                        ? "Ranked by the proportion of listed career skills in your profile. This is a starting point, not a hiring prediction or verified AI assessment."
                        : "Add your skills to personalise these results. These are sample career profiles, not live job vacancies."
                }}
            </p>
            <div class="grid gap-5 lg:grid-cols-2">
                <CareerCard
                    v-for="career in careers"
                    :key="career.id"
                    :career="career"
                    :has-skills="!!profile?.skills?.length"
                />
            </div>
        </section>
    </div>
</template>
