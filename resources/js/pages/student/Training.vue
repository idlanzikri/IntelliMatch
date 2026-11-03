<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { reactive, ref } from "vue";
import LocationMap from "@/components/LocationMap.vue";
import TablePagination from "@/components/admin/TablePagination.vue";
import type { Paginated } from "@/types/admin";
type Institution = {
    id: number;
    name: string;
    address: string | null;
    state: string | null;
    latitude: number | null;
    longitude: number | null;
    courses_count: number;
    agency: { name: string };
};
const props = defineProps<{
    institutions: Paginated<Institution>;
    markers: {
        id: number;
        name: string;
        latitude: number;
        longitude: number;
    }[];
    agencies: { id: number; name: string }[];
    states: string[];
    filters: Record<string, string>;
    canManage: boolean;
}>();
const filters = reactive({
    search: props.filters.search || "",
    agency: props.filters.agency || "",
    state: props.filters.state || "",
});
const mapOpen = ref(false);
</script>
<template>
    <Head title="Explore TVET training" />
    <h1 class="text-3xl font-semibold">Explore TVET training</h1>
    <p class="mt-3 text-slate-500">
        Find institutions and courses to support your career goals.
    </p>
    <form
        @submit.prevent="
            router.get('/training', filters, { preserveState: true })
        "
        class="my-6 grid gap-3 md:grid-cols-4"
    >
        <input
            v-model="filters.search"
            aria-label="Institution or course"
            placeholder="Institution or course keyword"
            class="rounded-lg border p-3"
        /><select
            v-model="filters.agency"
            aria-label="Agency"
            class="rounded-lg border p-3"
        >
            <option value="">All agencies</option>
            <option v-for="a in agencies" :key="a.id" :value="a.id">
                {{ a.name }}
            </option></select
        ><select
            v-model="filters.state"
            aria-label="State"
            class="rounded-lg border p-3"
        >
            <option value="">All states</option>
            <option v-for="s in states" :key="s">{{ s }}</option></select
        ><button class="rounded-lg bg-teal-700 p-3 text-white">
            Search training
        </button>
    </form>
    <div class="my-4 flex justify-between">
        <p>
            {{ institutions.total }} institutions · {{ markers.length }} with
            campus pins
        </p>
        <button class="font-semibold text-teal-700" @click="mapOpen = !mapOpen">
            {{ mapOpen ? "Hide map" : "Show map" }}
        </button>
    </div>
    <div v-if="mapOpen" class="mb-6">
        <LocationMap
            :points="markers"
            @select="(id) => router.visit(`/training/${id}`)"
        />
        <p class="mt-2 text-sm text-slate-500">
            Only campuses with confirmed coordinates appear. All matching
            institutions remain in the list.
        </p>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <article
            v-for="i in institutions.data"
            :key="i.id"
            class="rounded-xl border bg-white p-5"
        >
            <p class="text-xs text-teal-700">{{ i.agency.name }}</p>
            <h2 class="my-2 font-semibold">{{ i.name }}</h2>
            <p class="text-sm text-slate-500">{{ i.state }} {{ i.address }}</p>
            <p class="my-3 text-sm">
                {{ i.courses_count }} course records ·
                {{
                    i.latitude == null ? "Campus pin pending" : "Campus mapped"
                }}
            </p>
            <Link
                :href="`/training/${i.id}`"
                class="font-semibold text-teal-700"
                >View courses{{ canManage ? " / set campus pin" : "" }} →</Link
            >
        </article>
    </div>
    <p v-if="!institutions.data.length" class="p-8 text-center">
        No institutions match your search.
    </p>
    <TablePagination
        :current="institutions.current_page"
        :last="institutions.last_page"
        :total="institutions.total"
        :previous="institutions.prev_page_url"
        :next="institutions.next_page_url"
    />
    <p class="mt-5 text-xs text-slate-500">
        Source: TVET Madani, retrieved 3 November 2026. Confirm current course
        availability with the institution.
    </p>
</template>
