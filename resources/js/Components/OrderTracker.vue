<script setup>
/**
 * OrderTracker.vue - Customer-facing order status stepper card
 * Shows live progress through the kitchen pipeline for a single order.
 */
import { computed } from 'vue';

const props = defineProps({
    order: { type: Object, required: true },
});

const ORDER_STEPS = [
    { key: 'pending_waiter_approval', label: 'Received',  icon: 'receipt_long' },
    { key: 'sent_to_kitchen',         label: 'Approved',  icon: 'check_circle' },
    { key: 'Preparing',               label: 'Cooking',   icon: 'soup_kitchen' },
    { key: 'Ready',                   label: 'Ready!',    icon: 'notifications_active' },
    { key: 'picked_by_waiter',        label: 'En Route',  icon: 'delivery_dining' },
    { key: 'served',                  label: 'Served',    icon: 'restaurant' },
    { key: 'Closed',                  label: 'Settled',   icon: 'payments' },
];

const isRejected = computed(() => props.order.status === 'rejected');

const stepIdx = computed(() => {
    const idx = ORDER_STEPS.findIndex(s => s.key === props.order.status);
    return idx < 0 ? 0 : idx;
});

const total = computed(() => (props.order.billing?.total || 0).toFixed(2));

function stepState(i) {
    if (i < stepIdx.value) return 'done';
    if (i === stepIdx.value) return 'current';
    return 'future';
}
</script>

<template>
    <!-- Rejected order -->
    <div v-if="isRejected" class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-red-50 border-b border-red-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600">cancel</span>
            </div>
            <div>
                <p class="font-bold text-slate-900 text-sm">{{ order.customer || '—' }}</p>
                <p class="text-xs text-red-600 font-bold">{{ order.id }} — Order Rejected</p>
            </div>
        </div>
        <div class="px-5 py-4 text-xs text-red-700">
            {{ order.note ? `Reason: "${order.note}"` : 'This order was rejected by the waiter.' }}
        </div>
    </div>

    <!-- Active order -->
    <div v-else class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <!-- Header -->
        <div class="px-5 py-4 border-b border-slate-50 flex justify-between items-start">
            <div class="space-y-1">
                <p class="font-bold text-slate-900 text-sm">{{ order.customer || '—' }}</p>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs text-slate-400">{{ order.id }}</span>
                    <span class="px-2 py-0.5 rounded border text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border-emerald-200">
                        {{ order.status }}
                    </span>
                </div>
                <!-- Caption -->
                <div v-if="order.caption" class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-800 font-semibold">
                    <span class="material-symbols-outlined text-sm text-amber-600">rate_review</span>
                    <span>"{{ order.caption }}"</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400">Total</p>
                <p class="font-mono font-bold text-emerald-700 text-sm">{{ total }} ETB</p>
            </div>
        </div>

        <!-- Progress Stepper -->
        <div class="px-5 py-4 flex items-center overflow-x-auto gap-0">
            <template v-for="(step, i) in ORDER_STEPS" :key="step.key">
                <div class="flex flex-col items-center gap-1 min-w-0">
                    <div
                        :class="[
                            'w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all',
                            stepState(i) === 'done'    ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : '',
                            stepState(i) === 'current' ? 'bg-white border-emerald-500 text-emerald-700 shadow-md animate-pulse' : '',
                            stepState(i) === 'future'  ? 'bg-slate-50 border-slate-200 text-slate-300' : '',
                        ]"
                    >
                        <span class="material-symbols-outlined text-sm">{{ step.icon }}</span>
                    </div>
                    <span
                        :class="[
                            'text-[9px] font-bold uppercase text-center leading-tight truncate max-w-[52px]',
                            stepState(i) !== 'future' ? 'text-emerald-700' : 'text-slate-300',
                        ]"
                    >{{ step.label }}</span>
                </div>
                <!-- Connector line -->
                <div
                    v-if="i < ORDER_STEPS.length - 1"
                    :class="['flex-grow h-0.5 mt-4 transition-all', i < stepIdx ? 'bg-emerald-500' : 'bg-slate-200']"
                />
            </template>
        </div>

        <!-- Items & Notes -->
        <div class="px-5 pb-4 space-y-2">
            <div class="text-xs text-slate-500 space-y-1">
                <div v-for="item in (order.items || [])" :key="item.name" class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs text-emerald-500">check_small</span>
                    {{ item.name }}
                </div>
            </div>
            <div v-if="order.instruction" class="flex items-start gap-1.5 text-xs text-slate-500 italic">
                <span class="material-symbols-outlined text-sm text-slate-400">info</span>
                <span>"{{ order.instruction }}"</span>
            </div>
        </div>
    </div>
</template>
