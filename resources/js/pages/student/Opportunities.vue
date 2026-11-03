<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import LocationMap from "@/components/LocationMap.vue";
import { reactive, ref } from "vue";
import TablePagination from "@/components/admin/TablePagination.vue";
import type { Vacancy } from "@/types/industry";
import type { Paginated } from "@/types/admin";
const props = defineProps<{
    vacancies: Paginated<Vacancy>;
    filters: Record<string, string>;
    applied: number[];
    markers: {
        id: number;
        name: string;
        latitude: number;
        longitude: number;
    }[];
}>();
const mapOpen = ref(false);
const selected = ref<number | null>(null);
const filters = reactive({
    search: props.filters.search || "",
    location: props.filters.location || "",
    type: props.filters.type || "",
    arrangement: props.filters.arrangement || "",
});
function search() {
    router.get("/opportunities", filters, {
        preserveState: true,
        replace: true,
    });
}
</script>
<template>
    <Head title="Job opportunities" />
    <h1 class="text-3xl font-semibold">Job opportunities</h1>
    <p class="mt-3 text-slate-500">
        Find your next job or internship. Apply with your saved career profile.
    </p>
    <form
        @submit.prevent="search"
        class="my-7 grid gap-3 rounded-2xl border bg-white p-5 md:grid-cols-2 lg:grid-cols-5"
    >
        <input
            v-model="filters.search"
            aria-label="Search jobs"
            placeholder="Job title, company or keyword"
            class="rounded-lg border p-3"
        /><input
            v-model="filters.location"
            aria-label="Location"
            placeholder="Location"
            class="rounded-lg border p-3"
        /><select
            v-model="filters.type"
            aria-label="Job type"
            class="rounded-lg border p-3"
        >
            <option value="">All job types</option>
            <option
                v-for="t in [
                    'full-time',
                    'part-time',
                    'contract',
                    'internship',
                ]"
                :key="t"
            >
                {{ t }}
            </option></select
        ><select
            v-model="filters.arrangement"
            aria-label="Work arrangement"
            class="rounded-lg border p-3"
        >
            <option value="">All arrangements</option>
            <option v-for="t in ['on-site', 'hybrid', 'remote']" :key="t">
                {{ t }}
            </option></select
        ><button class="rounded-lg bg-teal-700 p-3 font-semibold text-white">
            Search jobs
        </button>
    </form>
    <div class="mb-4 flex gap-4">
        <button
            class="rounded-lg border px-4 py-2 text-teal-700"
            @click="mapOpen = !mapOpen"
        >
            {{ mapOpen ? "List view" : "Map view" }}</button
        ><Link href="/training" class="p-2 text-teal-700"
            >Explore TVET training →</Link
        >
    </div>
    <section v-if="mapOpen" class="mb-6">
        <LocationMap :points="markers" @select="(id) => (selected = id)" />
        <p class="mt-2 text-sm text-slate-500">
            {{ markers.length }} workplaces across all matching results. Remote
            roles and roles awaiting a workplace pin remain in the list.
        </p>
        <p v-if="selected" class="mt-2 rounded border bg-white p-3">
            {{ markers.find((m) => m.id === selected)?.name }}
            <Link
                :href="`/opportunities?vacancy=${selected}`"
                class="ml-3 font-semibold text-teal-700"
                >View opportunity →</Link
            >
        </p>
    </section>
    <p class="mb-4 text-sm text-slate-500">
        {{ vacancies.total }} opportunities · 10 per page
    </p>
    <div class="space-y-4">
        <article
            v-for="v in vacancies.data"
            :key="v.id"
            class="rounded-2xl border border-slate-200 bg-white p-6"
        >
            <p class="text-sm font-semibold text-teal-700">
                {{ v.company?.name }}
            </p>
            <h2 class="mt-2 text-xl font-semibold">{{ v.title }}</h2>
            <p class="mt-2 text-sm text-slate-500">
                {{ v.location }} · {{ v.type }} · {{ v.arrangement }}
            </p>
            <p class="my-3 text-sm">
                MYR {{ v.salary_min ?? "—" }} – {{ v.salary_max ?? "—" }} /
                month
            </p>
            <p class="line-clamp-2 text-sm leading-6 text-slate-600">
                {{ v.description }}
            </p>
            <details class="my-4 text-sm">
                <summary class="cursor-pointer font-semibold text-teal-700">
                    View job details
                </summary>
                <p class="my-3 whitespace-pre-wrap">{{ v.description }}</p>
                <p>{{ v.qualifications }}</p>
                <p class="mt-2">
                    Minimum qualification:
                    {{ v.qualification_level || "Not specified" }}
                </p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span
                        v-for="skill in v.skills"
                        :key="skill"
                        class="rounded bg-teal-50 px-2 py-1 text-teal-800"
                        >{{ skill }}</span
                    >
                </div>
            </details>
            <div class="flex items-center justify-between border-t pt-4">
                <span class="text-xs text-slate-500"
                    >Closes {{ v.closing_date.slice(0, 10) }}</span
                ><span
                    v-if="applied.includes(v.id)"
                    class="font-semibold text-teal-700"
                    >Applied ✓</span
                ><Link
                    v-else
                    href="/student/profile"
                    class="text-sm text-teal-700"
                    >Review my profile</Link
                ><Link
                    v-if="!applied.includes(v.id)"
                    :href="`/opportunities/${v.id}/apply`"
                    method="post"
                    as="button"
                    class="rounded-lg bg-teal-700 px-5 py-3 text-sm font-semibold text-white"
                    >Apply with my profile</Link
                >
            </div>
        </article>
    </div>
    <p
        v-if="!vacancies.data.length"
        class="rounded-xl border bg-white p-10 text-center"
    >
        No opportunities match your search. Try fewer filters.
    </p>
    <TablePagination
        :current="vacancies.current_page"
        :last="vacancies.last_page"
        :total="vacancies.total"
        :previous="vacancies.prev_page_url"
        :next="vacancies.next_page_url"
    />
</template>
