<script setup lang="ts">
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    ChevronDown,
    Check,
    ShieldCheck,
    UserCircle,
    LogOut,
    Menu,
    LayoutGrid,
    Compass,
    GraduationCap,
    Building2,
} from "@lucide/vue";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuItem,
} from "@/components/ui/dropdown-menu";
import type { BreadcrumbItem } from "@/types";

const props = defineProps<{
    workspace?: "admin" | "industry" | "institution" | "student";
    breadcrumbs?: BreadcrumbItem[];
}>();

const page = usePage();
const open = ref(false);

const role = computed(
    () =>
        props.workspace ??
        (page.props.auth.user.is_admin
            ? "admin"
            : ["industry", "institution"].includes(
                    String(page.props.auth.user.role),
                )
              ? String(page.props.auth.user.role)
              : "student"),
);

const title = computed(
    () => role.value.charAt(0).toUpperCase() + role.value.slice(1),
);

const initials = computed(() =>
    page.props.auth.user.name
        .split(" ")
        .map((n) => n[0])
        .slice(0, 2)
        .join("")
        .toUpperCase(),
);

const navigation = computed(() => {
    if (role.value === "admin")
        return [
            { title: "Overview", href: "/admin", icon: LayoutGrid },
            {
                title: "Company approvals",
                href: "/admin/companies",
                icon: Building2,
            },
            { title: "Students", href: "/admin/students", icon: GraduationCap },
            {
                title: "Career catalogue",
                href: "/admin/careers",
                icon: Compass,
            },
        ];

    if (role.value === "institution")
        return [
            { title: "Overview", href: "/institution", icon: LayoutGrid },
            {
                title: "Students",
                href: "/institution#students",
                icon: GraduationCap,
            },
        ];

    if (role.value === "industry")
        return [
            { title: "My vacancies", href: "/industry", icon: Building2 },
            {
                title: "Browse opportunities",
                href: "/opportunities",
                icon: Compass,
            },
        ];

    return [
        { title: "My dashboard", href: "/dashboard", icon: LayoutGrid },
        { title: "Job opportunities", href: "/opportunities", icon: Compass },
        {
            title: "My career profile",
            href: "/student/profile",
            icon: GraduationCap,
        },
        { title: "Explore careers", href: "/dashboard#careers", icon: Compass },
    ];
});

const active = (href: string) =>
    href.includes("#") ? page.url === href : page.url.split("?")[0] === href;
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <aside
            :class="open ? 'flex' : 'hidden md:flex'"
            class="fixed inset-y-0 left-0 z-40 w-64 flex-col border-r border-slate-200 bg-white"
        >
            <Link
                :href="navigation[0].href"
                class="flex h-24 items-center gap-3 border-b border-slate-100 px-6"
                ><img
                    src="/brand/intellimatch.jpeg"
                    alt=""
                    class="size-11 rounded-lg object-contain"
                />
                <div>
                    <span class="text-xl font-bold text-[#082b50]"
                        >Intelli<span class="text-teal-600">Match</span></span
                    >
                    <p
                        class="mt-1 text-[10px] font-semibold tracking-widest text-slate-400 uppercase"
                    >
                        {{ title }} workspace
                    </p>
                </div></Link
            >
            <div class="flex-1 p-4">
                <p
                    class="px-3 pt-5 pb-4 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                >
                    Workspace
                </p>
                <nav :aria-label="`${title} navigation`" class="space-y-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        @click="open = false"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition-colors"
                        :class="
                            active(item.href)
                                ? 'bg-teal-50 text-teal-800'
                                : 'text-slate-500 hover:bg-slate-50 hover:text-teal-700'
                        "
                        ><component :is="item.icon" :size="19" />{{
                            item.title
                        }}</Link
                    >
                </nav>
            </div>
            <p
                class="border-t border-slate-100 px-7 py-6 text-xs text-slate-400"
            >
                IntelliMatch · Your next chapter
            </p>
        </aside>
        <button
            v-if="open"
            aria-label="Close navigation"
            class="fixed inset-0 z-30 bg-slate-900/30 md:hidden"
            @click="open = false"
        />
        <div class="md:ml-64">
            <header
                class="sticky top-0 z-20 flex h-24 items-center justify-between gap-3 border-b border-slate-100 bg-white px-5 md:px-9"
            >
                <div class="flex items-center gap-3">
                    <button
                        aria-label="Toggle navigation"
                        :aria-expanded="open"
                        class="rounded-lg p-2 text-teal-800 md:hidden"
                        @click="open = !open"
                    >
                        <Menu :size="22" /></button
                    ><span class="text-sm font-medium text-slate-500">{{
                        breadcrumbs?.at(-1)?.title ?? `${title} workspace`
                    }}</span>
                </div>
                <DropdownMenu
                    ><DropdownMenuTrigger as-child
                        ><button
                            class="flex items-center gap-3 rounded-full bg-slate-50 py-2 pr-4 pl-2 text-teal-800 outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                        >
                            <span
                                class="flex size-9 items-center justify-center rounded-full bg-teal-700 text-sm font-semibold text-white"
                                >{{ initials }}</span
                            ><span
                                class="hidden max-w-48 truncate text-sm font-semibold sm:block"
                                >{{ page.props.auth.user.name }}</span
                            ><ChevronDown
                                :size="17"
                            /></button></DropdownMenuTrigger
                    ><DropdownMenuContent
                        align="end"
                        class="mt-3 w-80 overflow-hidden rounded-3xl border-slate-200 bg-white p-0 shadow-xl"
                    >
                        <div
                            class="flex items-center gap-4 bg-slate-50 px-5 py-5"
                        >
                            <span
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-teal-700 text-xl font-semibold text-white"
                                >{{ initials }}</span
                            ><span
                                class="truncate text-sm font-bold text-teal-800 uppercase"
                                >{{ page.props.auth.user.name }}</span
                            >
                        </div>
                        <div class="px-5 py-4">
                            <p
                                class="mb-3 flex items-center gap-2 text-[11px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                <ShieldCheck :size="16" />Role
                            </p>
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-teal-700 px-3 py-1 text-sm font-medium text-white"
                                ><Check :size="15" />{{ title }}</span
                            >
                        </div>
                        <DropdownMenuItem
                            as-child
                            class="rounded-none border-t border-slate-100 px-5 py-4"
                            ><Link
                                href="/settings/profile"
                                class="flex w-full items-center gap-4 text-slate-600"
                                ><UserCircle :size="20" />My profile</Link
                            ></DropdownMenuItem
                        >
                        <DropdownMenuItem
                            as-child
                            class="rounded-none border-t border-slate-200 px-5 py-4"
                            ><Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="flex w-full items-center gap-4 font-semibold text-red-600"
                                ><LogOut :size="20" />Log out</Link
                            ></DropdownMenuItem
                        >
                    </DropdownMenuContent></DropdownMenu
                >
            </header>
            <main class="mx-auto max-w-[1500px] p-5 md:p-9"><slot /></main>
        </div>
    </div>
</template>
