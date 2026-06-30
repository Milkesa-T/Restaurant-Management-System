<script setup>
/**
 * CustomerPortal.vue - Main Customer Portal mirroring Customer.html
 */
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

// Import child components
import MenuCard from '../Components/MenuCard.vue';
import CartDrawer from '../Components/CartDrawer.vue';
import OrderTracker from '../Components/OrderTracker.vue';
import OrderCaption from '../Components/OrderCaption.vue';
import QRScanner from '../Components/QRScanner.vue';
import Reservation from '../Components/Reservation.vue';

// Page routing state (local views: 'home', 'menu', 'reservations', 'orders')
const currentView = ref('home');

// Session states
const currentTable = ref(null);
const currentZone = ref('');
const currentWaiter = ref(null);
const selectedCategory = ref('All');

// Modal / Drawer open states
const qrScannerOpen = ref(false);
const cartDrawerOpen = ref(false);

// Refs for components
const cartRef = ref(null);
const captionRef = ref(null);

// Simulated Active Orders list
const activeOrders = ref([]);
const customerOrderIds = ref([]);

// Menu list
const dishes = [
    {
        id: 1,
        name: 'Doro Wat (Spicy Chicken Stew)',
        price: 650,
        category: 'Mains (Wats & Tibs)',
        description: 'Slow-simmered chicken drumsticks in rich Berbere sauce, seasoned with Niter Kibbeh, served with hard-boiled eggs and sourdough Injera.',
        dietary: ['spicy', 'halal'],
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDzZYcxiwtS3oy6TK7Ve1OjWNDxf1zQl5a2lXiPzO3ppiiWGeJUnNRVAaTOuYWXvQt1OD322UoeC0jsNo32_zDIcMOkxkiii4ETJNdR1VzgEbIKpFEdFI1HZFiaHrJw78v5FFAR9gHLEUavAI4s2ZUAzFD-HOaxUyHTqbCR2AEtx9l2TLdjpIkTk7ckIsMVSTzB3zdSnf4j23b9Kli-l3iBggGj5mgDpKPGSoM9yeyenUoZzytqf8y2fvMDlkP3trk_JYMDYHYzupk'
    },
    {
        id: 2,
        name: 'Shiro Wat (Chickpea Stew)',
        price: 350,
        category: 'Vegan Platters (Beyaynetu)',
        description: 'Ground chickpeas simmered with onions, garlic, and Berbere, resulting in a smooth, thick, savory wat. 100% vegan.',
        dietary: ['vegan', 'vegetarian'],
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBO-dMU0u0EbLC2j2gBVu-BGgpP-P3dtw0KJnV04RprqHYNUjs-LaELu50fjHoIqvTIy-T1GCryAeJF0kQ4AbbOV33DPRWbDdYQX-0xzVFcF3UsRds_s30yz9Us_8wlTpMkqNbZdXU9Sh_DVzck0z1e5gaBh7krxd9_XWa39fZn1C9_iRRDvB6aw3dcpkXXfc-wAfg0pOs7DYFt-iq8kWrtjPI6rFujpnc1R6Rz5AV3kkPs2P-6T3Q8z2Y-KqklzDcwT3U9MQDcfhE'
    },
    {
        id: 3,
        name: 'Special Kitfo',
        price: 550,
        category: 'Mains (Wats & Tibs)',
        description: 'Minced lean beef warmed in spiced clarified butter (Niter Kibbeh) and Mitmita. Served raw, medium, or well done.',
        dietary: ['spicy', 'halal'],
        image: null
    },
    {
        id: 4,
        name: 'Veggie Combo (Beyaynetu)',
        price: 400,
        category: 'Vegan Platters (Beyaynetu)',
        description: 'A colorful assortment of vegan dishes including red lentil stew (Misir Wat), yellow peas (Ater Wat), collard greens, and cabbage.',
        dietary: ['vegan', 'vegetarian', 'gluten-free'],
        image: null
    },
    {
        id: 5,
        name: 'Meat Sambusa (3 pcs)',
        price: 150,
        category: 'Starters (Qurs)',
        description: 'Crispy pastry shells filled with minced spiced beef, green chilies, and herbs.',
        dietary: ['halal'],
        image: null
    },
    {
        id: 6,
        name: 'Traditional Tej (Honey Wine)',
        price: 200,
        category: 'Beverages (Tella & Tej)',
        description: 'Authentic house-brewed Ethiopian honey wine, sweet and slightly effervescent.',
        dietary: [],
        image: null
    }
];

// Computed Category filter
const filteredDishes = computed(() => {
    if (selectedCategory.value === 'All') return dishes;
    return dishes.filter(d => d.category === selectedCategory.value);
});

// Featured Dishes for Hero
const featuredDishes = computed(() => dishes.filter(d => d.image).slice(0, 3));

// Cart badge counts
const cartCount = computed(() => cartRef.value?.cartCount || 0);

// Alert Notification Badge for Orders status
const ordersAlert = computed(() => {
    return activeOrders.value.some(o => ['Ready', 'picked_by_waiter', 'served'].includes(o.status));
});

// Load session storage on mount
onMounted(() => {
    customerOrderIds.value = JSON.parse(sessionStorage.getItem('customer_orders') || '[]');
    
    // Check URL parameters for table
    const params = new URLSearchParams(window.location.search);
    const tableParam = params.get('table');
    if (tableParam) {
        currentTable.value = tableParam;
        currentZone.value = 'Zone A — Garden Terrace';
        currentWaiter.value = 'Sarah Chen';
    }
});

// Navigation helper
const navigateTo = (viewId) => {
    currentView.value = viewId;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// QR Scanning
const openQRScanner = () => {
    qrScannerOpen.value = true;
};

const handleQRScan = (result) => {
    currentTable.value = result.table;
    currentZone.value = result.zone;
    currentWaiter.value = result.waiter;
};

// Add to Cart with caption requirement
const handleAddDish = (dish) => {
    if (!currentTable.value) {
        alert('Please scan a table QR code first!');
        openScanner();
        return;
    }
    
    captionRef.value.requireCaption((captionText) => {
        cartRef.value.addItem(dish);
        cartDrawerOpen.value = true;
    });
};

// Cart Submit Order
const handlePlaceOrder = (details) => {
    const orderId = '#' + Math.floor(Math.random() * 1000).toString(16).toUpperCase().padStart(3, '0') + '-' + ['A', 'B', 'C', 'Z'][Math.floor(Math.random() * 4)];
    
    const newOrder = {
        id: orderId,
        customer: `Table ${currentTable.value} — ${details.caption || 'Guest'}`,
        caption: details.caption,
        seconds: 0,
        status: 'pending_waiter_approval',
        instruction: details.note,
        items: details.items.map(i => ({ name: `${i.qty}x ${i.name}`, checked: false })),
        billing: { total: details.total },
    };

    activeOrders.value.push(newOrder);
    customerOrderIds.value.push(orderId);
    sessionStorage.setItem('customer_orders', JSON.stringify(customerOrderIds.value));

    alert(`Order ${orderId} has been successfully sent to the kitchen!`);
    navigateTo('orders');
};
</script>

<template>
    <Head title="Customer Hub — Ethiopian Culinary Hub" />

    <div class="bg-slate-50 text-slate-900 font-sans selection:bg-emerald-600 selection:text-white min-h-screen pb-16 md:pb-0">
        
        <!-- Header Nav -->
        <header id="top-nav" class="fixed top-0 w-full z-40 bg-white/80 backdrop-blur-md shadow-sm transition-all">
            <nav class="flex justify-between items-center h-16 px-8 max-w-7xl mx-auto">
                <!-- Brand -->
                <div class="flex items-center gap-8">
                    <span @click="navigateTo('home')" class="text-xl font-black text-emerald-700 cursor-pointer select-none">
                        Ethiopian Culinary Hub
                    </span>
                    <div class="hidden md:flex gap-5">
                        <button 
                            @click="navigateTo('home')" 
                            :class="['text-sm font-semibold transition-colors pb-0.5', currentView === 'home' ? 'text-emerald-700 border-b-2 border-emerald-700' : 'text-slate-500 hover:text-emerald-700']"
                        >Home</button>
                        <button 
                            @click="navigateTo('menu')" 
                            :class="['text-sm font-semibold transition-colors pb-0.5', currentView === 'menu' ? 'text-emerald-700 border-b-2 border-emerald-700' : 'text-slate-500 hover:text-emerald-700']"
                        >Menu</button>
                        <button 
                            @click="navigateTo('reservations')" 
                            :class="['text-sm font-semibold transition-colors pb-0.5', currentView === 'reservations' ? 'text-emerald-700 border-b-2 border-emerald-700' : 'text-slate-500 hover:text-emerald-700']"
                        >Reservations</button>
                        <button 
                            @click="navigateTo('orders')" 
                            :class="['text-sm font-semibold transition-colors pb-0.5', currentView === 'orders' ? 'text-emerald-700 border-b-2 border-emerald-700' : 'text-slate-500 hover:text-emerald-700']"
                        >My Orders</button>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <div v-if="currentTable" class="px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-lg text-xs font-mono">
                        Table #{{ currentTable }}
                    </div>

                    <button @click="openQRScanner" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 rounded-lg text-xs font-bold border border-slate-200 hover:border-emerald-300 transition-all">
                        <span class="material-symbols-outlined text-sm">qr_code_scanner</span>
                        <span class="hidden sm:inline">Switch Table</span>
                    </button>

                    <button @click="cartDrawerOpen = true" class="p-2 rounded-full hover:bg-slate-100 transition-all active:scale-95 relative">
                        <span class="material-symbols-outlined text-emerald-700">shopping_cart</span>
                        <span v-if="cartCount" class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold">
                            {{ cartCount }}
                        </span>
                    </button>

                    <button @click="navigateTo('orders')" class="p-2 rounded-full hover:bg-slate-100 transition-all active:scale-95 relative">
                        <span class="material-symbols-outlined text-emerald-700">notifications</span>
                        <span v-if="ordersAlert" class="absolute -top-1 -right-1 bg-blue-500 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">!</span>
                    </button>
                </div>
            </nav>
        </header>

        <!-- Mobile Bottom Nav -->
        <nav class="md:hidden fixed bottom-0 left-0 w-full z-40 bg-white/90 backdrop-blur-md border-t border-slate-200 flex justify-around items-center h-16 shadow-lg">
            <button @click="navigateTo('home')" :class="['flex flex-col items-center justify-center gap-0.5', currentView === 'home' ? 'text-emerald-700' : 'text-slate-400']">
                <span class="material-symbols-outlined text-xl">home</span>
                <span class="text-[9px] font-bold">Home</span>
            </button>
            <button @click="navigateTo('menu')" :class="['flex flex-col items-center justify-center gap-0.5', currentView === 'menu' ? 'text-emerald-700' : 'text-slate-400']">
                <span class="material-symbols-outlined text-xl">menu_book</span>
                <span class="text-[9px] font-bold">Menu</span>
            </button>
            <button @click="openQRScanner" class="flex flex-col items-center justify-center gap-0.5 text-slate-400">
                <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-300 -mt-4">
                    <span class="material-symbols-outlined text-xl text-white">qr_code_scanner</span>
                </div>
                <span class="text-[9px] font-bold text-emerald-600">Scan QR</span>
            </button>
            <button @click="navigateTo('reservations')" :class="['flex flex-col items-center justify-center gap-0.5', currentView === 'reservations' ? 'text-emerald-700' : 'text-slate-400']">
                <span class="material-symbols-outlined text-xl">calendar_today</span>
                <span class="text-[9px] font-bold">Booking</span>
            </button>
            <button @click="navigateTo('orders')" :class="['flex flex-col items-center justify-center gap-0.5', currentView === 'orders' ? 'text-emerald-700' : 'text-slate-400']">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
                <span class="text-[9px] font-bold">Orders</span>
            </button>
        </nav>

        <!-- Main Workspace -->
        <main class="pt-16">
            
            <!-- ── VIEW: HOME ── -->
            <div v-if="currentView === 'home'" class="space-y-24 pb-24">
                <!-- Hero Section -->
                <section class="relative h-[88vh] min-h-[580px] flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 z-0">
                        <div class="w-full h-full bg-cover bg-center scale-105" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuAp6XZGyw1skk2UJm3VcSSjBBN54FyIFGycS8QBtSMP4YC9-s0Fkr5XeWA3k8UvhTOSXdT-oafCtN-mfj_YzMHJgXN_w98A4ahJui8F-OT-eFZZA1necALtXY8gd9OxrX-0SIL3SSSSCL4T3Sp4okVEP-cQ_cRQFQuDtxA2k98-fp5lzdRmBRbZzkey19zoL_rK7ePI330mbUxIDcQ3x-xjwgG7HjaUhT0TG3gbzj2qv75qpmKNbHCEHk6qv4FUmhCZ9COaIKvVHQeJ')"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/75 via-slate-900/40 to-transparent"></div>
                    </div>
                    <div class="relative z-10 w-full max-w-7xl px-8 text-white">
                        <div class="max-w-2xl space-y-6">
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full text-xs font-bold uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Kitchen Live Now
                            </div>
                            <h1 class="text-5xl md:text-6xl font-black leading-tight text-white">A Symphony of Taste & Heritage</h1>
                            <p class="text-lg text-white/80 leading-relaxed max-w-xl">Experience the pinnacle of Ethiopian gastronomy, where ancient tradition meets modern precision. Scan your table QR to begin.</p>
                            <div class="flex flex-wrap gap-4 pt-2">
                                <button @click="navigateTo('menu')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-xl font-bold transition-all active:scale-95 shadow-lg shadow-emerald-900/30 flex items-center gap-2">
                                    <span>View Digital Menu</span>
                                    <span class="material-symbols-outlined">menu_book</span>
                                </button>
                                <button @click="openQRScanner" class="bg-white/10 hover:bg-white/20 border border-white/30 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-bold transition-all active:scale-95 flex items-center gap-2">
                                    <span class="material-symbols-outlined">qr_code_scanner</span>
                                    <span>Scan Table QR</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- About Section -->
                <section class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-center px-8">
                    <div class="space-y-8">
                        <div class="inline-block px-4 py-1 bg-emerald-50 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-200">
                            The Culinary Hub Experience
                        </div>
                        <h2 class="text-3xl font-black text-slate-900">Modernizing the Ancient Art of Ethiopian Dining</h2>
                        <p class="text-slate-500 leading-relaxed">At Ethiopian Culinary Hub, we've taken the authentic, complex spice profiles of Addis Ababa and integrated them into a high-density, precision dining experience for the modern connoisseur.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-100 border border-slate-200/50">
                                <span class="material-symbols-outlined text-emerald-600 p-2 bg-emerald-50 rounded-lg">spa</span>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">Authentic Flavors</h4>
                                    <p class="text-slate-500 text-xs mt-1">Spices sourced directly from Ethiopia.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-100 border border-slate-200/50">
                                <span class="material-symbols-outlined text-blue-600 p-2 bg-blue-50 rounded-lg">speed</span>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">Modern Experience</h4>
                                    <p class="text-slate-500 text-xs mt-1">Zero-friction ordering & clinical precision.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-emerald-600/5 rounded-full blur-3xl group-hover:bg-emerald-600/10 transition-all"></div>
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                            <img class="w-full h-80 object-cover" alt="Chef Hands" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBLUxowiSHjXxFg4c-C4hett5LqJ1ykDNCILs1gyb_B6uUipohZvOGhhm6Qy5z7fm6u40PLByUbOzx080aQ7cnG1Z-fYy9PnLlgLFRUFj4JkmoBLPVwp-5yLi1Mrz-U7CncT5nnbxF45M_Vgwn8IqYCmFYiuApZpmCagUuu6sWCLXSbpNMyRps32QH3gLty82Et8b0szS4AdwPw-2nQgi8YoIqgH-MfqmbraB76rJH6WFf7vV0U1XsZrJ3UwkQnENzVicMfcFLhhleu"/>
                        </div>
                    </div>
                </section>

                <!-- Featured Dishes -->
                <section class="max-w-7xl mx-auto px-8">
                    <div class="flex justify-between items-end mb-12">
                        <div class="space-y-1">
                            <h2 class="text-3xl font-black text-slate-900">Featured Delicacies</h2>
                            <p class="text-slate-500 text-sm">Curated highlights from our seasonal menu</p>
                        </div>
                        <button @click="navigateTo('menu')" class="text-emerald-700 font-bold flex items-center gap-2 hover:translate-x-1 transition-transform text-sm">
                            <span>View Full Menu</span><span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <MenuCard 
                            v-for="dish in featuredDishes" 
                            :key="dish.id" 
                            :dish="dish" 
                            @add="handleAddDish"
                        />
                    </div>
                </section>
            </div>

            <!-- ── VIEW: MENU ── -->
            <div v-if="currentView === 'menu'" class="max-w-7xl mx-auto px-8 pb-24 space-y-10">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-700 text-3xl">restaurant_menu</span> Digital Culinary Menu
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">Every dish prepared with fresh spices and heritage sourcing.</p>
                    </div>
                </div>

                <!-- Category Filters -->
                <div class="flex justify-center gap-2 flex-wrap border-b border-slate-200 pb-5">
                    <button 
                        @click="selectedCategory = 'All'" 
                        :class="['px-4 py-2 rounded-full font-bold text-xs transition-all border', selectedCategory === 'All' ? 'bg-emerald-600 text-white' : 'border-slate-200 hover:border-emerald-600 text-slate-600']"
                    >ALL DISHES</button>
                    <button 
                        @click="selectedCategory = 'Starters (Qurs)'" 
                        :class="['px-4 py-2 rounded-full font-bold text-xs transition-all border', selectedCategory === 'Starters (Qurs)' ? 'bg-emerald-600 text-white' : 'border-slate-200 hover:border-emerald-600 text-slate-600']"
                    >STARTERS</button>
                    <button 
                        @click="selectedCategory = 'Mains (Wats & Tibs)'" 
                        :class="['px-4 py-2 rounded-full font-bold text-xs transition-all border', selectedCategory === 'Mains (Wats & Tibs)' ? 'bg-emerald-600 text-white' : 'border-slate-200 hover:border-emerald-600 text-slate-600']"
                    >MAINS</button>
                    <button 
                        @click="selectedCategory = 'Vegan Platters (Beyaynetu)'" 
                        :class="['px-4 py-2 rounded-full font-bold text-xs transition-all border', selectedCategory === 'Vegan Platters (Beyaynetu)' ? 'bg-emerald-600 text-white' : 'border-slate-200 hover:border-emerald-600 text-slate-600']"
                    >VEGAN / FASTING</button>
                    <button 
                        @click="selectedCategory = 'Beverages (Tella & Tej)'" 
                        :class="['px-4 py-2 rounded-full font-bold text-xs transition-all border', selectedCategory === 'Beverages (Tella & Tej)' ? 'bg-emerald-600 text-white' : 'border-slate-200 hover:border-emerald-600 text-slate-600']"
                    >BEVERAGES</button>
                </div>

                <!-- Menu Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <MenuCard 
                        v-for="dish in filteredDishes" 
                        :key="dish.id" 
                        :dish="dish" 
                        @add="handleAddDish"
                    />
                </div>
            </div>

            <!-- ── VIEW: RESERVATIONS ── -->
            <div v-if="currentView === 'reservations'">
                <Reservation />
            </div>

            <!-- ── VIEW: ORDERS TRACKER ── -->
            <div v-if="currentView === 'orders'" class="max-w-7xl mx-auto px-8 pb-24 space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-700">receipt_long</span> Your Orders Tracker
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">Live preparation updates as the kitchen cooks.</p>
                    </div>
                </div>

                <div v-if="activeOrders.length === 0" class="flex flex-col items-center justify-center py-16 text-center opacity-60">
                    <span class="material-symbols-outlined text-6xl text-emerald-700 mb-4">restaurant_menu</span>
                    <h3 class="font-bold text-slate-700 mb-2">No Active Orders Placed</h3>
                    <p class="text-sm text-slate-500 max-w-xs">Place an order from our digital menu and watch its status update live.</p>
                    <button @click="navigateTo('menu')" class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded-lg font-bold text-xs shadow">GO TO MENU</button>
                </div>
                <div v-else class="space-y-5">
                    <OrderTracker 
                        v-for="order in activeOrders" 
                        :key="order.id" 
                        :order="order" 
                    />
                </div>
            </div>
        </main>

        <!-- Modals & Overlays -->
        <QRScanner :open="qrScannerOpen" @close="qrScannerOpen = false" @scan="handleQRScan" />
        <OrderCaption ref="captionRef" />
        <CartDrawer 
            ref="cartRef"
            :open="cartDrawerOpen" 
            :table-label="currentTable ? `Table #${currentTable}` : 'No Table'" 
            :caption="captionRef?.savedCaption || ''" 
            @close="cartDrawerOpen = false" 
            @place-order="handlePlaceOrder"
        />
    </div>
</template>
