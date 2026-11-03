<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import type { StudentProfile, CareerMatch } from "@/types/career";
import type { Vacancy } from "@/types/industry";
const props = defineProps<{
    profile:
        | (StudentProfile & {
              target_career_id: number | null;
              completed_steps: string[] | null;
          })
        | null;
    careers: (CareerMatch & { missing: string[] })[];
    vacancies: Vacancy[];
}>();
const selected = computed(() =>
    props.careers.find((c) => c.id === props.profile?.target_career_id),
);
function choose(id: number) {
    router.put(
        "/student/career-paths",
        { target_career_id: id, completed_steps: [] },
        { preserveScroll: true },
    );
}
function toggle(step: string) {
    const steps = props.profile?.completed_steps || [];
    router.put(
        "/student/career-paths",
        {
            target_career_id: selected.value?.id,
            completed_steps: steps.includes(step)
                ? steps.filter((s) => s !== step)
                : [...steps, step],
        },
        { preserveScroll: true },
    );
}
</script>
<template>
    <Head title="Career paths" />
    <h1 class="text-3xl font-semibold">Choose your career path</h1>
    <p class="my-4 text-slate-500">
        Set one target career. Explore its skills, plan your next steps and
        discover relevant vacancies.
    </p>
    <div class="grid gap-4 md:grid-cols-2">
        <article
            v-for="c in careers"
            :key="c.id"
            class="rounded-xl border bg-white p-5"
            :class="
                selected?.id === c.id
                    ? 'border-teal-600 ring-1 ring-teal-600'
                    : 'border-slate-200'
            "
        >
            <p class="text-xs text-teal-700">{{ c.sector }}</p>
            <h2 class="my-2 text-xl font-semibold">{{ c.title }}</h2>
            <p class="text-sm text-slate-500">{{ c.description }}</p>
            <p class="my-3 text-sm">
                {{ c.skills.length - c.missing.length }} of
                {{ c.skills.length }} skills in your profile
            </p>
            <button
                @click="choose(c.id)"
                :disabled="selected?.id === c.id"
                class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white disabled:bg-teal-50 disabled:text-teal-800"
            >
                {{
                    selected?.id === c.id
                        ? "Your target career ✓"
                        : "Choose this path"
                }}
            </button>
        </article>
    </div>
    <section v-if="selected" class="mt-8 rounded-2xl border bg-white p-6">
        <h2 class="text-2xl font-semibold">Your plan: {{ selected.title }}</h2>
        <p class="my-3 text-sm text-slate-500">
            Track learning progress here. Completing a step does not
            automatically verify or add a skill. Update your profile when you
            can demonstrate it.
        </p>
        <h3 class="mt-5 font-semibold">Skills to develop</h3>
        <label
            v-for="skill in selected.missing"
            :key="skill"
            class="my-3 flex items-start gap-3 rounded-lg bg-slate-50 p-4"
            ><input
                type="checkbox"
                :checked="profile?.completed_steps?.includes(skill)"
                @change="toggle(skill)"
                class="mt-1 accent-teal-700"
            /><span
                ><strong>{{ skill }}</strong
                ><span class="block text-sm text-slate-500"
                    >Complete introductory training, practise with a supervised
                    task and keep evidence of your work.</span
                ></span
            ></label
        >
        <p v-if="!selected.missing.length" class="my-3 text-teal-700">
            Your profile includes all listed skills. Keep practising and
            building evidence.
        </p>
        <label class="my-4 flex gap-3"
            ><input
                type="checkbox"
                :checked="profile?.completed_steps?.includes('project')"
                @change="toggle('project')"
            /><span
                >Build a practical project demonstrating
                {{ selected.skills.slice(0, 2).join(" and ") }}.</span
            ></label
        ><label class="my-4 flex gap-3"
            ><input
                type="checkbox"
                :checked="profile?.completed_steps?.includes('qualification')"
                @change="toggle('qualification')"
            /><span
                >Discuss relevant certificates, diploma programmes and required
                licences with your institution. Check the requirements of
                individual vacancies.</span
            ></label
        ><Link href="/student/profile" class="font-semibold text-teal-700"
            >Update my career profile →</Link
        >
        <h3 class="mt-8 text-xl font-semibold">
            Jobs linked to your target career
        </h3>
        <article
            v-for="v in vacancies"
            :key="v.id"
            class="mt-4 rounded-lg border p-4"
        >
            <h4 class="font-semibold">{{ v.title }}</h4>
            <p class="text-sm text-slate-500">
                {{ v.company?.name }} · {{ v.location }}
            </p>
            <p class="mt-2 text-sm">
                Skills to develop:
                {{
                    v.skills
                        .filter((s) => !profile?.skills?.includes(s))
                        .join(", ") || "Your profile includes all listed skills"
                }}
            </p>
            <Link
                :href="`/opportunities?search=${encodeURIComponent(v.title)}`"
                class="mt-2 inline-block text-sm font-semibold text-teal-700"
                >View opportunity →</Link
            >
        </article>
        <p v-if="!vacancies.length" class="my-4 text-sm text-slate-500">
            No open vacancies linked to this career yet.
        </p>
    </section>
</template>
