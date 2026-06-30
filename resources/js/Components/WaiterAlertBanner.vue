<script setup>
const props = defineProps({
    alerts: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['dismiss', 'action']);

const alertConfig = {
    warning: { class: 'bg-amber-950/40 border-amber-700/30', icon: 'text-amber-400', iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
    info: { class: 'bg-blue-950/40 border-blue-700/30', icon: 'text-blue-400', iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    danger: { class: 'bg-red-950/40 border-red-700/30', icon: 'text-red-400', iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
};
</script>

<template>
    <transition-group name="alert" tag="div" class="space-y-2 mb-4">
        <div
            v-for="alert in alerts"
            :key="alert.id"
            class="flex items-center gap-3 p-3 rounded-xl border"
            :class="alertConfig[alert.type]?.class || 'bg-gray-900/60 border-gray-700/30'"
        >
            <svg class="w-5 h-5 shrink-0" :class="alertConfig[alert.type]?.icon || 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="alertConfig[alert.type]?.iconPath" />
            </svg>
            <div class="flex-1">
                <span class="text-sm font-semibold text-white">{{ alert.table }}</span>
                <span class="text-sm text-gray-300 ml-2">{{ alert.message }}</span>
                <span class="text-xs text-gray-500 ml-2">{{ alert.time }}</span>
            </div>
            <button @click="emit('action', alert.id)" class="text-xs text-emerald-400 hover:text-emerald-300 bg-emerald-950/30 hover:bg-emerald-950/60 px-3 py-1 rounded-lg transition-colors border border-emerald-900/20 font-medium">
                Attend
            </button>
            <button @click="emit('dismiss', alert.id)" class="p-1 text-gray-500 hover:text-white transition-colors rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </transition-group>
</template>

<style scoped>
.alert-enter-active, .alert-leave-active { transition: all 0.3s ease; }
.alert-enter-from, .alert-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
