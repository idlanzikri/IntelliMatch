<script setup lang="ts">
import type { CareerMatch } from '@/types/career';
defineProps<{ career: CareerMatch; hasSkills: boolean }>();
</script>
<template>
    <article class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-sm">
        <div class="mb-5 flex items-start justify-between gap-4">
            <span class="text-xs font-semibold tracking-wider text-teal-700 uppercase">{{ career.sector }}</span>
            <span v-if="hasSkills" class="whitespace-nowrap rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800">{{ career.score }}% skill coverage</span>
        </div>
        <h3 class="mb-2 text-xl font-semibold">{{ career.title }}</h3>
        <p class="mb-5 text-sm leading-6 text-slate-600">{{ career.description }}</p>
        <div v-if="hasSkills && career.matched.length" class="mb-4">
            <p class="mb-2 text-xs font-semibold text-slate-500 uppercase">Your matching skills</p>
            <div class="flex flex-wrap gap-2"><span v-for="skill in career.matched" :key="skill" class="rounded-md bg-teal-50 px-2 py-1 text-xs text-teal-800">{{ skill }}</span></div>
        </div>
        <p class="mb-2 text-xs font-semibold text-slate-500 uppercase">{{ hasSkills ? 'Skills to develop' : 'Skills to explore' }}</p>
        <div class="flex flex-wrap gap-2"><span v-for="skill in career.missing" :key="skill" class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ skill }}</span></div>
    </article>
</template>
