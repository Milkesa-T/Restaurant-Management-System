<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

// Import our new components
import MenuCard from '../Components/MenuCard.vue';
import CartDrawer from '../Components/CartDrawer.vue';
import OrderTracker from '../Components/OrderTracker.vue';
import OrderCaption from '../Components/OrderCaption.vue';
import OrderTicketCard from '../Components/OrderTicketCard.vue';
import FloorTableCard from '../Components/FloorTableCard.vue';
import QRScanner from '../Components/QRScanner.vue';
import Reservation from '../Components/Reservation.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

// App Tabs
const activeTab = ref('guide'); // guide, customer, staff, reserve

// Simulated Shared State
const selectedTable = ref(null);
const selectedZone = ref('');
const assignedWaiter = ref('');
const caption = ref('');
const orders = ref([
    {
        id: '#284-A',
        customer: 'Table 14 — Desta',
        caption: 'Anniversary Dinner 💖',
        seconds: 420,
        status: 'Preparing',
        instruction: 'Allergic to peanut oil',
        items: [
            { name: 'Doro Wat (Spicy Chicken)', checked: true },
            { name: 'Special Kitfo (Rare)', checked: false }
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
            { name: 'Veggie Combo (Beyaynetu)', checked: false },
            { name: 'Shiro Wat', checked: false }
        ],
        billing: { total: 750 },
        note: ''
    }
]);

// Menu Catalog Data
const dishes = [
    {
        id: 1,
        name: 'Doro Wat (Spicy Chicken Stew)',
        price: 650,
        category: 'Mains',
        description: 'Slow-simmered chicken drumsticks in rich Berbere sauce, seasoned with Niter Kibbeh, served with hard-boiled eggs and sourdough Injera.',
        dietary: ['spicy', 'halal'],
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDzZYcxiwtS3oy6TK7Ve1OjWNDxf1zQl5a2lXiPzO3ppiiWGeJUnNRVAaTOuYWXvQt1OD322UoeC0jsNo32_zDIcMOkxkiii4ETJNdR1VzgEbIKpFEdFI1HZFiaHrJw78v5FFAR9gHLEUavAI4s2ZUAzFD-HOaxUyHTqbCR2AEtx9l2TLdjpIkTk7ckIsMVSTzB3zdSnf4j23b9Kli-l3iBggGj5mgDpKPGSoM9yeyenUoZzytqf8y2fvMDlkP3trk_JYMDYHYzupk'
    },
    {
        id: 2,
        name: 'Shiro Wat (Chickpea Stew)',
        price: 350,
        category: 'Mains',
        description: 'Ground chickpeas simmered with onions, garlic, and Berbere, resulting in a smooth, thick, savory wat. 100% vegan.',
        dietary: ['vegan', 'vegetarian'],
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBO-dMU0u0EbLC2j2gBVu-BGgpP-P3dtw0KJnV04RprqHYNUjs-LaELu50fjHoIqvTIy-T1GCryAeJF0kQ4AbbOV33DPRWbDdYQX-0xzVFcF3UsRds_s30yz9Us_8wlTpMkqNbZdXU9Sh_DVzck0z1e5gaBh7krxd9_XWa39fZn1C9_iRRDvB6aw3dcpkXXfc-wAfg0pOs7DYFt-iq8kWrtjPI6rFujpnc1R6Rz5AV3kkPs2P-6T3Q8z2Y-KqklzDcwT3U9MQDcfhE'
    },
    {
        id: 3,
        name: 'Special Kitfo',
        price: 550,
        category: 'Mains',
        description: 'Minced lean beef warmed in spiced clarified butter (Niter Kibbeh) and Mitmita. Served raw, medium, or well done.',
        dietary: ['spicy', 'halal'],
        image: null
    },
    {
        id: 4,
        name: 'Veggie Combo (Beyaynetu)',
        price: 400,
        category: 'Mains',
        description: 'A colorful assortment of vegan dishes including red lentil stew (Misir Wat), yellow peas (Ater Wat), collard greens, and cabbage.',
        dietary: ['vegan', 'vegetarian', 'gluten-free'],
        image: null
    },
    {
        id: 5,
        name: 'Meat Sambusa (3 pcs)',
        price: 150,
        category: 'Starters',
        description: 'Crispy pastry shells filled with minced spiced beef, green chilies, and herbs.',
        dietary: ['halal'],
        image: null
    }
];

// Tables setup for Waiter Floor Map
const tables = ref([
    { tableNum: 1, status: 'Available' },
    { tableNum: 2, status: 'Occupied' },
    { tableNum: 3, status: 'Ordering' },
    { tableNum: 4, status: 'Served / Bill' },
    { tableNum: 14, status: 'Occupied' }
]);

// Component Refs
const qrScannerOpen = ref(false);
const cartDrawerOpen = ref(false);
const cartRef = ref(null);
const captionRefEl = ref(null);

// Customer Actions
const openScanner = () => {
    qrScannerOpen.value = true;
};

const handleQRScan = (result) => {
    selectedTable.value = result.table;
    selectedZone.value = result.zone;
    assignedWaiter.value = result.waiter;
    
    // Update simulated table state
    const tbl = tables.value.find(t => t.tableNum === result.table);
    if (tbl) tbl.status = 'Ordering';
};

const addDishToCart = (dish) => {
    if (!selectedTable.value) {
        alert('Please scan a Table QR code first before adding items to order!');
        openScanner();
        return;
    }
    
    captionRefEl.value.requireCaption((newCaption) => {
        caption.value = newCaption;
        cartRef.value.addItem(dish);
        cartDrawerOpen.value = true;
    });
};

const handlePlaceOrder = (orderDetails) => {
    const newOrderId = '#ORD-' + Math.floor(100 + Math.random() * 900);
    const newOrder = {
        id: newOrderId,
        customer: `Table ${selectedTable.value} — ${selectedZone.value.split(' — ')[1] || 'Guest'}`,
        caption: orderDetails.caption,
        seconds: 0,
        status: 'sent_to_kitchen',
        instruction: orderDetails.note,
        items: orderDetails.items.map(i => ({ name: `${i.name} (x${i.qty})`, checked: false })),
        billing: { total: orderDetails.total },
        note: ''
    };
    
    orders.value.unshift(newOrder);
    
    // Update simulated table status to occupied
    const tbl = tables.value.find(t => t.tableNum === selectedTable.value);
    if (tbl) tbl.status = 'Occupied';

    alert(`Order ${newOrderId} has been sent to the kitchen successfully!`);
};

// Staff / KDS Actions
const handleStatusChange = ({ orderId, status }) => {
    const order = orders.value.find(o => o.id === orderId);
    if (order) {
        order.status = status;
        
        // Dynamic waiter table state updates
        const tableNumVal = parseInt((order.customer || '').match(/\d+/)?.[0]);
        const tbl = tables.value.find(t => t.tableNum === tableNumVal);
        if (tbl) {
            if (status === 'Preparing') tbl.status = 'Occupied';
            else if (status === 'Ready') tbl.status = 'Served / Bill';
        }
    }
};

const handleNoteEdit = ({ orderId, currentNote }) => {
    const note = prompt(`Enter kitchen/waiter note for ${orderId}:`, currentNote);
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
</script>

<template>
    <Head title="Welcome to Gourmet Hub" />

    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans">
        <!-- Brand Header Bar -->
        <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-40 backdrop-blur-md bg-opacity-95">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-900/30">
                    <span class="material-symbols-outlined text-white">restaurant_menu</span>
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-wide text-white">Gourmet Hub</h1>
                    <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-widest">Restaurant Management System</p>
                </div>
            </div>

            <!-- Dashboard Navigation Tabs -->
            <div class="flex items-center gap-1.5 bg-slate-950 p-1 rounded-xl border border-slate-800">
                <button 
                    @click="activeTab = 'guide'"
                    :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2', activeTab === 'guide' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200']"
                >
                    <span class="material-symbols-outlined text-base">info</span>
                    <span>System Guide</span>
                </button>
                <button 
                    @click="activeTab = 'customer'"
                    :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2', activeTab === 'customer' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200']"
                >
                    <span class="material-symbols-outlined text-base">qr_code_scanner</span>
                    <span>Digital Menu</span>
                </button>
                <button 
                    @click="activeTab = 'staff'"
                    :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2', activeTab === 'staff' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200']"
                >
                    <span class="material-symbols-outlined text-base">soup_kitchen</span>
                    <span>Live KDS & Floor Monitor</span>
                </button>
                <button 
                    @click="activeTab = 'reserve'"
                    :class="['px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2', activeTab === 'reserve' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200']"
                >
                    <span class="material-symbols-outlined text-base">calendar_month</span>
                    <span>Reservations</span>
                </button>
            </div>

            <!-- Auth links -->
            <div class="flex items-center gap-3">
                <Link 
                    v-if="$page.props.auth?.user" 
                    :href="route('dashboard')"
                    class="px-4 py-2 rounded-xl bg-slate-800 text-white hover:bg-slate-700 text-xs font-bold transition-all"
                >
                    Staff Dashboard
                </Link>
                <template v-else>
                    <Link 
                        :href="route('login')"
                        class="text-xs text-slate-400 hover:text-white font-bold"
                    >
                        Log in
                    </Link>
                    <Link 
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all"
                    >
                        Register
                    </Link>
                </template>
            </div>
        </header>

        <!-- Main Body Workspace -->
        <main class="flex-grow">
            <!-- TAB 1: system guide -->
            <div v-if="activeTab === 'guide'" class="max-w-4xl mx-auto px-6 py-12 space-y-12">
                <div class="text-center space-y-4 max-w-xl mx-auto">
                    <h2 class="text-3xl font-black text-white leading-tight">Welcome to Gourmet Hub RMS</h2>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        A full-stack, multi-role management suite powered by **Laravel 11**, **Vue 3**, and **Inertia.js**.
                        Use the interactive dashboard tabs above to test the layout and components instantly.
                    </p>
                </div>

                <!-- Features Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                    <a href="/customer" class="group bg-slate-900 border border-slate-800 hover:border-purple-500 p-5 rounded-2xl transition-all hover:shadow-lg hover:-translate-y-0.5 flex flex-col gap-3">
                        <div class="w-11 h-11 bg-purple-950 text-purple-400 border border-purple-800 rounded-xl flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all">
                            <span class="material-symbols-outlined">qr_code_scanner</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-white mb-1">Customer Digital Menu →</h3>
                            <p class="text-slate-400 text-xs leading-relaxed">QR table scan, occasion captions, dish ordering, and live order tracker.</p>
                        </div>
                        <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">/customer</span>
                    </a>

                    <a href="/staff" class="group bg-slate-900 border border-slate-800 hover:border-blue-500 p-5 rounded-2xl transition-all hover:shadow-lg hover:-translate-y-0.5 flex flex-col gap-3">
                        <div class="w-11 h-11 bg-blue-950 text-blue-400 border border-blue-800 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <span class="material-symbols-outlined">badge</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-white mb-1">Staff Portal →</h3>
                            <p class="text-slate-400 text-xs leading-relaxed">Order approvals, KDS queue, and live waiter floor map. Full staff operations dashboard.</p>
                        </div>
                        <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">/staff</span>
                    </a>

                    <a href="/kds" class="group bg-slate-900 border border-slate-800 hover:border-emerald-500 p-5 rounded-2xl transition-all hover:shadow-lg hover:-translate-y-0.5 flex flex-col gap-3">
                        <div class="w-11 h-11 bg-emerald-950 text-emerald-400 border border-emerald-800 rounded-xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                            <span class="material-symbols-outlined">tablet_mac</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-white mb-1">KDS Tablet →</h3>
                            <p class="text-slate-400 text-xs leading-relaxed">Real-time orders queue with live prep timers. Connected to the backend database.</p>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">/kds</span>
                    </a>

                    <a href="/chef" class="group bg-slate-900 border border-slate-800 hover:border-amber-500 p-5 rounded-2xl transition-all hover:shadow-lg hover:-translate-y-0.5 flex flex-col gap-3">
                        <div class="w-11 h-11 bg-amber-950 text-amber-400 border border-amber-800 rounded-xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-all">
                            <span class="material-symbols-outlined">menu_book</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-white mb-1">Chef Terminal →</h3>
                            <p class="text-slate-400 text-xs leading-relaxed">Dish editor, catalog creator, and ingredients inventory with safety thresholds.</p>
                        </div>
                        <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">/chef</span>
                    </a>
                </div>

                <!-- Default Logins Alert -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <h3 class="font-bold text-white flex items-center gap-2 mb-4 text-sm uppercase tracking-wider">
                        <span class="material-symbols-outlined text-emerald-400">lock_open</span>
                        Seeded Demo Accounts
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 space-y-1">
                            <p class="font-bold text-emerald-400">Owner</p>
                            <p class="font-mono text-slate-400">owner@gourmethub.com</p>
                            <p class="text-slate-500 font-mono">password</p>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 space-y-1">
                            <p class="font-bold text-blue-400">Manager</p>
                            <p class="font-mono text-slate-400">manager@gourmethub.com</p>
                            <p class="text-slate-500 font-mono">password</p>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 space-y-1">
                            <p class="font-bold text-slate-300">Waiter</p>
                            <p class="font-mono text-slate-500">waiter@restaurant-rms.com</p>
                            <p class="text-slate-500 font-mono">password</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Customer Ordering Menu (Includes QR Scanner, Caption, MenuCard, Cart Drawer, Order Tracker) -->
            <div v-if="activeTab === 'customer'" class="max-w-6xl mx-auto px-6 py-10 space-y-8">
                
                <!-- Table Details Header -->
                <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 flex flex-wrap items-center justify-between gap-6">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full" :class="selectedTable ? 'bg-emerald-500' : 'bg-red-500'"></span>
                            <h3 class="font-black text-lg text-white">
                                {{ selectedTable ? `Table #${selectedTable} Activated` : 'No Active Session' }}
                            </h3>
                        </div>
                        <p class="text-xs text-slate-400">
                            {{ selectedTable ? `${selectedZone} · Assigned Waiter: ${assignedWaiter}` : 'Please activate a session by simulating a Table QR Code scan.' }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button 
                            @click="openScanner"
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-950/20"
                        >
                            <span class="material-symbols-outlined text-base">qr_code_scanner</span>
                            <span>Simulate QR Scan</span>
                        </button>
                        <button 
                            @click="cartDrawerOpen = true"
                            class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 border border-slate-700 relative"
                        >
                            <span class="material-symbols-outlined text-base">shopping_cart</span>
                            <span>Open Cart</span>
                            <span v-if="cartRef?.cartCount" class="absolute -top-2 -right-2 bg-emerald-600 text-white text-[9px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-slate-900">
                                {{ cartRef.cartCount }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Main Layout: Catalog Menu vs Tracker -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Dishes Menu Cards (2/3 width) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-black text-xl text-white">Chef's Signature Selections</h3>
                            <span class="text-xs text-emerald-400 font-bold uppercase tracking-wider">Simulated menu</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <MenuCard 
                                v-for="dish in dishes" 
                                :key="dish.id" 
                                :dish="dish" 
                                @add="addDishToCart" 
                            />
                        </div>
                    </div>

                    <!-- Right: Live Order Stepper Tracker (1/3 width) -->
                    <div class="space-y-6">
                        <h3 class="font-black text-xl text-white">Your Orders</h3>
                        <div v-if="orders.length === 0" class="text-center p-8 bg-slate-900 border border-slate-800 rounded-2xl text-slate-500 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-2 text-slate-600">receipt_long</span>
                            <p>No active orders placed this session.</p>
                        </div>
                        <div v-else class="space-y-4">
                            <OrderTracker 
                                v-for="order in orders" 
                                :key="order.id" 
                                :order="order" 
                            />
                        </div>
                    </div>
                </div>

                <!-- Custom modules & Modals -->
                <QRScanner :open="qrScannerOpen" @close="qrScannerOpen = false" @scan="handleQRScan" />
                <OrderCaption ref="captionRefEl" />
                <CartDrawer 
                    ref="cartRef" 
                    :open="cartDrawerOpen" 
                    :table-label="selectedTable ? `Table #${selectedTable}` : 'No Table'" 
                    :caption="caption" 
                    @close="cartDrawerOpen = false" 
                    @place-order="handlePlaceOrder"
                />
            </div>

            <!-- TAB 3: Staff KDS & Floor Monitor Dashboard -->
            <div v-if="activeTab === 'staff'" class="max-w-6xl mx-auto px-6 py-10 space-y-10">
                <!-- Waiter Floor Map table view -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-xl text-white">Waiter Floor Map</h3>
                        <span class="text-xs text-emerald-400 font-bold uppercase tracking-wider">Live table states</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                        <FloorTableCard 
                            v-for="tbl in tables" 
                            :key="tbl.tableNum"
                            :tableNum="tbl.tableNum"
                            :status="tbl.status"
                            :orders="orders.filter(o => o.customer.includes(`Table ${tbl.tableNum}`))"
                            @click="selectedTable = tbl.tableNum; activeTab = 'customer'"
                        />
                    </div>
                </div>

                <!-- KDS orders checklist tickets queue -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-xl text-white">Kitchen Display System (KDS) Queue</h3>
                        <span class="text-xs text-amber-400 font-bold uppercase tracking-wider">Active tickets</span>
                    </div>

                    <div v-if="orders.length === 0" class="text-center p-12 bg-slate-900 border border-slate-800 rounded-3xl text-slate-500 text-xs">
                        <span class="material-symbols-outlined text-4xl mb-2 text-slate-600">soup_kitchen</span>
                        <p>No active kitchen tickets. Send an order from the Digital Menu tab!</p>
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <OrderTicketCard 
                            v-for="order in orders" 
                            :key="order.id" 
                            :order="order"
                            @status-change="handleStatusChange"
                            @note-edit="handleNoteEdit"
                            @toggle-item="handleToggleItem"
                        />
                    </div>
                </div>
            </div>

            <!-- TAB 4: Reservations booking form -->
            <div v-if="activeTab === 'reserve'">
                <Reservation />
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 border-t border-slate-800 py-6 text-center text-xs text-slate-500">
            <p>Gourmet Hub RMS v1.0.0 · Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</p>
        </footer>
    </div>
</template>
