<script setup lang="ts">
import PublicNavigation from "@/components/PublicNavigation.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps<{
    filters: { q: string; programme: string };
    programmes: string[];
    samples: { id: number; programme: string; skills: string[] }[];
}>();

const query = ref(props.filters.q);
const programme = ref(props.filters.programme);

watch(
    () => props.filters,
    (value) => {
        query.value = value.q;
        programme.value = value.programme;
    },
);

function search() {
    router.get("/talent", {
        q: query.value.trim(),
        programme: programme.value,
    });
}
</script>
<template>
    <Head title="Discover talent" />
    <div class="min-h-screen bg-[#fafcfd] text-[#102d49]">
        <PublicNavigation />
        <main class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
            <p
                class="text-xs font-bold tracking-widest text-teal-700 uppercase"
            >
                For institutions
            </p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight">
                Discover talent
            </h1>
            <p class="mt-4 max-w-2xl leading-7 text-slate-500">
                Explore practical skills and training across TVET programmes.
                Sign in to your institution workspace to view students linked to
                your institution.
            </p>
            <form
                class="mt-8 grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 md:grid-cols-[1fr_16rem_auto]"
                @submit.prevent="search"
            >
                <div>
                    <label for="talent-search" class="text-sm font-semibold"
                        >Programme or skill</label
                    ><input
                        id="talent-search"
                        v-model="query"
                        type="search"
                        maxlength="500"
                        placeholder="e.g. networking"
                        class="mt-2 w-full rounded-lg border border-slate-300 p-3 focus:outline-teal-600"
                    />
                </div>
                <div>
                    <label for="programme" class="text-sm font-semibold"
                        >Programme</label
                    ><select
                        id="programme"
                        v-model="programme"
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white p-3 focus:outline-teal-600"
                    >
                        <option value="">All programmes</option>
                        <option
                            v-for="item in programmes"
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
                    Search examples
                </button>
            </form>
            <div
                class="mt-6 rounded-xl bg-teal-50 p-4 text-sm leading-6 text-teal-900"
            >
                Illustrative profiles only. These examples do not represent real
                students. Public student listings will be available once
                students can choose to make their profiles visible.
            </div>
            <div class="mt-6 flex justify-between gap-4">
                <p role="status" class="text-sm text-slate-500">
                    {{ samples.length }} sample profiles found
                </p>
                <Link
                    v-if="filters.q || filters.programme"
                    href="/talent"
                    class="text-sm font-semibold text-teal-700 underline"
                    >Clear filters</Link
                >
            </div>
            <div class="mt-6 grid gap-5 md:grid-cols-3">
                <article
                    v-for="sample in samples"
                    :key="sample.id"
                    class="rounded-2xl border border-slate-200 bg-white p-6"
                >
                    <p class="text-xs font-semibold text-teal-700">
                        Sample student profile
                    </p>
                    <h2 class="mt-3 text-xl font-semibold">
                        {{ sample.programme }}
                    </h2>
                    <div class="my-5 flex flex-wrap gap-2">
                        <span
                            v-for="skill in sample.skills"
                            :key="skill"
                            class="rounded-full bg-slate-100 px-3 py-1 text-xs"
                            >{{ skill }}</span
                        >
                    </div>
                    <Link
                        href="/institution"
                        class="text-sm font-semibold text-teal-700"
                        >Institution sign in ↗</Link
                    >
                </article>
            </div>
            <div
                v-if="!samples.length"
                class="mt-6 rounded-2xl border border-dashed border-slate-300 p-10 text-center"
            >
                <h2 class="text-xl font-semibold">No sample profiles found</h2>
                <p class="mt-3 text-slate-500">
                    Try a different programme or skill.
                </p>
                <Link
                    href="/talent"
                    class="mt-5 inline-block font-semibold text-teal-700"
                    >Browse all examples →</Link
                >
            </div>
            <p class="mt-10 text-sm text-slate-500">
                Institution access is provisioned by an administrator. Already
                have access?
                <Link
                    href="/institution"
                    class="font-semibold text-teal-700 underline"
                    >Open your workspace</Link
                >
            </p>
        </main>
    </div>
</template>
