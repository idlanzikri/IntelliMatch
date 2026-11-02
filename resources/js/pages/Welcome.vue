<script setup lang="ts">
import { ref } from "vue";
import PublicNavigation from "@/components/PublicNavigation.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    ArrowRight,
    ArrowUpRight,
    Check,
    Compass,
    GraduationCap,
    Route,
    Sparkles,
} from "@lucide/vue";

const query = ref("");
function search(value = query.value) {
    router.get('/careers', value.trim() ? { q: value.trim() } : {});
}

const steps = [
    {
        number: "01",
        title: "Start with your strengths",
        text: "Tell us about your programme, practical skills, and the work that interests you.",
    },
    {
        number: "02",
        title: "Explore your possibilities",
        text: "Compare your skills with career profiles and understand why a path could suit you.",
    },
    {
        number: "03",
        title: "Plan your next move",
        text: "See the skills you still need to develop and take your next step with direction.",
    },
];
</script>

<template>
    <Head title="Find your direction" />
    <div class="min-h-screen bg-[#fafcfd] text-[#102d49]">
        <PublicNavigation />
        <main>
            <section
                class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.15fr_1fr] lg:px-10 lg:py-24"
            >
                <div>
                    <p
                        class="mb-6 flex items-center gap-2 text-xs font-bold tracking-[0.18em] text-teal-700 uppercase"
                    >
                        <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                        Made for the next generation of TVET talent
                    </p>
                    <h1
                        class="max-w-2xl text-5xl leading-[1.08] font-semibold tracking-tight sm:text-6xl lg:text-7xl"
                    >
                        Your skills.<br />Your potential.<br /><span
                            class="text-teal-600"
                            >Your next chapter.</span
                        >
                    </h1>
                    <p class="mt-7 max-w-lg text-lg leading-8 text-slate-500">
                        You are learning skills that matter. Discover where they
                        can take you—with career guidance built around your
                        strengths.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-6">
                        <a
                            href="#career-search"
                            class="inline-flex items-center gap-3 rounded-lg bg-teal-700 px-6 py-4 font-semibold text-white transition hover:bg-teal-800"
                            >Find my direction <ArrowUpRight :size="20" /></a
                        ><a
                            href="#how-it-works"
                            class="inline-flex items-center gap-2 text-sm font-semibold"
                            >See how it works <ArrowRight :size="17"
                        /></a>
                    </div>
                    <p
                        class="mt-6 flex items-center gap-2 text-sm text-slate-500"
                    >
                        <Check :size="16" class="text-teal-600" /> Start with
                        your profile. No resume needed.
                    </p>
                </div>
                <div
                    id="career-search"
                    class="rounded-[2rem] border border-cyan-100 bg-white p-8 shadow-[0_25px_80px_-40px_#007c91] sm:p-10"
                >
                    <p
                        class="flex items-center gap-2 text-xs font-semibold tracking-widest text-teal-700 uppercase"
                    >
                        <Sparkles :size="18" /> Discover your possibilities
                    </p>
                    <h2 class="mt-5 text-3xl font-semibold">
                        Find a career that fits you.
                    </h2>
                    <p class="mt-4 leading-7 text-slate-500">
                        Start with your skills, interests, or a career you have
                        in mind.
                    </p>
                    <form class="mt-6" @submit.prevent="search()">
                        <label for="career-query" class="text-sm font-semibold"
                            >What interests you?</label
                        ><textarea
                            id="career-query"
                            v-model="query"
                            rows="3"
                            maxlength="500"
                            placeholder="e.g. I enjoy troubleshooting and repairing machines"
                            class="mt-3 w-full rounded-xl border border-slate-300 bg-slate-50 p-4 focus:outline-teal-600"
                        ></textarea
                        ><button
                            type="submit"
                            class="mt-3 w-full rounded-lg bg-teal-700 px-6 py-4 font-semibold text-white hover:bg-teal-800"
                        >
                            Find my career ↗
                        </button>
                    </form>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <button
                            v-for="prompt in [
                                'Troubleshooting',
                                'Electrical',
                                'Technology',
                            ]"
                            :key="prompt"
                            class="rounded-full border border-teal-100 bg-teal-50 px-3 py-2 text-sm text-teal-800"
                            @click="search(prompt)"
                        >
                            {{ prompt }}
                        </button>
                    </div>
                    <p
                        class="mt-6 border-t border-slate-100 pt-4 text-xs leading-5 text-slate-500"
                    >
                        Free to explore. Search currently matches keywords in
                        listed careers. Create a profile for skill-based
                        recommendations.
                    </p>
                </div>
            </section>
            <section id="possibilities" class="bg-[#092c4f] text-white">
                <div
                    class="mx-auto grid max-w-7xl gap-8 px-6 py-10 md:grid-cols-3 lg:px-10"
                >
                    <div class="flex gap-4">
                        <GraduationCap class="shrink-0 text-cyan-300" />
                        <div>
                            <h2 class="font-semibold">
                                Your learning has a future
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-blue-100">
                                Connect practical training to career
                                possibilities.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <Compass class="shrink-0 text-cyan-300" />
                        <div>
                            <h2 class="font-semibold">Understand your fit</h2>
                            <p class="mt-2 text-sm leading-6 text-blue-100">
                                See matching skills and areas to develop.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <Route class="shrink-0 text-cyan-300" />
                        <div>
                            <h2 class="font-semibold">
                                Move forward with direction
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-blue-100">
                                Use your skill gaps to guide what you learn
                                next.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
            <section
                id="how-it-works"
                class="mx-auto max-w-7xl px-6 py-20 lg:px-10"
            >
                <p
                    class="mb-3 text-xs font-bold tracking-[0.2em] text-teal-700 uppercase"
                >
                    A little clarity goes a long way
                </p>
                <h2
                    class="max-w-xl text-4xl leading-tight font-semibold tracking-tight"
                >
                    From “what comes next?”<br />to a place to start.
                </h2>
                <div class="mt-12 grid gap-10 md:grid-cols-3">
                    <article
                        v-for="step in steps"
                        :key="step.number"
                        class="border-t border-slate-200 pt-6"
                    >
                        <span class="text-sm font-semibold text-teal-600">{{
                            step.number
                        }}</span>
                        <h3 class="mt-5 text-xl font-semibold">
                            {{ step.title }}
                        </h3>
                        <p class="mt-3 leading-7 text-slate-500">
                            {{ step.text }}
                        </p>
                    </article>
                </div>
            </section>
            <section
                id="institutions"
                class="mx-auto max-w-7xl px-6 pb-20 lg:px-10"
            >
                <div
                    class="grid items-center gap-8 rounded-3xl bg-[#eaf5f5] p-8 md:grid-cols-[1fr_auto] md:p-12"
                >
                    <div>
                        <p
                            class="mb-3 flex items-center gap-2 text-xs font-bold tracking-widest text-teal-700 uppercase"
                        >
                            <Sparkles :size="16" /> For TVET institutions
                        </p>
                        <h2 class="text-3xl font-semibold">
                            Better guidance starts with understanding.
                        </h2>
                        <p class="mt-4 max-w-2xl leading-7 text-slate-600">
                            Our vision is to help educators connect student
                            strengths with industry needs. Sign in to your
                            institution workspace to review linked student
                            profiles and career matches.
                        </p>
                    </div>
                    <Link
                        href="/talent"
                        class="inline-flex items-center gap-3 font-semibold text-teal-800"
                        >Discover talent <ArrowUpRight :size="20"
                    /></Link>
                </div>
            </section>
        </main>
        <footer class="border-t border-slate-200 bg-white">
            <div
                class="mx-auto flex max-w-7xl flex-col justify-between gap-4 px-6 py-7 text-sm text-slate-500 sm:flex-row lg:px-10"
            >
                <span class="font-semibold text-slate-800"
                    >IntelliMatch
                    <span class="ml-2 font-normal text-slate-500"
                        >Learning today. Possibilities tomorrow.</span
                    ></span
                ><span>TVET Career Guidance · Group 9</span>
            </div>
        </footer>
    </div>
</template>
