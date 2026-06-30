<script setup>
/**
 * FloorTableCard.vue - Waiter floor map table card component
 */
import { computed } from 'vue';

const props = defineProps({
    tableNum: {
        type: [Number, String],
        required: true
    },
    status: {
        type: String,
        required: true
    },
    orders: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['click']);

const label = computed(() => `T-${String(props.tableNum).padStart(2, '0')}`);

const STATUS_CONFIG = {
    'Ordering':      { color: 'bg-amber-500',  text: 'text-amber-700',  border: 'border-amber-200',  pulse: true },
    'Occupied':      { color: 'bg-emerald-600', text: 'text-emerald-700', border: 'border-emerald-100', pulse: false },
    'Served / Bill': { color: 'bg-blue-500',   text: 'text-blue-700',   border: 'border-blue-100',   pulse: false },
    'Cleaning':      { color: 'bg-cyan-400',   text: 'text-cyan-700',   border: 'border-cyan-100',   pulse: false },
    'Reserved':      { color: 'bg-slate-300',  text: 'text-slate-400',  border: 'border-slate-100',  pulse: false },
    'Available':     { color: 'bg-slate-200',  text: 'text-slate-400',  border: 'border-slate-100',  pulse: false },
};

const cfg = computed(() => STATUS_CONFIG[props.status] || STATUS_CONFIG['Available']);

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

const elapsed = computed(() => {
    return props.orders.length > 0 ? formatSeconds(props.orders[0].seconds || 0) : '';
});

const desc = computed(() => {
    if (props.orders.length > 0) {
        return (props.orders[0].customer || '').split('—')[1]?.trim() || 'Guest';
    }
    if (props.status === 'Reserved') {
        return '6 Guests • Mr. Smith';
    }
    return 'TABLE CLEAN';
});

const onClick = () => {
    emit('click');
};
</script>

<template>
    <div 
        @click="onClick"
        :class="['relative bg-white rounded-2xl border shadow-sm p-4 cursor-pointer transition-all hover:shadow-md hover:-translate-y-0.5 overflow-hidden group', cfg.border]"
    >
        <div :class="['absolute top-0 left-0 w-1.5 h-full', cfg.color]"></div>
        <span v-if="cfg.pulse" :class="['absolute top-3 right-3 w-2 h-2 rounded-full animate-pulse', cfg.color]"></span>
        
        <div class="flex justify-between items-start mb-3 ml-2">
            <span class="text-xl font-black text-slate-800">{{ label }}</span>
            <span v-if="elapsed" :class="['text-[10px] font-mono font-bold', cfg.text]">{{ elapsed }}</span>
        </div>
        
        <div class="ml-2 mb-3">
            <p :class="['font-bold text-xs mb-0.5', cfg.text]">{{ status }}</p>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ desc }}</p>
        </div>
        
        <div class="flex gap-1 ml-2">
            <div :class="['h-1 flex-grow rounded-full', status !== 'Available' ? cfg.color : 'bg-slate-100']"></div>
            <div :class="['h-1 flex-grow rounded-full', status === 'Occupied' || status === 'Served / Bill' ? cfg.color : 'bg-slate-100']"></div>
            <div class="h-1 flex-grow bg-slate-100 rounded-full"></div>
        </div>
    </div>
</template>
