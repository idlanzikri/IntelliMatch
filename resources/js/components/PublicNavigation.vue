<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ChevronDown } from "@lucide/vue";
import { onClickOutside } from "@vueuse/core";
import { onBeforeUnmount, onMounted, ref } from "vue";

const menu = ref<"student" | "institution" | null>(null);
const menus = ref<HTMLElement | null>(null);
const studentButton = ref<HTMLButtonElement | null>(null);
const institutionButton = ref<HTMLButtonElement | null>(null);

onClickOutside(menus, () => {
    menu.value = null;
});

function escape(event: KeyboardEvent) {
    if (event.key !== "Escape" || !menu.value) return;
    (menu.value === "student"
        ? studentButton
        : institutionButton
    ).value?.focus();
    menu.value = null;
}
onMounted(() => document.addEventListener("keydown", escape));
onBeforeUnmount(() => document.removeEventListener("keydown", escape));
</script>

<template>
    <header class="border-b border-slate-200 bg-white">
        <nav
            aria-label="Main navigation"
            class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-4 lg:px-10"
        >
            <Link href="/" class="flex items-center gap-2"
                ><img
                    src="/brand/intellimatch.jpeg"
                    alt=""
                    class="size-12 rounded-lg object-contain"
                /><span class="text-xl font-bold tracking-tight"
                    >Intelli<span class="text-teal-600">Match</span></span
                ></Link
            >
            <div
                ref="menus"
                class="order-3 flex w-full flex-wrap items-center gap-5 text-sm font-medium md:order-none md:w-auto"
                @click="
                    ($event.target as HTMLElement).closest('a') && (menu = null)
                "
                @focusout="
                    !($event.currentTarget as HTMLElement).contains(
                        $event.relatedTarget as Node,
                    ) && (menu = null)
                "
            >
                <a href="/#how-it-works" class="hover:text-teal-600"
                    >How it works</a
                >
                <div class="relative">
                    <button
                        ref="studentButton"
                        :aria-expanded="menu === 'student'"
                        aria-controls="student-menu"
                        class="inline-flex cursor-pointer items-center gap-1.5 py-2 hover:text-teal-600"
                        @click="menu = menu === 'student' ? null : 'student'"
                    >
                        For students
                        <ChevronDown
                            :size="16"
                            aria-hidden="true"
                            class="transition-transform"
                            :class="{ 'rotate-180': menu === 'student' }"
                        />
                    </button>
                    <div
                        v-if="menu === 'student'"
                        id="student-menu"
                        class="absolute left-0 top-full z-30 mt-2 w-60 rounded-xl border border-slate-200 bg-white p-2 shadow-lg"
                    >
                        <Link
                            href="/careers"
                            class="block rounded-lg px-4 py-3 hover:bg-teal-50"
                            >Explore careers</Link
                        >
                        <Link
                            href="/register"
                            class="block rounded-lg px-4 py-3 text-teal-700 hover:bg-teal-50"
                            >Create a student account</Link
                        >
                    </div>
                </div>
                <div class="relative">
                    <button
                        ref="institutionButton"
                        :aria-expanded="menu === 'institution'"
                        aria-controls="institution-menu"
                        class="inline-flex cursor-pointer items-center gap-1.5 py-2 hover:text-teal-600"
                        @click="
                            menu = menu === 'institution' ? null : 'institution'
                        "
                    >
                        For institutions
                        <ChevronDown
                            :size="16"
                            aria-hidden="true"
                            class="transition-transform"
                            :class="{ 'rotate-180': menu === 'institution' }"
                        />
                    </button>
                    <div
                        v-if="menu === 'institution'"
                        id="institution-menu"
                        class="absolute left-0 top-full z-30 mt-2 w-60 rounded-xl border border-slate-200 bg-white p-2 shadow-lg"
                    >
                        <Link
                            href="/talent"
                            class="block rounded-lg px-4 py-3 hover:bg-teal-50"
                            >Discover talent</Link
                        >
                        <Link
                            href="/institution"
                            class="block rounded-lg px-4 py-3 text-teal-700 hover:bg-teal-50"
                            >Institution sign in</Link
                        >
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-5">
                <Link href="/login" class="text-sm font-semibold">Log in</Link
                ><Link
                    href="/register"
                    class="hidden rounded-lg bg-[#102d49] px-5 py-3 text-sm font-semibold text-white sm:block"
                    >Get started ↗</Link
                >
            </div>
        </nav>
    </header>
</template>
