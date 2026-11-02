<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import PasswordInput from "@/components/PasswordInput.vue";
import TextLink from "@/components/TextLink.vue";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Spinner } from "@/components/ui/spinner";
/* @chisel-registration */
import { register } from "@/routes";
/* @end-chisel-registration */
import { store } from "@/routes/login";
import { request } from "@/routes/password";


defineOptions({
    layout: {
        title: "Log in to your account",
        description: "Enter your email and password below to log in",
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>



    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-4"
    >
        <div class="grid gap-4">
            <div class="grid gap-2">
                <Label class="text-[#102d49]" for="email">Email address</Label>
                <Input
                    class="!border-slate-300 !bg-white text-[#102d49] focus-visible:border-teal-600 focus-visible:ring-teal-600/20"
                    id="email"
                    type="email"
                    name="email"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label class="text-[#102d49]" for="password">Password</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm text-teal-700 hover:text-teal-800"
                        :tabindex="5"
                    >
                        Forgot your password?
                    </TextLink>
                </div>
                <PasswordInput
                    class="!border-slate-300 !bg-white text-[#102d49] focus-visible:border-teal-600 focus-visible:ring-teal-600/20"
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3 text-[#102d49]">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Remember me</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-1 w-full bg-teal-700 text-white hover:bg-teal-800"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Log in
            </Button>
        </div>

        <!-- @chisel-registration -->
        <div class="text-center text-sm text-slate-500">
            Don't have an account?
            <TextLink class="text-teal-700 hover:text-teal-800" :href="register()" :tabindex="5">Sign up</TextLink>
        </div>
        <!-- @end-chisel-registration -->
    </Form>
</template>
