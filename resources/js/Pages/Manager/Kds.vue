<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const kdsLanes = ref([
    {
        id: 1,
        name: 'Italian Kitchen',
        color: 'blue',
        tickets: [
            {
                id: 'ORD-892',
                table: 'T-03',
                chef: 'David Chef',
                status: 'preparing',
                totalTime: 15,
                elapsed: 8,
                items: [
                    { name: 'Margherita Pizza', qty: 1, notes: 'Extra cheese', done: false },
                    { name: 'Garlic Bread', qty: 2, notes: null, done: true },
                ],
            },
            {
                id: 'ORD-891',
                table: 'T-01',
                chef: 'David Chef',
                status: 'preparing',
                totalTime: 12,
                elapsed: 3,
                items: [
                    { name: 'Classic Beef Burger', qty: 2, notes: 'Double Patty', done: false },
                    { name: 'Pasta Carbonara', qty: 1, notes: null, done: false },
                ],
            },
        ]
    },
    {
        id: 2,
        name: 'Ethiopian Kitchen',
        color: 'amber',
        tickets: [
            {
                id: 'ORD-890',
                table: 'T-11',
                chef: 'Hassan Chef',
                status: 'fired',
                totalTime: 25,
                elapsed: 1,
                items: [
                    { name: 'Kitfo', qty: 1, notes: 'Medium rare', done: false },
                    { name: 'Shiro Wat', qty: 1, notes: null, done: false },
                ],
            },
        ]
    },
]);

const laneColors = {
    blue: {
        header: 'bg-blue-900/30 border-blue-800/50',
        badge: 'bg-blue-500/20 text-blue-400',
        timer: 'text-blue-400',
        progress_bg: 'bg-gray-800',
        progress_bar: 'bg-blue-500',
        ticket_border: 'border-blue-900/30',
    },
    amber: {
        header: 'bg-amber-900/20 border-amber-800/40',
        badge: 'bg-amber-500/20 text-amber-400',
        timer: 'text-amber-400',
        progress_bg: 'bg-gray-800',
        progress_bar: 'bg-amber-500',
        ticket_border: 'border-amber-900/30',
    }
};

function progressPct(elapsed, total) {
    return Math.min((elapsed / total) * 100, 100);
}

function progressColor(elapsed, total) {
    const pct = (elapsed / total) * 100;
    if (pct >= 80) return 'bg-red-500';
    if (pct >= 55) return 'bg-amber-500';
    return 'bg-emerald-500';
}
</script>

<template>
    <Head title="Kitchen Display System" />

    <AuthenticatedLayout active-nav="KDS Menu">
        <template #header>
            <PageHeader title="Kitchen Display System" :breadcrumbs="[{ label: 'Dashboard', url: route('dashboard') }, { label: 'KDS Menu' }]">
                <template #actions>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        <span class="text-sm text-gray-400">Auto-assignment Active</span>
                    </div>
                </template>
            </PageHeader>
        </template>

        <!-- KDS Lane Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div 
                v-for="lane in kdsLanes" 
                :key="lane.id"
                class="flex flex-col gap-4"
            >
                <!-- Lane Header -->
                <div class="flex items-center justify-between px-4 py-3 rounded-xl border" :class="laneColors[lane.color].header">
                    <h2 class="font-bold text-white font-outfit">{{ lane.name }}</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-bold" :class="laneColors[lane.color].badge">
                        {{ lane.tickets.length }} Active Tickets
                    </span>
                </div>

                <!-- Tickets -->
                <div class="space-y-4">
                    <div 
                        v-for="ticket in lane.tickets" 
                        :key="ticket.id"
                        class="bg-[#11141c]/80 border rounded-2xl overflow-hidden backdrop-blur-md"
                        :class="laneColors[lane.color].ticket_border"
                    >
                        <!-- Ticket Header -->
                        <div class="p-4 border-b border-gray-800/50 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-white font-outfit font-mono">{{ ticket.id }}</span>
                                    <span class="text-xs text-gray-400 bg-gray-800 px-2 py-0.5 rounded-lg border border-gray-700/30">{{ ticket.table }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">Assigned: {{ ticket.chef }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold font-mono" :class="laneColors[lane.color].timer">{{ ticket.elapsed }}m</p>
                                <p class="text-xs text-gray-500">of {{ ticket.totalTime }} min</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="h-1 w-full bg-gray-800">
                            <div 
                                class="h-1 transition-all duration-1000"
                                :class="progressColor(ticket.elapsed, ticket.totalTime)"
                                :style="{ width: progressPct(ticket.elapsed, ticket.totalTime) + '%' }"
                            ></div>
                        </div>

                        <!-- Order Items -->
                        <div class="p-4 space-y-2">
                            <div 
                                v-for="(item, idx) in ticket.items" 
                                :key="idx"
                                class="flex items-center gap-3 py-2 px-3 rounded-xl border transition-all"
                                :class="item.done 
                                    ? 'bg-emerald-900/10 border-emerald-900/20 opacity-60' 
                                    : 'bg-gray-900/40 border-gray-800/40'"
                            >
                                <input 
                                    type="checkbox" 
                                    v-model="item.done"
                                    class="h-4 w-4 rounded accent-emerald-500 cursor-pointer" 
                                />
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-white" :class="item.done ? 'line-through text-gray-500' : ''">
                                        {{ item.qty }}x {{ item.name }}
                                    </p>
                                    <p v-if="item.notes" class="text-xs text-amber-400/80 mt-0.5">📝 {{ item.notes }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Actions -->
                        <div class="px-4 pb-4 flex gap-2">
                            <button class="flex-1 py-2 text-sm font-semibold text-emerald-950 bg-emerald-400 hover:bg-emerald-300 rounded-xl transition-all shadow-[0_0_12px_rgba(52,211,153,0.2)]">
                                Mark All Ready
                            </button>
                            <button class="px-4 py-2 text-sm text-gray-400 bg-gray-800 hover:bg-gray-700 rounded-xl transition-all border border-gray-700/50">
                                Reassign
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="lane.tickets.length === 0" class="py-16 text-center text-gray-600">
                        <svg class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm">No active tickets. All clear!</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
