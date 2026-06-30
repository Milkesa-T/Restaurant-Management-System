<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        required: true,
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
        // Example: [{ label: 'Dashboard', url: '/dashboard' }, { label: 'Settings', url: null }]
    },
});
</script>

<template>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <!-- Breadcrumbs -->
            <nav v-if="breadcrumbs.length > 0" class="flex text-sm text-gray-400 mb-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li v-for="(crumb, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                        <Link v-if="crumb.url" :href="crumb.url" class="inline-flex items-center hover:text-white transition-colors">
                            {{ crumb.label }}
                        </Link>
                        <span v-else class="text-gray-500">
                            {{ crumb.label }}
                        </span>
                        
                        <!-- Separator -->
                        <svg v-if="index < breadcrumbs.length - 1" class="w-3 h-3 mx-1 text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                    </li>
                </ol>
            </nav>
            
            <!-- Page Title -->
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl font-outfit">
                {{ title }}
            </h1>
        </div>

        <!-- Actions Slot (e.g. Add Button) -->
        <div class="flex items-center gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>
