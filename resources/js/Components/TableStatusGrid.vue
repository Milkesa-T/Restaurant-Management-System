<script setup>
import { ref } from 'vue';
import TableCard from './TableCard.vue';

const props = defineProps({
    tables: {
        type: Array,
        required: true,
    }
});

const emit = defineEmits(['table-click']);

const filter = ref('all');

const filteredTables = () => {
    if (filter.value === 'all') return props.tables;
    return props.tables.filter(t => t.status === filter.value);
};
</script>

<template>
    <div class="space-y-6">
        <!-- Legend and Filters -->
        <div class="flex flex-wrap items-center gap-4 text-sm bg-[#1e1e24]/50 p-4 rounded-xl border border-gray-800">
            <button @click="filter = 'all'" :class="filter === 'all' ? 'text-white font-medium' : 'text-gray-400 hover:text-gray-300'" class="transition-colors">
                All Tables
            </button>
            <div class="w-px h-4 bg-gray-700 mx-1"></div>
            <button @click="filter = 'occupied'" class="flex items-center gap-2 group transition-colors" :class="filter === 'occupied' ? 'opacity-100' : 'opacity-60 hover:opacity-100'">
                <div class="h-2 w-2 rounded-full bg-emerald-400 group-hover:shadow-[0_0_8px_rgba(52,211,153,0.8)]"></div>
                <span class="text-gray-300">Occupied</span>
            </button>
            <button @click="filter = 'ordering'" class="flex items-center gap-2 group transition-colors" :class="filter === 'ordering' ? 'opacity-100' : 'opacity-60 hover:opacity-100'">
                <div class="h-2 w-2 rounded-full bg-amber-400 group-hover:shadow-[0_0_8px_rgba(251,191,36,0.8)]"></div>
                <span class="text-gray-300">Ordering</span>
            </button>
            <button @click="filter = 'cleaning'" class="flex items-center gap-2 group transition-colors" :class="filter === 'cleaning' ? 'opacity-100' : 'opacity-60 hover:opacity-100'">
                <div class="h-2 w-2 rounded-full bg-blue-400 group-hover:shadow-[0_0_8px_rgba(96,165,250,0.8)]"></div>
                <span class="text-gray-300">Cleaning</span>
            </button>
            <button @click="filter = 'empty'" class="flex items-center gap-2 group transition-colors" :class="filter === 'empty' ? 'opacity-100' : 'opacity-60 hover:opacity-100'">
                <div class="h-2 w-2 rounded-full bg-gray-500"></div>
                <span class="text-gray-300">Empty</span>
            </button>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            <transition-group name="list">
                <TableCard 
                    v-for="table in filteredTables()" 
                    :key="table.id" 
                    :table="table" 
                    @action="emit('table-click', $event)"
                />
            </transition-group>
        </div>

        <!-- Empty State -->
        <div v-if="filteredTables().length === 0" class="py-12 text-center text-gray-500">
            No tables match the selected filter.
        </div>
    </div>
</template>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
