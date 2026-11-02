<script setup lang="ts">
import WorkspaceLayout from '@/layouts/WorkspaceLayout.vue';
import type { BreadcrumbItem } from '@/types';
defineProps<{ breadcrumbs?: BreadcrumbItem[] }>();
</script>
<template><WorkspaceLayout :breadcrumbs="breadcrumbs"><slot /></WorkspaceLayout></template>
