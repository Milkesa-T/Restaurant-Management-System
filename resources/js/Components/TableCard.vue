<script setup>
import { computed } from 'vue';

const props = defineProps({
    table: {
        type: Object,
        required: true,
        // Example: { id: 1, number: '04', name: 'T-04', capacity: 4, status: 'occupied', timeSeated: '00:45', activeOrder: true }
    }
});

const emit = defineEmits(['action']);

const statusStyles = computed(() => {
    switch (props.table.status) {
        case 'occupied':
            return {
                border: 'border-emerald-500/50',
                dot: 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]',
                bg: 'bg-gradient-to-br from-emerald-900/20 to-[#1e1e24]'
            };
        case 'ordering':
            return {
                border: 'border-amber-500/50',
                dot: 'bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.8)]',
                bg: 'bg-gradient-to-br from-amber-900/20 to-[#1e1e24]'
            };
        case 'cleaning':
            return {
                border: 'border-blue-500/50',
                dot: 'bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.8)]',
                bg: 'bg-gradient-to-br from-blue-900/20 to-[#1e1e24]'
            };
        case 'empty':
        default:
            return {
                border: 'border-gray-700',
                dot: 'bg-gray-500',
                bg: 'bg-[#1e1e24]'
            };
    }
});

const statusText = computed(() => {
    switch (props.table.status) {
        case 'occupied': return 'Table Occupied';
        case 'ordering': return 'New QR Order';
        case 'cleaning': return 'Needs Cleaning';
        case 'empty': return 'Available';
        default: return props.table.status;
    }
});
</script>

<template>
    <button 
        @click="emit('action', table)"
        class="relative w-full rounded-2xl border p-5 text-left transition-all duration-300 hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/50 group overflow-hidden"
        :class="[statusStyles.bg, statusStyles.border]"
    >
        <!-- Top header area -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-400 tracking-wider uppercase">Table</span>
                <span class="text-3xl font-bold text-white font-outfit leading-none mt-1">{{ table.number }}</span>
            </div>
            <div v-if="table.timeSeated" class="text-xs font-mono font-medium text-gray-400 bg-black/40 px-2 py-1 rounded">
                T-{{ table.timeSeated }}
            </div>
        </div>

        <!-- Content Area -->
        <div class="mt-4">
            <div class="flex items-center gap-2 mb-1.5">
                <div class="h-2 w-2 rounded-full" :class="statusStyles.dot"></div>
                <h4 class="text-sm font-semibold text-gray-100">{{ statusText }}</h4>
            </div>
            
            <p class="text-xs text-gray-400 truncate">
                {{ table.capacity }} Guests Capacity
            </p>
            
            <p v-if="table.activeOrder" class="text-xs text-emerald-400/90 mt-1 font-medium">
                Active Order
            </p>
        </div>

        <!-- Hover Glow Effect -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-10 transition-opacity duration-300 pointer-events-none bg-gradient-to-t from-white/20 to-transparent"></div>
    </button>
</template>
