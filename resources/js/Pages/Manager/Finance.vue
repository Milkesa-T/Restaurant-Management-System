<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const periodTab = ref('today');
const periods = [
    { key: 'today', label: 'Today' },
    { key: 'week', label: 'This Week' },
    { key: 'month', label: 'This Month' },
];

const summaryStats = ref([
    { name: 'Gross Revenue', value: '$1,245.50', change: '+18.5%', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' },
    { name: 'Total Expenses', value: '$310.00', change: '-3.2% vs avg', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', color: 'text-red-400 bg-red-500/10 border-red-500/20' },
    { name: 'Net Profit', value: '$935.50', change: '75.1% margin', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', color: 'text-purple-400 bg-purple-500/10 border-purple-500/20' },
    { name: 'Total Orders Served', value: '47 Orders', change: '6 Avg per hour', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', color: 'text-blue-400 bg-blue-500/10 border-blue-500/20' },
]);

const topItems = ref([
    { rank: 1, name: 'Margherita Pizza', qty: 18, revenue: '$198.00' },
    { rank: 2, name: 'Classic Beef Burger', qty: 14, revenue: '$175.00' },
    { rank: 3, name: 'Kitfo', qty: 9, revenue: '$162.00' },
    { rank: 4, name: 'Pasta Carbonara', qty: 11, revenue: '$154.00' },
    { rank: 5, name: 'Shiro Wat', qty: 13, revenue: '$130.00' },
]);

const expenses = ref([
    { id: 1, category: 'Supplies', description: 'Metro Wholesale - Weekly Produce', amount: '$180.00', date: 'Today, 09:00', status: 'paid' },
    { id: 2, category: 'Utilities', description: 'Monthly gas bill', amount: '$95.00', date: 'Today, 08:30', status: 'paid' },
    { id: 3, category: 'Salaries', description: 'Daily advance - Charlie Waiter', amount: '$35.00', date: 'Yesterday', status: 'paid' },
]);
</script>

<template>
    <Head title="Finance" />

    <AuthenticatedLayout active-nav="Finance">
        <template #header>
            <PageHeader title="Finance & Reports" :breadcrumbs="[{ label: 'Dashboard', url: route('dashboard') }, { label: 'Finance' }]">
                <template #actions>
                    <div class="flex items-center gap-1 bg-[#11141c]/60 p-1 rounded-xl border border-gray-800/80">
                        <button
                            v-for="p in periods" :key="p.key"
                            @click="periodTab = p.key"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                            :class="periodTab === p.key ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-gray-200'"
                        >
                            {{ p.label }}
                        </button>
                    </div>
                    <button class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 rounded-xl transition-all border border-gray-700/50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export PDF
                    </button>
                </template>
            </PageHeader>
        </template>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            <div v-for="stat in summaryStats" :key="stat.name" class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md flex items-center justify-between">
                <div class="space-y-1.5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ stat.name }}</p>
                    <p class="text-2xl font-bold text-white font-outfit leading-none">{{ stat.value }}</p>
                    <p class="text-xs text-gray-400">{{ stat.change }}</p>
                </div>
                <div class="h-12 w-12 rounded-xl flex items-center justify-center border" :class="stat.color">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="stat.icon" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Top Selling Items -->
            <div class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md">
                <div class="flex items-center justify-between mb-4 border-b border-gray-800/50 pb-3">
                    <h3 class="font-bold text-white font-outfit">Top Selling Items</h3>
                    <span class="text-xs text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded font-bold">Today</span>
                </div>
                <div class="space-y-3">
                    <div v-for="item in topItems" :key="item.rank" class="flex items-center gap-4">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" :class="item.rank <= 3 ? 'bg-amber-500/20 text-amber-400' : 'bg-gray-800 text-gray-400'">
                            {{ item.rank }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ item.name }}</p>
                            <div class="mt-1 h-1 bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-1 bg-emerald-500 rounded-full" :style="{ width: (item.qty / 20 * 100) + '%' }"></div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-white font-mono">{{ item.revenue }}</p>
                            <p class="text-xs text-gray-500">{{ item.qty }} sold</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses -->
            <div class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md">
                <div class="flex items-center justify-between mb-4 border-b border-gray-800/50 pb-3">
                    <h3 class="font-bold text-white font-outfit">Expenses Log</h3>
                    <button class="text-xs text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 px-2 py-0.5 rounded font-bold transition-colors">
                        + Add Expense
                    </button>
                </div>
                <div class="divide-y divide-gray-800/40">
                    <div v-for="expense in expenses" :key="expense.id" class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-white">{{ expense.description }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <span class="text-gray-400">{{ expense.category }}</span> · {{ expense.date }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-red-400 font-mono">{{ expense.amount }}</p>
                            <span class="text-xs text-gray-500 capitalize">{{ expense.status }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
