<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import LocationMap from "@/components/LocationMap.vue";
import { ref } from "vue";
import type { Vacancy } from "@/types/industry";

const props = defineProps<{
    vacancy: Vacancy | null;
    careers: { id: number; title: string }[];
    approved: boolean;
}>();

const emit = defineEmits<{ close: [] }>();
const skills = ref(props.vacancy?.skills.join("\n") || "");

const form = useForm({
    latitude: props.vacancy?.latitude ?? (null as number | null),
    longitude: props.vacancy?.longitude ?? (null as number | null),
    qualification_level: props.vacancy?.qualification_level || "",
    title: props.vacancy?.title || "",
    description: props.vacancy?.description || "",
    type: props.vacancy?.type || "full-time",
    arrangement: props.vacancy?.arrangement || "on-site",
    location: props.vacancy?.location || "",
    career_id: props.vacancy?.career_id ?? (null as number | null),
    salary_min: props.vacancy?.salary_min ?? (null as number | null),
    salary_max: props.vacancy?.salary_max ?? (null as number | null),
    qualifications: props.vacancy?.qualifications || "",
    skills: [] as string[],
    closing_date: props.vacancy?.closing_date?.slice(0, 10) || "",
    application_url: props.vacancy?.application_url || "",
    status: props.vacancy?.status || "draft",
});

function submit() {
    form.skills = skills.value
        .split("\n")
        .map((s) => s.trim())
        .filter(Boolean);
    const options = { onSuccess: () => emit("close") };
    if (props.vacancy)
        form.put(`/industry/vacancies/${props.vacancy.id}`, options);
    else form.post("/industry/vacancies", options);
}
</script>
<template>
    <section class="my-7 rounded-xl border border-teal-200 bg-white p-7">
        <div class="mb-6 flex justify-between">
            <h2 class="text-xl font-semibold">
                {{ vacancy ? "Edit vacancy" : "New vacancy" }}
            </h2>
            <button @click="emit('close')">Cancel</button>
        </div>
        <form class="space-y-5" @submit.prevent="submit">
            <div class="grid gap-5 md:grid-cols-2">
                <label class="text-sm font-medium"
                    >Job title<input
                        v-model="form.title"
                        required
                        maxlength="180"
                        class="mt-2 block w-full rounded-lg border p-3" /></label
                ><label class="text-sm font-medium"
                    >Location<input
                        v-model="form.location"
                        required
                        maxlength="180"
                        class="mt-2 block w-full rounded-lg border p-3" /></label
                ><label class="text-sm font-medium"
                    >Employment type<select
                        v-model="form.type"
                        class="mt-2 block w-full rounded-lg border p-3"
                    >
                        <option
                            v-for="type in [
                                'full-time',
                                'part-time',
                                'contract',
                                'internship',
                            ]"
                            :key="type"
                        >
                            {{ type }}
                        </option>
                    </select></label
                ><label class="text-sm font-medium"
                    >Work arrangement<select
                        v-model="form.arrangement"
                        class="mt-2 block w-full rounded-lg border p-3"
                    >
                        <option
                            v-for="value in ['on-site', 'hybrid', 'remote']"
                            :key="value"
                        >
                            {{ value }}
                        </option>
                    </select></label
                ><label class="text-sm font-medium"
                    >Monthly salary minimum (MYR, optional)<input
                        v-model="form.salary_min"
                        type="number"
                        min="0"
                        class="mt-2 block w-full rounded-lg border p-3" /></label
                ><label class="text-sm font-medium"
                    >Monthly salary maximum (MYR, optional)<input
                        v-model="form.salary_max"
                        type="number"
                        min="0"
                        class="mt-2 block w-full rounded-lg border p-3" /></label
                ><label class="text-sm font-medium"
                    >Closing date<input
                        v-model="form.closing_date"
                        required
                        type="date"
                        class="mt-2 block w-full rounded-lg border p-3" /></label
                ><label class="text-sm font-medium"
                    >Related career (optional)<select
                        v-model="form.career_id"
                        class="mt-2 block w-full rounded-lg border p-3"
                    >
                        <option :value="null">No linked career</option>
                        <option
                            v-for="career in careers"
                            :key="career.id"
                            :value="career.id"
                        >
                            {{ career.title }}
                        </option>
                    </select></label
                >
            </div>
            <section v-if="form.arrangement !== 'remote'" class="space-y-3">
                <h3 class="font-semibold">Actual workplace location</h3>
                <LocationMap editable :latitude="form.latitude" :longitude="form.longitude" @pick="(lat, lng) => { form.latitude = lat; form.longitude = lng; }" />
                <div class="grid gap-3 md:grid-cols-2"><label>Latitude<input v-model="form.latitude" type="number" step="any" min="-90" max="90" class="block w-full rounded border p-2" /></label><label>Longitude<input v-model="form.longitude" type="number" step="any" min="-180" max="180" class="block w-full rounded border p-2" /></label></div>
                <button type="button" class="text-sm text-teal-700" @click="form.latitude = null; form.longitude = null">Clear pin</button>
                <p class="text-sm text-slate-500">A workplace pin is required to publish on-site and hybrid roles. Drafts can be saved without a pin.</p>
            </section>
            <label class="block text-sm font-medium"
                >Job description<textarea
                    v-model="form.description"
                    required
                    rows="4"
                    maxlength="5000"
                    class="mt-2 block w-full rounded-lg border p-3"
                /></label
            ><label class="block text-sm font-medium"
                >Required skills (one per line)<textarea
                    v-model="skills"
                    required
                    rows="3"
                    class="mt-2 block w-full rounded-lg border p-3"
                /></label
            ><label class="block text-sm font-medium"
                >Qualifications and certificates<textarea
                    v-model="form.qualifications"
                    rows="3"
                    maxlength="2000"
                    class="mt-2 block w-full rounded-lg border p-3"
                /></label
            ><label class="block text-sm font-medium"
                >Minimum qualification level<select
                    v-model="form.qualification_level"
                    class="mt-2 block w-full rounded-lg border p-3"
                >
                    <option value="">Not specified</option>
                    <option value="certificate">Certificate</option>
                    <option value="diploma">Diploma</option>
                    <option value="degree">Degree</option>
                </select></label
            ><label class="block text-sm font-medium"
                >Company careers URL (optional)<input
                    v-model="form.application_url"
                    type="url"
                    maxlength="1000"
                    placeholder="https://company.example/apply"
                    class="mt-2 block w-full rounded-lg border p-3" /></label
            ><label class="block text-sm font-medium"
                >Status<select
                    v-model="form.status"
                    class="mt-2 block w-full rounded-lg border p-3"
                >
                    <option value="draft">Draft</option>
                    <option value="published" :disabled="!approved">
                        Published (approved companies only)
                    </option>
                    <option value="closed">Closed</option>
                    <option value="archived">Archived</option>
                </select></label
            >
            <div
                v-if="Object.keys(form.errors).length"
                role="alert"
                class="rounded-lg bg-red-50 p-4 text-sm text-red-700"
            >
                <p v-for="(error, key) in form.errors" :key="key">
                    {{ error }}
                </p>
            </div>
            <button
                :disabled="form.processing"
                class="rounded-lg bg-teal-700 px-6 py-3 font-semibold text-white disabled:opacity-50"
            >
                {{ form.processing ? "Saving…" : "Save vacancy" }}
            </button>
        </form>
    </section>
</template>
