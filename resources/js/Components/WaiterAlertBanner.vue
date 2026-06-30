<script setup>
import { computed } from 'vue';

const props = defineProps({
    alerts: {
        type: Array,
        required: true,
        // Example: [{ id: 1, table: 'T-05', message: 'Needs Assistance', type: 'warning', time: '2m ago' }]
    }
});

const emit = defineEmits(['dismiss', 'action']);

const hasAlerts = computed(() => props.alerts.length > 0);
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform -translate-y-full opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-full opacity-0"
    >
        <div v-if="hasAlerts" class="fixed top-0 left-0 right-0 z-50 flex flex-col gap-2 p-4 pt-4 sm:pt-6 px-4 sm:px-6 pointer-events-none">
            <div 
                v-for="alert in alerts" 
                :key="alert.id"
                class="pointer-events-auto mx-auto w-full max-w-3xl rounded-xl shadow-lg shadow-amber-900/20 bg-gradient-to-r from-amber-950 to-[#1e1a14] border border-amber-800/50 p-4 flex items-center justify-between backdrop-blur-md"
            >
                <div class="flex items-center gap-4">
                    <!-- Alert Icon -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-amber-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-amber-100">
                            Table {{ alert.table }} - {{ alert.message }}
                        </h3>
                        <p class="text-xs text-amber-400/80 mt-0.5">Alert triggered {{ alert.time }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        @click="emit('action', alert.id)"
                        class="px-4 py-1.5 text-sm font-medium text-amber-950 bg-amber-400 hover:bg-amber-300 rounded-lg shadow-[0_0_15px_rgba(251,191,36,0.3)] transition-all"
                    >
                        Acknowledge
                    </button>
                    <button 
                        @click="emit('dismiss', alert.id)"
                        class="text-amber-500 hover:text-amber-300 p-1 rounded-md hover:bg-amber-500/10 transition-colors"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>
