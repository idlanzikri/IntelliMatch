<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import type { StudentProfile } from "@/types/career";
const props = defineProps<{
    profile: StudentProfile | null;
    availableSkills: string[];
}>();
const form = useForm({
    qualification_level: props.profile?.qualification_level || '',
    institution: props.profile?.institution || "",
    programme: props.profile?.programme || "",
    location: props.profile?.location || "",
    interests: props.profile?.interests || "",
    skills: props.profile?.skills || ([] as string[]),
});
defineOptions({
    layout: {
        breadcrumbs: [{ title: "My career profile", href: "/student/profile" }],
    },
});
</script>
<template>
    <Head title="My career profile" />
    <main class="min-h-full bg-slate-50 p-6 text-slate-900 md:p-10">
        <div class="mx-auto max-w-3xl">
            <p
                class="text-xs font-semibold tracking-widest text-teal-700 uppercase"
            >
                Built around you
            </p>
            <h1 class="mt-2 text-3xl font-semibold">My career profile</h1>
            <p class="mt-3 mb-8 text-slate-500">
                Tell us what you are learning and what you can do. You can
                update this anytime.
            </p>
            <form
                class="space-y-8 rounded-2xl border border-slate-200 bg-white p-6 md:p-8"
                @submit.prevent="form.put('/student/profile')"
            >
                <section><label class="mb-5 block text-sm font-medium">Highest qualification held<select v-model="form.qualification_level" class="mt-2 block w-full rounded-lg border p-3"><option value="">Not specified</option><option value="certificate">Certificate</option><option value="diploma">Diploma</option><option value="degree">Degree</option></select><span class="text-red-600">{{ form.errors.qualification_level }}</span></label>
                    <h2 class="mb-5 text-xl font-semibold">Your education</h2>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="space-y-2 text-sm font-medium"
                            >Institution<input
                                v-model="form.institution"
                                maxlength="180"
                                class="block w-full rounded-lg border border-slate-300 p-3"
                                placeholder="Your college or training centre"
                            /><span class="text-red-600">{{
                                form.errors.institution
                            }}</span></label
                        >
                        <label class="space-y-2 text-sm font-medium"
                            >Programme<input
                                v-model="form.programme"
                                maxlength="180"
                                class="block w-full rounded-lg border border-slate-300 p-3"
                                placeholder="e.g. Diploma in Electrical Engineering"
                            /><span class="text-red-600">{{
                                form.errors.programme
                            }}</span></label
                        >
                        <label class="space-y-2 text-sm font-medium"
                            >Preferred location<input
                                v-model="form.location"
                                maxlength="180"
                                class="block w-full rounded-lg border border-slate-300 p-3"
                                placeholder="e.g. Melaka"
                            /><span class="text-red-600">{{
                                form.errors.location
                            }}</span></label
                        >
                    </div>
                </section>
                <fieldset>
                    <legend class="text-xl font-semibold">
                        Your practical skills
                    </legend>
                    <p class="mt-2 mb-4 text-sm text-slate-500">
                        Choose skills you already have. These are self-reported
                        and have not been verified.
                    </p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label
                            v-for="skill in availableSkills"
                            :key="skill"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 p-3 text-sm"
                            ><input
                                v-model="form.skills"
                                type="checkbox"
                                :value="skill"
                                class="size-4 accent-teal-600"
                            />{{ skill }}</label
                        >
                    </div>
                    <p class="mt-2 text-sm text-red-600">
                        {{ form.errors.skills }}
                    </p>
                </fieldset>
                <label class="block text-sm font-medium"
                    >Career interests<textarea
                        v-model="form.interests"
                        maxlength="2000"
                        rows="4"
                        class="mt-2 block w-full rounded-lg border border-slate-300 p-3"
                        placeholder="What kind of work would you like to explore?"
                    /><span class="text-red-600">{{
                        form.errors.interests
                    }}</span></label
                >
                <p
                    v-if="Object.keys(form.errors).length"
                    role="alert"
                    class="text-sm text-red-600"
                >
                    Please review the highlighted fields.
                </p>
                <div class="flex items-center gap-5">
                    <button
                        :disabled="form.processing"
                        class="rounded-lg bg-teal-700 px-6 py-3 font-semibold text-white disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? "Saving…"
                                : "Save and view my matches"
                        }}</button
                    ><Link href="/dashboard" class="text-sm text-slate-600"
                        >Cancel</Link
                    >
                </div>
            </form>
        </div>
    </main>
</template>
