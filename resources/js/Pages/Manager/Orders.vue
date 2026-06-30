<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const filter = ref('all');
const filters = [
    { key: 'all', label: 'All Orders' },
    { key: 'pending_waiter_approval', label: 'Awaiting Approval' },
    { key: 'approved', label: 'Approved' },
    { key: 'in_preparation', label: 'In Kitchen' },
    { key: 'served', label: 'Served' },
    { key: 'cancelled', label: 'Cancelled' },
];

const orders = ref([
    { id: 'ORD-895', table: 'T-04', session: 'SES-041', source: 'QR', items: 3, total: '$45.50', status: 'pending_waiter_approval', time: '1m ago' },
    { id: 'ORD-892', table: 'T-03', session: 'SES-037', source: 'QR', items: 2, total: '$17.50', status: 'in_preparation', time: '12m ago' },
    { id: 'ORD-891', table: 'T-01', session: 'SES-032', source: 'Waiter', items: 3, total: '$31.00', status: 'in_preparation', time: '18m ago' },
    { id: 'ORD-890', table: 'T-11', session: 'SES-030', source: 'QR', items: 2, total: '$28.00', status: 'approved', time: '21m ago' },
    { id: 'ORD-889', table: 'T-05', session: 'SES-029', source: 'Waiter', items: 4, total: '$62.00', status: 'served', time: '1h ago' },
    { id: 'ORD-888', table: 'T-02', session: 'SES-028', source: 'QR', items: 1, total: '$10.00', status: 'cancelled', time: '2h ago' },
]);

const statusConfig = {
    pending_waiter_approval: { label: 'Awaiting Approval', class: 'bg-amber-500/20 text-amber-400' },
    approved: { label: 'Approved', class: 'bg-blue-500/20 text-blue-400' },
    in_preparation: { label: 'In Kitchen', class: 'bg-purple-500/20 text-purple-400' },
    served: { label: 'Served', class: 'bg-emerald-500/20 text-emerald-400' },
    cancelled: { label: 'Cancelled', class: 'bg-red-500/20 text-red-400' },
};

const filteredOrders = () => filter.value === 'all' ? orders.value : orders.value.filter(o => o.status === filter.value);
</script>

<template>
    <Head title="Orders" />

    <AuthenticatedLayout active-nav="Orders">
        <template #header>
            <PageHeader title="Order Management" :breadcrumbs="[{ label: 'Dashboard', url: route('dashboard') }, { label: 'Orders' }]">
                <template #actions>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New POS Order
                    </button>
                </template>
            </PageHeader>
        </template>

        <!-- Status Filter Pills -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button
                v-for="f in filters" :key="f.key"
                @click="filter = f.key"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all border"
                :class="filter === f.key 
                    ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30 shadow-[0_0_10px_rgba(52,211,153,0.1)]' 
                    : 'text-gray-400 border-gray-700/50 hover:text-white hover:border-gray-600 bg-transparent'"
            >
                {{ f.label }}
            </button>
        </div>

        <!-- Orders Table -->
        <div class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl overflow-hidden backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-300 text-left">
                    <thead class="text-xs uppercase text-gray-500 border-b border-gray-800/50">
                        <tr>
                            <th class="px-6 py-3">Order ID</th>
                            <th class="px-6 py-3">Table</th>
                            <th class="px-6 py-3">Session</th>
                            <th class="px-6 py-3">Source</th>
                            <th class="px-6 py-3">Items</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Time</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/40">
                        <tr v-for="order in filteredOrders()" :key="order.id" class="hover:bg-gray-800/10 transition-colors">
                            <td class="px-6 py-4 font-bold text-white font-mono">{{ order.id }}</td>
                            <td class="px-6 py-4">{{ order.table }}</td>
                            <td class="px-6 py-4 text-gray-400 font-mono text-xs">{{ order.session }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium" :class="order.source === 'QR' ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-700 text-gray-300'">
                                    {{ order.source }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ order.items }}</td>
                            <td class="px-6 py-4 font-bold text-white font-mono">{{ order.total }}</td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ order.time }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="statusConfig[order.status]?.class">
                                    {{ statusConfig[order.status]?.label || order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <button class="text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-1 rounded-lg transition-colors border border-gray-700/50">View</button>
                                <button v-if="order.status === 'pending_waiter_approval'" class="text-xs text-emerald-400 hover:text-emerald-300 bg-emerald-950/30 hover:bg-emerald-950/60 px-3 py-1 rounded-lg transition-colors border border-emerald-900/30">Approve</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="filteredOrders().length === 0" class="py-16 text-center text-gray-600">
                <p class="text-sm">No orders found for this filter.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
