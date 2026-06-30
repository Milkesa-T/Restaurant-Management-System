<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';

// Mock Operational Data for Branch Manager Dashboard
const stats = ref([
    { name: 'Active Sessions', value: '6 Tables', change: '+2 seated recently', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', color: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' },
    { name: 'Pending QR Orders', value: '3 Orders', change: 'Awaiting Waiter PIN', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', color: 'text-amber-400 bg-amber-500/10 border-amber-500/20' },
    { name: 'Kitchen prep Load', value: '9 Items', change: '5 Italian, 4 Ethiopian', icon: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z', color: 'text-blue-400 bg-blue-500/10 border-blue-500/20' },
    { name: "Today's Gross Sales", value: '$1,245.50', change: '+18% vs yesterday', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-purple-400 bg-purple-500/10 border-purple-500/20' },
]);

const activeSessions = ref([
    { id: 1, table: 'Table 3', guests: 4, duration: '1h 12m', status: 'Dining', bill: '$78.50' },
    { id: 2, table: 'Table 5', guests: 2, duration: '45m', status: 'Ordering', bill: '$32.00' },
    { id: 3, table: 'Table 12 (Rooftop)', guests: 6, duration: '2h 5m', status: 'Bill Requested', bill: '$185.00' },
    { id: 4, table: 'Table 2', guests: 3, duration: '15m', status: 'Dining', bill: '$45.50' },
]);

const pendingOrders = ref([
    { id: 'ORD-892', table: 'Table 5', time: '2m ago', items: '1x Margherita Pizza, 1x Fresh Orange Juice', total: '$15.00', status: 'Awaiting PIN' },
    { id: 'ORD-891', table: 'Table 1', time: '5m ago', items: '2x Classic Beef Burger, 2x Espresso', total: '$31.00', status: 'Awaiting PIN' },
    { id: 'ORD-890', table: 'Table 11 (Rooftop)', time: '8m ago', items: '1x Kitfo, 1x Shiro Wat', total: '$28.00', status: 'Awaiting PIN' },
]);

const chefStatus = ref([
    { name: 'David Chef (Italian)', load: '5 items', status: 'Active (Pizza Station)', level: 'Busy' },
    { name: 'Hassan Chef (Ethiopian)', load: '4 items', status: 'Active (Traditional)', level: 'Moderate' },
]);
</script>

<template>
    <Head title="Manager Control Center" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Manager Control Center">
                <template #actions>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                        Launch Live POS
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 rounded-xl transition-all border border-gray-700/50">
                        Print Daily Report
                    </button>
                </template>
            </PageHeader>
        </template>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div 
                v-for="stat in stats" 
                :key="stat.name"
                class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md flex items-center justify-between"
            >
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ stat.name }}</p>
                    <p class="text-3xl font-bold text-white font-outfit leading-none">{{ stat.value }}</p>
                    <p class="text-xs text-gray-400">{{ stat.change }}</p>
                </div>
                <div class="h-12 w-12 rounded-xl flex items-center justify-center border" :class="stat.color">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="stat.icon" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
            
            <!-- Left: Active Tables (Dining Sessions) -->
            <div class="xl:col-span-8 bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md">
                <div class="flex items-center justify-between mb-4 border-b border-gray-800/50 pb-3">
                    <h3 class="font-bold text-white font-outfit">Active Dining Sessions</h3>
                    <span class="text-xs text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded font-bold">Realtime</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-300">
                        <thead>
                            <tr class="text-xs font-semibold uppercase text-gray-500 border-b border-gray-800/50">
                                <th class="py-3">Table</th>
                                <th class="py-3">Guests</th>
                                <th class="py-3">Duration</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-right">Current Bill</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/30">
                            <tr v-for="session in activeSessions" :key="session.id" class="hover:bg-gray-800/10 transition-colors">
                                <td class="py-3.5 font-semibold text-white">{{ session.table }}</td>
                                <td class="py-3.5">{{ session.guests }} Guests</td>
                                <td class="py-3.5 font-mono text-gray-400">{{ session.duration }}</td>
                                <td class="py-3.5">
                                    <span 
                                        class="px-2 py-0.5 rounded-full text-xs font-medium"
                                        :class="session.status === 'Bill Requested' 
                                            ? 'bg-amber-500/20 text-amber-400 shadow-[0_0_10px_rgba(245,158,11,0.1)]' 
                                            : 'bg-emerald-500/20 text-emerald-400'"
                                    >
                                        {{ session.status }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-right font-mono font-bold text-white">{{ session.bill }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Kitchen Load & Chef Status -->
            <div class="xl:col-span-4 bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 border-b border-gray-800/50 pb-3">
                        <h3 class="font-bold text-white font-outfit">Kitchen Hub Load</h3>
                        <span class="text-xs text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded font-bold">2 Stations</span>
                    </div>

                    <div class="space-y-4">
                        <div v-for="chef in chefStatus" :key="chef.name" class="p-3 bg-gray-900/40 border border-gray-800/50 rounded-xl">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-semibold text-white">{{ chef.name }}</h4>
                                <span 
                                    class="text-xs px-2 py-0.5 rounded font-bold"
                                    :class="chef.level === 'Busy' ? 'bg-red-500/10 text-red-400' : 'bg-amber-500/10 text-amber-400'"
                                >
                                    {{ chef.level }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ chef.status }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full rounded-full transition-all"
                                        :class="chef.level === 'Busy' ? 'bg-red-500 w-[80%]' : 'bg-amber-500 w-[50%]'"
                                    ></div>
                                </div>
                                <span class="text-xs font-bold text-gray-300 shrink-0">{{ chef.load }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-800/30">
                    <p class="text-xs text-gray-500 text-center">Auto-Assignment load balancer is active</p>
                </div>
            </div>
        </div>

        <!-- Third Row: Pending QR Orders (2-Step Waiter Verification Queue) -->
        <div class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md mt-6">
            <div class="flex items-center justify-between mb-4 border-b border-gray-800/50 pb-3">
                <div>
                    <h3 class="font-bold text-white font-outfit">Waiter verification Queue</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Orders submitted via QR menu awaiting staff verification</p>
                </div>
                <span class="text-xs text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded font-bold">Awaiting Action</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead>
                        <tr class="text-xs font-semibold uppercase text-gray-500 border-b border-gray-800/50">
                            <th class="py-3">Order ID</th>
                            <th class="py-3">Table</th>
                            <th class="py-3">Time Elapsed</th>
                            <th class="py-3">Items Ordered</th>
                            <th class="py-3">Total Cost</th>
                            <th class="py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/30">
                        <tr v-for="order in pendingOrders" :key="order.id" class="hover:bg-gray-800/10 transition-colors">
                            <td class="py-3.5 font-semibold text-white font-mono">{{ order.id }}</td>
                            <td class="py-3.5">{{ order.table }}</td>
                            <td class="py-3.5 text-amber-400 font-mono">{{ order.time }}</td>
                            <td class="py-3.5 text-gray-400 max-w-[280px] truncate">{{ order.items }}</td>
                            <td class="py-3.5 font-bold text-white font-mono">{{ order.total }}</td>
                            <td class="py-3.5 text-right space-x-2">
                                <button class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold transition-all">
                                    Approve
                                </button>
                                <button class="px-3 py-1 bg-red-950/40 hover:bg-red-900/40 text-red-400 rounded-lg text-xs font-semibold transition-all border border-red-900/30">
                                    Reject
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
