<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
defineProps<{ current: number; last: number; total: number; previous: string | null; next: string | null }>();
</script>
<template><div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 px-5 py-4 text-sm text-slate-500"><span>{{ total }} records · Page {{ current }} of {{ last }}</span><div class="flex gap-4"><Link v-if="previous" :href="previous" class="font-medium text-teal-700">Previous</Link><Link v-if="next" :href="next" class="font-medium text-teal-700">Next</Link></div></div></template>
