<script setup>
/**
 * StaffPortal.vue - Staff Portal Page (KDS, Waiter Floor Map, and Order Approvals)
 */
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

// Import our Vue components
import OrderTicketCard from '../Components/OrderTicketCard.vue';
import FloorTableCard from '../Components/FloorTableCard.vue';

// Local tabs: 'kds', 'floormap', 'approvals'
const activeTab = ref('kds');

// Active orders list
const orders = ref([
    {
        id: '#284-A',
        customer: 'Table 14 — Desta',
        caption: 'Anniversary Dinner 💖',
        seconds: 420,
        status: 'Preparing',
        instruction: 'Allergic to peanut oil',
        items: [
            { name: 'Doro Wat (Spicy Chicken) (x2)', checked: true },
            { name: 'Special Kitfo (Rare) (x1)', checked: false }
        ],
        billing: { total: 1200 },
        note: 'Requires extra injera'
    },
    {
        id: '#289-C',
        customer: 'Table 03 — Abraham',
        caption: 'Business Lunch',
        seconds: 80,
        status: 'sent_to_kitchen',
        instruction: 'Mild spice level',
        items: [
            { name: 'Veggie Combo (Beyaynetu) (x1)', checked: false },
            { name: 'Shiro Wat (x1)', checked: false }
        ],
        billing: { total: 750 },
        note: ''
    },
    {
        id: '#291-B',
        customer: 'Table 02 — Eden',
        caption: '',
        seconds: 10,
        status: 'pending_waiter_approval',
        instruction: '',
        items: [
            { name: 'Meat Sambusa (3 pcs) (x2)', checked: false }
        ],
        billing: { total: 300 },
        note: ''
    }
]);

// Table Floor Map status
const tables = ref([
    { tableNum: 1, status: 'Available' },
    { tableNum: 2, status: 'Ordering' },
    { tableNum: 3, status: 'Occupied' },
    { tableNum: 4, status: 'Served / Bill' },
    { tableNum: 5, status: 'Cleaning' },
    { tableNum: 6, status: 'Reserved' },
    { tableNum: 14, status: 'Occupied' }
]);

// Computed lists
const kdsOrders = computed(() => orders.value.filter(o => o.status === 'sent_to_kitchen' || o.status === 'Preparing' || o.status === 'Ready'));
const pendingApprovalOrders = computed(() => orders.value.filter(o => o.status === 'pending_waiter_approval'));

// KDS / Chef Actions
const handleStatusChange = ({ orderId, status }) => {
    const order = orders.value.find(o => o.id === orderId);
    if (order) {
        order.status = status;
        
        // Update table states based on order status changes
        const match = order.customer.match(/Table\s+(\d+)/);
        if (match) {
            const tableNumVal = parseInt(match[1]);
            const tbl = tables.value.find(t => t.tableNum === tableNumVal);
            if (tbl) {
                if (status === 'Preparing') tbl.status = 'Occupied';
                if (status === 'Ready') tbl.status = 'Served / Bill';
            }
        }
    }
};

const handleNoteEdit = ({ orderId, currentNote }) => {
    const note = prompt(`Enter note for order ${orderId}:`, currentNote);
    if (note !== null) {
        const order = orders.value.find(o => o.id === orderId);
        if (order) order.note = note;
    }
};

const handleToggleItem = ({ orderId, itemIndex, checked }) => {
    const order = orders.value.find(o => o.id === orderId);
    if (order && order.items[itemIndex]) {
        order.items[itemIndex].checked = checked;
    }
};

// Approval Actions
const approveOrder = (orderId) => {
    const order = orders.value.find(o => o.id === orderId);
    if (order) {
        order.status = 'sent_to_kitchen';
        order.seconds = 0;
        
        // Update floor map
        const match = order.customer.match(/Table\s+(\d+)/);
        if (match) {
            const tableNumVal = parseInt(match[1]);
            const tbl = tables.value.find(t => t.tableNum === tableNumVal);
            if (tbl) tbl.status = 'Occupied';
        }
        alert(`Order ${orderId} approved and sent to KDS!`);
    }
};

const rejectOrder = (orderId) => {
    const reason = prompt('Enter reason for rejection:');
    if (reason !== null) {
        const order = orders.value.find(o => o.id === orderId);
        if (order) {
            order.status = 'rejected';
            order.note = reason || 'Order rejected by staff.';
            
            // Free table
            const match = order.customer.match(/Table\s+(\d+)/);
            if (match) {
                const tableNumVal = parseInt(match[1]);
                const tbl = tables.value.find(t => t.tableNum === tableNumVal);
                if (tbl) tbl.status = 'Available';
            }
        }
    }
};
</script>

<template>
    <Head title="Staff Portal — Gourmet Hub" />

    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans">
        
        <!-- Header -->
        <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-900/30">
                    <span class="material-symbols-outlined text-white">badge</span>
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-wide text-white">Staff Management Portal</h1>
                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Kitchen & Service Operations</p>
                </div>
            </div>

            <!-- Portal Tabs -->
            <div class="flex items-center gap-1.5 bg-slate-950 p-1 rounded-xl border border-slate-800">
                <button 
                    @click="activeTab = 'kds'"
                    :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2', activeTab === 'kds' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200']"
                >
                    <span class="material-symbols-outlined text-base">tablet_mac</span>
                    <span>KDS Queue ({{ kdsOrders.length }})</span>
                </button>
                <button 
                    @click="activeTab = 'floormap'"
                    :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2', activeTab === 'floormap' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200']"
                >
                    <span class="material-symbols-outlined text-base">map</span>
                    <span>Floor Table Map</span>
                </button>
                <button 
                    @click="activeTab = 'approvals'"
                    :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2', activeTab === 'approvals' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200']"
                >
                    <span class="material-symbols-outlined text-base">rate_review</span>
                    <span>Approvals ({{ pendingApprovalOrders.length }})</span>
                </button>
            </div>

            <!-- Home link -->
            <Link href="/" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">home</span>
                Exit to Landing Page
            </Link>
        </header>

        <!-- Main Body -->
        <main class="flex-grow p-6">
            
            <!-- ── VIEW 1: KDS QUEUE ── -->
            <div v-if="activeTab === 'kds'" class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-xl font-black text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">soup_kitchen</span> Active Kitchen Tickets
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Real-time checklist, prep timers, and order states.</p>
                    </div>
                </div>

                <div v-if="kdsOrders.length === 0" class="text-center py-20 bg-slate-900 border border-slate-800 rounded-3xl text-slate-500 text-sm">
                    <span class="material-symbols-outlined text-5xl mb-2 text-slate-600">playlist_add_check</span>
                    <p class="font-bold text-slate-400">All caught up!</p>
                    <p class="text-xs text-slate-500 mt-1">Submit new orders from the Customer Portal to fill this queue.</p>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fadeIn">
                    <OrderTicketCard 
                        v-for="order in kdsOrders" 
                        :key="order.id"
                        :order="order"
                        @status-change="handleStatusChange"
                        @note-edit="handleNoteEdit"
                        @toggle-item="handleToggleItem"
                    />
                </div>
            </div>

            <!-- ── VIEW 2: FLOOR MAP ── -->
            <div v-if="activeTab === 'floormap'" class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-xl font-black text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">deck</span> Waiter Dining Floor Map
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Live table seating and elapsed service durations.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <FloorTableCard 
                        v-for="tbl in tables" 
                        :key="tbl.tableNum"
                        :tableNum="tbl.tableNum"
                        :status="tbl.status"
                        :orders="orders.filter(o => o.customer.includes(`Table ${tbl.tableNum}`))"
                    />
                </div>
            </div>

            <!-- ── VIEW 3: APPROVALS ── -->
            <div v-if="activeTab === 'approvals'" class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-xl font-black text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">rate_review</span> Pending Customer QR Orders
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Review, approve, or reject customer self-ordered tickets.</p>
                    </div>
                </div>

                <div v-if="pendingApprovalOrders.length === 0" class="text-center py-20 bg-slate-900 border border-slate-800 rounded-3xl text-slate-500 text-sm">
                    <span class="material-symbols-outlined text-5xl mb-2 text-slate-600">done_all</span>
                    <p class="font-bold text-slate-400">No pending approvals</p>
                    <p class="text-xs text-slate-500 mt-1">Submitted orders from the QR Menu will show up here first.</p>
                </div>
                <div v-else class="space-y-4">
                    <div 
                        v-for="order in pendingApprovalOrders" 
                        :key="order.id" 
                        class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-blue-600/50 transition-all"
                    >
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-black text-base text-white font-mono">{{ order.id }}</span>
                                <span class="px-2.5 py-1 bg-amber-900/30 text-amber-300 border border-amber-800/40 rounded-lg text-xs font-bold">
                                    {{ order.customer }}
                                </span>
                                <span v-if="order.caption" class="px-2.5 py-1 bg-blue-900/30 text-blue-300 border border-blue-800/40 rounded-lg text-xs font-bold italic">
                                    🎉 "{{ order.caption }}"
                                </span>
                            </div>
                            <div class="text-xs text-slate-400 space-y-1.5 pl-1.5 border-l border-slate-800">
                                <p v-for="(item, idx) in order.items" :key="idx" class="flex items-center gap-1.5">
                                    <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                                    {{ item.name }}
                                </p>
                            </div>
                            <p v-if="order.instruction" class="text-[10px] text-slate-400 italic">Instruction: "{{ order.instruction }}"</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button 
                                @click="rejectOrder(order.id)" 
                                class="px-4 py-2 border border-slate-800 text-slate-400 hover:text-red-400 hover:border-red-900 rounded-xl text-xs font-bold transition-all active:scale-95"
                            >
                                Reject
                            </button>
                            <button 
                                @click="approveOrder(order.id)" 
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all active:scale-95 shadow-md shadow-blue-900/20"
                            >
                                Approve & Send to KDS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-slate-900 border-t border-slate-800 py-4 text-center text-[10px] text-slate-500">
            <p>Staff Operations v1.0.0 · Gourmet Hub RMS</p>
        </footer>
    </div>
</template>
