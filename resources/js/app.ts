import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import InstitutionLayout from '@/layouts/InstitutionLayout.vue';
import IndustryLayout from '@/layouts/IndustryLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import PublicAuthLayout from '@/layouts/auth/PublicAuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
            case name.startsWith('discovery/'):
                return null;
            case name.startsWith('industry/'):
                return IndustryLayout;
            case name.startsWith('institution/'):
                return InstitutionLayout;
            case name.startsWith('admin/'):
                return AdminLayout;
            case name === 'auth/Login':
                return PublicAuthLayout;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    withApp: (app) => {
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
