<script setup>
/**
 * OrderTicketCard.vue - KDS Order Ticket Card component (Chef / Waiter views)
 */
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['statusChange', 'noteEdit', 'toggleItem']);

// Timer calculation
const seconds = ref(props.order.seconds || 0);
let timerId = null;

onMounted(() => {
    timerId = setInterval(() => {
        seconds.value++;
    }, 1000);
});

onUnmounted(() => {
    if (timerId) clearInterval(timerId);
});

const formatSeconds = (totalSeconds) => {
    const hrs = Math.floor(totalSeconds / 3600);
    const mins = Math.floor((totalSeconds % 3600) / 60);
    const secs = totalSeconds % 60;
    return [
        hrs > 0 ? String(hrs).padStart(2, '0') : null,
        String(mins).padStart(2, '0'),
        String(secs).padStart(2, '0')
    ].filter(Boolean).join(':');
};

const elapsed = computed(() => formatSeconds(seconds.value));

const timerClass = computed(() => {
    if (seconds.value > 900) return 'text-red-600';
    if (seconds.value > 480) return 'text-amber-600';
    return 'text-emerald-600';
});

const borderClass = computed(() => {
    if (seconds.value > 900) return 'border-red-200 shadow-red-100';
    if (seconds.value > 480) return 'border-amber-200 shadow-amber-100';
    return 'border-slate-100';
});

const tableLabel = computed(() => (props.order.customer || '').split('—')[0].trim());
const guestName = computed(() => (props.order.customer || '').split('—')[1]?.trim() || 'Guest');

const canStart = computed(() => props.order.status === 'sent_to_kitchen');
const canReady = computed(() => props.order.status === 'Preparing');

const toggleItem = (idx, event) => {
    emit('toggleItem', { orderId: props.order.id, itemIndex: idx, checked: event.target.checked });
};

const startPrep = () => {
    emit('statusChange', { orderId: props.order.id, status: 'Preparing' });
};

const markReady = () => {
    emit('statusChange', { orderId: props.order.id, status: 'Ready' });
};

const openNote = () => {
    emit('noteEdit', { orderId: props.order.id, currentNote: props.order.note || '' });
};
</script>

<template>
    <div :class="['bg-white rounded-2xl border shadow-sm overflow-hidden flex flex-col transition-all', borderClass]">
        <!-- Ticket Header -->
        <div class="px-4 py-3 bg-slate-900 text-white flex justify-between items-start">
            <div>
                <p class="font-black text-sm tracking-wide">{{ tableLabel }}</p>
                <p class="text-slate-400 text-[10px] font-mono">{{ order.id }} · {{ guestName }}</p>
                <!-- Caption -->
                <div v-if="order.caption" class="flex items-center gap-1 px-2 py-1 bg-amber-900/30 rounded border border-amber-700 text-[10px] text-amber-300 font-bold mt-1">
                    <span class="material-symbols-outlined text-xs">rate_review</span> "{{ order.caption }}"
                </div>
            </div>
            <div class="text-right">
                <p :class="['font-mono font-black text-lg', timerClass]">{{ elapsed }}</p>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border-emerald-200">
                    {{ order.status }}
                </span>
            </div>
        </div>

        <!-- Items checklist -->
        <div class="px-4 py-3 flex-grow space-y-2 border-b border-slate-50">
            <label v-for="(item, idx) in order.items" :key="idx" class="flex items-center gap-2 cursor-pointer group select-none">
                <input 
                    type="checkbox" 
                    :checked="item.checked"
                    @change="toggleItem(idx, $event)"
                    class="w-4 h-4 rounded border-slate-300 accent-emerald-600 cursor-pointer" 
                />
                <span :class="['text-xs font-semibold transition-all', item.checked ? 'line-through text-slate-300' : 'text-slate-700']">
                    {{ item.name }}
                </span>
            </label>
            <p v-if="order.instruction" class="text-[10px] text-slate-400 italic border-l-2 border-amber-300 pl-2 mt-1">
                "{{ order.instruction }}"
            </p>
            <p v-if="order.note" class="text-[10px] text-red-500 font-bold border-l-2 border-red-300 pl-2">
                {{ order.note }}
            </p>
        </div>

        <!-- Actions -->
        <div class="px-4 py-3 flex gap-2">
            <button 
                v-if="canStart" 
                @click="startPrep" 
                class="flex-grow py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 active:scale-95 transition-all flex items-center justify-center gap-1 shadow-sm"
            >
                <span class="material-symbols-outlined text-sm">soup_kitchen</span> Start Prep
            </button>
            <button 
                v-if="canReady" 
                @click="markReady" 
                class="flex-grow py-2 bg-sky-600 text-white rounded-lg text-xs font-bold hover:bg-sky-700 active:scale-95 transition-all flex items-center justify-center gap-1 shadow-sm"
            >
                <span class="material-symbols-outlined text-sm">notifications_active</span> Mark Ready
            </button>
            <button 
                @click="openNote" 
                title="Edit Note" 
                class="px-3 py-2 bg-slate-100 rounded-lg text-slate-500 hover:bg-amber-50 hover:text-amber-700 transition-colors"
            >
                <span class="material-symbols-outlined text-sm">sticky_note_2</span>
            </button>
        </div>
    </div>
</template>
