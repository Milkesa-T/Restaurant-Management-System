<script setup>
/**
 * CartDrawer.vue - Shopping Cart side drawer
 * Manages cart state internally and emits place-order event.
 */
import { ref, computed } from 'vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    tableLabel: { type: String, default: '—' },
    caption: { type: String, default: '' },
});

const emit = defineEmits(['close', 'place-order']);

const cart = ref([]);

// Exposed so parent / MenuCard can add items
function addItem(dish) {
    const exist = cart.value.find(i => i.name === dish.name);
    if (exist) {
        exist.qty++;
    } else {
        cart.value.push({ name: dish.name, price: Number(dish.price), qty: 1, image: dish.image || '' });
    }
}

function adjustQty(index, delta) {
    cart.value[index].qty += delta;
    if (cart.value[index].qty <= 0) cart.value.splice(index, 1);
}

function removeItem(index) {
    cart.value.splice(index, 1);
}

function clearCart() {
    cart.value = [];
}

const cartCount  = computed(() => cart.value.reduce((s, i) => s + i.qty, 0));
const subtotal   = computed(() => cart.value.reduce((s, i) => s + i.price * i.qty, 0));
const tax        = computed(() => subtotal.value * 0.15);
const total      = computed(() => subtotal.value + tax.value);

const specialNote = ref('');

function placeOrder() {
    emit('place-order', {
        items: cart.value.map(i => ({ ...i })),
        subtotal: subtotal.value,
        tax: tax.value,
        total: total.value,
        note: specialNote.value,
        caption: props.caption,
    });
    clearCart();
    specialNote.value = '';
    emit('close');
}

// Expose addItem so parent can call it via template ref
defineExpose({ addItem, clearCart, cartCount });
</script>

<template>
    <!-- Backdrop -->
    <Transition name="fade">
        <div v-if="open" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm" @click="emit('close')" />
    </Transition>

    <!-- Drawer -->
    <Transition name="slide-right">
        <div v-if="open" class="fixed right-0 top-0 bottom-0 z-50 w-full max-w-sm bg-white shadow-2xl flex flex-col border-l border-slate-100">

            <!-- Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-900 text-white">
                <div>
                    <p class="font-black text-base flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-400">shopping_bag</span>
                        Your Order
                    </p>
                    <p class="text-xs text-slate-400 font-mono">{{ tableLabel }}</p>
                    <p v-if="caption" class="text-[10px] text-amber-400 font-semibold mt-0.5">
                        <span class="material-symbols-outlined text-xs">rate_review</span>
                        "{{ caption }}"
                    </p>
                </div>
                <button @click="emit('close')" class="p-2 rounded-full hover:bg-white/10 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="cart.length === 0" class="flex-grow flex flex-col items-center justify-center gap-3 text-slate-300 px-8 text-center">
                <span class="material-symbols-outlined text-6xl">shopping_cart</span>
                <p class="font-bold text-slate-400">Your cart is empty</p>
                <p class="text-xs text-slate-300">Browse the menu and add dishes to start your order.</p>
            </div>

            <!-- Cart items -->
            <div v-else class="flex-grow overflow-y-auto px-4 py-3 space-y-2">
                <div
                    v-for="(item, index) in cart"
                    :key="item.name"
                    class="flex items-center gap-3 bg-white p-3 border border-slate-100 rounded-xl shadow-sm"
                >
                    <!-- Thumbnail -->
                    <div class="w-12 h-12 rounded-lg bg-slate-50 flex-shrink-0 overflow-hidden flex items-center justify-center border border-slate-100">
                        <img v-if="item.image" :src="item.image" class="w-full h-full object-cover rounded" :alt="item.name" />
                        <span v-else class="material-symbols-outlined text-sm text-slate-300">restaurant</span>
                    </div>

                    <!-- Info -->
                    <div class="flex-grow min-w-0">
                        <h4 class="text-sm font-bold text-slate-900 truncate">{{ item.name }}</h4>
                        <p class="text-xs text-slate-400 font-mono">{{ item.price }} ETB × {{ item.qty }}</p>
                    </div>

                    <!-- Qty controls -->
                    <div class="flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50">
                        <button @click="adjustQty(index, -1)" class="w-6 h-6 hover:bg-white rounded flex items-center justify-center text-xs font-bold text-slate-600">−</button>
                        <span class="text-xs font-bold px-1.5 font-mono">{{ item.qty }}</span>
                        <button @click="adjustQty(index, 1)" class="w-6 h-6 hover:bg-white rounded flex items-center justify-center text-xs font-bold text-slate-600">+</button>
                    </div>

                    <!-- Remove -->
                    <button @click="removeItem(index)" class="text-red-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>
            </div>

            <!-- Footer / Summary -->
            <div v-if="cart.length > 0" class="border-t border-slate-100 px-5 py-4 space-y-3 bg-slate-50">
                <!-- Special note -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Special Instructions</label>
                    <input
                        v-model="specialNote"
                        type="text"
                        placeholder="e.g. No onions, extra sauce…"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all"
                    />
                </div>

                <!-- Totals -->
                <div class="space-y-1 text-xs font-mono">
                    <div class="flex justify-between text-slate-500"><span>Subtotal</span><span>{{ subtotal.toFixed(2) }} ETB</span></div>
                    <div class="flex justify-between text-slate-500"><span>Tax (15%)</span><span>{{ tax.toFixed(2) }} ETB</span></div>
                    <div class="flex justify-between font-black text-slate-900 text-sm border-t border-slate-200 pt-1 mt-1">
                        <span>Total</span><span class="text-emerald-700">{{ total.toFixed(2) }} ETB</span>
                    </div>
                </div>

                <button
                    @click="placeOrder"
                    class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 active:scale-95 transition-all shadow-lg shadow-emerald-200 flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined text-base">receipt_long</span>
                    Place Order · {{ cartCount }} item{{ cartCount !== 1 ? 's' : '' }}
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-right-enter-active, .slide-right-leave-active { transition: transform 0.3s ease; }
.slide-right-enter-from, .slide-right-leave-to { transform: translateX(100%); }
</style>
