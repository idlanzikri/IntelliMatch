<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ChartNoAxesCombined, GraduationCap, Compass, ArrowUpRight } from '@lucide/vue';
const page = usePage();
const navigation = [{ title: 'Company approvals', href: '/admin/companies', icon: Compass }, { title: 'Overview', href: '/admin', icon: ChartNoAxesCombined }, { title: 'Students', href: '/admin/students', icon: GraduationCap }, { title: 'Career catalogue', href: '/admin/careers', icon: Compass }];
</script>
<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex max-w-[1500px] items-center justify-between px-5 py-4 md:px-9"><Link href="/admin" class="flex items-center gap-3"><img src="/brand/intellimatch.jpeg" alt="" class="size-11 rounded-lg object-contain" /><div><span class="text-xl font-bold">Intelli<span class="text-teal-600">Match</span></span><p class="text-[10px] font-semibold tracking-[0.2em] text-slate-500 uppercase">Administration</p></div></Link><div class="flex items-center gap-5 text-sm"><span class="hidden text-slate-500 sm:block">{{ page.props.auth.user.name }}</span><Link href="/dashboard" class="flex items-center gap-2 font-medium text-teal-700">Student view <ArrowUpRight :size="16" /></Link><Link href="/logout" method="post" as="button" class="font-medium">Log out</Link></div></div></header>
        <div class="mx-auto flex max-w-[1500px] flex-col md:flex-row"><aside class="border-b border-slate-200 p-4 md:min-h-[calc(100vh-78px)] md:w-60 md:shrink-0 md:border-r md:border-b-0 md:p-6"><p class="mb-5 hidden text-xs font-semibold tracking-widest text-slate-400 uppercase md:block">Workspace</p><nav aria-label="Admin navigation" class="flex gap-2 overflow-x-auto md:flex-col"><Link v-for="item in navigation" :key="item.href" :href="item.href" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium whitespace-nowrap" :class="(item.href === '/admin' ? page.url === '/admin' : page.url.startsWith(item.href)) ? 'bg-teal-50 text-teal-800' : 'text-slate-500 hover:bg-slate-100'"><component :is="item.icon" :size="19" />{{ item.title }}</Link></nav></aside><main class="min-w-0 flex-1 p-5 md:p-10"><slot /></main></div>
    </div>
</template>
