<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const inventoryItems = ref([
    { id: 1, name: 'Beef Patty', category: 'Meat', unit: 'pcs', current: 50, min: 10, reorder: 15, cost: 2.50, status: 'ok' },
    { id: 2, name: 'Burger Bun', category: 'Bakery', unit: 'pcs', current: 60, min: 15, reorder: 20, cost: 0.50, status: 'ok' },
    { id: 3, name: 'Cheddar Cheese Slice', category: 'Dairy', unit: 'pcs', current: 12, min: 50, reorder: 70, cost: 0.20, status: 'critical' },
    { id: 4, name: 'Pizza Dough', category: 'Bakery', unit: 'pcs', current: 11, min: 10, reorder: 12, cost: 1.00, status: 'low' },
    { id: 5, name: 'Mozzarella Cheese', category: 'Dairy', unit: 'g', current: 5000, min: 1000, reorder: 1500, cost: 0.01, status: 'ok' },
    { id: 6, name: 'Pasta Semolina', category: 'Dry Goods', unit: 'g', current: 4000, min: 1000, reorder: 1500, cost: 0.002, status: 'ok' },
    { id: 7, name: 'Bacon Slices', category: 'Meat', unit: 'pcs', current: 18, min: 30, reorder: 45, cost: 0.40, status: 'critical' },
]);

const statusConfig = {
    ok: { label: 'Sufficient', class: 'bg-emerald-500/20 text-emerald-400' },
    low: { label: 'Low Stock', class: 'bg-amber-500/20 text-amber-400' },
    critical: { label: 'Critical', class: 'bg-red-500/20 text-red-400 animate-pulse' },
};

const lowCount = inventoryItems.value.filter(i => i.status !== 'ok').length;
</script>

<template>
    <Head title="Inventory" />

    <AuthenticatedLayout active-nav="Inventory">
        <template #header>
            <PageHeader title="Inventory Management" :breadcrumbs="[{ label: 'Dashboard', url: route('dashboard') }, { label: 'Inventory' }]">
                <template #actions>
                    <button class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 rounded-xl transition-all border border-gray-700/50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Receive Stock
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Item
                    </button>
                </template>
            </PageHeader>
        </template>

        <!-- Alert Banner for low stock -->
        <div v-if="lowCount > 0" class="mb-6 flex items-center gap-4 p-4 rounded-xl bg-red-950/30 border border-red-900/40">
            <svg class="w-6 h-6 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm text-red-300">
                <span class="font-bold">{{ lowCount }} items</span> are at low or critical stock levels. Consider placing a purchase order.
            </p>
            <button class="ml-auto text-sm font-medium px-3 py-1.5 bg-red-700/40 hover:bg-red-700/60 text-red-300 rounded-lg transition-colors border border-red-700/30">
                Order Supplies
            </button>
        </div>

        <!-- Inventory Table -->
        <div class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl overflow-hidden backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-300 text-left">
                    <thead class="text-xs uppercase text-gray-500 border-b border-gray-800/50">
                        <tr>
                            <th class="px-6 py-3">Ingredient</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Current Stock</th>
                            <th class="px-6 py-3">Min Stock</th>
                            <th class="px-6 py-3">Reorder At</th>
                            <th class="px-6 py-3">Avg. Cost</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/40">
                        <tr v-for="item in inventoryItems" :key="item.id" class="hover:bg-gray-800/10 transition-colors">
                            <td class="px-6 py-4 font-semibold text-white">{{ item.name }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ item.category }}</td>
                            <td class="px-6 py-4 font-mono font-bold" :class="item.status !== 'ok' ? 'text-red-400' : 'text-white'">
                                {{ item.current.toLocaleString() }} <span class="text-gray-600 font-normal">{{ item.unit }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-400">{{ item.min.toLocaleString() }}</td>
                            <td class="px-6 py-4 font-mono text-gray-400">{{ item.reorder.toLocaleString() }}</td>
                            <td class="px-6 py-4 font-mono text-gray-300">${{ item.cost }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="statusConfig[item.status]?.class">
                                    {{ statusConfig[item.status]?.label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <button class="text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-1 rounded-lg transition-colors border border-gray-700/50">Adjust</button>
                                <button class="text-xs text-blue-400 bg-blue-950/20 hover:bg-blue-950/40 px-3 py-1 rounded-lg transition-colors border border-blue-900/20">Reorder</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
