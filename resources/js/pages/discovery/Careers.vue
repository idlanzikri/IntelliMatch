<script setup lang="ts">
import PublicNavigation from "@/components/PublicNavigation.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps<{
    filters: { q: string; sector: string };
    sectors: string[];
    careers: {
        data: {
            id: number;
            title: string;
            sector: string;
            preview: string;
            skills: string[];
        }[];
        total: number;
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const query = ref(props.filters.q);
const sector = ref(props.filters.sector);

watch(
    () => props.filters,
    (value) => {
        query.value = value.q;
        sector.value = value.sector;
    },
);

function search() {
    router.get("/careers", { q: query.value.trim(), sector: sector.value });
}
</script>
<template>
    <Head title="Explore careers" />
    <div class="min-h-screen bg-[#fafcfd] text-[#102d49]">
        <PublicNavigation />
        <main class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
            <p
                class="text-xs font-bold tracking-widest text-teal-700 uppercase"
            >
                For students
            </p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight">
                Explore careers
            </h1>
            <p class="mt-4 max-w-2xl leading-7 text-slate-500">
                Discover where your skills can take you. Browse previews, then
                sign in to explore your fit and skills to develop.
            </p>
            <form
                class="mt-8 grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 md:grid-cols-[1fr_14rem_auto]"
                @submit.prevent="search"
            >
                <div>
                    <label for="career-search" class="text-sm font-semibold"
                        >Skills, interests, or career</label
                    ><input
                        id="career-search"
                        v-model="query"
                        type="search"
                        maxlength="500"
                        placeholder="e.g. electrical or troubleshooting"
                        class="mt-2 w-full rounded-lg border border-slate-300 p-3 focus:outline-teal-600"
                    />
                </div>
                <div>
                    <label for="sector" class="text-sm font-semibold"
                        >Sector</label
                    ><select
                        id="sector"
                        v-model="sector"
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white p-3 focus:outline-teal-600"
                    >
                        <option value="">All sectors</option>
                        <option
                            v-for="item in sectors"
                            :key="item"
                            :value="item"
                        >
                            {{ item }}
                        </option>
                    </select>
                </div>
                <button
                    class="self-end rounded-lg bg-teal-700 px-6 py-3 font-semibold text-white hover:bg-teal-800"
                >
                    Search careers
                </button>
            </form>
            <div class="mt-6 flex items-center justify-between gap-4">
                <p role="status" class="text-sm text-slate-500">
                    {{ careers.total }}
                    {{ careers.total === 1 ? "career" : "careers" }} found<span
                        v-if="filters.q"
                    >
                        for “{{ filters.q }}”</span
                    >
                </p>
                <Link
                    v-if="filters.q || filters.sector"
                    href="/careers"
                    class="text-sm font-semibold text-teal-700 underline"
                    >Clear filters</Link
                >
            </div>
            <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="career in careers.data"
                    :key="career.id"
                    class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6"
                >
                    <p class="text-xs font-semibold text-teal-700">
                        {{ career.sector }}
                    </p>
                    <h2 class="mt-3 text-xl font-semibold">
                        {{ career.title }}
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        {{ career.preview }}
                    </p>
                    <div class="my-5 flex flex-wrap gap-2">
                        <span
                            v-for="skill in career.skills"
                            :key="skill"
                            class="rounded-full bg-slate-100 px-3 py-1 text-xs"
                            >{{ skill }}</span
                        >
                    </div>
                    <Link
                        href="/dashboard"
                        class="mt-auto pt-3 text-sm font-semibold text-teal-700"
                        >Explore your fit · Sign in ↗</Link
                    >
                </article>
            </div>
            <div
                v-if="!careers.data.length"
                class="mt-6 rounded-2xl border border-dashed border-slate-300 p-10 text-center"
            >
                <h2 class="text-xl font-semibold">No careers found</h2>
                <p class="mt-3 text-slate-500">
                    Try another skill, a broader search, or a different sector.
                </p>
                <Link
                    href="/careers"
                    class="mt-5 inline-block font-semibold text-teal-700"
                    >Browse all careers →</Link
                >
            </div>
            <nav
                v-if="careers.last_page > 1"
                aria-label="Career result pages"
                class="mt-8 flex items-center justify-between gap-4"
            >
                <Link
                    v-if="careers.prev_page_url"
                    :href="careers.prev_page_url"
                    class="rounded-lg border border-slate-300 px-4 py-2"
                    >← Previous</Link
                ><span v-else></span
                ><span class="text-sm"
                    >Page {{ careers.current_page }} of
                    {{ careers.last_page }}</span
                ><Link
                    v-if="careers.next_page_url"
                    :href="careers.next_page_url"
                    class="rounded-lg border border-slate-300 px-4 py-2"
                    >Next →</Link
                ><span v-else></span>
            </nav>
            <p class="mt-10 text-sm text-slate-500">
                New here?
                <Link
                    href="/register"
                    class="font-semibold text-teal-700 underline"
                    >Create your student account</Link
                >
            </p>
        </main>
    </div>
</template>
