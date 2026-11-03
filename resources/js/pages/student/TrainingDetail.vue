<script setup lang="ts">
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import LocationMap from "@/components/LocationMap.vue";
const props = defineProps<{
    institution: {
        id: number;
        name: string;
        address: string | null;
        state: string | null;
        latitude: number | null;
        location_is_approximate: boolean;
        longitude: number | null;
        agency: { name: string };
        courses: { id: number; name: string; source_url: string }[];
    };
}>();
const page = usePage();
const search = ref("");
const courses = computed(() =>
    props.institution.courses.filter((c) =>
        c.name.toLowerCase().includes(search.value.toLowerCase()),
    ),
);
const form = useForm({
    latitude: props.institution.latitude,
    longitude: props.institution.longitude,
});
</script>
<template>
    <Head :title="institution.name" /><Link
        href="/training"
        class="text-teal-700"
        >← Explore TVET training</Link
    >
    <p class="mt-6 text-sm text-teal-700">{{ institution.agency.name }}</p>
    <h1 class="my-3 text-2xl font-semibold">{{ institution.name }}</h1>
    <p v-if="institution.location_is_approximate" class="mb-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">Demo location: a randomly assigned city within the institution's state. This pin does not represent its actual address.</p>
    <p class="mb-5 text-slate-500">
        {{ institution.address }} {{ institution.state }}
    </p>
    <form
        v-if="page.props.auth.user.is_admin"
        @submit.prevent="form.put(`/training/${institution.id}/location`)"
        class="my-6 space-y-3 rounded-xl border bg-white p-5"
    >
        <h2 class="font-semibold">Confirm campus location</h2>
        <LocationMap
            editable
            :latitude="form.latitude"
            :longitude="form.longitude"
            @pick="
                (lat, lng) => {
                    form.latitude = lat;
                    form.longitude = lng;
                }
            "
        />
        <div class="flex flex-wrap gap-3">
            <label
                >Latitude<input
                    v-model="form.latitude"
                    required
                    type="number"
                    step="any"
                    min="-90"
                    max="90"
                    class="block rounded border p-2" /></label
            ><label
                >Longitude<input
                    v-model="form.longitude"
                    required
                    type="number"
                    step="any"
                    min="-180"
                    max="180"
                    class="block rounded border p-2"
            /></label>
        </div>
        <p v-for="e in form.errors" :key="e" class="text-red-700">{{ e }}</p>
        <button
            :disabled="form.processing"
            class="rounded bg-teal-700 px-4 py-2 text-white"
        >
            Save confirmed pin
        </button>
    </form>
    <LocationMap
        v-else-if="
            institution.latitude != null && institution.longitude != null
        "
        :latitude="institution.latitude"
        :longitude="institution.longitude"
        :points="[
            {
                id: institution.id,
                name: institution.name,
                latitude: institution.latitude,
                longitude: institution.longitude,
            },
        ]"
    />
    <h2 class="my-5 text-xl font-semibold">
        Courses ({{ institution.courses.length }})
    </h2>
    <input
        v-model="search"
        aria-label="Search courses"
        placeholder="Search courses"
        class="mb-4 w-full rounded-lg border p-3"
    />
    <ul class="divide-y rounded-xl border bg-white">
        <li v-for="c in courses" :key="c.id" class="p-4">
            <a
                :href="c.source_url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-teal-700"
                >{{ c.name }} ↗</a
            >
        </li>
    </ul>
    <p v-if="!courses.length" class="my-4">
        No courses listed for this selection.
    </p>
</template>
