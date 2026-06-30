<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, Head } from '@inertiajs/vue3';

const props = defineProps({
    initialOrders: {
        type: Array,
        required: true
    }
});

// Reactive orders list
const orders = ref(JSON.parse(JSON.stringify(props.initialOrders)));

// Completed orders stack for recall
const completedStack = ref([]);

// Note modal state
const noteModal = ref({
    isOpen: false,
    targetOrderId: null,
    customMessage: ''
});

// Toast notification state
const toast = ref({
    show: false,
    message: ''
});

// Timer interval reference
let timerInterval = null;

// Format seconds into MM:SS
const formatTime = (totalSeconds) => {
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
};

// Increment timers every second
onMounted(() => {
    timerInterval = setInterval(() => {
        orders.value.forEach(order => {
            if (order.status !== 'Ready') {
                order.elapsed_seconds += 1;
            }
        });
    }, 1000);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

// Dynamic stats
const activeOrdersCount = computed(() => orders.value.length);

const avgPrepTime = computed(() => {
    if (orders.value.length === 0) return '00:00';
    const totalSeconds = orders.value.reduce((sum, order) => sum + order.elapsed_seconds, 0);
    const avg = totalSeconds / orders.value.length;
    return formatTime(Math.floor(avg));
});

// Toggle individual item completion
const toggleItemCompletion = (orderId, itemIndex) => {
    const order = orders.value.find(o => o.id === orderId);
    if (order && order.items && order.items[itemIndex]) {
        order.items[itemIndex].completed = !order.items[itemIndex].completed;
        showToastNotification(`Updated item in ${orderId}`);
    }
};

// Update order status
const updateOrderStatus = (orderId, newStatus) => {
    const orderIndex = orders.value.findIndex(o => o.id === orderId);
    if (orderIndex === -1) return;

    const order = orders.value[orderIndex];
    
    if (newStatus === 'Ready') {
        // Complete order: animate removal, save to recall stack
        order.status = 'Ready';
        showToastNotification(`Order ${orderId} marked READY and cleared.`);
        
        // Push copy to recall stack
        completedStack.value.push(JSON.parse(JSON.stringify(order)));
        
        // Remove from active list with a slight delay for transition
        setTimeout(() => {
            const index = orders.value.findIndex(o => o.id === orderId);
            if (index !== -1 && orders.value[index].status === 'Ready') {
                orders.value.splice(index, 1);
            }
        }, 300);
    } else {
        order.status = newStatus;
        showToastNotification(`Order ${orderId} status set to ${newStatus.toUpperCase()}`);
    }
};

// Recall last completed order
const recallLastTicket = () => {
    if (completedStack.value.length === 0) {
        showToastNotification('No tickets to recall.');
        return;
    }
    const recalledOrder = completedStack.value.pop();
    // Set status back to Preparing so it appears active
    recalledOrder.status = 'Preparing';
    orders.value.unshift(recalledOrder);
    showToastNotification(`Recalled order ${recalledOrder.id} to active board.`);
};

// Open Note Modal
const openNoteModal = (orderId) => {
    const order = orders.value.find(o => o.id === orderId);
    if (!order) return;
    noteModal.value.targetOrderId = orderId;
    noteModal.value.customMessage = order.note || '';
    noteModal.value.isOpen = true;
};

// Close Note Modal
const closeNoteModal = () => {
    noteModal.value.isOpen = false;
    noteModal.value.targetOrderId = null;
    noteModal.value.customMessage = '';
};

// Fill template notes
const fillNoteMessage = (text) => {
    noteModal.value.customMessage = text;
};

// Save custom note
const saveNote = () => {
    const order = orders.value.find(o => o.id === noteModal.value.targetOrderId);
    if (order) {
        order.note = noteModal.value.customMessage || null;
        order.note_by = order.note ? 'Chef Terminal' : null;
        showToastNotification(`Note saved for ${order.id}`);
    }
    closeNoteModal();
};

// Show Toast
const showToastNotification = (msg) => {
    toast.value.message = msg;
    toast.value.show = true;
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// Print All Open Orders trigger
const printAllOpen = () => {
    showToastNotification('Sending all open tickets to KDS thermal printer...');
};

// View Station Summary
const viewStationSummary = () => {
    showToastNotification('Station Summary: 4 Active, 2 Delayed, Teff Flour critical.');
};

// View Kitchen Stats
const viewKitchenStats = () => {
    showToastNotification('Stats: Efficiency 94% | Avg Prep 18 min | Orders today: 42');
};

const refreshSystem = () => {
    // Reset to defaults
    orders.value = JSON.parse(JSON.stringify(props.initialOrders));
    completedStack.value = [];
    showToastNotification('KDS Board Refreshed.');
};
</script>

<template>
    <Head title="KDS Tablet - Ethiopian Culinary Hub" />

    <div class="bg-background text-on-surface font-body-md min-h-screen flex flex-col overflow-hidden select-none">
        
        <!-- TopAppBar -->
        <header class="sticky top-0 w-full z-50 glass-panel border-b border-outline-variant/30 flex justify-between items-center px-lg py-sm shadow-sm">
            <div class="flex items-center gap-md">
                <Link href="/chef" class="p-sm rounded-full hover:bg-surface-variant/50 transition-colors active:scale-90 flex items-center border border-transparent hover:border-outline-variant/30">
                    <span class="material-symbols-outlined text-primary">menu</span>
                </Link>
                <h1 class="font-title-md text-title-md uppercase tracking-wider text-primary font-bold">ETHIOPIAN CULINARY HUB</h1>
            </div>
            
            <div class="flex items-center gap-xl">
                <div class="flex flex-col items-end">
                    <span class="font-label-caps text-[10px] text-on-surface-variant tracking-wider">ACTIVE ORDERS</span>
                    <span class="font-title-md text-title-md text-primary font-extrabold">{{ activeOrdersCount }}</span>
                </div>
                <div class="flex flex-col items-end">
                    <span class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-wider">Avg Prep Time</span>
                    <span class="font-price-lg text-price-lg text-tertiary font-bold transition-all duration-500">{{ avgPrepTime }}</span>
                </div>
                <div class="h-8 w-px bg-outline-variant/30"></div>
                <button @click="refreshSystem" class="p-sm rounded-full hover:bg-surface-variant/50 transition-colors active:scale-95 flex items-center" title="Refresh Board">
                    <span class="material-symbols-outlined text-on-surface-variant">refresh</span>
                </button>
            </div>
        </header>

        <!-- Main KDS Content (Scrollable Grid) -->
        <main class="flex-1 overflow-y-auto p-lg custom-scrollbar pb-32">
            <div class="order-grid">
                <TransitionGroup name="list">
                    <div 
                        v-for="order in orders" 
                        :key="order.id"
                        class="bg-surface-container-lowest border rounded-xl flex flex-col h-fit transition-all duration-300 shadow-sm"
                        :class="[
                            order.elapsed_seconds >= 1200 ? 'border-2 border-error shadow-lg shadow-error/10' : 'border-outline-variant/50',
                            order.status === 'Ready' ? 'opacity-40 scale-95' : ''
                        ]"
                    >
                        <!-- Card Header -->
                        <div 
                            class="p-md border-b border-outline-variant/30 flex justify-between items-start"
                            :class="order.elapsed_seconds >= 1200 ? 'bg-error-container/20 border-error/20' : 'bg-secondary-container/10'"
                        >
                            <div>
                                <span class="font-price-sm text-price-sm font-bold" :class="order.elapsed_seconds >= 1200 ? 'text-error' : 'text-secondary'">{{ order.id }}</span>
                                <h3 class="font-title-md text-title-md font-semibold text-on-surface">{{ order.customer }}</h3>
                            </div>
                            
                            <!-- Timer Badge -->
                            <span 
                                class="font-price-sm text-price-sm px-sm py-xs rounded font-bold transition-all"
                                :class="[
                                    order.elapsed_seconds >= 1200 ? 'text-error bg-error-container animate-pulse' : 'text-on-surface-variant bg-surface-variant'
                                ]"
                            >
                                {{ formatTime(order.elapsed_seconds) }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-md flex-1 space-y-md">
                            
                            <!-- Allergy Instruction -->
                            <div 
                                v-if="order.instruction" 
                                class="flex items-center gap-sm p-md bg-error/10 border border-error/30 rounded-xl text-error animate-pulse shadow-sm"
                            >
                                <span class="material-symbols-outlined text-xl">warning</span>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Guest Instruction</span>
                                    <span class="font-title-md text-title-md uppercase font-bold tracking-tight">{{ order.instruction }}</span>
                                </div>
                            </div>

                            <!-- Delayed Kitchen note (If exists) -->
                            <div 
                                v-if="order.note" 
                                class="bg-tertiary-container/10 p-sm rounded border border-tertiary/20 flex gap-sm items-start"
                            >
                                <span class="material-symbols-outlined text-tertiary-container text-md">info</span>
                                <p class="text-xs text-on-tertiary-container italic leading-relaxed">
                                    "{{ order.note }}" <span v-if="order.note_by">— {{ order.note_by }}</span>
                                </p>
                            </div>

                            <!-- Order Items List -->
                            <ul class="space-y-sm">
                                <li 
                                    v-for="(item, index) in order.items" 
                                    :key="index"
                                    @click="toggleItemCompletion(order.id, index)"
                                    class="flex justify-between items-center py-sm border-b border-outline-variant/10 cursor-pointer hover:bg-surface-variant/10 px-xs rounded transition-all"
                                >
                                    <div class="flex items-center gap-sm flex-1 min-w-0">
                                        <!-- Item Image / Fallback -->
                                        <div class="w-12 h-12 rounded bg-surface-variant flex-shrink-0 overflow-hidden border border-outline-variant/10">
                                            <img 
                                                v-if="item.image" 
                                                :src="item.image" 
                                                :alt="item.name" 
                                                class="w-full h-full object-cover"
                                            />
                                            <div v-else class="w-full h-full bg-on-surface-variant/10 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-sm opacity-40">restaurant</span>
                                            </div>
                                        </div>
                                        
                                        <span 
                                            class="font-body-md text-body-md text-on-surface truncate"
                                            :class="{ 'line-through opacity-50': item.completed }"
                                        >
                                            <span class="font-extrabold text-lg text-primary mr-xs">{{ item.quantity }}x</span> 
                                            {{ item.name }}
                                        </span>
                                    </div>
                                    
                                    <!-- Checked Icon -->
                                    <span 
                                        class="material-symbols-outlined text-xl transition-all"
                                        :class="item.completed ? 'text-primary' : 'text-on-surface-variant opacity-60'"
                                    >
                                        {{ item.completed ? 'check_circle' : 'circle' }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <!-- Card Action buttons (NEW, PREP, READY) -->
                        <div class="p-md pt-0 grid grid-cols-3 gap-sm border-t border-outline-variant/10 pt-md mt-sm">
                            <button 
                                @click="updateOrderStatus(order.id, 'New')"
                                class="status-btn flex flex-col items-center py-2 rounded-lg border text-xs font-bold transition-all active:scale-95"
                                :class="order.status === 'New' ? 'border-primary bg-primary text-on-primary shadow-sm' : 'border-outline-variant/30 bg-surface text-on-surface-variant hover:bg-surface-variant/30'"
                            >
                                <span class="font-label-caps text-label-caps">NEW</span>
                            </button>
                            <button 
                                @click="updateOrderStatus(order.id, 'Preparing')"
                                class="status-btn flex flex-col items-center py-2 rounded-lg border text-xs font-bold transition-all active:scale-95"
                                :class="order.status === 'Preparing' ? 'border-primary bg-primary text-on-primary shadow-sm' : 'border-outline-variant/30 bg-surface text-on-surface-variant hover:bg-surface-variant/30'"
                            >
                                <span class="font-label-caps text-label-caps">PREP</span>
                            </button>
                            <button 
                                @click="updateOrderStatus(order.id, 'Ready')"
                                class="status-btn flex flex-col items-center py-2 rounded-lg border text-xs font-bold transition-all active:scale-95"
                                :class="order.status === 'Ready' ? 'border-primary bg-primary text-on-primary shadow-sm' : 'border-outline-variant/30 bg-surface text-on-surface-variant hover:bg-surface-variant/30'"
                            >
                                <span class="font-label-caps text-label-caps">READY</span>
                            </button>
                        </div>
                        
                        <!-- Add Note Button -->
                        <button 
                            @click="openNoteModal(order.id)"
                            class="mx-md mb-md p-sm text-left border border-outline rounded-lg flex items-center justify-center gap-xs text-on-surface-variant hover:bg-surface-variant/30 transition-colors bg-secondary-container/5 active:scale-98 border-dashed"
                        >
                            <span class="material-symbols-outlined text-sm">edit_note</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider">KITCHEN NOTE</span>
                        </button>
                    </div>
                </TransitionGroup>
            </div>
            
            <!-- Empty state -->
            <div v-if="orders.length === 0" class="flex flex-col items-center justify-center p-20 text-center space-y-md bg-surface-container/30 rounded-2xl border border-dashed border-outline-variant/50 max-w-lg mx-auto mt-10">
                <span class="material-symbols-outlined text-6xl text-primary opacity-50 animate-bounce">check_circle</span>
                <h3 class="text-xl font-bold">All Tickets Completed!</h3>
                <p class="text-sm text-on-surface-variant">Awesome job kitchen team. Grab a drink or recall the last ticket if you need to double check.</p>
                <button @click="recallLastTicket" class="px-lg py-sm bg-primary text-on-primary rounded-xl font-bold text-xs uppercase tracking-wider hover:opacity-90 active:scale-95 transition-all">Recall Last Ticket</button>
            </div>
        </main>

        <!-- Bottom Action Bar (KDS Controls) -->
        <footer class="fixed bottom-0 w-full z-50 bg-primary dark:bg-primary-container shadow-lg flex justify-around items-center h-20 px-gutter rounded-t-xl">
            <button 
                @click="recallLastTicket"
                class="flex items-center gap-md px-lg py-sm bg-primary-fixed-dim/20 text-on-primary rounded-xl transition-transform active:scale-95 duration-200 hover:bg-primary-fixed-dim/35"
            >
                <span class="material-symbols-outlined">undo</span>
                <span class="font-label-caps text-label-caps uppercase tracking-wider">Recall Last Ticket</span>
            </button>
            
            <div class="h-10 w-px bg-on-primary/20"></div>
            
            <button 
                @click="viewStationSummary"
                class="flex items-center gap-md px-lg py-sm text-on-primary/70 hover:text-on-primary transition-colors active:scale-95 duration-200"
            >
                <span class="material-symbols-outlined">inventory</span>
                <span class="font-label-caps text-label-caps uppercase tracking-wider">Station Summary</span>
            </button>
            
            <div class="h-10 w-px bg-on-primary/20"></div>
            
            <button 
                @click="printAllOpen"
                class="flex items-center gap-md px-lg py-sm text-on-primary/70 hover:text-on-primary transition-colors active:scale-95 duration-200"
            >
                <span class="material-symbols-outlined">print</span>
                <span class="font-label-caps text-label-caps uppercase tracking-wider">Print All Open</span>
            </button>
            
            <button 
                @click="viewKitchenStats"
                class="ml-auto flex items-center gap-sm px-lg py-md bg-secondary-fixed text-on-secondary-fixed rounded-full shadow-md hover:bg-secondary-fixed-dim hover:shadow-lg active:scale-95 transition-all"
            >
                <span class="material-symbols-outlined text-sm">analytics</span>
                <span class="font-label-caps text-label-caps uppercase font-bold">Kitchen Stats</span>
            </button>
        </footer>

        <!-- Toast Notification Alert -->
        <div 
            v-if="toast.show" 
            class="fixed bottom-24 right-6 z-[70] bg-inverse-surface text-inverse-on-surface px-lg py-md rounded-xl shadow-2xl flex items-center gap-sm border border-outline-variant/20 animate-in fade-in slide-in-from-bottom-5 duration-200"
        >
            <span class="material-symbols-outlined text-primary-fixed">info</span>
            <span class="text-sm font-semibold tracking-wide">{{ toast.message }}</span>
        </div>

        <!-- Note Modal -->
        <div v-if="noteModal.isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-md">
            <!-- Modal Backdrop -->
            <div class="absolute inset-0 bg-on-background/40 backdrop-blur-sm transition-all" @click="closeNoteModal"></div>
            
            <!-- Modal Box -->
            <div class="relative w-full max-w-md bg-surface rounded-xl shadow-2xl overflow-hidden border border-outline-variant animate-in fade-in zoom-in-95 duration-200">
                <div class="p-lg border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                    <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-primary font-bold">Prep Note: <span class="text-secondary font-mono">{{ noteModal.targetOrderId }}</span></h2>
                    <button class="p-sm rounded-full hover:bg-surface-variant transition-colors flex items-center active:scale-90" @click="closeNoteModal">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="p-lg space-y-lg">
                    <div class="space-y-sm">
                        <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">SELECT COMMON REASON</label>
                        <div class="grid grid-cols-2 gap-sm">
                            <button 
                                class="p-md text-xs font-semibold border border-outline-variant rounded-lg hover:bg-primary/5 hover:border-primary transition-colors text-left focus:ring-1 focus:ring-primary" 
                                @click="fillNoteMessage('Wait for Ingredients')"
                            >
                                Wait for Ingredients
                            </button>
                            <button 
                                class="p-md text-xs font-semibold border border-outline-variant rounded-lg hover:bg-primary/5 hover:border-primary transition-colors text-left focus:ring-1 focus:ring-primary" 
                                @click="fillNoteMessage('Kitchen Bottleneck')"
                            >
                                Kitchen Bottleneck
                            </button>
                            <button 
                                class="p-md text-xs font-semibold border border-outline-variant rounded-lg hover:bg-primary/5 hover:border-primary transition-colors text-left focus:ring-1 focus:ring-primary" 
                                @click="fillNoteMessage('Customer Delay')"
                            >
                                Customer Delay
                            </button>
                            <button 
                                class="p-md text-xs font-semibold border border-outline-variant rounded-lg hover:bg-primary/5 hover:border-primary transition-colors text-left focus:ring-1 focus:ring-primary" 
                                @click="fillNoteMessage('Substitution Needed')"
                            >
                                Substitution Needed
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-sm">
                        <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">CUSTOM MESSAGE</label>
                        <textarea 
                            v-model="noteModal.customMessage"
                            class="w-full h-32 p-md border border-outline-variant/50 rounded-lg bg-surface-container-lowest focus:ring-2 focus:ring-primary focus:border-transparent outline-none font-body-md text-sm custom-scrollbar" 
                            placeholder="Enter specific delay or instruction details..."
                        ></textarea>
                    </div>
                    
                    <div class="flex gap-md pt-md border-t border-outline-variant/20">
                        <button 
                            class="flex-1 py-md border border-outline text-on-surface-variant rounded-lg font-label-caps text-label-caps hover:bg-surface-variant transition-colors active:scale-95" 
                            @click="closeNoteModal"
                        >
                            CANCEL
                        </button>
                        <button 
                            class="flex-1 py-md bg-primary text-on-primary rounded-lg font-label-caps text-label-caps shadow-lg hover:opacity-90 active:scale-95 transition-all" 
                            @click="saveNote"
                        >
                            SAVE NOTE
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* Animated ticket transitions */
.list-enter-active,
.list-leave-active {
    transition: all 0.4s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
}
.list-move {
    transition: transform 0.4s ease;
}
</style>
