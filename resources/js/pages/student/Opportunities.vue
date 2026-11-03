<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import LocationMap from "@/components/LocationMap.vue";
import { reactive } from "vue";
import TablePagination from "@/components/admin/TablePagination.vue";
import type { Vacancy } from "@/types/industry";
import type { Paginated } from "@/types/admin";
const props = defineProps<{
    vacancies: Paginated<Vacancy>;
    filters: Record<string, string>;
    applied: number[];
    hasCareerProfile: boolean;
    distanceOrigin: string;
    distanceOrigins: { key: string; label: string; latitude: number; longitude: number }[];
    markers: {
        id: number;
        name: string;
        latitude: number;
        longitude: number;
    }[];
}>();
const filters = reactive({
    origin: props.distanceOrigin,
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
function selectPin(id?: number) {
    router.get('/opportunities', { ...filters, ...(id ? { pin: id } : {}) }, { preserveState: true, preserveScroll: true, replace: true });
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
    <div class="mb-4 rounded-xl border bg-white p-4"><label class="text-sm font-semibold">Calculate distance from<select v-model="filters.origin" class="ml-3 max-w-full rounded-lg border p-2" @change="search"><option value="none">Choose a city or institution</option><option v-for="o in distanceOrigins" :key="o.key" :value="o.key">{{ o.label }}</option></select></label><p class="mt-2 text-xs text-slate-500">Approximate straight-line distance using demo city pins; this is not driving distance or your home location.</p></div>
    <section class="mb-6">
        <LocationMap :points="markers" :selected-id="Number(props.filters.pin) || null" @select="selectPin" />
        <p class="mt-2 text-sm text-slate-500">
            {{ markers.length }} workplaces across all matching results. Demo locations
            use their company's assigned city pin and do not represent actual workplaces.
            Remote roles and roles awaiting a pin remain in the list.
        </p>
    </section>
    <p v-if="!hasCareerProfile" class="mb-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-900"><Link href="/student/profile" class="font-semibold underline">Complete your career profile</Link> to see your job fit percentages.</p>
    <div v-if="props.filters.pin" role="status" class="mb-4 flex items-center justify-between rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm"><p>{{ vacancies.total }} opportunities at the selected pin. All vacancies sharing this location appear below.</p><button class="ml-4 font-semibold text-teal-700" @click="selectPin()">Clear pin filter</button></div>
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
            <div class="my-3 flex flex-wrap items-center gap-3 text-sm"><span v-if="v.match" class="rounded-full bg-teal-100 px-3 py-1 font-bold text-teal-900">{{ v.match.score }}% fit</span><span v-else class="text-slate-500">Fit score unavailable</span><span class="rounded-full bg-slate-100 px-3 py-1">{{ v.arrangement === 'remote' ? 'Remote · no commute required' : v.distance_km != null ? `Approx. ${v.distance_km.toLocaleString()} km away` : filters.origin === 'none' ? 'Choose a starting location for distance' : 'Workplace distance unavailable' }}</span></div>
            <details v-if="v.match" class="my-3 rounded-lg bg-teal-50 p-3 text-sm"><summary class="cursor-pointer font-semibold text-teal-800">Why this fit score?</summary><p class="mt-2">70% skills coverage + 30% qualification level, based on your saved profile.</p><p class="mt-2">Skills coverage: {{ v.match.skills_score }}%. Qualification: {{ v.match.qualification_score == null ? 'Insufficient information (no qualification points)' : v.match.qualification_score === 100 ? 'Meets the minimum level' : 'Below the minimum level' }}.</p><p class="mt-2">Matched skills: {{ v.match.matched.join(', ') || 'None recorded' }}.</p><p class="mt-2">Skills to build: {{ v.match.missing.join(', ') || 'No listed skill gaps' }}.</p><p class="mt-2 text-xs text-slate-600">A profile-based indicator, not a hiring prediction. Distance does not change your fit score.</p></details>
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
            <details :open="!!props.filters.pin" class="my-4 text-sm">
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
