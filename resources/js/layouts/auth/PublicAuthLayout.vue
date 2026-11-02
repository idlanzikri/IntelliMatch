<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
defineProps<{ title?: string; description?: string }>();
</script>

<template>
    <div class="public-auth flex min-h-dvh flex-col bg-white text-[#102d49]">
        <header class="border-b border-slate-200 px-6 py-2">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                <Link href="/" class="flex items-center gap-2"><img src="/brand/intellimatch.jpeg" alt="" class="size-10 rounded-lg object-contain" /><span class="text-xl font-bold tracking-tight">Intelli<span class="text-teal-600">Match</span></span></Link>
                <Link href="/" class="text-sm font-semibold text-teal-700 hover:text-teal-800">← Back to home</Link>
            </div>
        </header>
        <main class="flex flex-1 items-center justify-center px-4 py-4 sm:px-6">
            <div class="w-full max-w-lg rounded-[2rem] border border-cyan-100 bg-white p-6 shadow-[0_25px_80px_-40px_#007c91] sm:px-8 sm:py-6">
                <p class="mb-2 text-xs font-bold tracking-widest text-teal-700 uppercase">Welcome back</p>
                <h1 class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
                <p class="mt-2 mb-5 text-sm leading-6 text-slate-500">{{ description }}</p>
                <slot />
            </div>
        </main>
        <footer class="border-t border-slate-100 px-6 py-3 text-center text-sm text-slate-500">Learning today. Possibilities tomorrow.</footer>
    </div>
</template>

<style scoped>
.public-auth {
    color-scheme: light;
    --background: #fff;
    --foreground: #102d49;
    --primary: #0f766e;
    --primary-foreground: #fff;
    --muted-foreground: #64748b;
    --border: #cbd5e1;
    --input: #cbd5e1;
    --ring: #0d9488;
}
</style>
