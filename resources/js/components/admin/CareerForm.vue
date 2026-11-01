<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import type { AdminCareer } from "@/types/admin";
const props = defineProps<{ career: AdminCareer | null }>();
const emit = defineEmits<{ close: [] }>();
const skillText = ref(props.career?.skills.join("\n") || "");
const form = useForm({
    title: props.career?.title || "",
    sector: props.career?.sector || "",
    description: props.career?.description || "",
    skills: props.career?.skills || ([] as string[]),
    is_active: props.career?.is_active ?? false,
});
function submit() {
    form.skills = skillText.value
        .split("\n")
        .map((skill) => skill.trim())
        .filter(Boolean);
    const options = { preserveScroll: true, onSuccess: () => emit("close") };
    if (props.career) form.put(`/admin/careers/${props.career.id}`, options);
    else form.post("/admin/careers", options);
}
</script>
<template>
    <section
        class="mb-8 rounded-xl border border-teal-200 bg-white p-6 md:p-8"
        aria-labelledby="career-form-title"
    >
        <div class="mb-6 flex items-center justify-between">
            <h2 id="career-form-title" class="text-xl font-semibold">
                {{ career ? "Edit career" : "Create career" }}
            </h2>
            <button
                type="button"
                class="text-sm text-slate-500"
                @click="emit('close')"
            >
                Cancel
            </button>
        </div>
        <form class="space-y-5" @submit.prevent="submit">
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="text-sm font-medium"
                    >Career title<input
                        v-model="form.title"
                        required
                        maxlength="180"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-3"
                    /><span class="text-xs text-red-600">{{
                        form.errors.title
                    }}</span></label
                ><label class="text-sm font-medium"
                    >Industry / sector<input
                        v-model="form.sector"
                        required
                        maxlength="180"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-3"
                    /><span class="text-xs text-red-600">{{
                        form.errors.sector
                    }}</span></label
                >
            </div>
            <label class="block text-sm font-medium"
                >Description<textarea
                    v-model="form.description"
                    required
                    maxlength="3000"
                    rows="3"
                    class="mt-2 block w-full rounded-lg border border-slate-300 p-3"
                /><span class="text-xs text-red-600">{{
                    form.errors.description
                }}</span></label
            ><label class="block text-sm font-medium"
                >Required skills
                <span class="font-normal text-slate-500">— one per line</span
                ><textarea
                    v-model="skillText"
                    required
                    rows="4"
                    class="mt-2 block w-full rounded-lg border border-slate-300 p-3"
                    placeholder="Troubleshooting&#10;Safety procedures"
                /><span class="text-xs text-red-600">{{
                    form.errors.skills
                }}</span></label
            ><label class="flex items-center gap-3 text-sm"
                ><input
                    v-model="form.is_active"
                    type="checkbox"
                    class="size-4 accent-teal-700"
                />Publish to student matching</label
            >
            <div
                v-if="Object.keys(form.errors).length"
                role="alert"
                class="rounded-lg bg-red-50 p-3 text-sm text-red-700"
            >
                <p v-for="(error, key) in form.errors" :key="key">
                    {{ error }}
                </p>
            </div>
            <button
                :disabled="form.processing"
                class="rounded-lg bg-teal-700 px-6 py-3 text-sm font-semibold text-white disabled:opacity-50"
            >
                {{ form.processing ? "Saving…" : "Save career" }}
            </button>
        </form>
    </section>
</template>
