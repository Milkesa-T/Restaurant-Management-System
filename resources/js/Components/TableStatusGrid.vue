<script setup>
import { defineEmits } from 'vue';

const props = defineProps({
    tables: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['table-click']);

const statusConfig = {
    occupied: { label: 'Occupied', class: 'bg-emerald-500/20 border-emerald-500/30 text-emerald-300', dot: 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.6)]' },
    empty: { label: 'Empty', class: 'bg-gray-900/60 border-gray-700/40 text-gray-500', dot: 'bg-gray-600' },
    ordering: { label: 'Ordering', class: 'bg-blue-500/20 border-blue-500/30 text-blue-300', dot: 'bg-blue-400 shadow-[0_0_8px_rgba(59,130,246,0.6)]' },
    cleaning: { label: 'Cleaning', class: 'bg-amber-500/20 border-amber-500/30 text-amber-300', dot: 'bg-amber-400 shadow-[0_0_8px_rgba(245,158,11,0.6)]' },
    reserved: { label: 'Reserved', class: 'bg-purple-500/20 border-purple-500/30 text-purple-300', dot: 'bg-purple-400' },
};
</script>

<template>
    <div>
        <!-- Legend -->
        <div class="flex flex-wrap gap-4 mb-5">
            <div v-for="(conf, key) in statusConfig" :key="key" class="flex items-center gap-2 text-xs text-gray-400">
                <span class="h-2 w-2 rounded-full" :class="conf.dot"></span>
                {{ conf.label }}
            </div>
        </div>

        <!-- Table Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-3 gap-4">
            <button 
                v-for="table in tables" 
                :key="table.id"
                @click="emit('table-click', table)"
                class="p-5 rounded-2xl border backdrop-blur-md transition-all hover:scale-[1.02] active:scale-[0.98] text-left cursor-pointer"
                :class="statusConfig[table.status]?.class || 'bg-gray-900/60 border-gray-700/40'"
            >
                <!-- Table Number & Dot -->
                <div class="flex items-start justify-between mb-3">
                    <span class="text-lg font-bold font-mono text-white">{{ table.name }}</span>
                    <span class="h-2.5 w-2.5 rounded-full mt-1" :class="statusConfig[table.status]?.dot || 'bg-gray-600'"></span>
                </div>

                <p class="text-xs capitalize" :class="statusConfig[table.status] ? '' : 'text-gray-500'">
                    {{ statusConfig[table.status]?.label || table.status }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Cap: {{ table.capacity }}</p>

                <!-- Session timer if occupied -->
                <div v-if="table.timeSeated" class="mt-3 pt-3 border-t border-gray-800/40">
                    <p class="text-xs text-gray-500">Seated for</p>
                    <p class="font-mono font-bold text-white text-sm mt-0.5">{{ table.timeSeated }}</p>
                </div>
            </button>
        </div>
    </div>
</template>
