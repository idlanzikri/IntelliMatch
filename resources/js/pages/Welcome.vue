<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, ArrowUpRight, Check, Compass, GraduationCap, Route, Sparkles } from '@lucide/vue';
const steps = [
    { number: '01', title: 'Start with your strengths', text: 'Tell us about your programme, practical skills, and the work that interests you.' },
    { number: '02', title: 'Explore your possibilities', text: 'Compare your skills with career profiles and understand why a path could suit you.' },
    { number: '03', title: 'Plan your next move', text: 'See the skills you still need to develop and take your next step with direction.' },
];
</script>
<template>
    <Head title="Find your direction" />
    <div class="min-h-screen bg-[#fafcfd] text-[#102d49]">
        <header class="border-b border-slate-200 bg-white"><nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-4 lg:px-10" aria-label="Main navigation">
            <Link href="/" class="flex items-center gap-2"><img src="/brand/intellimatch.jpeg" alt="" class="size-12 rounded-lg object-contain" /><span class="text-xl font-bold tracking-tight">Intelli<span class="text-teal-600">Match</span></span></Link>
            <div class="hidden gap-8 text-sm font-medium md:flex"><a href="#how-it-works" class="hover:text-teal-600">How it works</a><a href="#possibilities" class="hover:text-teal-600">For students</a><a href="#institutions" class="hover:text-teal-600">For institutions</a></div>
            <div class="flex items-center gap-5"><Link href="/login" class="text-sm font-semibold">Log in</Link><Link href="/register" class="hidden rounded-lg bg-[#102d49] px-5 py-3 text-sm font-semibold text-white sm:block">Get started <span aria-hidden="true">↗</span></Link></div>
        </nav></header>
        <main>
            <section class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.15fr_1fr] lg:px-10 lg:py-24">
                <div><p class="mb-6 flex items-center gap-2 text-xs font-bold tracking-[0.18em] text-teal-700 uppercase"><span class="h-2 w-2 rounded-full bg-teal-500"></span> Made for the next generation of TVET talent</p>
                    <h1 class="max-w-2xl text-5xl leading-[1.08] font-semibold tracking-tight sm:text-6xl lg:text-7xl">Your skills.<br />Your potential.<br /><span class="text-teal-600">Your next chapter.</span></h1>
                    <p class="mt-7 max-w-lg text-lg leading-8 text-slate-500">You are learning skills that matter. Discover where they can take you—with career guidance built around your strengths.</p>
                    <div class="mt-8 flex flex-wrap items-center gap-6"><Link href="/register" class="inline-flex items-center gap-3 rounded-lg bg-teal-700 px-6 py-4 font-semibold text-white transition hover:bg-teal-800">Find my direction <ArrowUpRight :size="20" /></Link><a href="#how-it-works" class="inline-flex items-center gap-2 text-sm font-semibold">See how it works <ArrowRight :size="17" /></a></div>
                    <p class="mt-6 flex items-center gap-2 text-sm text-slate-500"><Check :size="16" class="text-teal-600" /> Start with your profile. No resume needed.</p>
                </div>
                <div class="relative rounded-[2rem] border border-cyan-100 bg-white p-8 shadow-[0_25px_80px_-40px_#007c91] sm:p-12">
                    <p class="text-xs font-semibold tracking-[0.2em] text-teal-700 uppercase">Connect learning to possibility</p>
                    <img src="/brand/intellimatch.jpeg" alt="IntelliMatch — Your AI-powered career guide for TVET students" class="my-5 w-full" />
                    <div class="flex items-center gap-4 border-t border-slate-100 pt-5"><div class="rounded-full bg-teal-50 p-3"><Compass class="text-teal-600" :size="24" /></div><div><p class="font-semibold">A clearer path forward</p><p class="mt-1 text-sm text-slate-500">Built around what you can do.</p></div></div>
                </div>
            </section>
            <section id="possibilities" class="bg-[#092c4f] text-white"><div class="mx-auto grid max-w-7xl gap-8 px-6 py-10 md:grid-cols-3 lg:px-10"><div class="flex gap-4"><GraduationCap class="shrink-0 text-cyan-300" /><div><h2 class="font-semibold">Your learning has a future</h2><p class="mt-2 text-sm leading-6 text-blue-100">Connect practical training to career possibilities.</p></div></div><div class="flex gap-4"><Compass class="shrink-0 text-cyan-300" /><div><h2 class="font-semibold">Understand your fit</h2><p class="mt-2 text-sm leading-6 text-blue-100">See matching skills and areas to develop.</p></div></div><div class="flex gap-4"><Route class="shrink-0 text-cyan-300" /><div><h2 class="font-semibold">Move forward with direction</h2><p class="mt-2 text-sm leading-6 text-blue-100">Use your skill gaps to guide what you learn next.</p></div></div></div></section>
            <section id="how-it-works" class="mx-auto max-w-7xl px-6 py-20 lg:px-10"><p class="mb-3 text-xs font-bold tracking-[0.2em] text-teal-700 uppercase">A little clarity goes a long way</p><h2 class="max-w-xl text-4xl leading-tight font-semibold tracking-tight">From “what comes next?”<br />to a place to start.</h2><div class="mt-12 grid gap-10 md:grid-cols-3"><article v-for="step in steps" :key="step.number" class="border-t border-slate-200 pt-6"><span class="text-sm font-semibold text-teal-600">{{ step.number }}</span><h3 class="mt-5 text-xl font-semibold">{{ step.title }}</h3><p class="mt-3 leading-7 text-slate-500">{{ step.text }}</p></article></div></section>
            <section id="institutions" class="mx-auto max-w-7xl px-6 pb-20 lg:px-10"><div class="grid items-center gap-8 rounded-3xl bg-[#eaf5f5] p-8 md:grid-cols-[1fr_auto] md:p-12"><div><p class="mb-3 flex items-center gap-2 text-xs font-bold tracking-widest text-teal-700 uppercase"><Sparkles :size="16" /> For TVET institutions</p><h2 class="text-3xl font-semibold">Better guidance starts with understanding.</h2><p class="mt-4 max-w-2xl leading-7 text-slate-600">Our vision is to help educators connect student strengths with industry needs. Institutional reporting and AI resume guidance are planned for a future release.</p></div><Link href="/register" class="inline-flex items-center gap-3 font-semibold text-teal-800">Explore as a student <ArrowUpRight :size="20" /></Link></div></section>
        </main>
        <footer class="border-t border-slate-200 bg-white"><div class="mx-auto flex max-w-7xl flex-col justify-between gap-4 px-6 py-7 text-sm text-slate-500 sm:flex-row lg:px-10"><span class="font-semibold text-slate-800">IntelliMatch <span class="ml-2 font-normal text-slate-500">Learning today. Possibilities tomorrow.</span></span><span>TVET Career Guidance · Group 9</span></div></footer>
    </div>
</template>
